<?php

require_once 'BancoLogin.php';
require_once '../log/GeraLog.php';
require_once '../Validation/ValidaToken.php';
require_once '../util/UPermissao.php';

class Permissao
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


    function insert_permissoes()
    {
        try {
            $db = BancoLogin::conexao();
            $query = "INSERT INTO permissoes_sistema (modulo,nomeBanco) VALUES (:modulo,:nomeBanco)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nomeBanco', $_POST['nomeBanco'], PDO::PARAM_STR);
            $stmt->bindParam(':modulo', $_POST['modulo'], PDO::PARAM_INT);
            $stmt->execute();
            $status = 200;
            $status_message = 'Permissao adicionada com sucesso';
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

    function get_permissoes($banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT * FROM permissoes_sistema WHERE nomeBanco = '{$banco}'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $modulo = $stmt->fetch(PDO::FETCH_ASSOC);
            $modulo = $modulo['modulo'];
            $response = UPermissao::modulo($modulo);
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}
