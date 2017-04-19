<?php
require_once '../Model/TipoReparo.php';

$tpReparo = new TipoReparo();
$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["tpReparo_id"]))
        {
            $tpReparo_id=intval($_GET["tpReparo_id"]);
            $tpReparo->get_tpReparos($tpReparo_id);

        }
        else
        {
            $tpReparo->get_tpReparos();

        }
        break;
    case 'POST':

            $tpReparo->insert();

        break;
    case 'PUT':

        $tpReparo_id=intval($_GET["tpReparo_id"]);
        $tpReparo->update_tpReparos($tpReparo_id);
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
