<?php

require 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

Class Reparo
{

      public static function getUsuario(){
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        //var_dump($permicao) ;
        return $permicao;
    }
  public static function getData(){
        $getData = new GerarData();
        return $getData ->gerarDataHora();
    }

    public static  function geraLog($argumentos, $erroMysql ){
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog ->grava_log_erros_banco($arquivo,$argumentos, $erroMysql, self::getUsuario());
    }



    function get_Reparo($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM reparos as r JOIN veiculos as v on v.pk_veiculo=r.fk_veiculo  WHERE r.status='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_reparo = :id LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            }
else{
            $stmt = $db->prepare($query);
            $stmt->execute();
}
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
        unset($db);


        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function insert()
    {
		$db = Banco::conexao();
         $db->beginTransaction();

        try {
           

            $status = 'ATIVO';
	$usuario = self::getUsuario();
	$data = self::getData();

            $query = "INSERT INTO reparos(status,fk_veiculo,fk_tipos,valor,descricao,usuarioCriacao,dataCriacao) values (:status,:fkVeiculo,:fkTipo,:valor,:descricao,'{$usuario}','{$data}')";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':valor', $_POST['valor'], PDO::PARAM_STR);
            $stmt->bindParam(':fkTipo', $_POST['fkTipo'], PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $_POST['descricao'], PDO::PARAM_STR);
            $stmt->bindParam(':fkVeiculo', $_POST['fkVeiculo'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            $stmt->execute();
		 $fk_reparo = $db->lastInsertId();


	         $statusFinanceiro = "PENDENTE";
                    $descricao = "REPARO";
                    $classificacao = "VEICULOS";
                    $parcela = $_POST['parcela'];
                    $fkVeiculo = $_POST['fkVeiculo'];

                    parse_str(file_get_contents('php://input'), $post_vars);
                    for($i = 0; $i<$parcela; $i++){
                          $query3 = "INSERT INTO financeiros_entradas(fk_compra,descricao,classificacao, data_emissao, data_vencimento,valor,status,statusFinanceiro, usuarioCriacao, dataCriacao) values (
                          '$fk_reparo','$descricao','$classificacao','$data',:dataVencimento,:valor,'$status','$statusFinanceiro','$usuario','$data')";
                          $stmt = $db->prepare($query3);

                           $stmt->bindParam(':valor', $_POST['valorTotal'], PDO::PARAM_INT);
                           $stmt->bindParam(':dataVencimento', $post_vars['vencimento' . $i], PDO::PARAM_STR);
			      	             

                           $stmt->execute();
                    }
		
		 $db->commit();


            $response = array(
                'status' => 200,
                'status_message' => 'Reparo adicionado.'
            );
            header("HTTP/1.0 200 ");

        } catch (PDOException $e) {
 		$db->rollBack();
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Inseido.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_Reparos($id)
    {

        try {
            $db = Banco::conexao();
            parse_str(file_get_contents('php://input'), $post_vars);
	$usuario = self::getUsuario();
	$data = self::getData();
           /* $query = "UPDATE reparos  SET  descricao=:descricao, fk_veiculo=:fkVeiculo,fk_tipos=:fkTipo, valor=:valor,usuarioUpdate='{$usuario}',dataUpdate='{$data}' WHERE pk_reparo= :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fkTipo', $post_vars['fkTipo'], PDO::PARAM_STR);//fkTipo
            $stmt->bindParam(':fkVeiculo', $post_vars['fkVeiculo'], PDO::PARAM_STR);//fkVeiculo
            $stmt->bindParam(':valor', $post_vars['valor'], PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $post_vars['descricao'], PDO::PARAM_STR);

            $stmt->execute();*/

	       $query = "UPDATE reparos AS r LEFT JOIN financeiros_entradas AS fe ON r.pk_reparo = fe.fk_compra
			SET r.status = 'CANCELADO', r.usuarioUpdate ='$usuario', r.dataUpdate ='$data' ,fe.statusFinanceiro = 'CANCELADA', fe.usuarioUpdate ='$usuario', fe.dataUpdate ='$data' WHERE r.pk_reparo=:id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);         
           


            $stmt->execute();

            $response = array(
                'status' => 200,
                'status_message' => 'Reparo Cancelado com sucesso'

            );
            header("HTTP/1.0 200 ");
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTP/1.0 400");
            self::getUsuario();
            $argumentos = "Update .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_Reparos($id)
    {
        try {
            $db = Banco::conexao();
            $status = 'DESATIVADO';

	     $usuario = self::getUsuario();
            
            $data = self::getData();


            $query = "SELECT * FROM reparos WHERE status ='ATIVO' AND pk_reparo=:id";
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
                $query = "UPDATE reparos AS r LEFT JOIN financeiros_entradas AS fe ON r.pk_reparo = fe.fk_compra
			SET r.status = 'DESATIVADO', r.usuarioUpdate ='$usuario', r.dataUpdate ='$data' ,fe.statusFinanceiro = 'DESATIVADO', fe.usuarioUpdate ='$usuario', fe.dataUpdate ='$data' WHERE r.pk_reparo=:id";

                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Reparo Excluido com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTP/1.0 400");
            self::getUsuario();
            $argumentos = "delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

