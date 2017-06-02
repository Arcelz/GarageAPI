<?php

require 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

Class Veiculo
{
      public static function getUsuario(){
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        //var_dump($permicao) ;
        return $permicao;
    }

    public static  function geraLog($argumentos, $erroMysql ){
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog ->grava_log_erros_banco($arquivo,$argumentos, $erroMysql, self::getUsuario());
    }


    function pesuisar_veiculo(){

        try
        {
            $db = Banco::conexao();
             $response = array();
             
             $consulta =  $_POST['consulta'];
            
             $query = "SELECT * FROM veiculos WHERE placa LIKE '%$consulta%' AND status ='ATIVO'";

            $stmt = $db->prepare($query);
            //$stmt->bindParam(':id', $id, PDO::PARAM_STR);
          
            $stmt->execute();
            $row = $stmt->fetchAll();
            //var_dump($row);


            if ($row == null) {
                 $response = array(
                    'status' => 400,
                    'status_message' => 'Nao foi possivel realizar a pesquisa'
                );
                header("HTTP/1.0 400 ");

            } else {
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //$response[]= $row;
                    array_push($response, $row);
                }

            }

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }


        header('Content-Type: application/json');
        echo json_encode($response);


    }

    function get_Veiculo($id = 0)
    {
        try {
            $db = Banco::conexao();

//            
            //Essa query busca todos os regestritos
            $query = "SELECT v.pk_veiculo, tv.nome, m.nome As nomeMarca, mo.nome AS nomeModelo, v.placa, v.ano, v.valor_compra, v.status  FROM veiculos AS v LEFT JOIN tipos_veiculos AS tv ON v.fk_tipo = tv.pk_tipo LEFT JOIN marcas m ON v.fk_marca = m.pk_marca LEFT JOIN modelos AS mo ON v.fk_modelo = mo.pk_modelo WHERE v.status ='ATIVO'";

            //select * FROM veiculos WHERE nome like '%%' and pk_veiculo = 1 and status = 'ATIVO'

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query = " SELECT v.pk_veiculo, tv.pk_tipo, tv.nome, m.pk_marca, m.nome As nomeMarca,mo.pk_modelo, mo.nome AS nomeModelo, v.placa, v.ano, v.valor_compra, v.status  FROM veiculos AS v LEFT JOIN tipos_veiculos AS tv ON v.fk_tipo = tv.pk_tipo LEFT JOIN marcas m ON v.fk_marca = m.pk_marca LEFT JOIN 
				modelos AS mo ON v.fk_modelo = mo.pk_modelo WHERE v.status ='ATIVO' AND v.pk_veiculo  = :id LIMIT 1";

            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();
            //var_dump($row);


            if ($row == null) {
                $response = array(
                    'status' => 404,
                    'status_message' => 'Recurso nao encontrado'
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
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

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

            $query = "INSERT INTO veiculos(fk_tipo,fk_marca,ano,valor_compra,statusVeiculo,status, placa, fk_modelo) values
                        (:fkTipo,:fkMarca,:ano,:valorCompra,:statusVeiculo,:status, :placa, :fkModelo)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':fkTipo', $_POST['fkTipo'], PDO::PARAM_INT);
            $stmt->bindParam(':fkMarca', $_POST['fkMarca'], PDO::PARAM_INT);
             $stmt->bindParam(':fkModelo', $_POST['fkModelo'], PDO::PARAM_INT);
            $stmt->bindParam(':ano', $_POST['ano'], PDO::PARAM_STR);
            $stmt->bindParam(':placa', $_POST['placa'], PDO::PARAM_STR);
            $stmt->bindParam(':valorCompra', $_POST['valorCompra'], PDO::PARAM_STR);
           
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':statusVeiculo', $statusVeiculo, PDO::PARAM_STR);

            $stmt->execute();

            $response = array(
                'status' => 200,
                'status_message' => 'Veiculo adicionado.'
            );
            header("HTTP/1.0 200 ");

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Inserindo .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

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

           $query = "UPDATE veiculos  SET  fk_tipo=:fkTipo,fk_modelo=:fkModelo, fk_marca=:fkMarca, ano=:ano, valor_compra=:valorCompra WHERE pk_veiculo= :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $post_vars['pk_veiculo'], PDO::PARAM_INT);
            $stmt->bindParam(':fkTipo', $post_vars['fkTipo'], PDO::PARAM_INT);
            $stmt->bindParam(':fkMarca', $post_vars['fkMarca'], PDO::PARAM_INT);//fkTipo
            $stmt->bindParam(':ano', $post_vars['ano'], PDO::PARAM_STR);//fkVeiculo
            $stmt->bindParam(':valorCompra', $post_vars['valorCompra'], PDO::PARAM_STR);
            $stmt->bindParam(':fkModelo', $post_vars['fkModelo'], PDO::PARAM_STR);

            $stmt->execute();
            $response = array(
                'status' => 200,
                'status_message' => 'Veiculo Atualizado com sucesso'

            );
            header("HTTP/1.0 200 ");
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );

            self::getUsuario();
            $argumentos = "Upadate .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


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
                    'status' => 404,
                    'status_message' => 'Recurso nao encontrado'

                );
                header("HTTP/1.0 404 ");
            } else {
                $query = "UPDATE  veiculos SET status='{$status}',statusVeiculo='{$statusVeiculo}' WHERE pk_veiculo= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Veiculo Excluido com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );

            self::getUsuario();
            $argumentos = "delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

