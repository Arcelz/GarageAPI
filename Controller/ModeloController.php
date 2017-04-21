<?php
require_once '../Model/Modelo.php';
require_once '../Validation/ValidacaoVazio.php';

$modelo = new Modelo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["modelo_id"]))
        {
            $modelo_id=intval($_GET["modelo_id"]);
            $modelo->get_Modelo($modelo_id);

        }
        else
        {
            $modelo->get_Modelo();

        }
        break;
    case 'POST':

            $validacao = new ValidacaoVazio();
            $returnValidacao = $validacao->verificaNome();

            if($returnValidacao <100){
                $modelo->insert();
            }else{
                //Aqui vai imprimir o resultado da validação
                header('Content-Type: application/json');
                echo json_encode($returnValidacao);

            }


        break;
    case 'PUT':

        $modelo_id=intval($_GET["modelo_id"]);
        $modelo->update_Modelo($modelo_id);
        break;
    case 'DELETE':
        // Delete Product
        $modelo_id=intval($_GET["modelo_id"]);
        $modelo->delete_Modelo($modelo_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode($response);
        break;
}
