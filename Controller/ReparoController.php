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
            $reparo->get_Reparos($tpReparo_id);

        }
        else
        {
            $reparo->get_Reparos();

        }
        break;
    case 'POST':

            $reparo->insert();

        break;
    case 'PUT':

        $reparo_id=intval($_GET["reparo_id"]);
        $reparo->update_tpReparos($tpReparo_id);
        break;
    case 'DELETE':
        // Delete Product
        $tpReparo_id=intval($_GET["tpReparo_id"]);
        $tpReparo->delete_tpReparos($tpReparo_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
