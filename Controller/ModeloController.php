<?php
require_once '../Model/Modelo.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

$modelo = new Modelo();
$usuario = new Usuario();//instancia a classe de usuario para a chamada das funcoes
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '15V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["modelo_id"])) {
                    $modelo_id = intval($_GET["modelo_id"]);
                    $modelo->get_Modelo($modelo_id);

                } else {
                    $modelo->get_Modelo();

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
            if ($valor == '15C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();
                $returnValidacao = $validacao->verificaNome();

                if ($returnValidacao < 100) {
                    $modelo->insert();
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
            if ($valor == '15C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $modelo_id = intval($_GET["modelo_id"]);
                $modelo->update_Modelo($modelo_id);
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
            if ($valor == '15D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $modelo_id = intval($_GET["modelo_id"]);
                $modelo->delete_Modelo($modelo_id);
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
