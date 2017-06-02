<?php

require_once 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

class GrupoPermissao
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


    function insert_grupos()
    {
        $status = 0;
        $status_message = '';
        $array = $_POST['permissao_id'];
                try {
                    $db = Banco::conexao();
                    $query = "DELETE FROM grupos_permissoes WHERE grupo_id = :grupo_id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':grupo_id', $_POST['grupo_id'], PDO::PARAM_STR);
                    $stmt->execute();
		 for ($i=0;$i<count($array);$i++){
                    $query = "INSERT INTO grupos_permissoes (grupo_id,permissao_id) VALUES (:grupo_id,:permissao_id)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':grupo_id', $_POST['grupo_id'], PDO::PARAM_STR);
                    $stmt->bindParam(':permissao_id', $array[$i], PDO::PARAM_STR);
                    $stmt->execute();
		}
                    $status = 200;
                    $status_message = 'Grupo de permissão adicionado com sucesso';
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

    function get_grupos($grupo_id = 0)
    {
        try {
            $db = Banco::conexao();
            $query = "SELECT gp.grupo_id,gp.permissao_id,g.nome as grupo,p.nome as permissao from grupos_permissoes as gp  JOIN grupos as g on gp.grupo_id=g.pk_grupo JOIN permissoes as p ON p.pk_permissao = gp.permissao_id";
            if ($grupo_id != 0) {
                $query .= " WHERE gp.grupo_id = :grupo_id";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->execute();
            while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                $response[] = $row;
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_grupo($grupo_id)
    {
        try {
            $db = Banco::conexao();
            $query = "DELETE FROM grupos_permissoes WHERE grupo_id = :grupo_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
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
            $argumentos = "Delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    function update_grupo($grupo_id)
    {
        $status = 0;
        $statusMessage = '';
                try {
                    $db = Banco::conexao();

                    parse_str(file_get_contents('php://input'), $post_vars);
                    $query = "SELECT * FROM grupos WHERE grupo_id = :grupo_id AND permissao_old_id=:permissao_old_id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
                    $stmt->bindParam(':permissao_old_id', $post_vars['permissao_old_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    if ($stmt->rowCount() != 0) {
                        $query = "UPDATE grupos_permissoes SET permissao_id WHERE grupo_id=:grupo_id AND permissao_old_id=:permissao_old_id";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':permissao_id', $post_vars['permissao_id'], PDO::PARAM_INT);
                        $stmt->bindParam(':permissao_old_id', $post_vars['permissao_old_id'], PDO::PARAM_INT);
                        $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $status = 200;
                        $statusMessage = 'Grupo Permissao alterado com sucesso.';

                    } else {
                        $status = 400;
                        $status_message = 'Grupo Permissao nao encontrado.';
                    }
                } catch
                (PDOException $e) {
                    $status = 400;
                    $status_message = $e->getMessage();

                    self::getUsuario();
            $argumentos = "delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

                }


        $response = array(
            'status' => $status,
            'status_message' => $statusMessage
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}
