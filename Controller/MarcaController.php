<?php
require_once '../Model/Marca.php';

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

            $marca->insert();

        break;
    case 'PUT':

        $marca_id=intval($_GET["marca_id"]);
        $marca->update_Marca($marca_id);
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
