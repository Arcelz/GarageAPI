<?php
require_once '../Model/Reparo.php';
require_once '../Validation/ValidaToken.php';

$reparo = new Reparo();
$usuario = new Usuario();//instancia a classe de usuario para a chamada das funcoes
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '18V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Retrive Products
                if (!empty($_GET["Reparo_id"])) {
                    $reeparo_id = intval($_GET["Reparo_id"]);
                    $reparo->get_Reparo($reparo_id);

                } else {
                    $reparo->get_Reparo();

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
            if ($valor == '18C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $reparo->insert();
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
            if ($valor == '18C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $reparo_id = intval($_GET["reparo_id"]);
                $reparo->update_Reparos($reparo_id);
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
            if ($valor == '18D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Delete Product
                $reparo_id = intval($_GET["reparo_id"]);
                $reparo->delete_Reparos($reparo_id);
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
