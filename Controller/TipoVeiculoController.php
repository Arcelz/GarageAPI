<?php
require_once '../Model/TipoVeiculo.php';
require_once '../Validation/ValidacaoVazio.php';

$tpVeiculo = new TipoVeiculo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["tpVeiculo_id"]))
        {
            $tpVeiculo_id=intval($_GET["tpVeiculo_id"]);
            $tpVeiculo->get_tpVeiculo($tpVeiculo_id);

        }
        else
        {
            $tpVeiculo->get_tpVeiculo();

        }
        break;
    case 'POST':
            $validacao = new ValidacaoVazio();

            $returnValidacao = $validacao->verificaNome();
            if($returnValidacao <100) {
                $tpVeiculo->insert_tpVeiculo();
            }
            else{
                header('Content-Type: application/json');
                echo json_encode($returnValidacao);
            }

        break;
    case 'PUT':

        $validacao = new ValidacaoVazio();

        $returnValidacao = $validacao->verificaNome();
        if($returnValidacao <100) {
            $tpVeiculo_id = intval($_GET["tpVeiculo_id"]);
            $tpVeiculo->update_tpVeiculo($tpVeiculo_id);
        }
        else{
            header('Content-Type: application/json');
            echo json_encode($returnValidacao);
        }
        break;
    case 'DELETE':

        $tpVeiculo_id=intval($_GET["tpVeiculo_id"]);
        $tpVeiculo->delete_tpVeiculo($tpVeiculo_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode($response);
        break;
}
