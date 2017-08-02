<?php
require_once '../Model/Venda.php';
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


$venda = new Venda();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = (array)$validaToken->token();

header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':
        if (isset($permicao['vendaVisualizar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            if (!empty($_GET["venda_id"])) {
                $venda_id = intval($_GET["venda_id"]);
                $venda->get_Venda($venda_id);
            } else {
                $venda->get_Venda();
            }
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'POST':
        if (isset($permicao['vendaCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            if (empty($_POST["consulta"])) {
                $venda->insert_Venda();
            } else {
                $venda->pesquisar_valor();
            }
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'PUT':
        if (isset($permicao['vendaCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $venda_id = intval($_GET["venda_id"]);
            $venda->update_Venda($venda_id);
        } else {
            header("HTTP/1.0 203 Acesso n�o permitido");
        }
        break;
    case 'DELETE':
        if (isset($permicao['vendaDeletar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $venda_id = intval($_GET["venda_id"]);
            $fk_veiculo = intval($_GET["fk_veiculo"]);
            $venda->delete_Venda($venda_id, $fk_veiculo);
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
