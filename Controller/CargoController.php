<?php
require_once '../Model/Cargo.php';
require_once '../Validation/ValidacaoVazio.php';

$cargo = new Cargo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["cargo_id"]))
        {
            $cargo_id=intval($_GET["cargo_id"]);
            $cargo->get_cargo($cargo_id);

        }
        else
        {
            $cargo->get_cargo();

        }
        break;
    case 'POST':
            $validacao = new ValidacaoVazio();
            $returnValidacao = $validacao->verificaNome();
            if($returnValidacao <100){
                $cargo->insert();
            }else{
                header('Content-Type: application/json');
                echo json_encode($returnValidacao);
            }


        break;
    case 'PUT':

        $validacao = new ValidacaoVazio();
        $returnValidacao = $validacao->verificaNome();
        
        if($returnValidacao <100){
            $cargo_id=intval($_GET["cargo_id"]);
            $cargo->update_cargo($cargo_id);
        }else{
            header('Content-Type: application/json');
            echo json_encode($returnValidacao);
        }


        break;
    case 'DELETE':
        // Delete Product
        $cargo_id=intval($_GET["cargo_id"]);
        $cargo->delete_cargo($cargo_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
