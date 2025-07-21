<?php
// =================================================================
// PASO 1: LEEMOS LAS FECHAS UNA SOLA VEZ AL PRINCIPIO
// =================================================================
$fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
$fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;

if($_SESSION["perfil"] == "Especial" || $_SESSION["perfil"] == "Vendedor"){
    echo '<script> window.location = "inicio"; </script>';
    return;
}

// =================================================================
// PASO 2: OBTENEMOS TODOS LOS DATOS PARA LA PÁGINA DE UNA SOLA VEZ
// =================================================================
$entradasResult = ControladorContabilidad::ctrSumaTotalEntradas($fechaInicial, $fechaFinal);
$deudaResult = ControladorVentas::ctrSumaTotalDeuda($fechaInicial, $fechaFinal);
$vendidoResult = ControladorVentas::ctrSumaTotalVentasGeneral($fechaInicial, $fechaFinal);
$totalGastosResult = ControladorContabilidad::ctrSumaTotalGastos($fechaInicial, $fechaFinal);
$totalesEntradasPorMedioPago = ControladorContabilidad::ctrSumaEntradasPorMedioPago($fechaInicial, $fechaFinal);
$gastosPorMedioPago = ControladorContabilidad::ctrSumaGastosPorMedioPago($fechaInicial, $fechaFinal);
$ventasPorVendedor = ControladorVentas::ctrSumaVentasPorVendedor($fechaInicial, $fechaFinal);

$totalEntradas = $entradasResult["total"] ?? 0;
$totalDeuda = $deudaResult["total_deuda"] ?? 0;
$totalVendido = $vendidoResult["total_ventas"] ?? 0;
$totalGastos = $totalGastosResult["total"] ?? 0;

$entradasEfectivo = ControladorContabilidad::ctrSumaTotalPorTipoYMedio("Entrada", "Efectivo", $fechaInicial, $fechaFinal);
$gastosEfectivo = ControladorContabilidad::ctrSumaTotalPorTipoYMedio("Gasto", "Efectivo", $fechaInicial, $fechaFinal);
$arqueoDeEfectivo = ($entradasEfectivo["total"] ?? 0) - ($gastosEfectivo["total"] ?? 0);
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Reportes de ventas</h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Reportes de ventas</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <div class="input-group">
                    <button type="button" class="btn btn-default" id="daterange-btn2">
                        <span>
                            <i class="fa fa-calendar"></i> 
                            <?php
                                if(isset($_GET["fechaInicial"])){
                                    echo $_GET["fechaInicial"]." - ".$_GET["fechaFinal"];
                                } else {
                                    echo 'Rango de fecha';
                                }
                            ?>
                        </span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </div>
                <div class="box-tools pull-right">
                    <?php
                        $urlDescarga = "vistas/modulos/descargar-reporte.php?reporte=reporte";
                        if (isset($_GET["fechaInicial"])) {
                            $urlDescarga .= "&fechaInicial=" . $_GET["fechaInicial"] . "&fechaFinal=" . $_GET["fechaFinal"];
                        }
                    ?>
                    <a href="<?= $urlDescarga ?>">
                        <button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <h2>Resumen General</h2>
                    <hr>
                    <div class="col-lg-4 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>$<?= number_format($totalEntradas, 0) ?></h3>
                                <p>Total Entradas (Dinero Ingresado)</p>
                            </div>
                            <div class="icon"><i class="ion ion-arrow-up-a"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>$<?= number_format($totalDeuda, 0) ?></h3>
                                <p>Total por Cobrar (Deuda)</p>
                            </div>
                            <div class="icon"><i class="ion ion-alert-circled"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>$<?= number_format($totalVendido, 0) ?></h3>
                                <p>Total Vendido (Generado en Ventas)</p>
                            </div>
                            <div class="icon"><i class="ion ion-social-usd"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <h2>Total de Entradas por Medio de Pago</h2>
                    <hr>
                    <?php
                        $colores = ["bg-aqua", "bg-green", "bg-yellow", "bg-red", "bg-blue", "bg-purple"];
                        $colorIndex = 0;
                        foreach ($totalesEntradasPorMedioPago as $key => $value) {
                            if($value["total_entradas"] > 0){
                                echo '<div class="col-lg-3 col-xs-6"><div class="small-box ' . $colores[$colorIndex] . '"><div class="inner"><h3>$' . number_format($value["total_entradas"], 0) . '</h3><p>' . $value["medio_pago"] . '</p></div><div class="icon"><i class="ion ion-social-usd"></i></div></div></div>';
                                $colorIndex = ($colorIndex + 1) % count($colores);
                            }
                        }
                    ?>
                </div>

                <div class="row">
                    <h2>Arqueo y Resumen de Gastos</h2>
                    <hr>
                    <div class="col-lg-6 col-xs-12">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>$<?= number_format($arqueoDeEfectivo, 0) ?></h3>
                                <p>Arqueo de Efectivo (Entradas Efectivo - Gastos Efectivo)</p>
                            </div>
                            <div class="icon"><i class="ion ion-calculator"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xs-12">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>$<?= number_format($totalGastos, 0) ?></h3>
                                <p>Total de Gastos (Todos los medios de pago)</p>
                            </div>
                            <div class="icon"><i class="ion ion-arrow-graph-down-right"></i></div>
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="col-xs-12">
                        <?php include "reportes/grafico-ventas.php"; ?>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <?php include "reportes/productos-mas-vendidos.php"; ?>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <?php include "reportes/vendedores.php"; ?>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <?php include "reportes/compradores.php"; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script>
$(function(){
    // Evento para el botón de rango de fechas
    // CORREGIDO: Usamos el ID correcto '#daterange-btn2'
    $('#daterange-btn2').on('apply.daterangepicker', function(ev, picker) {
        var fechaInicial = picker.startDate.format('YYYY-MM-DD');
        var fechaFinal = picker.endDate.format('YYYY-MM-DD');
        
        // Recargamos la página con las nuevas fechas en la URL
        window.location = "index.php?ruta=reportes&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;
    });
});
</script>