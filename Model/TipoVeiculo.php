<?php

require 'Banco.php';

Class TipoVeiculo
{

    function get_tpVeiculo($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM tipos_veiculos WHERE status ='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_tipo = :id LIMIT 1";

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

    public function insert_tpVeiculo()
    {

        try {
            $db = Banco::conexao();

            $status = 'ATIVO';

            $query = "INSERT INTO tipos_veiculos(nome,status) values (:nome,:status)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            $stmt->execute();

            $response = array(
                'code' => 200,
                'message' => 'Tipo de Veiculo adicionado.'
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

    function update_tpVeiculo($id)
    {

        try {
            $db = Banco::conexao();
            parse_str(file_get_contents('php://input'), $post_vars);

            $query = "UPDATE tipos_veiculos  SET nome=:nome  WHERE pk_tipo=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);

            $stmt->execute();
            $response = array(
                'code' => 200,
                'message' => 'Tipo de Veiculo Atualizado com sucesso'

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

    function delete_tpVeiculo($id)
    {
        try {
            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM tipos_veiculos WHERE status ='ATIVO' AND pk_tipo=:id";
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
                $query = "UPDATE  tipos_veiculos SET status='{$status}' WHERE pk_tipo= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Tipo de Veiculo Excluido com Sucesso'
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

