<?php

require 'Banco.php';

Class Compra
{

    function get_Comra($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM compras WHERE status ='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query = " SELECT * FROM compras AS c LEFT JOIN compras_itens AS ci ON c.pk_compra = ci.fk_compra WHERE c.pk_compra = :id AND status ='ATIVO'";

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

        header('Content-Type: application/json');
        echo json_encode($response);
    }


    public function insert_Compra()
    {

        try {
            $db = Banco::conexao();
            $status = 'ATIVO';

            //Este bloco é responsavel por retornar o ultimo registro de venda realizado.
            //Usamos este metado para mostrar organizado a quantidade de venda realizada. Para não ter que usar o id.
            $queryCont = "SELECT pk_compra,numero FROM compras ORDER BY pk_compra DESC LIMIT 1 ";
            $stmt = $db->prepare($queryCont);
            $stmt->execute();

            $returnNumero = $stmt->fetch(PDO::FETCH_ASSOC);
            $numero = $returnNumero['numero'] + 1;
            // ======== =========== ==========================

            $query = "INSERT INTO compras(fk_fornecedor,fk_funcionario,datas,valor_compra,status,numero) values 
                (:fkFornecedor, :fkFuncionario,:datas,:valorCompra,:status,:numero )";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':fkFornecedor', $_POST['fkFornecedor'], PDO::PARAM_INT);
            $stmt->bindParam(':fkFuncionario', $_POST['fkFuncionario'], PDO::PARAM_INT);
            $stmt->bindParam(':datas', $_POST['data'], PDO::PARAM_STR);
            $stmt->bindParam(':valorCompra', $_POST['valorCompra'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':numero', $numero, PDO::PARAM_INT);
            $stmt->execute();


            //Este bloco é responsavel pela inserção das vendas itens. usamos a variavel $returnn para verificar se
            // foi inserido a venda corretamente. Caso sim cai no IF e salva a vendas_itens
            $returnn = $stmt->rowCount();

            if ($returnn > 0) {
                $fk_compra = $db->lastInsertId();//retorna o ultimo id

                //("INSERT INTO clientes_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_cliente)
                $query2 = "INSERT INTO compras_itens(fk_compra,fk_veiculo,valor_compra) 
                    values (:fkCompra,:fkVeiculo,:valorCompra)";
                $stmt = $db->prepare($query2);

                $stmt->bindParam(':fkCompra', $fk_compra, PDO::PARAM_INT);
                $stmt->bindParam(':fkVeiculo', $_POST['fkVeiculo'], PDO::PARAM_INT);
                $stmt->bindParam(':valorCompra', $_POST['valorCompra'], PDO::PARAM_STR);

                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Compra realizada.'
                );
                header("HTTP/1.0 200 ");
            }
            // ======== =========== ==========================
        } catch (PDOException $e) {
            $response = array(
                'code' => 400,
                'message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");

        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_Compra($id)
    {

        try {

            $db = Banco::conexao();

            parse_str(file_get_contents('php://input'), $post_vars);

            /// UPDATE funcionarios AS f LEFT JOIN funcionarios_enderecos as fe ON f.pk_funcionario = fe.fk_funcionario SET f.nome = nome
            //WHERE f.pk_funcionario = ??
            $query = "UPDATE compras AS co LEFT JOIN compras_itens AS ci ON co.pk_compra = ci.fk_compra
			SET co.fk_fornecedor = :fkFornecedor, co.fk_funcionario = :fkFuncionario, co.datas = :datas, ci.fk_veiculo = :fkVeiculo, ci.valor_compra =:valorCompra
			WHERE co.pk_compra=:id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fkFornecedor', $post_vars['fkFornecedor'], PDO::PARAM_INT);
            $stmt->bindParam(':fkFuncionario', $post_vars['fkFuncionario'], PDO::PARAM_INT);
            $stmt->bindParam(':datas', $post_vars['datas'], PDO::PARAM_STR);
            $stmt->bindParam(':fkVeiculo', $post_vars['fkVeiculo'], PDO::PARAM_STR);
            $stmt->bindParam(':valorCompra', $post_vars['valorCompra'], PDO::PARAM_STR);


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

        header('Content-Type: application/json');
        echo json_encode($response);
    }


    function delete_Compra($id)
    {

        try {

            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM compras WHERE status ='ATIVO' AND pk_compra=:id";
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
                $query = "UPDATE  compras SET status='{$status}' WHERE pk_compra= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Compra Excluida com Sucesso'
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





