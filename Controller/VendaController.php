<?php
require_once '../Model/Venda.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';
$venda = new Venda();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();

header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '24V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["venda_id"])) {
                    $venda_id = intval($_GET["venda_id"]);

                    $venda->get_Venda($venda_id);

                } else {
                    $venda->get_Venda();

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
            if ($valor == '24C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $venda->insert_Venda();
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
            if ($valor == '24D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Delete Product
                $venda_id = intval($_GET["venda_id"]);
                $venda->delete_Venda($venda_id);
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
