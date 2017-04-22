<?php
require_once '../Model/Cliente.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

$cliente = new Cliente();

$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '2V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["cliente_id"])) {
                    $cliente_id = intval($_GET["cliente_id"]);

                    $cliente->get_Cliente($cliente_id);

                } else {
                    $cliente->get_Cliente();

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
            if ($valor == '2C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $validacao = new ValidacaoVazio();
                $returnValidacao = $validacao->verificaCamposEndereco();

                //Quando a validação esta ok retorna 1 para inserir no banco
                if ($validacao->verificaCamposEndereco() < 100) {

                    $cliente->insert_Cliente();

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
            if ($valor == '2C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $validacao = new ValidacaoVazio();

                $returnValidacao = $validacao->verificaCamposEndereco();
                echo $returnValidacao;

                if ($validacao->verificaCamposEndereco() < 100) {

                    $cliente_id = intval($_GET["cliente_id"]);
                    $cliente->update_Cliente($cliente_id);

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
            if ($valor == '2D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Delete Product
                $cliente_id = intval($_GET["cliente_id"]);
                $cliente->delete_Cliente($cliente_id);

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
