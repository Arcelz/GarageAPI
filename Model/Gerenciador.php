<?php

require_once 'BancoLogin.php';
require_once '../log/GeraLog.php';
require_once '../Validation/ValidaToken.php';

class Gerenciador
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

    function insert_gerenciador()
    {
        try {
            $db = BancoLogin::conexao();
            $query = "INSERT INTO gerenciadores (nomeEmpresa,cnpj,email,contato,status) VALUES (:nomeEmpresa,:cnpj,:email,:contato,'ATIVO')";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nomeEmpresa', $_POST['nomeEmpresa'], PDO::PARAM_STR);
            $stmt->bindParam(':cnpj', $_POST['cnpj'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindParam(':contato', $_POST['contato'], PDO::PARAM_STR);
            $stmt->execute();

            $status = 200;
            $status_message = 'Gerenciador adicionado com sucesso';
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

    function get_gerenciadores($gerenciador_id = 0)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT * FROM gerenciadores WHERE status = 'ATIVO'";
            if ($gerenciador_id != 0) {
                $query .= " AND id_gerenciador = :gerenciador_id LIMIT 1";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':gerenciador_id', $gerenciador_id, PDO::PARAM_INT);
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

    function delete_gerenciador($gerenciador_id)
    {
        $status = 'DESATIVADO';

        try {
            $db = BancoLogin::conexao();
            $query = "UPDATE  gerenciadores SET status='{$status}' WHERE id_gerenciador=:gerenciador_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':gerenciador_id', $gerenciador_id, PDO::PARAM_INT);
            $stmt->execute();
            $response = array(
                'status' => 200,
                'status_message' => 'Gerenciador deletado com sucesso.'
            );
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


    function update_gerenciador($gerenciador_id)
    {
        parse_str(file_get_contents('php://input'), $post_vars);
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT * FROM gerenciadores WHERE id_gerenciador=:gerenciador_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':gerenciador_id', $gerenciador_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
                $query = "UPDATE gerenciadores SET email=:email,nomeEmpresa=:nomeEmpresa,cnpj=:cnpj,contato=:contato WHERE id_gerenciador=:gerenciador_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':gerenciador_id', $gerenciador_id, PDO::PARAM_INT);
                $stmt->bindParam(':nomeEmpresa', $post_vars['nomeEmpresa'], PDO::PARAM_STR);
                $stmt->bindParam(':cnpj', $post_vars['cnpj'], PDO::PARAM_INT);
                $stmt->bindParam(':email', $post_vars['email'], PDO::PARAM_STR);
                $stmt->bindParam(':contato', $post_vars['contato'], PDO::PARAM_STR);
                $stmt->execute();
                $status = 200;
                $status_message = 'Gerenciador alterado com sucesso';
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