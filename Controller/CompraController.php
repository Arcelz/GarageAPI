<?php
require_once '../Model/Compra.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';
$compra = new Compra();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();

header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '4V
            
            '
            ) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["compra_id"])) {
                    $compra_id = intval($_GET["compra_id"]);
                    $compra->get_Comra($compra_id);

                } else {
                    $compra->get_Comra();

                }
                return $verificado = false;
            }
        }
        if ($verificado) {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    case 'POST':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '4C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $compra->insert_Compra();
                return $verificado = false;
            }
        }
        if ($verificado) {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    case 'PUT':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '4C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $compra_id = intval($_GET["compra_id"]);
                $compra->update_Compra($compra_id);
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
            if ($valor == '4D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $compra_id = intval($_GET["compra_id"]);
                $compra->delete_Compra($compra_id);
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
