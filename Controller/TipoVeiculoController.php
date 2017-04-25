<?php
require_once '../Model/TipoVeiculo.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

$tpVeiculo = new TipoVeiculo();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();

header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '20V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Retrive Products
                if (!empty($_GET["tpVeiculo_id"])) {
                    $tpVeiculo_id = intval($_GET["tpVeiculo_id"]);
                    $tpVeiculo->get_tpVeiculo($tpVeiculo_id);

                } else {
                    $tpVeiculo->get_tpVeiculo();

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
            if ($valor == '20C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();

                $returnValidacao = $validacao->verificaNome();
                if ($returnValidacao < 100) {
                    $tpVeiculo->insert_tpVeiculo();
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
    case 'PUT':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '20C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();

                $returnValidacao = $validacao->verificaNome();
                if ($returnValidacao < 100) {
                    $tpVeiculo_id = intval($_GET["tpVeiculo_id"]);
                    $tpVeiculo->update_tpVeiculo($tpVeiculo_id);
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
            if ($valor == '20D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $tpVeiculo_id = intval($_GET["tpVeiculo_id"]);
                $tpVeiculo->delete_tpVeiculo($tpVeiculo_id);

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
        echo json_encode($response);
        break;
}
