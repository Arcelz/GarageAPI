<?php

require_once 'Banco.php';

class GrupoPermissao
{
    function insert_grupos()
    {
        $status = 0;
        $statusMessage = '';
                try {
                    $db = Banco::conexao();
                    $query = "INSERT INTO grupos_permissoes (grupo_id,permissao_id) VALUES (:grupo_id,:permissao_id)";
                    $stmt = $db->prepare($query);

                    $stmt->bindParam(':grupo_id', $_POST['grupo_id'], PDO::PARAM_STR);
                    $stmt->bindParam(':permissao_id', $_POST['permissao_id'], PDO::PARAM_STR);

                    $stmt->execute();
                    $status = 1;
                    $statusMessage = 'Grupo de permissão adicionado com sucesso';
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
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

    function delete_grupo($grupo_id)
    {
        try {
            parse_str(file_get_contents('php://input'), $post_vars);
            $db = Banco::conexao();
            $query = "DELETE FROM grupos_permissoes WHERE grupo_id = :grupo_id AND permissao_id=:permissao_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->bindParam(':permissao_id', $post_vars['permissao_id'], PDO::PARAM_INT);
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
                        $status = 1;
                        $statusMessage = 'Grupo Permissao alterado com sucesso.';

                    } else {
                        $status = 0;
                        $statusMessage = 'Grupo Permissao nao encontrado.';
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
