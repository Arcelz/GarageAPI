<?php

require_once 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

class Usuario
{
     public static function getUsuario(){
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        //var_dump($permicao) ;
        return $permicao;
    }

    public static  function geraLog($argumentos, $erroMysql ){
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog ->grava_log_erros_banco($arquivo,$argumentos, $erroMysql, self::getUsuario());
    }


    function insert_usuario()
    {
        $status = 0;
    	$ativo = 'ATIVO';
        $statusMessage = '';
                try {
                    $db = Banco::conexao();
                    $query = "INSERT INTO usuarios (login,senha,fk_funcionario,status) VALUES (:login,:senha,:fk_funcionario,:status)";
                    $stmt = $db->prepare($query);

                    $stmt->bindParam(':login', $_POST['login'], PDO::PARAM_STR);
                    $stmt->bindParam(':senha', password_hash($_POST['senha'], PASSWORD_DEFAULT, ['cost' => 10]), PDO::PARAM_STR);
                    $stmt->bindParam(':fk_funcionario', $_POST['funcionario_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':status', $ativo, PDO::PARAM_STR);
                    $stmt->execute();
                    $usuarioId=$db->lastInsertId();

                    $query2 = "INSERT INTO usuarios_grupos (grupo_id,usuario_id) VALUES (:grupo_id,:usuario_id)";
                    $stmt2 = $db->prepare($query2);
                    $stmt2->bindParam(':grupo_id', $_POST['grupo_id'], PDO::PARAM_INT);
                    $stmt2->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
                    $stmt2->execute();
                    $status=200;
                    $status_message='Usuario adicionado com sucesso';
                } catch (PDOException $e) {
                    $status = 400;
                    $status_message= $e->getMessage();

                    self::getUsuario();
                    $argumentos = "Inserido .....";
                    self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

                }
        $response = array(
            'status' => $status,
            'status_message' => $statusMessage
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_usuarios($usuario_id = 0)
    {
        try {
            $db = Banco::conexao();
            $query = "SELECT u.usuario_id,u.login,f.pk_funcionario,f.nome as 'f_nome',g.pk_grupo,g.nome as 'g_nome' FROM usuarios as u JOIN funcionarios as f on u.fk_funcionario = f.pk_funcionario JOIN usuarios_grupos ug on u.usuario_id=ug.usuario_id JOIN grupos as g on g.pk_grupo=ug.grupo_id WHERE u.status = 'ATIVO'";
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
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_usuario($usuario_id)
    {
            $status = 'DESATIVADO';

        try {
            $db = Banco::conexao();
            $query = "UPDATE  usuarios SET status='{$status}' WHERE usuario_id=:usuario_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $response = array(
                'status' => 200,
                'status_message' => 'Usuario deletado com sucesso.'
            );
        } catch
        (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );

            self::getUsuario();
            $argumentos = "Delete.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    function update_usuario($usuario_id)
    {
        parse_str(file_get_contents('php://input'), $post_vars);
        $status = 0;
        $statusMessage = '';
                try {
                    $db = Banco::conexao();
                    $query = "SELECT * FROM usuarios WHERE usuario_id=:usuario_id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                    $stmt->execute();
                    if ($stmt->rowCount() != 0) {
                            $query = "UPDATE usuarios SET login=:login,fk_funcionario=:fk_funcionario WHERE usuario_id=:usuario_id";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                            $stmt->bindParam(':login', $post_vars['login'], PDO::PARAM_STR);
                            $stmt->bindParam(':fk_funcionario', $post_vars['funcionario_id'], PDO::PARAM_STR);
                            $stmt->execute();
                            $status = 200;
                            $status_message= 'Usuario alterado com sucesso.';
                    } else {
                        $status = 400;
                        $statusMessage = 'Id nao encontrado.';
                    }
                } catch
                (PDOException $e) {
                    $status = 400;
                    $status_message= $e->getMessage();

                    self::getUsuario();
                    $argumentos = "Update.....";
                   self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

                }


        $response = array(
            'status' => $status,
            'status_message' => $status_message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}