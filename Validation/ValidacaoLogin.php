<?php
require_once '../Model/BancoLogin.php';

/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/04/2017
 * Time: 14:39
 */
class ValidacaoLogin
{

    function verifica_login()
    {
        $db = BancoLogin::conexao();
        $query = "SELECT * FROM usuarios WHERE statusUsuario='ATIVO' and login=:login";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':login', $_POST['login'], PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $senha2 = $result['senha'];
        if (password_verify($_POST['senha'], $senha2)) {
            if ($result['statusGeral'] != 'PAGO') {
                return 2;
            }
            return 1;
        }
        return 0;
    }
}