<?php
require_once '../Model/Cargo.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

// Allow from any origin
if(isset($_SERVER["HTTP_ORIGIN"]))
{
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}
else
{
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if($_SERVER["REQUEST_METHOD"] == "OPTIONS")
{
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}


$cargo = new Cargo();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();
header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
	
        $verificacao = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '1V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["cargo_id"])) {
                    $cargo_id = intval($_GET["cargo_id"]);
                    $cargo->get_cargo($cargo_id);

                } else {
                    $cargo->get_cargo();

                }
                return $verificacao = false;

            }
        }
        if ($verificacao) {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    case 'POST':
        $verificacao = true;
        foreach ($permicao as $valor) {
            if ($valor == '1C') {

                $validacao = new ValidacaoVazio();
                $returnValidacao = $validacao->verificaNome();
                if ($returnValidacao < 100) {
                    $cargo->insert();
                } else {
                    header('Content-Type: application/json');
                    echo json_encode($returnValidacao);
                }
                return $verificacao = false;

            }
        }
        if ($verificacao) {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    case 'PUT':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '1C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $validacao = new ValidacaoVazio();
                $returnValidacao = $validacao->verificaNome();

                if ($returnValidacao < 100) {
                    $cargo_id = intval($_GET["cargo_id"]);
                    $cargo->update_cargo($cargo_id);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode($returnValidacao);
                }

                return $verificado = false;
            }
        }
        if ($verificado) {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'DELETE':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '1D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Delete Product
                $cargo_id = intval($_GET["cargo_id"]);
                $cargo->delete_cargo($cargo_id);

                return $verificado = false;
            }
        }
        if ($verificado) {
            header("HTTP/1.0 203 Acesso não permitido");
        }

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
