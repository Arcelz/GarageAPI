<?php
// Connect to database
require_once '../Model/GrupoPermissao.php';
require_once '../Validation/ValidaToken.php';

$grupo = new GrupoPermissao();//instancia a classe de usuario para a chamada das funcoes
$validaToken = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
$permicao=$validaToken->token();
header('Access-Control-Allow-Origin: *');
        $request_method = $_SERVER["REQUEST_METHOD"];
        switch ($request_method) {
            case 'GET':
                $verificado = true;
                foreach ($permicao as $valor) {// percorre o array de permicoes
                    if ($valor == '13V') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                        if (!empty($_GET["grupo_id"])) {
                            $grupo_id = intval($_GET["grupo_id"]);
                            $grupo->get_grupos($grupo_id);
                        } else {
                            $grupo->get_grupos();
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
                    if ($valor == '13C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                        $grupo->insert_grupos();
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
                    if ($valor == '13C') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                        $grupo_id = intval($_GET["grupo_id"]);
                        $grupo->update_grupo($grupo_id);
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
                    if ($valor == '13D') {// verifica se o usuario tem permicao para acessar se tive acessa as funcoes
                        $grupo_id = intval($_GET["grupo_id"]);
                        $grupo->delete_grupo($grupo_id);
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

