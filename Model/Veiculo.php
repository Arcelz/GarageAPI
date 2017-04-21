<?php

require 'Banco.php';

Class Veiculo{

    function get_Veiculo($id=0)
    {
        try{
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query="SELECT * FROM veiculos WHERE status ='ATIVO'";

            $response =array();
            if($id != 0)
            {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_veiculo = :id LIMIT 1";

            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();
            //var_dump($row);


            if($row == null) {
                $response = array(
                    'code'=>404,
                    'message' => 'Recurso nao encontrado'
                );
                header("HTTP/1.0 404 ");

            }else{
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    //$response[]= $row;
                    array_push($response,$row);
                }

            }

        }catch(PDOException $e){
            $response = array(
                'code'=>400,
                'message'=>$e->getMessage()
            );
            header("HTTP/1.0 400 ");
        }



        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function insert_Veiculo()
    {

        try {
            $db = Banco::conexao();

            $status = 'ATIVO';
            $statusVeiculo = 'GARAGEM';

            $query = "INSERT INTO veiculos(fk_tipo,fk_marca,ano,valor_compra,valor_venda,statusVeiculo,status) values
                        (:fkTipo,:fkMarca,:ano,:valorCompra,:valorVenda,:statusVeiculo,:status)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':fkTipo', $_POST['fkTipo'], PDO::PARAM_INT);
            $stmt->bindParam(':fkMarca', $_POST['fkMarca'], PDO::PARAM_INT);
            $stmt->bindParam(':ano', $_POST['ano'], PDO::PARAM_STR);
            $stmt->bindParam(':valorCompra', $_POST['valorCompra'], PDO::PARAM_STR);
            $stmt->bindParam(':valorVenda', $_POST['valorVenda'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':statusVeiculo', $statusVeiculo, PDO::PARAM_STR);

            $stmt->execute();

            $response = array(
                'code'=>200,
                'message'=>'Veiculo adicionado.'
            );
            header("HTTP/1.0 200 ");

        }
        catch (PDOException $e){
            $response = array(
                'code'=>400,
                'message'=>$e->getMessage()
            );
            header("HTTP/1.0 400 ");
        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_Veiculo($id)
    {

        try {
            $db = Banco::conexao();

            parse_str(file_get_contents('php://input'), $post_vars);

            $query = "UPDATE veiculos  SET  fk_tipo=:fkTipo, fk_marca=:fkMarca, ano=:ano, valor_compra=:valorCompra, valor_venda=:valorVenda WHERE pk_veiculo= :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fkTipo', $post_vars['fkTipo'], PDO::PARAM_INT);
            $stmt->bindParam(':fkMarca', $post_vars['fkMarca'], PDO::PARAM_INT);//fkTipo
            $stmt->bindParam(':ano', $post_vars['ano'], PDO::PARAM_STR);//fkVeiculo
            $stmt->bindParam(':valorCompra', $post_vars['valorCompra'], PDO::PARAM_STR);
            $stmt->bindParam(':valorVenda', $post_vars['valorVenda'], PDO::PARAM_STR);



            $stmt->execute();
            $response = array(
                'code' => 200,
                'message' => 'Veiculo Atualizado com sucesso'

            );
            header("HTTP/1.0 200 ");
        }catch (PDOException $e){
            $response=array(
                'code' => 400,
                'errorMysql: ' =>$e->getMessage()
            );

        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_Veiculo($id)
    {
        try {
            $db = Banco::conexao();
            $status = 'DESATIVADO';
            $statusVeiculo = 'DESATIVADO';

            $query = "SELECT * FROM veiculos WHERE status ='ATIVO' AND pk_veiculo=:id";
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
                $query = "UPDATE  veiculos SET status='{$status}',statusVeiculo='{$statusVeiculo}' WHERE pk_veiculo= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'code' => 200,
                    'message' => 'Veiculo Excluido com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }
        }
        catch (PDOException $e){
            $response=array(
                'code' => 400,
                'errorMysql: ' =>$e->getMessage()
            );
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

