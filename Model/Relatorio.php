<?php

require 'Banco.php';

Class Relatorio
{

 function get_Relatorio($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos

            $response = array();
            if ($id == 0) {
         	  $response = array(
                    "Contas a pagar por fornecedor","Contas pagas por fornecedor","Contas a pagar por cliente","Contas pagas por cliente","Reparos a pagar","Reparos pagos"
                );
                header("HTTP/1.0 200 ");            }
	    else if ($id == 1) {
                $query = "select pk_fornecedor, nome, sum(valor) as Valor from fornecedores as f join compras c on f.pk_fornecedor = c.fk_fornecedor join financeiros_entradas as fs on c.pk_compra = fs.fk_compra where  fs.statusFinanceiro ='PENDENTE' group by pk_fornecedor";
            }
	    else if ($id == 2) {
                $query = "select pk_fornecedor, nome, sum(valor) as Valor from fornecedores as f join compras c on f.pk_fornecedor = c.fk_fornecedor join financeiros_entradas as fs on c.pk_compra = fs.fk_compra where  fs.statusFinanceiro ='PAGO' group by pk_fornecedor";
            }
	    else if($id == 3){
	        $query = "select pk_cliente, nome, sum(valor) as Valor from clientes as c join vendas v on c.pk_cliente = v.fk_clientes join financeiros_saidas as fe on v.pk_venda = fe.fk_venda where  fe.statusFinanceiro ='PENDENTE' group by pk_cliente ";
            }
	    else if($id == 4){
	        $query = "select pk_cliente, nome, sum(valor) as Valor from clientes as c join vendas v on c.pk_cliente = v.fk_clientes join financeiros_saidas as fe on v.pk_venda = fe.fk_venda where  fe.statusFinanceiro ='PAGO' group by pk_cliente ";
            }
	   else if($id == 5){
             $query = "select fk_compra,Sum(fe.valor) as Valor,r.descricao,v.placa from  financeiros_entradas as fe join reparos as r on fe.fk_compra=r.pk_reparo join veiculos as v on v.pk_veiculo=r.fk_veiculo where  statusFinanceiro ='PENDENTE' and fe.descricao='REPARO'  group by fk_compra";
} 
 else if($id == 6){
             $query = "select fk_compra,Sum(fe.valor) as Valor,r.descricao,v.placa from  financeiros_entradas as fe join reparos as r on fe.fk_compra=r.pk_reparo join veiculos as v on v.pk_veiculo=r.fk_veiculo where  statusFinanceiro ='PAGO' and fe.descricao='REPARO'  group by fk_compra";
}
	     else if ($id==10){
		   $query= "SELECT sum(soma) as caixa FROM(
				SELECT sum(valor *(-1))AS soma , cast('E'AS CHAR(1))AS t FROM financeiros_entradas WHERE data_baixa IS NOT NULL AND statusFinanceiro='PAGO' 
				UNION ALL
				SELECT sum(valor) AS soma, cast('S'AS CHAR(1))AS t FROM financeiros_saidas WHERE data_baixa IS NOT NULL AND statusFinanceiro='PAGO'
				UNION ALL 
				SELECT sum(valor *(-1))as soma, cast('R'as char(1)) as t FROM reparos WHERE status='ATIVO'
				) tabela";
	     		}
	     else if($id ==11){
			$query="SELECT ano , SUM(soma) as caixa FROM(
				SELECT EXTRACT(YEAR FROM data_baixa)as ano, SUM(valor *(-1))AS soma , CAST('E'AS CHAR(1))AS t FROM financeiros_entradas WHERE data_baixa IS NOT NULL AND statusFinanceiro='PAGO' GROUP BY ano
				UNION ALL
				SELECT EXTRACT(YEAR FROM data_baixa)as ano, SUM(valor) AS soma, CAST('S'AS CHAR(1))AS t  FROM financeiros_saidas WHERE data_baixa IS NOT NULL AND statusFinanceiro='PAGO'GROUP BY ano
				UNION ALL
				SELECT EXTRACT(YEAR FROM dataCriacao)as ano, SUM(valor *(-1))as soma, CAST('R'AS CHAR(1)) as t FROM reparos WHERE status='ATIVO' GROUP BY ano
				)tabela GROUP BY ano 
				HAVING ano >= EXTRACT(YEAR FROM CURDATE())";
		}else if ($id==12){
			$query="SELECT   extract(month from data_baixa)as mes, sum(valor * (-1))as despesas FROM garage.financeiros_entradas where statusFinanceiro='pago' group by mes
					having mes = extract(month from curdate())";

		}else if($id==13){
			$query="SELECT   extract(month from data_baixa)as mes, sum(valor)as receitas FROM garage.financeiros_saidas where statusFinanceiro='pago' group by mes
					having mes = extract(month from curdate())";
		}else if($id==14){
			$query="SELECT   mes,  sum(soma) as saldo FROM(
				SELECT EXTRACT(MONTH FROM data_baixa) as mes, data_baixa, sum(valor *(-1))AS soma , cast('P'AS CHAR(1))AS t FROM financeiros_entradas WHERE  statusFinanceiro='PAGO' group by mes
				UNION ALL
				SELECT EXTRACT(MONTH FROM data_baixa)as mes, data_baixa, sum(valor) AS soma, cast('R'AS CHAR(1))AS t FROM financeiros_saidas WHERE  statusFinanceiro='PAGO' group by mes

				) tabela group by mes
				HAVING mes = EXTRACT(MONTH FROM CURDATE())";
		}else if($id==15){
			$query="SELECT  date_format(data_baixa, '%M')meses, mes,  sum(soma) as saldo FROM(
				SELECT EXTRACT(MONTH FROM data_baixa) as mes, data_baixa, sum(valor *(-1))AS soma , cast('P'AS CHAR(1))AS t FROM financeiros_entradas WHERE  statusFinanceiro='PAGO' group by mes
				UNION ALL
				SELECT EXTRACT(MONTH FROM data_baixa)as mes, data_baixa, sum(valor) AS soma, cast('R'AS CHAR(1))AS t FROM financeiros_saidas WHERE  statusFinanceiro='PAGO' group by mes

			) tabela group by mes";

		}else if($id==16){
			$query="SELECT f.nome, SUM(valor)AS saldo FROM funcionarios AS f LEFT JOIN cargos AS c ON f.fk_cargo = c.pk_cargos JOIN vendas AS v ON f.pk_funcionario = v.fk_funcionarios
				JOIN financeiros_saidas AS fs ON v.pk_venda = fs.fk_venda
				WHERE fk_cargo = 3 AND fs.statusFinanceiro='pago'
				GROUP BY f.nome";
		}else if($id==17){
			$query="
				select count(pk_cliente)as count from clientes
				";
		}else if($id==18){
			$query="select count(pk_veiculo)as vendido from veiculos where statusVeiculo='vendido'";
		}else if($id=19){
			$query="select count(pk_veiculo)as garagem from veiculos where statusVeiculo='garagem'";

		}
		


	   if($id !=0){
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

}
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

function  caixa(){   


    try{
            $db = Banco::conexao();
            $saldoInicial = 50000;     
            $response = array();

            $queryDespesas = "SELECT   extract(month from data_baixa)as mes, sum(valor) FROM garage.financeiros_entradas where statusFinanceiro='pago' group by mes
                            having mes = extract(month from curdate())";

            $stmt = $db->prepare($queryDespesas);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $despesasResult = $stmt->fetch(PDO::FETCH_ASSOC);
            $depesasMes =  $despesasResult['mes'];
            $depesasValor =  $despesasResult['valor'];

            $queryReceber = "SELECT   extract(month from data_baixa)as mes, sum(valor) FROM garage.financeiros_saidas where statusFinanceiro='pago' group by mes
                            having mes = extract(month from curdate())";  

            $stmt = $db->prepare($queryReceber);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $receitasResult = $stmt->fetch(PDO::FETCH_ASSOC);
            $receitasMes =  $receitasResult['mes'];
            $receitasValor =  $receitasResult['valor'];


            $saldoAcumuladoFinal = $saldoInicial + $receitasValor + $depesasValor;
            $lucroOuPrejuizo= $receitasValor + $depesasValor;

            $response = array(
                    'status' => 200,
                    'saldoAcumuladoFinal' => $saldoAcumuladoFinal,
                    'lucroOuPrejuizo'=> $lucroOuPrejuizo
                );
                header("HTTP/1.0 200 ");

    }catch(PDOException $e){    
        $response = array(
                'status' => 400,
                'message' => $e->getMessage()
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

