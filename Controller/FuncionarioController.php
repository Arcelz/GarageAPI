<?php
require_once '../Model/Funcionario.php';
require_once '../Validation/ValidacaoVazio.php';
require_once '../Validation/ValidaToken.php';

// Allow from any origin
if (isset($_SERVER["HTTP_ORIGIN"])) {
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}


$funcionario = new Funcionario();
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
$permicao = (array)$validaToken->token();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        if (isset($permicao['funcionarioVisualizar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            if (!empty($_GET["funcionario_id"])) {
                $funcionario_id = intval($_GET["funcionario_id"]);
                $funcionario->get_Funcionario($funcionario_id);
            } else {
                $funcionario->get_Funcionario();
            }
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'POST':
        if (isset($permicao['funcionarioCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $validacao = new ValidacaoVazio();
            $returnValidacao = $validacao->verificaCamposEndereco();
            //Quando a validação esta ok retorna 1 para inserir no banco
            if ($validacao->verificaCamposEndereco() < 100) {
                $funcionario->insert_Funcionario();
            } else {
                //Aqui vai imprimir o resultado da validação
                header("HTTP/1.0 400 ");
                header('Content-Type: application/json');
                echo json_encode($returnValidacao);
            }
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'PUT':
        if (isset($permicao['funcionarioCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $validacao = new ValidacaoVazio();
            $returnValidacao = $validacao->verificaCamposEndereco();
            if ($validacao->verificaCamposEndereco() < 100) {
                $funcionario_id = intval($_GET["funcionario_id"]);
                $funcionario->update_Funcionario($funcionario_id);
            } else {
                //Aqui vai imprimir o resultado da validação
                header("HTTP/1.0 400 ");
                header('Content-Type: application/json');
                echo json_encode($returnValidacao);

            }
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'DELETE':
        if (isset($permicao['funcionarioDeletar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $funcionario_id = intval($_GET["funcionario_id"]);
            $funcionario->delete_Funcionario($funcionario_id);
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode($response);
        break;
}
