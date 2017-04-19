<?php
require_once '../Model/Fornecedor.php';
require_once '../Validation/ValidacaoVazio.php';

$fornecedor = new Fornecedor();

$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{

    case 'GET':

        if(!empty($_GET["fornecedor_id"]))
        {
            $fornecedor_id=intval($_GET["fornecedor_id"]);

            $fornecedor->get_Fornecedor($fornecedor_id);

        }
        else
        {
            $fornecedor->get_Fornecedor();

        }
        break;
    case 'POST':

        $validacao = new ValidacaoVazio();

        $returnValidacao = $validacao->verificaCamposEndereco();

        //Quando a validação esta ok retorna 1 para inserir no banco
        if($validacao->verificaCamposEndereco() <100){

            $fornecedor->insert_Fornecedor();

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

            $fornecedor_id=intval($_GET["fornecedor_id"]);

            $fornecedor->update_Fornecedor($fornecedor_id);

        }
        else{

            //Aqui vai imprimir o resultado da validação
            header('Content-Type: application/json');
            echo json_encode($returnValidacao);

        }


        break;
    case 'DELETE':
        // Delete Product
        $fornecedor_id=intval($_GET["fornecedor_id"]);
        $fornecedor->delete_Fornecedor($fornecedor_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
