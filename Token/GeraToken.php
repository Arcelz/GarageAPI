<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/04/2017
 * Time: 11:58
 */
require_once '../vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class GeraToken
{
    function gerar_token($permicao,$nome)
    {
        $signer = new Sha256();
        $token = (new Builder())->setIssuer('api.garage')// Configures the issuer (iss claim)
        ->setAudience('api.garage')// Configures the audience (aud claim)
        ->setId('123apigarage456', true)// Configura o id (jti claim), replicating as a header item
        ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
        ->setNotBefore(time() + 60)// Configures the time that the token can be used (nbf claim)
        ->setExpiration(time() + 3600)// Configura a data de expiração do token
        ->set('Permicao', $permicao)// Define a permicao para o sistema
        ->set('Nome', $nome['nome'])//Define o nome do usuario
        ->set('Email',$nome['email'])//Define o emails
        ->set('Avatar',$nome['avatar'])//Define o emails
        ->sign($signer, 'chave')// cria uma chave de assinatura privada
        ->getToken(); // Recupera o token
        return $token;
    }
}