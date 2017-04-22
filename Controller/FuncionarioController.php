<?php
require_once '../Model/Funcionario.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';
$funcionario = new Funcionario();

$usuario = new Usuario();//instancia a classe de usuario para a chamada das funcoes
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':

        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '10V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["funcionario_id"])) {
                    $funcionario_id = intval($_GET["funcionario_id"]);

                    $funcionario->get_Funcionario($funcionario_id);

                } else {
                    $funcionario->get_Funcionario();

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
            if ($valor == '10C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes

                $validacao = new ValidacaoVazio();

                $returnValidacao = $validacao->verificaCamposEndereco();

                //Quando a validação esta ok retorna 1 para inserir no banco
                if ($validacao->verificaCamposEndereco() < 100) {

                    $funcionario->insert_Funcionario();

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
            if ($valor == '10C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $validacao = new ValidacaoVazio();

                $returnValidacao = $validacao->verificaCamposEndereco();
                echo $returnValidacao;

                if ($validacao->verificaCamposEndereco() < 100) {

                    $funcionario_id = intval($_GET["funcionario_id"]);
                    $funcionario->update_Funcionario($funcionario_id);

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
            if ($valor == '10D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $funcionario_id = intval($_GET["funcionario_id"]);
                $funcionario->delete_Funcionario($funcionario_id);

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
        // echo json_encode($response);
        break;
}
