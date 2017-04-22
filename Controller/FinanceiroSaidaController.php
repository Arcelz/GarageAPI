<?php
require_once '../Model/FinanceiroSaida.php';
require_once '../Validation/ValidacaoVazio.php';

$finSaida = new FinanceiroSaida();

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {

    case 'GET':

        if (!empty($_GET["saida_id"])) {
            $finSaida_id = intval($_GET["saida_id"]);
            $finSaida->get_FinSaida($finSaida_id);

        } else {
            $finSaida->get_FinSaida();

        }
        break;
    case 'POST':

        $finSaida->insert_FinSaida();
        break;
    case 'PUT':

        $compra_id = intval($_GET["compra_id"]);
        $compra->update_Compra($compra_id);
        break;
    case 'DELETE':
        // Delete Product
        $compra_id = intval($_GET["compra_id"]);
        $compra->delete_Compra($compra_id);

        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
