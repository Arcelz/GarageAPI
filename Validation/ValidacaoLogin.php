<?php
require_once '../Model/Banco.php';
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/04/2017
 * Time: 14:39
 */
class ValidacaoLogin
{

    function verifica_login($login, $senha)
    {
        $db = Banco::conexao();
        $query="SELECT * FROM usuarios WHERE login=:login";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':login', $_POST['login'], PDO::PARAM_STR);
        $stmt->execute();

        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        $senha2 = $result['senha'];
        if (password_verify($senha, $senha2)) {
            return true;
        }
        else {
            return false;
        }

    }
}