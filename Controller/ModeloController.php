<?php
require_once '../Model/Modelo.php';

$modelo = new Modelo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["modelo_id"]))
        {
            $modelo_id=intval($_GET["modelo_id"]);
            $modelo->get_Modelo($modelo_id);

        }
        else
        {
            $modelo->get_Modelo();

        }
        break;
    case 'POST':

            $modelo->insert();

        break;
    case 'PUT':

        $modelo_id=intval($_GET["modelo_id"]);
        $modelo->update_Modelo($modelo_id);
        break;
    case 'DELETE':
        // Delete Product
        $modelo_id=intval($_GET["modelo_id"]);
        $modelo->delete_Modelo($modelo_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
