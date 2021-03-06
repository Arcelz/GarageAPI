<?php

require_once 'BancoLogin.php';
require_once '../log/GeraLog.php';
require_once '../Validation/ValidaToken.php';
require_once '../Validation/ValidaModels/ValitronUsuario.php';
require_once '../util/DataConversor.php';


class Usuario
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


    function insert_usuario($banco)
    {
        $data = new DataConversor();
        $data = $data->converter();
        $usuarioValida = new ValitronUsuario();
        $usuarioValida = $usuarioValida->validaUsuarioPost($data);
        if($usuarioValida === true) {
            try {
                $db = BancoLogin::conexao();
                $query = "INSERT INTO usuarios (login,senha,fk_funcionario,statusUsuario,email,nomeBanco,statusGeral) VALUES (:login,:senha,:fk_funcionario,'ATIVO',:email,'{$banco}','PAGO')";
                $stmt = $db->prepare($query);

                $stmt->bindParam(':login', $data['login'], PDO::PARAM_STR);
                $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
                $stmt->bindParam(':senha', password_hash($data['senha'], PASSWORD_DEFAULT, ['cost' => 10]), PDO::PARAM_STR);
                $stmt->bindParam(':fk_funcionario', $data['funcionario_id'], PDO::PARAM_INT);
                $stmt->execute();
                $usuarioId = $db->lastInsertId();

                $query2 = "INSERT INTO usuarios_grupos (grupo_id,usuario_id) VALUES (:grupo_id,'{$usuarioId}')";
                $stmt2 = $db->prepare($query2);
                $stmt2->bindParam(':grupo_id', $data['grupo_id'], PDO::PARAM_INT);
                $stmt2->execute();
                $status = 200;
                $status_message = 'Usuario adicionado com sucesso';
            } catch (PDOException $e) {
                $status = 400;
                $status_message = $e->getMessage();

                self::getUsuario();
                $argumentos = "Inserido .....";
                self::geraLog($argumentos, $e->getMessage()); //chama a função para gravar os logs

            }
        }
        else{
            $status = 401;
            $status_message = $usuarioValida;
        }
        $response = array(
            'status' => $status,
            'status_message' => $status_message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_usuarios($usuario_id = 0,$banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT u.usuario_id,u.login,g.grupo_id,g.nome as 'g_nome' FROM usuarios as u JOIN grupos as g on g.grupo_id=u.grupo_id WHERE u.statusUsuario = 'ATIVO' AND u.nomeBanco='{$banco}'";
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

    function delete_usuario($usuario_id,$banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "UPDATE  usuarios SET statusUsuario='DESATIVADO' WHERE usuario_id=:usuario_id AND nomeBanco='{$banco}'";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            var_dump($stmt->rowCount());
            if($stmt->rowCount()!=0){
                $response = array(
                    'status' => 200,
                    'status_message' => 'Usuario deletado com sucesso.'
                );
            }
            else{
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