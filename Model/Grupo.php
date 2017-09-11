<?php

require_once 'BancoLogin.php';
require_once '../log/GeraLog.php';
require_once '../Validation/ValidaToken.php';

class Grupo
{
    public static function getUsuario()
    {
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        return $permicao;
    }

    public static function geraLog($argumentos, $erroMysql)
    {
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog->grava_log_erros_banco($arquivo, $argumentos, $erroMysql, self::getUsuario());
    }


    function insert_grupos($banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "INSERT INTO grupos (nome,descricao) VALUES (:nome,:descricao,nomeBanco = '{$banco}')";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $_POST['descricao'], PDO::PARAM_STR);

            $stmt->execute();
            $status = 200;
            $status_message = 'Grupo adicionado com sucesso';
        } catch (PDOException $e) {
            $status = 400;
            $status_message = $e->getMessage();
        }

        $response = array(
            'status' => $status,
            'status_message' => $status_message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_grupos($pk_grupo = 0,$banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT * FROM grupos WHERE nomeBanco = '{$banco}'";
            if ($pk_grupo != 0) {
                $query .= " AND grupo_id = :pk_grupo LIMIT 1";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_INT);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $response[] = $row;
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );

            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog($argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_grupo($pk_grupo,$banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "DELETE FROM grupos WHERE pk_grupo=:pk_grupo AND nomeBanco = '{$banco}'";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                $response = array(
                    'status' => 400,
                    'status_message' => 'Falha ao deletar grupo não encontrado.'
                );
            } else {
                $response = array(
                    'status' => 200,
                    'status_message' => 'Grupo deletado com sucesso.'
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


    function update_grupo($pk_grupo,$banco)
    {
        try {
            $db = BancoLogin::conexao();
            parse_str(file_get_contents('php://input'), $post_vars);
            $query = "SELECT * FROM grupos WHERE pk_grupo = :pk_grupo";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
                $query = "UPDATE grupos SET nome = :nome,descricao = :descricao WHERE pk_grupo = :pk_grupo AND nomeBanco = '{$banco}'";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);
                $stmt->bindParam(':descricao', $post_vars['descricao'], PDO::PARAM_STR);
                $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_STR);
                $stmt->execute();
                $status = 200;
                $status_message = 'Grupo alterado com sucesso.';

            } else {
                $status = 400;
                $status_message = 'Grupo nao encontrado.';
            }
        } catch
        (PDOException $e) {
            $status = 400;
            $status_message = $e->getMessage();

            self::getUsuario();
            $argumentos = "update .....";
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
