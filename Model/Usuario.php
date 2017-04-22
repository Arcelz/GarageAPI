<?php

require_once 'Banco.php';

class Usuario
{
    function insert_usuario()
    {
        $status = 0;
        $statusMessage = '';
        try {
            $db = Banco::conexao();
            $query = "INSERT INTO usuarios (login,senha,fk_funcionario,status) VALUES (:login,:senha,:fk_funcionario,:status)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':login', $_POST['login'], PDO::PARAM_STR);
            $stmt->bindParam(':senha', password_hash($_POST['senha'], PASSWORD_DEFAULT, ['cost' => 10]), PDO::PARAM_STR);
            $stmt->bindParam(':fk_funcionario', $_POST['funcionario_id'], PDO::PARAM_INT);
            $stmt->bindParam(':status', $_POST['status'], PDO::PARAM_STR);
            $stmt->execute();
            $status = 1;
            $statusMessage = 'Usuario adicionado';
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

    function get_usuarios($usuario_id = 0)
    {
        try {
            $db = Banco::conexao();
            $query = "SELECT * FROM usuarios";
            if ($usuario_id != 0) {
                $query .= " WHERE usuario_id = :usuario_id LIMIT 1";
            }
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
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

    function delete_usuario($usuario_id)
    {
        try {
            $db = Banco::conexao();
            $query = "DELETE FROM usuarios WHERE usuario_id=:usuario_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $response = array(
                'status' => 1,
                'status_message' => 'Usuario deletado com sucesso.'
            );
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


    function update_usuario($usuario_id)
    {
        parse_str(file_get_contents('php://input'), $post_vars);
        $status = 0;
        $statusMessage = '';
        try {
            $db = Banco::conexao();
            $senha = $post_vars["senha"];
            $query = "SELECT * FROM usuarios WHERE usuario_id=:usuario_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() != 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $senha2 = $result['senha'];
                if (password_verify($senha, $senha2)) {
                    $query = "UPDATE usuarios SET login=:login,senha=:senhaNova,status=:status WHERE usuario_id=:usuario_id";
                    $stmt = $db->prepare($query);
                    $senhaNova = password_hash($post_vars['senha_nova'], PASSWORD_DEFAULT, ['cost' => 10]);
                    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                    $stmt->bindParam(':login', $post_vars['login'], PDO::PARAM_STR);
                    $stmt->bindParam(':senhaNova', $senhaNova, PDO::PARAM_STR);
                    $stmt->bindParam(':status', $post_vars['status'], PDO::PARAM_STR);
                    $stmt->execute();
                    $status = 1;
                    $statusMessage = 'Senha alterada com sucesso.';

                } else {
                    $status = 0;
                    $statusMessage = 'Senha não confere.';
                }
            } else {
                $status = 0;
                $statusMessage = 'Id nao encontrado.';
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
