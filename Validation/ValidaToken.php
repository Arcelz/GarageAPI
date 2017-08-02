<?php
require_once '../vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class ValidaToken
{
    function token_vazio()
    {
        if (isset(apache_request_headers()["Authorization"])) {// Pega o token do cabeçalho) {//verifica se o cabeçãlho com a authorization esta vazio
            return true;
        } else {
            return false;
        }
    }

    function recebe_token()
    {
        return apache_request_headers()["Authorization"];// Pega o token do cabeçalho
    }

    function valida_token($token)
    {
        try {
            $parser = new Parser();
            $oToken = $parser->parse($token);
            $signer = new Sha256();//define a assinatura da chave
            $expirado = $oToken->isExpired();
            $tokenValido = $oToken->verify($signer, 'chave');
            if ($expirado == false && $tokenValido == true) {
                return true;// retorna true quando o token estiver valido e com sua validade
            } else {
                return false; // retorna false se o token nao for valido ou a validade estiver expirada
            }
        } catch (Exception $e) {
            return false;
        }
    }

    function verifica_permicao($token)
    {
        $parser = new Parser();
        $oToken = $parser->parse($token);
        $permicao = $oToken->getClaim('Permicao');
        return $permicao;

    }

    function token()
    {
        if (ValidaToken::token_vazio()) {//verifica se o cabeçãlho com a authorization esta vazio
            $token = ValidaToken::recebe_token();
            $tokenValido = ValidaToken::valida_token($token);//Verifica se token e valido
            if ($tokenValido) {
                $permicao = ValidaToken::verifica_permicao($token);// recebe um array de permicoes
                return $permicao;
            } else {
                header('HTTP/1.0 400 Token Invalido');
                die();
            }

        }
        else{
            header('HTTP/1.0 401 Não Autorizado');
            die();
        }
    }

    function usuario(){
        if (ValidaToken::token_vazio()) {//verifica se o cabeçãlho com a authorization esta vazio
            $token = ValidaToken::recebe_token();
            $tokenValido = ValidaToken::valida_token($token);//Verifica se token e valido
            if ($tokenValido) {
                $usuario = ValidaToken::busca_usuario($token);// recebe um array de permicoes
                return $usuario;
            } else {
                header('HTTP/1.0 400 Token Invalido');
                die();
            }

        }
        else{
            header('HTTP/1.0 401 Não Autorizado');
            die();
        }


    }

    function busca_usuario($token)
    {
        $parser = new Parser();
        $oToken = $parser->parse($token);
        $usuario = $oToken->getClaim('Nome');
        return $usuario;

    }

    function busca_banco()
    {
        $parser = new Parser();
        $token = ValidaToken::recebe_token();
        $oToken = $parser->parse($token);
        $banco = $oToken->getClaim('Permicao');
        return $banco;
    }

}