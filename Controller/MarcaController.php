<?php
require_once '../Model/Marca.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';
$marca = new Marca();

$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao=$validaToken->token();
header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '14C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Retrive Products
                if (!empty($_GET["marca_id"])) {
                    $marca_id = intval($_GET["marca_id"]);
                    $marca->get_Marca($marca_id);

                } else {
                    $marca->get_Marca();

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
            if ($valor == '14C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $validacaoNome = new ValidacaoVazio();
                $validacaoFk = new ValidacaoVazio();

                $returnNome = $validacaoNome->verificaNome();
                $returnFk = $validacaoFk->verificaFk();

                $response = array();
                if ($returnNome < 100 && $returnFk < 100) {
                    $marca->insert();

                } else {
                    $response[] = $returnNome;
                    $response[] = $returnFk;
                    header('Content-Type: application/json');
                    echo json_encode($response);

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
            if ($valor == '14C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $validacaoNome = new ValidacaoVazio();
                $validacaoFk = new ValidacaoVazio();

                $returnNome = $validacaoNome->verificaNome();
                $returnFk = $validacaoFk->verificaFk();

                $response = array();
                if ($returnNome < 100 && $returnFk < 100) {
                    $marca_id = intval($_GET["marca_id"]);
                    $marca->update_Marcas($marca_id);

                } else {
                    $response[] = $returnNome;
                    $response[] = $returnFk;
                    header('Content-Type: application/json');
                    echo json_encode($response);

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
            if ($valor == '14D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $marca_id = intval($_GET["marca_id"]);
                $marca->delete_Marca($marca_id);
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
