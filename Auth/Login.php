<?php
// Connect to database
require_once '../Validation/ValidacaoLogin.php';
require_once '../Validation/ValidaUsuario.php';
require_once '../Token/GeraToken.php';

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
            $response = array('Resposta' => 'Usuario Logado',
                'Codigo' => '0',
                'Token' => $tokenString);
        } else {
            $response = array('Resposta' => 'Senha Incorreta',
                'Codigo' => '1');
        }
    }
    else if(!isset($_POST['login'])&&!isset($_POST['senha'])){
        $response = array('Resposta' => 'Por favor informe o login e a senha',
            'Codigo' => '1');
    }
    else if(!isset($_POST['login'])){
        $response = array('Resposta' => 'Por favor informe o login',
            'Codigo' => '1');
    }
    else if(!isset($_POST['senha'])){
        $response = array('Resposta' => 'Por favor informe o senha',
            'Codigo' => '1');
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}