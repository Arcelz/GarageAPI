<?php

require 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

Class FinanceiroSaida
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


    /**
     * @param int $id
     */
    function get_FinSaida($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos

            $response = array();
            if ($id == 0) {
                $query = "SELECT * FROM financeiros_entradas WHERE status ='ATIVO' AND statusFinanceiro='PENDENTE'";
            }
	    else if ($id == 1) {
                $query = "SELECT * FROM financeiros_entradas WHERE status ='ATIVO' AND statusFinanceiro='PAGO'";
            }
	    else if ($id == 2) {
                $query = "SELECT * FROM financeiros_entradas WHERE status ='ATIVO' AND statusFinanceiro='CANCELADA'";
            }
	     else if ($id==4){
		   $query= "SELECT sum(soma) as caixa FROM(SELECT sum(valor *(-1))AS soma , cast('E'AS CHAR(1))AS t FROM financeiros_entradas
 			     WHERE data_baixa IS NOT NULL AND statusFinanceiro='PAGO'  UNION ALL
			     SELECT sum(valor) AS soma, cast('S'AS CHAR(1))AS t FROM financeiros_saidas
		            WHERE data_baixa IS NOT NULL AND statusFinanceiro='PAGO' ) tabela ";
	     }else if($id==3){
			$query="select  sum(valor)as caixa  from financeiros_entradas where statusFinanceiro ='pendente' and status='ATIVO'";
	     }else if($id==5){
		$query="select  sum(valor) as caixa  from financeiros_saidas where statusFinanceiro ='pendente' and status='ATIVO'";
	     }

            $stmt = $db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetchAll();

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


    function update_Compra($id)
    {

        try {

            $db = Banco::conexao();
	$usuario = self::getUsuario();
	$data = self::getData();
            parse_str(file_get_contents('php://input'), $post_vars);

            $query = "UPDATE financeiros_entradas SET statusFinanceiro='PAGO',data_baixa='$data',usuarioUpdate='$usuario',dataUpdate='$data' WHERE pk_entrada=:id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $response = array(
                'status' => 200,
                'status_message' => 'Financeiro Entrada Atualizado'
            );
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400");
            self::getUsuario();
            $argumentos = "Update .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


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
                    'status' => 404,
                    'status_message' => 'Recurso nao encontrado'

                );

                header("HTTP/1.0 404 ");
            } else {
                $query = "UPDATE  compras SET status='{$status}' WHERE pk_compra= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Compra Excluida com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }


        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Delete.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}





