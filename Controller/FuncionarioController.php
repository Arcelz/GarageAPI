<?php
require_once '../Model/Funcionario.php';
require_once '../Validation/ValidacaoVazio.php';

$funcionario = new Funcionario();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{

    case 'GET':

        if(!empty($_GET["funcionario_id"]))
        {
            $funcionario_id=intval($_GET["funcionario_id"]);

            $funcionario->get_Funcionario($funcionario_id);

        }
        else
        {
            $funcionario->get_Funcionario();

        }
        break;
    case 'POST':

        $validacao = new ValidacaoVazio();

            $returnValidacao = $validacao->verificaCamposEndereco();

            //Quando a validação esta ok retorna 1 para inserir no banco
            if($validacao->verificaCamposEndereco() <100){

                $funcionario->insert_Funcionario();

            }
            else{

                //Aqui vai imprimir o resultado da validação
                header('Content-Type: application/json');
                echo json_encode($returnValidacao);

            }


        break;
    case 'PUT':

        $validacao = new ValidacaoVazio();

        $returnValidacao = $validacao->verificaCamposEndereco();
        echo $returnValidacao;

        if($validacao->verificaCamposEndereco() <100){

            $funcionario_id=intval($_GET["funcionario_id"]);
            $funcionario->update_Funcionario($funcionario_id);

        }
        else{

            //Aqui vai imprimir o resultado da validação
            header('Content-Type: application/json');
            echo json_encode($returnValidacao);

        }


        break;
    case 'DELETE':

        $funcionario_id=intval($_GET["funcionario_id"]);
        $funcionario->delete_Funcionario($funcionario_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
       // echo json_encode($response);
        break;
}
