<?php

require_once 'Banco.php';

class Permissao
{
    function insert_permissoes()
    {
        $status = 0;
        $statusMessage = '';

                try {
                    $db = Banco::conexao();
                    $query = "INSERT INTO permissoes (nome,permissao,fk_modulo) VALUES (:nome,:permissao,:modulo_id)";
                    $stmt = $db->prepare($query);

                    $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
                    $stmt->bindParam(':permissao', $_POST['permissao'], PDO::PARAM_STR);
                    $stmt->bindParam(':modulo_id', $_POST['modulo_id'], PDO::PARAM_INT);

                    $stmt->execute();
                    $status = 1;
                    $statusMessage = 'Permissao adicionada com sucesso';
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

    function get_permissoes($pk_permissao = 0)
    {
        try {
            $db = Banco::conexao();
            $query = "SELECT * FROM permissoes";
            if ($pk_permissao != 0) {
                $query .= " WHERE pk_permissao = :pk_permissao LIMIT 1";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
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

    function delete_permissao($pk_permissao)
    {
        try {
            $db = Banco::conexao();
            $query = "DELETE FROM permissoes WHERE pk_permissao=:pk_permissao";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
                $response = array(
                    'status' => 0,
                    'status_message' => 'Falha ao deletar permissão não encontrada.'
                );
            } else {
                $response = array(
                    'status' => 1,
                    'status_message' => 'Permissão deletada com sucesso.'
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


    function update_permissao($pk_permissao)
    {
        $status = 0;
        $statusMessage = '';
                try {
                    $db = Banco::conexao();

                    parse_str(file_get_contents('php://input'), $post_vars);
                    $query = "SELECT * FROM permissoes WHERE pk_permissao = :pk_permissao";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
                    $stmt->execute();
                    if ($stmt->rowCount() != 0) {
                        $query = "UPDATE permissoes SET nome = :nome,permissao = :permissao,fk_modulo = :modulo_id WHERE pk_permissao=:pk_permissao";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);
                        $stmt->bindParam(':permissao', $post_vars['permissao'], PDO::PARAM_STR);
                        $stmt->bindParam(':modulo_id', $post_vars['modulo_id'], PDO::PARAM_INT);
                        $stmt->bindParam(':pk_permissao', $pk_permissao, PDO::PARAM_INT);
                        $stmt->execute();
                        $status = 1;
                        $statusMessage = 'Permissão alterada com sucesso.';

                    } else {
                        $status = 0;
                        $statusMessage = 'Permissão nao encontrada.';
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
