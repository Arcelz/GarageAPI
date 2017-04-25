<?php
require_once '../Model/Veiculo.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

$veiculo = new Veiculo();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();

header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '23V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["veiculo_id"])) {
                    $veiculo_id = intval($_GET["veiculo_id"]);
                    $veiculo->get_Veiculo($veiculo_id);

                } else {
                    $veiculo->get_Veiculo();

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
            if ($valor == '23C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $veiculo->insert_Veiculo();
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
            if ($valor == '23C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $veiculo_id = intval($_GET["veiculo_id"]);
                $veiculo->update_Veiculo($veiculo_id);
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
            if ($valor == '23D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $veiculo_id = intval($_GET["veiculo_id"]);
                $veiculo->delete_Veiculo($veiculo_id);

                return $verificado = false;
            }
        }
        if ($verificado) {
            header("HTTP/1.0 203 Acesso não permitido");
        }


        break;
    default:

        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode($response);
        break;
}
