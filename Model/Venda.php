<?php

require 'Banco.php';

Class Venda
{

    function get_Venda($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM vendas WHERE status ='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query = " SELECT * FROM vendas AS v LEFT JOIN vendas_itens AS vi ON v.pk_venda = vi.fk_venda WHERE v.pk_venda = :id AND status ='ATIVO'";

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


    public function insert_Venda()
    {

        try {
            $db = Banco::conexao();
            $status = 'ATIVO';

            //Este bloco é responsavel por retornar o ultimo registro de venda realizado.
            //Usamos este metado para mostrar organizado a quantidade de venda realizada. Para não ter que usar o id.
            $queryCont = "SELECT pk_venda,numero FROM vendas ORDER BY pk_venda DESC LIMIT 1 ";
            $stmt = $db->prepare($queryCont);
            $stmt->execute();

            $returnNumero = $stmt->fetch(PDO::FETCH_ASSOC);
            $numero = $returnNumero['numero'] + 1;
            // ======== =========== ==========================

            $query = "INSERT INTO vendas(fk_clientes,fk_funcionarios,datas,valor_compra,status,numero) values 
                (:fkCliente, :fkFuncionario,:datas,:valorCompra,:status,:numero )";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':fkCliente', $_POST['fkCliente'], PDO::PARAM_INT);
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
                $fk_venda = $db->lastInsertId();//retorna o ultimo id

                //("INSERT INTO clientes_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_cliente)
                $query2 = "INSERT INTO vendas_itens(fk_venda,fk_veiculo,valor_venda) 
                    values (:fkVenda,:fkVeiculo,:valorVenda)";
                $stmt = $db->prepare($query2);

                $stmt->bindParam(':fkVenda', $fk_venda, PDO::PARAM_INT);
                $stmt->bindParam(':fkVeiculo', $_POST['fkVeiculo'], PDO::PARAM_INT);
                $stmt->bindParam(':valorVenda', $_POST['valorVenda'], PDO::PARAM_STR);

                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Venda adicionado.'
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

    function delete_Venda($id)
    {

        try {

            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM vendas WHERE status ='ATIVO' AND pk_venda=:id";
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
                $query = "UPDATE  vendas SET status='{$status}' WHERE pk_venda= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Venda Excluida com Sucesso'
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





