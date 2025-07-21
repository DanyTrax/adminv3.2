<?php
session_start();
// --- REQUIRES ---
require_once "../../controladores/ventas.controlador.php";
require_once "../../modelos/ventas.modelo.php";
require_once "../../controladores/clientes.controlador.php";
require_once "../../modelos/clientes.modelo.php";
require_once "../../controladores/usuarios.controlador.php";
require_once "../../modelos/usuarios.modelo.php";
// La lógica de contabilidad ya no es necesaria si todo se calcula desde ventas
// require_once "../../controladores/contabilidad.controlador.php";
// require_once "../../modelos/contabilidad.modelo.php";

// --- HEADERS ---
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

// --- CORRECCIÓN DE LA LÍNEA CON ERROR ---
$fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
$fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;


// =================================================================
// PASO 1: OBTENER LA LISTA DETALLADA (NUESTRA ÚNICA FUENTE DE VERDAD)
// =================================================================
$ventasDetalladas = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);

// =================================================================
// PASO 2: CALCULAR TODOS LOS RESÚMENES A PARTIR DE LA LISTA DETALLADA
// =================================================================
$totalVendido = 0;
$totalEntradas = 0;
$totalDeuda = 0;
$totalGastos = 0; // Se mantiene separado si viene de otra tabla
$entradasPorMedioPago = [];

if(!empty($ventasDetalladas)){
    foreach ($ventasDetalladas as $venta) {
        $listaProductos = json_decode($venta["productos"], true);
        if (is_array($listaProductos)) {
            foreach ($listaProductos as $producto) {
                // Sumamos al Total Vendido
                $totalVendido += $producto['total'];

                // Asumimos que si un medio de pago no es 'Credito', es una entrada. Ajusta esta lógica si es necesario.
                if ($venta['medio_pago'] != 'Credito') { 
                    $totalEntradas += $producto['total'];

                    // Sumamos al desglose por medio de pago
                    if (!isset($entradasPorMedioPago[$venta['medio_pago']])) {
                        $entradasPorMedioPago[$venta['medio_pago']] = 0;
                    }
                    $entradasPorMedioPago[$venta['medio_pago']] += $producto['total'];

                } else {
                    $totalDeuda += $producto['total'];
                }
            }
        }
    }
}

// =================================================================
// PASO 3: CONSTRUIR EL EXCEL CON LOS DATOS CALCULADOS
// =================================================================
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
        <td>" . number_format($totalEntradas, 0, '', '') . "</td>
        <td>" . number_format($totalDeuda, 0, '', '') . "</td>
        <td>" . number_format($totalVendido, 0, '', '') . "</td>
        <td>" . number_format($totalGastos, 0, '', '') . "</td>
    </tr>
</table><br><br>

<table border='1'>
    <tr><td colspan='2' style='font-weight:bold; background-color:#00a65a; color:white; text-align:center;'>TOTAL DE ENTRADAS POR MEDIO DE PAGO</td></tr>
    <tr><td style='font-weight:bold;'>Medio de Pago</td><td style='font-weight:bold;'>Total Entradas</td></tr>
");
if (!empty($entradasPorMedioPago)) { 
    foreach ($entradasPorMedioPago as $medio => $total) { 
        echo "<tr><td>" . utf8_decode($medio) . "</td><td>" . number_format($total, 0, '', '') . "</td></tr>"; 
    } 
}
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
        if (is_array($listaProductos)) {
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
}
echo "</table><br><br>";
?>