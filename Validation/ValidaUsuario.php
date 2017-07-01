<?php
require_once '../Model/BancoLogin.php';
require_once '../Model/Banco.php';


/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/04/2017
 * Time: 14:39
 */
class ValidaUsuario
{

    function valida_permicao($login)// função para trazer as permições do usuario no sistema.
    {
        $db = BancoLogin::conexao();
        $query = "SELECT p.nome,u.nomeBanco FROM usuarios AS u JOIN usuarios_grupos as ug ON u.usuario_id=ug.usuario_id JOIN grupos AS g ON g.grupo_id=ug.grupo_id JOIN grupos_permissoes AS gp ON gp.grupo_id= g.grupo_id JOIN permissoes as p ON p.permissao_id = gp.permissao_id where u.login = :login";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $verifica = true;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($verifica) {
                $response['nomeBanco'] = $row['nomeBanco'];
                $verifica = false;
            }
            $response[$row['nome']] = true;
        }
        return $response;
    }

    function valida_funcionario($login)// função para trazer as permições do usuario no sistema.
    {
        $db = BancoLogin::conexao();
        $query = "SELECT u.fk_funcionario as nome  FROM usuarios as u where u.login = :login";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['nome'];
    }

    function valida_dados($funcionario)// função que retorna os dados do usuario.
    {
        $db = Banco::conexao();
        $query = "SELECT f.nome,f.email,f.avatar from funcionarios as f WHERE f.pk_funcionario = :funcionario";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':funcionario', $funcionario, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}