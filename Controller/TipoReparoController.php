<?php
require_once '../Model/TipoReparo.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

$tpReparo = new TipoReparo();


$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
$permicao=$validaToken->token();
header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '19V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Retrive Products
                if (!empty($_GET["tpReparo_id"])) {
                    $tpReparo_id = intval($_GET["tpReparo_id"]);
                    $tpReparo->get_tpReparo($tpReparo_id);

                } else {
                    $tpReparo->get_tpReparo();

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
            if ($valor == '19C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();
                $returnValidacao = $validacao->verificaNome();
                if ($returnValidacao < 100) {
                    $tpReparo->insert();
                } else {

                    //Aqui vai imprimir o resultado da validação
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
            if ($valor == '19C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();
                $returnValidacao = $validacao->verificaNome();

                if ($returnValidacao < 100) {
                    $tpReparo_id = intval($_GET["tpReparo_id"]);
                    $tpReparo->update_tpReparo($tpReparo_id);
                } else {

                    //Aqui vai imprimir o resultado da validação
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
            if ($valor == '19D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Delete Product
                $tpReparo_id = intval($_GET["tpReparo_id"]);
                $tpReparo->delete_tpReparo($tpReparo_id);
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
