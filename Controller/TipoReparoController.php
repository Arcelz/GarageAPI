<?php
require_once '../Model/TipoReparo.php';
require_once '../Validation/ValidacaoVazio.php';

$tpReparo = new TipoReparo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["tpReparo_id"]))
        {
            $tpReparo_id=intval($_GET["tpReparo_id"]);
            $tpReparo->get_tpReparo($tpReparo_id);

        }
        else
        {
            $tpReparo->get_tpReparo();

        }
        break;
    case 'POST':

            $validacao = new ValidacaoVazio();
            $returnValidacao = $validacao->verificaNome();
            if($returnValidacao <100){
                $tpReparo->insert();
            }else{

                //Aqui vai imprimir o resultado da validação
                header('Content-Type: application/json');
                echo json_encode($returnValidacao);
            }


        break;
    case 'PUT':

        $validacao = new ValidacaoVazio();
        $returnValidacao = $validacao->verificaNome();

        if($returnValidacao <100){
            $tpReparo_id=intval($_GET["tpReparo_id"]);
            $tpReparo->update_tpReparo($tpReparo_id);
        }else{

            //Aqui vai imprimir o resultado da validação
            header('Content-Type: application/json');
            echo json_encode($returnValidacao);
        }

        break;
    case 'DELETE':
        // Delete Product
        $tpReparo_id=intval($_GET["tpReparo_id"]);
        $tpReparo->delete_tpReparo($tpReparo_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
