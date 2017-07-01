<?php
// Connect to database
require_once '../Model/GrupoPermissao.php';
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


$grupo = new GrupoPermissao();//instancia a classe de usuario para a chamada das funcoes
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
$permicao = $validaToken->token();
header('Access-Control-Allow-Origin: *');
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        if (isset($permicao['permissaoVisualizar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            if (!empty($_GET["grupo_id"])) {
                $grupo_id = intval($_GET["grupo_id"]);
                $grupo->get_grupos($grupo_id,$permicao['nomeBanco']);
            } else {
                $grupo->get_grupos(0,$permicao['nomeBanco']);
            }
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'POST':
        if (isset($permicao['permissaoCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $grupo->insert_grupos();
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'PUT':
        if (isset($permicao['permissaoCriar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
            $grupo_id = intval($_GET["grupo_id"]);
            $grupo->update_grupo($grupo_id);
        } else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    case 'DELETE':
            if (isset($permicao['permissaoDeletar'])) {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                $grupo_id = intval($_GET["grupo_id"]);
                $grupo->delete_grupo($grupo_id);
                return $verificado = false;
        }
        else {
            header("HTTP/1.0 203 Acesso não permitido");
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Método não definido");
        break;
}

