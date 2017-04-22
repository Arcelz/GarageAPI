<?php

require 'Banco.php';

Class Cliente
{

    function get_Cliente($product_id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM clientes WHERE status ='ATIVO'";

            $response = array();
            if ($product_id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_cliente = :cargo_id LIMIT 1";

            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':cargo_id', $_GET["cliente_id"], PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();


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

    public function insert_Cliente()
    {

        try {
            $db = Banco::conexao();
            $status = 'ATIVO';

            $query = "INSERT INTO clientes(nome,cpf,email,contato1,contato,status) values (:nome,:cpf,:email,:contato1,:contato,:status)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':cpf', $_POST['cpf'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindParam(':contato1', $_POST['contato1'], PDO::PARAM_STR);
            $stmt->bindParam(':contato', $_POST['contato'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();

            $te = $stmt->rowCount();
            //var_dump($te);
            if ($te > 0) {
                $fk_cliente = $db->lastInsertId();

                //("INSERT INTO clientes_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_cliente)
                $query2 = "INSERT INTO clientes_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_cliente) 
                    values (:logradouro,:bairro,:cidade,:estado,:pais,:cep,:fk_cliente)";
                $stmt = $db->prepare($query2);

                $stmt->bindParam(':logradouro', $_POST['logradouro'], PDO::PARAM_STR);
                $stmt->bindParam(':bairro', $_POST['bairro'], PDO::PARAM_STR);
                $stmt->bindParam(':cidade', $_POST['cidade'], PDO::PARAM_STR);
                $stmt->bindParam(':estado', $_POST['estado'], PDO::PARAM_STR);
                $stmt->bindParam(':pais', $_POST['pais'], PDO::PARAM_STR);
                $stmt->bindParam(':cep', $_POST['cep'], PDO::PARAM_STR);
                $stmt->bindParam(':fk_cliente', $fk_cliente, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Cliente adicionado.'
                );
                header("HTTP/1.0 200 ");
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

    function update_Cliente($cliente_id)
    {

        try {

            $db = Banco::conexao();

            parse_str(file_get_contents('php://input'), $post_vars);

            /// UPDATE funcionarios AS f LEFT JOIN funcionarios_enderecos as fe ON f.pk_funcionario = fe.fk_funcionario SET f.nome = nome
            //WHERE f.pk_funcionario = ??
            $query = "UPDATE clientes AS cli  JOIN clientes_enderecos AS ce ON cli.pk_cliente = ce.fk_cliente
          SET cli.nome=:nome,cli.cpf=:cpf,cli.email=:email,cli.contato1=:contato1,ce.logradouro=:logradouro,ce.bairro=:bairro,ce.cidade=:cidade,
            ce.estado=:estado,ce.pais=:pais,ce.cep=:cep,cli.contato=:contato  WHERE cli.pk_cliente= :pk_cliente";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_cliente', $cliente_id, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':cpf', $post_vars['cpf'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $post_vars['email'], PDO::PARAM_STR);
            $stmt->bindParam(':contato', $post_vars['contato'], PDO::PARAM_STR);
            $stmt->bindParam(':contato1', $post_vars['contato1'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $post_vars['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':logradouro', $post_vars['logradouro'], PDO::PARAM_STR);
            $stmt->bindParam(':bairro', $post_vars['bairro'], PDO::PARAM_STR);
            $stmt->bindParam(':cidade', $post_vars['cidade'], PDO::PARAM_STR);
            $stmt->bindParam(':pais', $post_vars['pais'], PDO::PARAM_STR);
            $stmt->bindParam(':cep', $post_vars['cep'], PDO::PARAM_STR);

            $stmt->execute();
            $response = array(
                'code' => 200,
                'message' => 'Cliente Atualizado com sucesso'


            );
            header("HTTP/1.0 400 ");
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

    function delete_Cliente($cliente_id)
    {

        try {

            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM clientes WHERE status ='ATIVO' AND pk_cliente=:pk_cliente";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pk_cliente', $cliente_id, PDO::PARAM_INT);
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
                $query = "UPDATE  clientes SET status='{$status}' WHERE pk_cliente= :pk_cliente";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':pk_cliente', $cliente_id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Cliente Excluido com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }


        } catch (PDOException $e) {
            $response = array(
                'code' => 400,
                'errorMysql: ' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

