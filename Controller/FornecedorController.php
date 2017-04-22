<?php
require_once '../Model/Fornecedor.php';
require_once '../Validation/ValidacaoVazio.php';

$fornecedor = new Fornecedor();

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '8V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["fornecedor_id"])) {
                    $fornecedor_id = intval($_GET["fornecedor_id"]);

                    $fornecedor->get_Fornecedor($fornecedor_id);

                } else {
                    $fornecedor->get_Fornecedor();

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
            if ($valor == '8C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();

                $returnValidacao = $validacao->verificaCamposEndereco();

                //Quando a validação esta ok retorna 1 para inserir no banco
                if ($validacao->verificaCamposEndereco() < 100) {

                    $fornecedor->insert_Fornecedor();

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
            if ($valor == '8C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();

                $returnValidacao = $validacao->verificaCamposEndereco();
                echo $returnValidacao;

                if ($validacao->verificaCamposEndereco() < 100) {

                    $fornecedor_id = intval($_GET["fornecedor_id"]);

                    $fornecedor->update_Fornecedor($fornecedor_id);

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
            if ($valor == '8D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                // Delete Product
                $fornecedor_id = intval($_GET["fornecedor_id"]);
                $fornecedor->delete_Fornecedor($fornecedor_id);

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
