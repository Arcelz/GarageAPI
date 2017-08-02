<?php

require_once 'Banco.php';
require_once '../Validation/GerarData.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

Class Compra
{
    public static function getData(){
        $getData = new GerarData();
        return $getData ->gerarDataHora();
    }

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


    function get_Comra($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT c.pk_compra, fo.nome AS nomeFornecedor, f.nome as nomeFuncionario, c.datas, c.numero, c.statusCompra, ci.valor_compra FROM compras AS c LEFT JOIN fornecedores AS fo ON c.fk_fornecedor = fo.pk_fornecedor LEFT JOIN funcionarios AS f ON c.fk_funcionario = f.pk_funcionario LEFT JOIN compras_itens AS ci ON c.pk_compra = ci.fk_compra WHERE c.status = 'ATIVO' AND c.statusCompra='EFETUADA'";

            $response = array();
            if ($id == 1) {
                
                $query = " SELECT c.pk_compra, fo.nome AS nomeFornecedor, f.nome as nomeFuncionario, c.datas, c.numero, c.statusCompra, ci.valor_compra FROM compras AS c LEFT JOIN fornecedores AS fo ON c.fk_fornecedor = fo.pk_fornecedor LEFT JOIN funcionarios AS f ON c.fk_funcionario = f.pk_funcionario LEFT JOIN compras_itens AS ci ON c.pk_compra = ci.fk_compra WHERE c.status = 'ATIVO' AND c.statusCompra='CANCELADA'";

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
            $argumentos = "Pesquisando compra.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }


    public function insert_Compra()
    {
	  $db = Banco::conexao();
         $db->beginTransaction();

        try {
           
            $status = 'ATIVO';
            $statusCompra = "EFETUADA";
            $statusVeiculo = 'GARAGEM';    

            $datas = self::getData();           
           

            $query0 = "INSERT INTO veiculos(fk_tipo,fk_marca,ano,valor_compra,statusVeiculo,status, placa, fk_modelo) values
                        (:fkTipo,:fkMarca,:ano,:valorCompra,:statusVeiculo,:status, :placa, :fkModelo)";
            $stmt = $db->prepare($query0);

            $stmt->bindParam(':fkTipo', $_POST['fkTipo'], PDO::PARAM_INT);
            $stmt->bindParam(':fkMarca', $_POST['fkMarca'], PDO::PARAM_INT);
             $stmt->bindParam(':fkModelo', $_POST['fkModelo'], PDO::PARAM_INT);
            $stmt->bindParam(':ano', $_POST['ano'], PDO::PARAM_STR);
            $stmt->bindParam(':placa', $_POST['placa'], PDO::PARAM_STR);
            $stmt->bindParam(':valorCompra', $_POST['valorCompra'], PDO::PARAM_STR);
           
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':statusVeiculo', $statusVeiculo, PDO::PARAM_STR);

            $stmt->execute();
            $fk_Veiculo = $db->lastInsertId();//retorna o ultimo id
           // $dataCriacao = self::getData();
            //Este bloco � responsavel por retornar o ultimo registro de venda realizado.
            //Usamos este metado para mostrar organizado a quantidade de venda realizada. Para n�o ter que usar o id.
         /*   $queryCont = "SELECT pk_compra,numero FROM compras ORDER BY pk_compra DESC LIMIT 1 ";
            $stmt = $db->prepare($queryCont);
            $stmt->execute();

            $returnNumero = $stmt->fetch(PDO::FETCH_ASSOC);
            $numero = $returnNumero['numero'] + 1;
            // ======== =========== ==========================*/

            $query = "INSERT INTO compras(fk_fornecedor,fk_funcionario,datas,status,numero, statusCompra, dataCriacao, usuarioInsert) values 
                (:fkFornecedor, :fkFuncionario,:datas,:status,:numero, :statusCompra, :dataCriacao, :usuario )";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':fkFornecedor', $_POST['fkFornecedor'], PDO::PARAM_INT);
            $stmt->bindParam(':fkFuncionario', $_POST['fkFuncionario'], PDO::PARAM_INT);
            $stmt->bindParam(':datas',  $datas, PDO::PARAM_STR);          
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':statusCompra', $statusCompra, PDO::PARAM_STR);
            $stmt->bindParam(':numero', $numero, PDO::PARAM_INT);
            $stmt->bindParam(':dataCriacao', $datas, PDO::PARAM_STR);
             $stmt->bindParam(':usuario', self::getUsuario(), PDO::PARAM_STR);
            $stmt->execute();

            
            //Este bloco � responsavel pela inser��o das vendas itens. usamos a variavel $returnn para verificar se
            // foi inserido a venda corretamente. Caso sim cai no IF e salva a vendas_itens
            
            $returnn = $stmt->rowCount();

            if ($returnn > 0) {
                $fk_compra = $db->lastInsertId();//retorna o ultimo id

                //("INSERT INTO clientes_enderecos(logradouro,bairro,cidade,estado,pais,cep,fk_cliente)
                $query2 = "INSERT INTO compras_itens(fk_compra,fk_veiculo,valor_compra) 
                    values (:fkCompra,:fkVeiculo,:valorCompra)";
                $stmt = $db->prepare($query2);
                
                $stmt->bindParam(':fkCompra', $fk_compra, PDO::PARAM_INT);
                $stmt->bindParam(':fkVeiculo',  $fk_Veiculo, PDO::PARAM_INT);
                $stmt->bindParam(':valorCompra', $_POST['valorCompra'], PDO::PARAM_INT);
                
                $stmt->execute();                
               
                
                 $returnComprasItens = $stmt->rowCount();

                 if($returnComprasItens >0){
                    $statusFinanceiro = "PENDENTE";
		      $descricao = "COMPRA";
                    $classificacao = "VEICULOS";
                    $usuario = self::getUsuario();
                    $parcela = $_POST['parcela'];
                    //$valor = $_POST['valorTotal'];

                    parse_str(file_get_contents('php://input'), $post_vars);
                    for($i = 0; $i<$parcela; $i++){
                          $query3 = "INSERT INTO financeiros_entradas(fk_compra,descricao,classificacao, data_emissao, data_vencimento,valor,status,statusFinanceiro, usuarioCriacao, dataCriacao) values (
                          '$fk_compra','$descricao','$classificacao','$datas',:dataVencimento,:valor,'$status','$statusFinanceiro','$usuario','$datas')";
                          $stmt = $db->prepare($query3);

                           $stmt->bindParam(':valor', $_POST['valorTotal'], PDO::PARAM_INT);
                           $stmt->bindParam(':dataVencimento', $post_vars['vencimento' . $i], PDO::PARAM_STR);             

                           $stmt->execute();
                    }

                  
                 }
		    $db->commit();
                $response = array(
                    'status' => 200,
                    'status_message' => 'Compra realizada.'
                );
                header("HTTP/1.0 200 ");
            }
            // ======== =========== ==========================
        } catch (PDOException $e) {
            $db->rollBack();
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "inserindo.....";
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
             $statusCompra = "CANCELADA";
             $datas = self::getData();
            parse_str(file_get_contents('php://input'), $post_vars);          
          
            $query = "UPDATE compras AS co LEFT JOIN financeiros_entradas AS fe ON co.pk_compra = fe.fk_compra
			SET co.statusCompra = '$statusCompra', co.usuarioUpdate ='$usuario', co.dataUpdate ='$datas' ,fe.statusFinanceiro = '$statusCompra', fe.usuarioUpdate ='$usuario', fe.dataUpdate ='$datas' WHERE co.pk_compra=:id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $post_vars['pkCompra'], PDO::PARAM_INT);         
           


            $stmt->execute();
            $response = array(
                'status' => 200,
                'status_message' => 'Compra Cancelada com Sucesso'
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
            self::geraLog( $argumentos, $e->getMessage()); //chama a fun��o para gravar os logs

        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_Compra($id)
    {

        try {

            $db = Banco::conexao();
            $status = 'DESATIVADO';
	     $usuario = self::getUsuario();
            
            $datas = self::getData();

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
		   $query = "UPDATE compras AS co LEFT JOIN financeiros_entradas AS fe ON co.pk_compra = fe.fk_compra
			SET co.status = '$status', co.usuarioUpdate ='$usuario', co.dataUpdate ='$datas', fe.status = '$status', fe.usuarioUpdate ='$usuario', fe.dataUpdate='$datas' 
            WHERE co.pk_compra=:id";

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
            $argumentos = "delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}





