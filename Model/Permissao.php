<?php

require_once 'BancoLogin.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

class Permissao
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



    function insert_permissoes()
    {
                try {
                    $db = Banco::conexao();
                    $query = "INSERT INTO permissoes (nome,permissao,nomeBanco) VALUES (:nome,:permissao,:nomeBanco)";
                    $stmt = $db->prepare($query);

                    $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
                    $stmt->bindParam(':permissao', $_POST['permissao'], PDO::PARAM_STR);
                    $stmt->bindParam(':nomeBanco', $_POST['nomeBanco'], PDO::PARAM_INT);

                    $stmt->execute();
                    $status = 200;
                    $status_message= 'Permissao adicionada com sucesso';
                } catch (PDOException $e) {
                    $status = 400;
                    $status_message= $e->getMessage();
                }

        $response = array(
            'status' => $status,
            'status_message' => $status_message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_permissoes($pk_permissao = 0,$banco)
    {
        try {
            $db = Banco::conexao();
            $query = "SELECT * FROM permissoes WHERE nomeBanco = '{$banco}'";
            if ($pk_permissao != 0) {
                $query .= " AND pk_permissao = :pk_permissao LIMIT 1";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $response[] = $row;
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_permissao($pk_permissao)
    {
        try {
            $db = Banco::conexao();
            $query = "DELETE FROM permissoes WHERE pk_permissao=:pk_permissao";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
                $response = array(
                    'status' => 400,
                    'status_message' => 'Falha ao deletar permissão não encontrada.'
                );
            } else {
                $response = array(
                    'status' => 200,
                    'status_message' => 'Permissão deletada com sucesso.'
                );
            }
        } catch
        (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );

        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    function update_permissao($pk_permissao)
    {
                try {
                    $db = Banco::conexao();

                    parse_str(file_get_contents('php://input'), $post_vars);
                    $query = "SELECT * FROM permissoes WHERE pk_permissao = :pk_permissao";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
                    $stmt->execute();
                    if ($stmt->rowCount() != 0) {
                        $query = "UPDATE permissoes SET nome = :nome,permissao = :permissao,fk_modulo = :modulo_id WHERE pk_permissao=:pk_permissao";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);
                        $stmt->bindParam(':permissao', $post_vars['permissao'], PDO::PARAM_STR);
                        $stmt->bindParam(':modulo_id', $post_vars['modulo_id'], PDO::PARAM_INT);
                        $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
                        $stmt->execute();
                        $status = 200;
                        $status_message= 'Permissão alterada com sucesso.';

                    } else {
                        $status = 400;
                        $status_message= 'Permissão nao encontrada.';
                    }
                } catch
                (PDOException $e) {
                    $status = 400;
                    $status_message= $e->getMessage();

                }

        $response = array(
            'status' => $status,
            'status_message' => $statusMessage
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}
