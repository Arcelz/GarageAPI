<?php
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
        $db = Banco::conexao();
        $query = "SELECT GROUP_CONCAT(p.permissao) as permissoes,GROUP_CONCAT(p.fk_modulo) as modulo FROM usuarios AS u JOIN usuarios_grupos as ug ON u.usuario_id=ug.usuario_id JOIN grupos AS g ON g.pk_grupo=ug.grupo_id JOIN grupos_permissoes AS gp ON gp.grupo_id= g.pk_grupo JOIN permissoes as p ON p.pk_permissao = gp.permissao_id where u.login = :login";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $permissoes = $result['permissoes'];
        $modulo = $result['modulo'];
        $arrayPermicao = preg_split('/,/', $permissoes);
        $arrayModulo = preg_split('/,/', $modulo);
        for ($i=0; $i < count($arrayPermicao) ; $i++) {
            $permissoesModulo[]=$arrayModulo[$i].$arrayPermicao[$i];
        }
        return $permissoesModulo;

    }

    function valida_dados($login)// função que retorna os dados do usuario.
    {

        $db = Banco::conexao();
        $query = "SELECT f.nome,f.email from usuarios as u JOIN funcionarios as f on u.fk_funcionario=f.pk_funcionario WHERE u.login = :login";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}