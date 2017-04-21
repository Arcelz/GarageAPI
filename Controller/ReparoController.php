<?php
require_once '../Model/Reparo.php';

$reparo = new Reparo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["Reparo_id"]))
        {
            $reeparo_id=intval($_GET["Reparo_id"]);
            $reparo->get_Reparo($reparo_id);

        }
        else
        {
            $reparo->get_Reparo();

        }
        break;
    case 'POST':

            $reparo->insert();

        break;
    case 'PUT':

        $reparo_id=intval($_GET["reparo_id"]);
        $reparo->update_Reparos($reparo_id);
        break;
    case 'DELETE':
        // Delete Product
        $reparo_id=intval($_GET["reparo_id"]);
        $reparo->delete_Reparos($reparo_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
