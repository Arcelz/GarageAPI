<?php
require_once '../Model/Relatorio.php';
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



$relatorio = new Relatorio();
$request_method = $_SERVER["REQUEST_METHOD"];
if ($request_method == 'GET') {
	   $get = $_GET["relatorio_id"];
        if (!empty($_GET["relatorio_id"])) {
            $relatorio_id = intval($_GET["relatorio_id"]);
            $relatorio->get_Relatorio($relatorio_id);
        } else {
            $relatorio->get_Relatorio();

        }
}
else{
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
}

