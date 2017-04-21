<?php
require_once '../Model/Marca.php';
require_once '../Validation/ValidacaoVazio.php';

$marca = new Marca();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["marca_id"]))
        {
            $marca_id=intval($_GET["marca_id"]);
            $marca->get_Marca($marca_id);

        }
        else
        {
            $marca->get_Marca();

        }
        break;
    case 'POST':

            $validacaoNome = new ValidacaoVazio();
            $validacaoFk = new ValidacaoVazio();

           $returnNome = $validacaoNome->verificaNome();
           $returnFk =  $validacaoFk->verificaFk();

           $response = array();
            if($returnNome < 100 && $returnFk <100){
                $marca->insert();

            }else{
                $response[] = $returnNome;
                $response[] = $returnFk;
                header('Content-Type: application/json');
                echo json_encode($response);

            }



        break;
    case 'PUT':

        $validacaoNome = new ValidacaoVazio();
        $validacaoFk = new ValidacaoVazio();

        $returnNome = $validacaoNome->verificaNome();
        $returnFk =  $validacaoFk->verificaFk();

        $response = array();
        if($returnNome < 100 && $returnFk <100){
            $marca_id=intval($_GET["marca_id"]);
            $marca->update_Marcas($marca_id);

        }else{
            $response[] = $returnNome;
            $response[] = $returnFk;
            header('Content-Type: application/json');
            echo json_encode($response);

        }


        break;
    case 'DELETE':
        // Delete Product
        $marca_id=intval($_GET["marca_id"]);
        $marca->delete_Marca($marca_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
