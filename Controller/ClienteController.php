<?php
require_once '../Model/Cliente.php';
require_once '../Validation/ValidacaoVazio.php';

$cliente = new Cliente();

$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{

    case 'GET':

        if(!empty($_GET["cliente_id"]))
        {
            $cliente_id=intval($_GET["cliente_id"]);

            $cliente->get_Cliente($cliente_id);

        }
        else
        {
            $cliente->get_Cliente();

        }
        break;
    case 'POST':

        $validacao = new ValidacaoVazio();

        $returnValidacao = $validacao->verificaCamposEndereco();

        //Quando a validação esta ok retorna 1 para inserir no banco
        if($validacao->verificaCamposEndereco() <100){

            $cliente->insert_Cliente();

        }
        else{

            //Aqui vai imprimir o resultado da validação
            header('Content-Type: application/json');
            echo json_encode($returnValidacao);

        }
        break;
    case 'PUT':

        $validacao = new ValidacaoVazio();

        $returnValidacao = $validacao->verificaCamposEndereco();
        echo $returnValidacao;

        if($validacao->verificaCamposEndereco() <100){

            $cliente_id=intval($_GET["cliente_id"]);
            $cliente->update_Cliente($cliente_id);

        }
        else{

            //Aqui vai imprimir o resultado da validação
            header('Content-Type: application/json');
            echo json_encode($returnValidacao);

        }


        break;
    case 'DELETE':
        // Delete Product
        $cliente_id=intval($_GET["cliente_id"]);
        $cliente->delete_Cliente($cliente_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
