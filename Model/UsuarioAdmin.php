<?php

require_once 'BancoLogin.php';
require_once '../log/GeraLog.php';
require_once '../Validation/ValidaToken.php';
require_once '../util/Arquivo.php';
require_once '../util/UPermissao.php';

class UsuarioAdmin
{
    public static function getUsuario()
    {
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        //var_dump($permicao) ;
        return $permicao;
    }

    public static function geraLog($argumentos, $erroMysql)
    {
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog->grava_log_erros_banco($arquivo, $argumentos, $erroMysql, self::getUsuario());
    }


    function insert_usuario()
    {
        try {
            $db = BancoLogin::conexao();

            $query = "INSERT INTO grupos (nome,descricao,nomeBanco) VALUES ('gerente','gerente',:banco)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':banco', $_POST['cnpj'], PDO::PARAM_STR);
            $stmt->execute();
            $grupoId = $db->lastInsertId();
            $permissoes = new UPermissao();
            $permissoes = $permissoes->modulo($_POST['modulo']);

            $textoQuery = "";
            foreach ($permissoes as $key => $value) {
                $textoQuery .= "INSERT INTO permissoes (nome,nomeBanco,grupo_id) VALUES ('$key',:banco,{$grupoId});";
            }

            $query = $textoQuery;
            $stmt = $db->prepare($query);
            $stmt->bindParam(':banco', $_POST['cnpj'], PDO::PARAM_STR);
            $stmt->execute();


            $query = "INSERT INTO usuarios (login,senha,statusUsuario,email,nomeBanco,statusGeral,grupo_id) VALUES (:login,:senha,'ATIVO',:email,:banco,'PAGO',{$grupoId})";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':login', $_POST['login'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindParam(':banco', $_POST['cnpj'], PDO::PARAM_STR);
            $stmt->bindParam(':senha', password_hash($_POST['senha'], PASSWORD_DEFAULT, ['cost' => 10]), PDO::PARAM_STR);
            $stmt->execute();

            $query = "INSERT INTO empresas (nome,cnpj,logradouro,cidade,estado,pais) VALUES (:nomeEmpresa,:cnpj,:logradouro,:cidade,:estado,:pais)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nomeEmpresa', $_POST['nomeEmpresa'], PDO::PARAM_STR);
            $stmt->bindParam(':cnpj', $_POST['cnpj'], PDO::PARAM_STR);
            $stmt->bindParam(':logradouro', $_POST['logradouro'], PDO::PARAM_STR);
            $stmt->bindParam(':cidade', $_POST['cidade'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $_POST['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':pais', $_POST['pais'], PDO::PARAM_STR);
            $stmt->execute();

            $query = "INSERT INTO permissoes_sistema (modulo,nomeBanco) VALUES (:modulo,:banco)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':banco', $_POST['cnpj'], PDO::PARAM_STR);
            $stmt->bindParam(':modulo', $_POST['modulo'], PDO::PARAM_STR);
            $stmt->execute();

            $query = "CREATE DATABASE IF NOT EXISTS {$_POST['cnpj']}; USE {$_POST['cnpj']};" . Arquivo::retornaConteudo('../sql/scriptBanco.sql');
            $stmt = $db->prepare($query);
            $stmt->execute();

            $status = 200;
            $status_message = 'Usuario adicionado com sucesso';
        } catch (PDOException $e) {
            $status = 400;
            $status_message = $e->getMessage();

            self::getUsuario();
            $argumentos = "Inserido .....";
            self::geraLog($argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        $response = array(
            'status' => $status,
            'status_message' => $status_message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_usuarios($usuario_id = 0)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT u.usuario_id,u.login,g.grupo_id,g.nome as 'g_nome' FROM usuarios as u JOIN usuarios_grupos ug on u.usuario_id=ug.usuario_id JOIN grupos as g on g.grupo_id=ug.grupo_id WHERE u.statusUsuario = 'ATIVO'";
            if ($usuario_id != 0) {
                $query .= " AND u.usuario_id = :usuario_id LIMIT 1";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $response[] = $row;
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTPP/1.0 400");
            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog($argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_usuario($usuario_id)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "UPDATE  usuarios SET statusUsuario='DESATIVADO' WHERE usuario_id=:usuario_id'";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            var_dump($stmt->rowCount());
            if ($stmt->rowCount() != 0) {
                $response = array(
                    'status' => 200,
                    'status_message' => 'Usuario deletado com sucesso.'
                );
            } else {
                $response = array(
                    'status' => 200,
                    'status_message' => 'Usuario não encontrado.'
                );
            }
        } catch
        (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );

            self::getUsuario();
            $argumentos = "Delete.....";
            self::geraLog($argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    function update_usuario($usuario_id)
    {
        parse_str(file_get_contents('php://input'), $post_vars);
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT * FROM usuarios WHERE usuario_id=:usuario_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
                $query = "UPDATE usuarios as u LEFT JOIN usuarios_grupos AS gu on u.usuario_id = gu.usuario_id 
				SET u.login=:login,u.fk_funcionario=:fk_funcionario, gu.grupo_id=:grupo_id WHERE u.usuario_id=:usuario_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':login', $post_vars['login'], PDO::PARAM_STR);
                $stmt->bindParam(':grupo_id', $post_vars['grupo_id'], PDO::PARAM_INT);
                $stmt->bindParam(':fk_funcionario', $post_vars['funcionario_id'], PDO::PARAM_STR);
                $stmt->execute();
                $status = 200;
                $status_message = 'Usuario alterado com sucesso';
            } else {
                $status = 400;
                $status_message = 'Id nao encontrado';
            }
        } catch
        (PDOException $e) {
            $status = 400;
            $status_message = $e->getMessage();

            self::getUsuario();
            $argumentos = "Update.....";
            self::geraLog($argumentos, $e->getMessage()); //chama a função para gravar os logs

        }


        $response = array(
            'status' => $status,
            'status_message' => $status_message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}