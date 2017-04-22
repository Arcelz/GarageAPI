<?php

require 'Banco.php';

Class Funcionario
{

    function get_Funcionario($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM funcionarios WHERE status ='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_funcionario = :id LIMIT 1";

            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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

    public function insert_Funcionario()
    {

        try {
            $db = Banco::conexao();
            $status = 'ATIVO';

            $query = "INSERT INTO funcionarios(nome,cpf,email,contato1,contato,status,fk_cargo) values (:nome,:cpf,:email,:contato1,:contato,:status,:fkCargo)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);
            $stmt->bindParam(':cpf', $_POST['cpf'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindParam(':contato1', $_POST['contato1'], PDO::PARAM_STR);
            $stmt->bindParam(':contato', $_POST['contato'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':fkCargo', $_POST['fkCargo'], PDO::PARAM_INT);
            $stmt->execute();

            $te = $stmt->rowCount();//retornado quantas linhas foi executada.
            //var_dump($te);
            if ($te > 0) {
                $fk = $db->lastInsertId();
                //("INSERT INTO clientes_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_cliente)
                $query2 = "INSERT INTO funcionarios_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_funcionario) 
                    values (:logradouro,:bairro,:cidade,:estado,:pais,:cep,:fk)";
                $stmt = $db->prepare($query2);

                $stmt->bindParam(':logradouro', $_POST['logradouro'], PDO::PARAM_STR);
                $stmt->bindParam(':bairro', $_POST['bairro'], PDO::PARAM_STR);
                $stmt->bindParam(':cidade', $_POST['cidade'], PDO::PARAM_STR);
                $stmt->bindParam(':estado', $_POST['estado'], PDO::PARAM_STR);
                $stmt->bindParam(':pais', $_POST['pais'], PDO::PARAM_STR);
                $stmt->bindParam(':cep', $_POST['cep'], PDO::PARAM_STR);
                $stmt->bindParam(':fk', $fk, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Funcionario adicionado.'
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

    function update_Funcionario($id)
    {

        try {

            $db = Banco::conexao();

            parse_str(file_get_contents('php://input'), $post_vars);

            /// UPDATE funcionarios AS f LEFT JOIN funcionarios_enderecos as fe ON f.pk_funcionario = fe.fk_funcionario SET f.nome = nome
            //WHERE f.pk_funcionario = ??
            $query = "UPDATE funcionarios AS f  JOIN funcionarios_enderecos AS fe ON f.pk_funcionario = fe.fk_funcionario
          SET f.nome=:nome,f.cpf=:cpf,f.email=:email,f.contato1=:contato1,fe.logradouro=:logradouro,fe.bairro=:bairro,fe.cidade=:cidade,
            fe.estado=:estado,fe.pais=:pais,fe.cep=:cep,f.contato=:contato  WHERE f.pk_funcionario= :id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
                'message' => 'Funcionario Atualizado com sucesso'
            );

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

    function delete_Funcionario($id)
    {

        try {

            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM funcionarios WHERE status ='ATIVO' AND pk_funcionario=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);//passando o paramento id na query.
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
                $query = "UPDATE  funcionarios SET status='{$status}' WHERE pk_funcionario=:id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Funcionario Excluido com Sucesso'
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

