<?php
// Connect to database
require_once '../Validation/ValidacaoLogin.php';
require_once '../Validation/ValidaUsuario.php';
require_once '../Token/GeraToken.php';

// Allow from any origin
if(isset($_SERVER["HTTP_ORIGIN"]))
{
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}
else
{
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if($_SERVER["REQUEST_METHOD"] == "OPTIONS")
{
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'POST':
        // Insert Product
        fazer_login();
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
function fazer_login()
{
    if (isset($_POST['login']) && isset($_POST['senha'])) {
        $login = $_POST['login'];
        $senha = $_POST['senha'];
        $validaLogin = new ValidacaoLogin();
        $validaLogin = $validaLogin->verifica_login($login, $senha);
        if ($validaLogin) {
            $validaUsuario = new ValidaUsuario();
            $permicao = $validaUsuario->valida_permicao($login);
            $dados = $validaUsuario->valida_dados($login);
            $gerarToken = new GeraToken();
            $token = $gerarToken->gerar_token($permicao, $dados['nome'], $dados['email']);
            $tokenString = (string)$token; // Transforma o token em uma string
            $response = array('status_message' => 'Usuario Logado',
                'status' => 200,
                'Token' => $tokenString);
        } else {
            $response = array('status_message' => 'Senha Incorreta',
                'Codigo' => 400);
        }
    }
    else if(!isset($_POST['login'])&&!isset($_POST['senha'])){
        $response = array('status_message' => 'Por favor informe o login e a senha',
            'status' => 400);
    }
    else if(!isset($_POST['login'])){
        $response = array('status_message' => 'Por favor informe o login',
            'status' => 400);
    }
    else if(!isset($_POST['senha'])){
        $response = array('status_message' => 'Por favor informe o senha',
            'status' => 400);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}