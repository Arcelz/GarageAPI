<?php
require_once '../Model/Compra.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

// Allow from any origin
if (isset($_SERVER["HTTP_ORIGIN"])) {
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}


$compra = new Compra();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = (array)$validaToken->token();

header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':
        if (isset($permicao['compraVisualizar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            if (!empty($_GET["compra_id"])) {
                $compra_id = intval($_GET["compra_id"]);
                $compra->get_Comra($compra_id);

            } else {
                $compra->get_Comra();

            }
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    case 'POST':

        if (isset($permicao['compraCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $compra->insert_Compra();
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    case 'PUT':
        if (isset($permicao['compraCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $compra_id = intval($_GET["compra_id"]);
            $compra->update_Compra($compra_id);
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    case 'DELETE':
            if (isset($permicao['compraDeletar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $compra_id = intval($_GET["compra_id"]);
                $compra->delete_Compra($compra_id);
            }
        else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
