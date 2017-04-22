<?php

require 'Banco.php';

Class FinanceiroSaida
{

    function get_FinSaida($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM financeiros_saidas WHERE status ='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_saida = :id LIMIT 1";

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


    public function insert_FinSaida()
    {

        try {
            $db = Banco::conexao();
            $status = 'ATIVO';


            $dataEmissao = $_POST['$dataEmissao'];
            $parcela = $_POST['parcela'];
            $valor = $_POST['valor'];
            $divisao = $valor / $parcela;


            parse_str(file_get_contents('php://input'), $post_vars);
            for ($i = 0; $i < $parcela; $i++) {

                $query = "INSERT INTO financeiros_saidas(fk_compra,data_emissao,data_vencimento,data_baixa,valor,status) values 
                (:fkCompra,:dataEmissao,:dataVencimento,:dataBaixa,:valor,:status)";
                $stmt = $db->prepare($query);

                $stmt->bindParam(':fkCompra', $_POST['fkCompra'], PDO::PARAM_INT);
                $stmt->bindParam(':dataEmissao', $_POST['dataEmissao'], PDO::PARAM_STR);
                $stmt->bindParam(':dataVencimento', $post_vars['vencimento' . $i], PDO::PARAM_STR);
                $stmt->bindParam(':dataBaixa', $_POST['dataBaixa'], PDO::PARAM_STR);
                $stmt->bindParam(':valor', $divisao, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
                $stmt->execute();
                //$vencimento = $post_vars['vencimento'.$i];
                //var_dump($vencimento);
            }

            $response = array(
                'code' => 200,
                'message' => 'Sou foda'
            );
            header("HTTP/1.0 200 ");

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





