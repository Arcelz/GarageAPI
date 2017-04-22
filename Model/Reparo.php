<?php

require 'Banco.php';

Class Reparo
{
    function get_Reparo($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM reparos WHERE status ='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_reparo = :id LIMIT 1";

            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();
            //var_dump($row);


            if ($row == null) {
                $response = array(
                    'code' => 404,
                    'message' => 'Recurso nao encontrado'
                );
                header("HTTP/1.0 404 ");

            } else {
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //$response[]= $row;
                    array_push($response, $row);
                }

            }

        } catch (PDOException $e) {
            $response = array(
                'code' => 400,
                'message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
        }
        unset($db);


        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function insert()
    {

        try {
            $db = Banco::conexao();

            $status = 'ATIVO';

            $query = "INSERT INTO reparos(status,fk_veiculo,fk_tipos,valor,descricao) values (:status,:fkVeiculo,:fkTipo,:valor,:descricao)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':valor', $_POST['valor'], PDO::PARAM_STR);
            $stmt->bindParam(':fkTipo', $_POST['fkTipo'], PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $_POST['descricao'], PDO::PARAM_STR);
            $stmt->bindParam(':fkVeiculo', $_POST['fkVeiculo'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            $stmt->execute();

            $response = array(
                'code' => 200,
                'message' => 'Reparo adicionado.'
            );
            header("HTTP/1.0 200 ");

        } catch (PDOException $e) {
            $response = array(
                'code' => 400,
                'message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_Reparos($id)
    {

        try {
            $db = Banco::conexao();
            parse_str(file_get_contents('php://input'), $post_vars);

            $query = "UPDATE reparos  SET  descricao=:descricao, fk_veiculo=:fkVeiculo,fk_tipos=:fkTipo, valor=:valor WHERE pk_reparo= :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fkTipo', $post_vars['fkTipo'], PDO::PARAM_STR);//fkTipo
            $stmt->bindParam(':fkVeiculo', $post_vars['fkVeiculo'], PDO::PARAM_STR);//fkVeiculo
            $stmt->bindParam(':valor', $post_vars['valor'], PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $post_vars['descricao'], PDO::PARAM_STR);

            $stmt->execute();
            $response = array(
                'code' => 200,
                'message' => 'Reparo Atualizado com sucesso'

            );
            header("HTTP/1.0 200 ");
        } catch (PDOException $e) {
            $response = array(
                'code' => 400,
                'errorMysql: ' => $e->getMessage()
            );

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_Reparos($id)
    {
        try {
            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM reparos WHERE status ='ATIVO' AND pk_reparo=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();


            //Essa condição é para verificar se a url existe no servidor. Porque fazemos a consulta pelos funcionarios ativos
            if ($row == null) {
                $response = array(
                    'code' => 404,
                    'message' => 'Recurso nao encontrado'

                );
                header("HTTP/1.0 404 ");
            } else {
                $query = "UPDATE  reparos SET status='{$status}' WHERE pk_reparo= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Reparo Excluido com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }
        } catch (PDOException $e) {
            $response = array(
                'code' => 400,
                'errorMysql: ' => $e->getMessage()
            );
        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

