<?php
session_start();
require_once "../../controladores/ventas.controlador.php";
require_once "../../modelos/ventas.modelo.php";
require_once "../../controladores/clientes.controlador.php";
require_once "../../modelos/clientes.modelo.php";
require_once "../../controladores/usuarios.controlador.php";
require_once "../../modelos/usuarios.modelo.php";
require_once "../../controladores/contabilidad.controlador.php";
require_once "../../modelos/contabilidad.modelo.php";

// Se construye el nombre del archivo dinámicamente
$nombreArchivo = 'reporte-detallado';
if (isset($_GET["fechaInicial"]) && !empty($_GET["fechaInicial"])) {
    $nombreArchivo .= '_' . $_GET["fechaInicial"] . '_a_' . $_GET["fechaFinal"];
}
$nombreArchivo .= '.xls';
header('Expires: 0');
header('Cache-control: private');
header("Content-type: application/vnd.ms-excel; charset=utf-8");
header("Cache-Control: cache, must-revalidate"); 
header('Content-Description: File Transfer');
header("Pragma: public"); 
header('Content-Disposition:; filename="' . $nombreArchivo . '"');
header("Content-Transfer-Encoding: binary");

$fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
$fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;

// OBTENCIÓN DE DATOS
$totalEntradas = ControladorContabilidad::ctrSumaTotalEntradas($fechaInicial, $fechaFinal);
$totalDeuda = ControladorVentas::ctrSumaTotalDeuda($fechaInicial, $fechaFinal);
$totalVendido = ControladorVentas::ctrSumaTotalVentasGeneral($fechaInicial, $fechaFinal);
$totalGastos = ControladorContabilidad::ctrSumaTotalGastos($fechaInicial, $fechaFinal);
$entradasPorMedioPago = ControladorContabilidad::ctrSumaEntradasPorMedioPago($fechaInicial, $fechaFinal);
$ventasDetalladas = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);
$listaGastos = ControladorContabilidad::filterBy($fechaInicial, $fechaFinal, null, 'Gasto');

// CONSTRUCCIÓN DEL EXCEL
echo utf8_decode("
<table border='1'>
    <tr><td colspan='4' style='font-weight:bold; background-color:#3c8dbc; color:white; text-align:center;'>RESUMEN FINANCIERO</td></tr>
    <tr>
        <td style='font-weight:bold;'>Total Entradas</td>
        <td style='font-weight:bold;'>Total por Cobrar (Deuda)</td>
        <td style='font-weight:bold;'>Total Vendido</td>
        <td style='font-weight:bold;'>Total Gastos</td>
    </tr>
    <tr>
        <td>" . number_format($totalEntradas["total"] ?? 0, 0, '', '') . "</td>
        <td>" . number_format($totalDeuda["total_deuda"] ?? 0, 0, '', '') . "</td>
        <td>" . number_format($totalVendido["total_ventas"] ?? 0, 0, '', '') . "</td>
        <td>" . number_format($totalGastos["total"] ?? 0, 0, '', '') . "</td>
    </tr>
</table><br><br>

<table border='1'>
    <tr><td colspan='2' style='font-weight:bold; background-color:#00a65a; color:white; text-align:center;'>TOTAL DE ENTRADAS POR MEDIO DE PAGO</td></tr>
    <tr><td style='font-weight:bold;'>Medio de Pago</td><td style='font-weight:bold;'>Total Entradas</td></tr>
");
if (!empty($entradasPorMedioPago)) { foreach ($entradasPorMedioPago as $item) { echo "<tr><td>" . $item['medio_pago'] . "</td><td>" . number_format($item['total_entradas'], 0, '', '') . "</td></tr>"; } }
echo "</table><br><br>";

echo utf8_decode("
<table border='1'>
    <tr><td colspan='8' style='font-weight:bold; background-color:#f39c12; color:white; text-align:center;'>VENTAS DETALLADAS POR PRODUCTO</td></tr>
    <tr><th>FECHA</th><th>FACTURA</th><th>CLIENTE</th><th>VENDEDOR</th><th>DESCRIPCIÓN</th><th>CANTIDAD</th><th>TOTAL</th><th>MEDIO DE PAGO</th></tr>
");
if(!empty($ventasDetalladas)){
    foreach ($ventasDetalladas as $venta) {
        $cliente = ControladorClientes::ctrMostrarClientes("id", $venta["id_cliente"]);
        $vendedor = ControladorUsuarios::ctrMostrarUsuarios("id", $venta["id_vendedor"]);
        $listaProductos = json_decode($venta["productos"], true);
        foreach ($listaProductos as $producto) {
            echo utf8_decode("<tr>
                <td>" . date('Y-m-d', strtotime($venta['fecha_venta'])) . "</td>
                <td>" . $venta['codigo'] . "</td>
                <td>" . $cliente['nombre'] . "</td>
                <td>" . $vendedor['nombre'] . "</td>
                <td>" . $producto['descripcion'] . "</td>
                <td>" . $producto['cantidad'] . "</td>
                <td>" . number_format($producto['total'], 0, '', '') . "</td>
                <td>" . $venta['medio_pago'] . "</td>
            </tr>");
        }
    }
}
echo "</table><br><br>";

echo utf8_decode("
<table border='1'>
    <tr><td colspan='5' style='font-weight:bold; background-color:#dd4b39; color:white; text-align:center;'>DESGLOSE DE GASTOS</td></tr>
    <tr><th>Fecha</th><th>Vendedor</th><th>Detalle</th><th>Medio de Pago</th><th>Valor</th></tr>
");
if(!empty($listaGastos)){
    foreach ($listaGastos as $gasto) {
        $vendedorGasto = ControladorUsuarios::ctrMostrarUsuarios("id", $gasto["id_vendedor"]);
        echo utf8_decode("<tr>
            <td>" . date('Y-m-d H:i:s', strtotime($gasto['fecha'])) . "</td>
            <td>" . $vendedorGasto['nombre'] . "</td>
            <td>" . $gasto['detalle'] . "</td>
            <td>" . $gasto['medio_pago'] . "</td>
            <td>" . number_format($gasto['valor'], 0, '', '') . "</td>
        </tr>");
    }
}
echo "</table>";
?>