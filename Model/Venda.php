<?php

require 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

Class Venda
{
      public static function getUsuario(){
        $getUsuario = new ValidaToken();//intancia a classe de validaÃ§Ã£o de token onde sera feita a verificacao do token
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


    function get_Venda($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT v.pk_venda, cl.nome AS nomeCliente, f.nome as nomeFuncionario, v.dataCriacao,  v.statusVenda, ci.valor_venda FROM vendas AS v LEFT JOIN clientes AS cl ON v.fk_clientes = cl.pk_cliente LEFT JOIN funcionarios AS f ON v.fk_funcionarios = f.pk_funcionario LEFT JOIN vendas_itens AS ci ON v.pk_venda = ci.fk_venda WHERE v.status = 'ATIVO'";

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
            self::geraLog( $argumentos, $e->getMessage()); //chama a funÃ§Ã£o para gravar os logs

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }


   public function insert_Venda()
    {
         $db = Banco::conexao();
         $db->beginTransaction();

        try {
           
            $status = 'ATIVO';
            $statusCompra = "EFETUADA";


            $datas = self::getData();
           /*// $dataCriacao = self::getData();
            //Este bloco é responsavel por retornar o ultimo registro de venda realizado.
            //Usamos este metado para mostrar organizado a quantidade de venda realizada. Para não ter que usar o id.
            $queryCont = "SELECT pk_compra,numero FROM compras ORDER BY pk_compra DESC LIMIT 1 ";
            $stmt = $db->prepare($queryCont);
            $stmt->execute();

            $returnNumero = $stmt->fetch(PDO::FETCH_ASSOC);
            $numero = $returnNumero['numero'] + 1;
            // ======== =========== ==========================*/

            $query = "INSERT INTO vendas(fk_clientes,fk_funcionarios,status, statusVenda, dataCriacao, usuarioCriacao) values 
                (:fkCliente, :fkFuncionario,:status, :statusCompra, :dataCriacao, :usuario )";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':fkCliente', $_POST['fkCliente'], PDO::PARAM_INT);
            $stmt->bindParam(':fkFuncionario', $_POST['fkFuncionario'], PDO::PARAM_INT);                   
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':statusCompra', $statusCompra, PDO::PARAM_STR);           
            $stmt->bindParam(':dataCriacao', $datas, PDO::PARAM_STR);
             $stmt->bindParam(':usuario', self::getUsuario(), PDO::PARAM_STR);
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
                $stmt->bindParam(':valorVenda', $_POST['valorVenda'], PDO::PARAM_INT);
                
                $stmt->execute();
                
                 $returnComprasItens = $stmt->rowCount();

                 if($returnComprasItens >0){
                    $statusFinanceiro = "PENDENTE";
                    $usuario = self::getUsuario();
                    $parcela = $_POST['parcela'];
                    //$valor = $_POST['valorTotal'];

                    parse_str(file_get_contents('php://input'), $post_vars);
                    for($i = 0; $i<$parcela; $i++){
                          $query3 = "INSERT INTO financeiros_saidas(fk_venda, data_emissao, data_vencimento,valor,status,statusFinanceiro, usuarioCriacao, dataCriacao) values (
                          '$fk_venda','$datas',:dataVencimento,:valor,'$status','$statusFinanceiro','$usuario','$datas')";
                          $stmt = $db->prepare($query3);

                           $stmt->bindParam(':valor', $_POST['valorTotal'], PDO::PARAM_INT);
                           $stmt->bindParam(':dataVencimento', $post_vars['vencimento' . $i], PDO::PARAM_STR);             

                           $stmt->execute();
                    }

                  
                 }
                 $db->commit();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Venda realizada.'
                );
                header("HTTP/1.0 200 ");
            }
            // ======== =========== ==========================
        } catch (PDOException $e) {
            $db->rollBack();
            $response = array(
                'status' => 400,
                'message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "inserindo.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

      function update_Venda($id)
    {

        try {

            $db = Banco::conexao();
             $usuario = self::getUsuario();
             $statusCompra = "CANCELADA";
             $datas = self::getData();
            parse_str(file_get_contents('php://input'), $post_vars);          
          
            $query = "UPDATE vendas AS v LEFT JOIN financeiros_saidas AS fs ON v.pk_venda = fs.fk_venda
			SET v.statusVenda = '$statusCompra', v.usuarioUpdate ='$usuario', v.dataUpdate ='$datas', fs.statusFinanceiro = '$statusCompra', fs.usuarioUpdate ='$usuario', fs.dataUpdade='$datas' WHERE v.pk_venda=:id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $post_vars['pkVenda'], PDO::PARAM_INT);         
           


            $stmt->execute();
            $response = array(
                'status' => 200,
                'status_message' => 'Venda Cancelada com Sucesso'
            );
            header("HTTP/1.0 200 ");

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
             header("HTTP/1.0 400 ");
             self::getUsuario();
            $argumentos = "update.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

     function delete_Venda($id)
    {

        try {

            $db = Banco::conexao();
            $status = 'DESATIVADO';
             $usuario = self::getUsuario();
             
             $datas = self::getData();

            $query = "SELECT * FROM vendas WHERE status ='ATIVO' AND pk_venda=:id";
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
                $query = "UPDATE vendas AS v LEFT JOIN financeiros_saidas AS fs ON v.pk_venda = fs.fk_venda
			SET v.status = '$status', v.usuarioUpdate ='$usuario', v.dataUpdate ='$datas', fs.status = '$status', fs.usuarioUpdate ='$usuario', fs.dataUpdade='$datas' WHERE v.pk_venda=:id";


                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Venda Excluida com Sucesso'
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
            $argumentos = "delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}