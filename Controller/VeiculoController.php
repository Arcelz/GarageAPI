<?php
require_once '../Model/Veiculo.php';
require_once '../Validation/ValidacaoVazio.php';

$veiculo = new Veiculo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':

        if(!empty($_GET["veiculo_id"]))
        {
            $veiculo_id=intval($_GET["veiculo_id"]);
            $veiculo->get_Veiculo($veiculo_id);

        }
        else
        {
            $veiculo->get_Veiculo();

        }
        break;
    case 'POST':
                $veiculo->insert_Veiculo();
        break;
    case 'PUT':

            $veiculo_id = intval($_GET["veiculo_id"]);
            $veiculo->update_Veiculo($veiculo_id);


        break;
    case 'DELETE':

        $veiculo_id=intval($_GET["veiculo_id"]);
        $veiculo->delete_Veiculo($veiculo_id);

        break;
    default:

        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode($response);
        break;
}
