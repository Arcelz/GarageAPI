<?php

Class ValidacaoVazio{

function verificaNome()
    {

        parse_str(file_get_contents('php://input'), $post_vars);
        $nome = $post_vars["nome"];
        $response = array();

        if($post_vars !=null){
            if(empty($nome) || !preg_match("/^[a-zA-Záàâãéèêíïóôõöúçñ ]+$/",$nome)){
                //return  100;//Preencha some com letras
                //echo $post_vars;
                $response["code"]="400";
                $response["type"]="QueryStringException";
                $response["message"]="Preencha some com letras";
                header("HTTP/1.0 400.005");
                return $response;
            }else{
                return 1;//tudo ok
            }


        }
        else{
             $response["code"]="400";
             $response["type"]="QueryStringException";
            $response["message"]="Campo não pode ser vazio";
            header("HTTP/1.0 400.005");
            return $response;
        }

    }


    function verificaCamposEndereco(){
        parse_str(file_get_contents('php://input'), $post_vars);

            $response = array();
            if ($post_vars !=null) {
                $nome = $post_vars["nome"];
                $cpf = $post_vars["cpf"];
                $email = $post_vars["email"];
                $contato = $post_vars["contato"];
                $contato1 = $post_vars["contato1"];
                $cep = $post_vars["cep"];

               // echo $email;

                 if(empty($nome) || !preg_match("/^[a-zA-Záàâãéèêíïóôõöúçñ ]+$/",$nome)){
                    //return  100;//Preencha some com letras
                    //echo $post_vars;
                    $response["code"]="400";
                    $response["nome"]="Preencha some com letras";
                    header("HTTP/1.0 400");

                } else if(empty($cpf) || !preg_match("/^(([0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2})|([0-9]{11}))$/ ", $cpf)){
                    //return 101;//CPF invalido
                    $response["code"]="400";
                    $response["cpf"]="CPF invalido";

                }else if(empty($email) || !preg_match('/^([\w\-]+\.)*[\w\- ]+@([\w\- ]+\.)+([\w\-]{2,3})$/', $email)){
                    $response["code"]="400";
                    $response["email"]="EMAIL invalido";

                } else if (empty($contato) || !preg_match('/^\(?\d{2}\){0,1} ?9?\d{4}\-?\d{4}$/',$contato)){
                    $response["code"]="103";
                    $response["contato"]="Telefone invalido";
                } else if(empty($contato1) || !preg_match('/^\(?\d{2}\){0,1} ?9?\d{4}\-?\d{4}$/',$contato1)){
                    $response["code"]="103";
                    $response["contato1"]="Celular invalido";

                }else if(empty($cep) || !preg_match('/\d{5}-\d{3}/',$cep)){
                    $response["code"]="104";
                    $response["cep"]="CEP invalido";
                }
                else{

                    return 1;//tudo ok
                }

                return $response;

        }else{
                $response["code"]="400";
                $response["message"]="Preencha os campos obrigatorios";
                return $response;

        }


    }
}