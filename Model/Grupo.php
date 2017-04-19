<?php

require_once 'Banco.php';

class Grupo
{
    function insert_grupos()
    {
        $status = 0;
        $statusMessage = '';
                try {
                    $db = Banco::conexao();
                    $query = "INSERT INTO grupos (nome,descricao) VALUES (:nome,:descricao)";
                    $stmt = $db->prepare($query);

                    $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
                    $stmt->bindParam(':descricao', $_POST['descricao'], PDO::PARAM_STR);

                    $stmt->execute();
                    $status = 1;
                    $statusMessage = 'Grupo adicionado com sucesso';
                } catch (PDOException $e) {
                    $status = 2;
                    $statusMessage = $e->getMessage();
                }

        $response = array(
            'status' => $status,
            'status_message' => $statusMessage
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_grupos($pk_grupo = 0)
    {
        try {
            $db = Banco::conexao();
            $query = "SELECT * FROM grupos";
            if ($pk_grupo != 0) {
                $query .= " WHERE pk_grupo = :pk_grupo LIMIT 1";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_INT);
            $stmt->execute();
            while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                $response[] = $row;
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 0,
                'status_message' => $e->getMessage()
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_grupo($pk_grupo)
    {
        try {
            $db = Banco::conexao();
            $query = "DELETE FROM grupos WHERE pk_grupo=:pk_grupo";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
                $response = array(
                    'status' => 0,
                    'status_message' => 'Falha ao deletar grupo não encontrado.'
                );
            } else {
                $response = array(
                    'status' => 1,
                    'status_message' => 'Grupo deletado com sucesso.'
                );
            }
        } catch
        (PDOException $e) {
            $response = array(
                'status' => 0,
                'status_message' => $e->getMessage()
            );

        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    function update_grupo($pk_grupo)
    {
        $status = 0;
        $statusMessage = '';
                try {
                    $db = Banco::conexao();

                    parse_str(file_get_contents('php://input'), $post_vars);
                    $query = "SELECT * FROM grupos WHERE pk_grupo = :pk_grupo";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_INT);
                    $stmt->execute();
                    if ($stmt->rowCount() != 0) {
                        $query = "UPDATE grupos SET nome = :nome,descricao = :descricao WHERE pk_grupo = :pk_grupo";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);
                        $stmt->bindParam(':descricao', $post_vars['descricao'], PDO::PARAM_STR);
                        $stmt->bindParam(':pk_grupo', $pk_grupo, PDO::PARAM_STR);
                        $stmt->execute();
                        $status = 1;
                        $statusMessage = 'Grupo alterado com sucesso.';

                    } else {
                        $status = 0;
                        $statusMessage = 'Grupo nao encontrado.';
                    }
                } catch
                (PDOException $e) {
                    $status = 2;
                    $statusMessage = $e->getMessage();

                }


        $response = array(
            'status' => $status,
            'status_message' => $statusMessage
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}
