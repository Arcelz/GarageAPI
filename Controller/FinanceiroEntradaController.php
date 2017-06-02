<?php
require_once '../Model/FinanceiroEntrada.php';
require_once '../Validation/ValidacaoVazio.php';

// Allow from any origin
if(isset($_SERVER["HTTP_ORIGIN"]))
{
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}
else
{
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if($_SERVER["REQUEST_METHOD"] == "OPTIONS")
{
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}



$finSaida = new FinanceiroSaida();

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':

        if (!empty($_GET["entrada_id"])) {
            $finSaida_id = intval($_GET["entrada_id"]);
            $finSaida->get_FinSaida($finSaida_id);

        } else {
            $finSaida->get_FinSaida();

        }
        break;
       case 'PUT':

        $compra_id = intval($_GET["entrada_id"]);
        $finSaida ->update_Compra($compra_id);
        break;

    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
