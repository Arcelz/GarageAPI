<?php
// Connect to database
require_once '../Model/Usuario.php';
require_once '../Validation/ValidaToken.php';

$usuario = new Usuario();//instancia a classe de usuario para a chamada das funcoes
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token

$permicao = $validaToken->token();

header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        $verificado = true;
        foreach ($permicao as $valor) {// percorre o array de permicoes
            if ($valor == '21V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                if (!empty($_GET["usuario_id"])) {
                    $usuario_id = intval($_GET["usuario_id"]);
                    $usuario->get_usuarios($usuario_id);
                } else {
                    $usuario->get_usuarios();
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
            if ($valor == '21C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $usuario->insert_usuario();
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
            if ($valor == '21C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $usuario_id = intval($_GET["usuario_id"]);
                $usuario->update_usuario($usuario_id);
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
            if ($valor == '21D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $usuario_id = intval($_GET["usuario_id"]);
                $usuario->delete_usuario($usuario_id);
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


