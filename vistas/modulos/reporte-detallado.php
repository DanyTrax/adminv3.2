<?php
// --- Control de Acceso ---
if ($_SESSION["perfil"] != "Administrador") {
    echo '<script>window.location = "inicio";</script>';
    return;
}

// 1. Leemos las fechas de la URL para pasarlas a todas las funciones
$fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
$fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Reporte Detallado de Ventas</h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Reporte Detallado</li>
        </ol>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3>Ventas por Producto</h3>

                <div class="box-tools pull-right">

                <?php
                    // Se construye la URL para el botón de descarga
                    $urlDescarga = "vistas/modulos/descargar-reporte-detallado.php";
                    if (isset($_GET["fechaInicial"])) {
                        $urlDescarga .= "?fechaInicial=" . $_GET["fechaInicial"] . "&fechaFinal=" . $_GET["fechaFinal"];
                    }
                ?>  
                    <a href="<?= $urlDescarga ?>"style="margin-left:10px;">
                        <button class="btn btn-success" style="margin-right: 15px;">Descargar reporte en Excel</button>
                    </a>
                </div>
                <button type="button" class="btn btn-default pull-right" id="daterange-btn-detallado">
                    <span>
                        <i class="fa fa-calendar"></i> 
                        <?php
                            if ($fechaInicial) {
                                echo $fechaInicial . " - " . $fechaFinal;
                            } else {
                                echo 'Rango de fecha';
                            }
                        ?>
                    </span>
                    <i class="fa fa-caret-down"></i>
                </button>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
                    <thead>
                        <tr>
                            <th style="width:10px">#</th>
                            <th>Fecha</th>
                            <th>Factura</th>
                            <th>Vendedor</th>
                            <th>Cliente</th>
                            <th>Descripción del Producto</th>
                            <th>Cantidad</th>
                            <th>Total Producto</th>
                            <th>Medio de Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ventas = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);
                        $contadorFila = 1;
                        foreach ($ventas as $venta) {
                            $vendedor = ControladorUsuarios::ctrMostrarUsuarios("id", $venta["id_vendedor"]);
                            $cliente = ControladorClientes::ctrMostrarClientes("id", $venta["id_cliente"]);
                            $listaProductos = json_decode($venta["productos"], true);
                            foreach ($listaProductos as $producto) {
                                echo '<tr>
                                        <td>' . $contadorFila++ . '</td>
                                        <td>' . date('Y-m-d', strtotime($venta["fecha_venta"])) . '</td>
                                        <td>' . $venta["codigo"] . '</td>
                                        <td>' . $vendedor["nombre"] . '</td>
                                        <td>' . $cliente["nombre"] . '</td>
                                        <td>' . $producto["descripcion"] . '</td>
                                        <td>' . $producto["cantidad"] . '</td>
                                        <td>$ ' . number_format($producto["total"], 2) . '</td>
                                        <td>' . $venta["medio_pago"] . '</td>
                                      </tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Resúmenes Financieros</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <?php
                    $totalesEntradas = ControladorContabilidad::ctrSumaEntradasPorMedioPago($fechaInicial, $fechaFinal);
                    $colores = ["bg-aqua", "bg-green", "bg-yellow", "bg-red", "bg-blue", "bg-purple"];
                    $colorIndex = 0;
                    foreach ($totalesEntradas as $key => $value) {
                        if ($value["total_entradas"] > 0) {
                            echo '<div class="col-lg-3 col-xs-6">
                                    <div class="small-box ' . $colores[$colorIndex] . '">
                                        <div class="inner">
                                            <h3>$' . number_format($value["total_entradas"], 2) . '</h3>
                                            <p>' . $value["medio_pago"] . '</p>
                                        </div>
                                        <div class="icon"><i class="ion ion-social-usd"></i></div>
                                    </div>
                                  </div>';
                            $colorIndex = ($colorIndex + 1) % count($colores);
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="box-body">
        <div class="row">

            <?php
                // Leemos las fechas de la URL
                $fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
                $fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;
                
                // Obtenemos todos los datos necesarios
                $totalEntradas = ControladorContabilidad::ctrSumaTotalEntradas($fechaInicial, $fechaFinal)["total"] ?? 0;
                $totalDeuda = ControladorVentas::ctrSumaTotalDeuda($fechaInicial, $fechaFinal)["total_deuda"] ?? 0;
                $totalVendido = ControladorVentas::ctrSumaTotalVentasGeneral($fechaInicial, $fechaFinal)["total_ventas"] ?? 0;
            ?>

            <div class="col-lg-4 col-xs-12">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>$<?= number_format($totalEntradas, 2) ?></h3>
                        <p>Total Entradas (Dinero Ingresado)</p>
                    </div>
                    <div class="icon"><i class="ion ion-arrow-up-a"></i></div>
                </div>
            </div>

            <div class="col-lg-4 col-xs-12">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>$<?= number_format($totalDeuda, 2) ?></h3>
                        <p>Total por Cobrar (Deuda)</p>
                    </div>
                    <div class="icon"><i class="ion ion-alert-circled"></i></div>
                </div>
            </div>

            <div class="col-lg-4 col-xs-12">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>$<?= number_format($totalVendido, 2) ?></h3>
                        <p>Total Vendido (Generado en Ventas)</p>
                    </div>
                    <div class="icon"><i class="ion ion-social-usd"></i></div>
                </div>
            </div>

        </div>
    </div>
        </div>
    </section>
</div>