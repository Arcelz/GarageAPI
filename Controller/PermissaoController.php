<?php
// Connect to database
require_once '../Model/Permissao.php';
require_once '../Validation/ValidaToken.php';

$permissao = new Permissao();//instancia a classe de usuario para a chamada das funcoes
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '17V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["permissao_id"])) {
                    $permissao_id = intval($_GET["permissao_id"]);
                    $permissao->get_permissoes($permissao_id);
                } else {
                    $permissao->get_permissoes();
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
            if ($valor == '17C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $permissao->insert_permissoes();
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
            if ($valor == '17C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $permissao_id = intval($_GET["permissao_id"]);
                $permissao->update_permissao($permissao_id);
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
            if ($valor == '17D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $permissao_id = intval($_GET["permissao_id"]);
                $permissao->delete_permissao($permissao_id);
                return $verificado = false;
            }
        }
        if ($verificado) {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Método não definido");
        break;
}
