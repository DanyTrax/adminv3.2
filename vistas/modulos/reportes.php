<?php

if($_SESSION["perfil"] == "Especial" || $_SESSION["perfil"] == "Vendedor"){

  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>
<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Reportes de ventas
    
    </h1>

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
                
                }else{
                 
                  echo 'Rango de fecha';

                }

              ?>
            </span>

            <i class="fa fa-caret-down"></i>

          </button>

        </div>

        <div class="box-tools pull-right">
            <?php
                // 1. URL base
                $urlDescarga = "vistas/modulos/descargar-reporte.php?reporte=reporte";
        
                // 2. Añadimos el filtro de fecha si existe
                if (isset($_GET["fechaInicial"])) {
                    $urlDescarga .= "&fechaInicial=" . $_GET["fechaInicial"] . "&fechaFinal=" . $_GET["fechaFinal"];
                }
            ?>
            <a href="<?= $urlDescarga ?>">
                <button class="btn btn-success" style="margin-top:5px">Descargar reporte en Excel</button>
            </a>
        </div>
         
      </div>
      
      <section class="content">
        <div class="row">
            
            <h2>Resumen General</h2>
            <hr>
        
            <?php
                
                $fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
                $fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;
        
                // --- CÁLCULOS ---
                $entradasResult = ControladorContabilidad::ctrSumaTotalEntradas($fechaInicial, $fechaFinal);
                $deudaResult = ControladorVentas::ctrSumaTotalDeuda($fechaInicial, $fechaFinal);
                $vendidoResult = ControladorVentas::ctrSumaTotalVentasGeneral($fechaInicial, $fechaFinal);
        
                // --- VERIFICACIÓN DE RESULTADOS PARA EVITAR ERRORES ---
                // Si el resultado de la consulta no es válido, se usa 0 por defecto.
                $totalEntradas = $entradasResult["total"] ?? 0;
                $totalDeuda = $deudaResult["total_deuda"] ?? 0;
                $totalVendido = $vendidoResult["total_ventas"] ?? 0;
        
            ?>
        
            <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>$<?= number_format($totalEntradas, 2) ?></h3>
                        <p>Total Entradas (Dinero Ingresado)</p>
                    </div>
                    <div class="icon"><i class="ion ion-arrow-up-a"></i></div>
                </div>
            </div>
        
            <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>$<?= number_format($totalDeuda, 2) ?></h3>
                        <p>Total por Cobrar (Deuda)</p>
                    </div>
                    <div class="icon"><i class="ion ion-alert-circled"></i></div>
                </div>
            </div>
        
            <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>$<?= number_format($totalVendido, 2) ?></h3>
                        <p>Total Vendido (Generado en Ventas)</p>
                    </div>
                    <div class="icon"><i class="ion ion-social-usd"></i></div>
                </div>
            </div>
        
        </div>
        </section>
      
      <section class="content">
      <div class="row">
    <h2>Total de Entradas por Medio de Pago</h2>
    <hr>
    <?php
        // 1. Leemos las fechas de la URL
        $fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
        $fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;

        // 2. Pasamos las fechas al controlador
        $totalesEntradas = ControladorContabilidad::ctrSumaEntradasPorMedioPago($fechaInicial, $fechaFinal);
        
        $colores = ["bg-aqua", "bg-green", "bg-yellow", "bg-red", "bg-blue", "bg-purple"];
        $colorIndex = 0;

        foreach ($totalesEntradas as $key => $value) {
            if($value["total_entradas"] > 0){
                echo '<div class="col-lg-3 col-xs-6">
                        <div class="small-box ' . $colores[$colorIndex] . '">
                            <div class="inner">
                                <h3>$' . number_format($value["total_entradas"], 2) . '</h3>
                                <p>' . $value["medio_pago"] . '</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-social-usd"></i>
                            </div>
                        </div>
                      </div>';
                $colorIndex = ($colorIndex + 1) % count($colores);
            }
        }
    ?>
</div>
</section>
<section class="content">
    <hr>
<div class="row">
    
    <h2>Arqueo y Resumen de Gastos</h2>
    <hr>

    <?php
        // Leemos las fechas de la URL (si ya lo tienes arriba, puedes omitir estas dos líneas)
        $fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
        $fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;

        // --- CÁLCULO DEL ARQUEO DE EFECTIVO ---
        $entradasEfectivo = ControladorContabilidad::ctrSumaTotalPorTipoYMedio("Entrada", "Efectivo", $fechaInicial, $fechaFinal);
        $gastosEfectivo = ControladorContabilidad::ctrSumaTotalPorTipoYMedio("Gasto", "Efectivo", $fechaInicial, $fechaFinal);
        $arqueoDeEfectivo = $entradasEfectivo["total"] - $gastosEfectivo["total"];

        // --- CÁLCULO DEL TOTAL DE GASTOS ---
        $totalGastos = ControladorContabilidad::ctrSumaTotalGastos($fechaInicial, $fechaFinal);
    ?>

    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>$<?= number_format($arqueoDeEfectivo, 2) ?></h3>
                <p>Arqueo de Efectivo (Entradas Efectivo - Gastos Efectivo)</p>
            </div>
            <div class="icon">
                <i class="ion ion-calculator"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>$<?= number_format($totalGastos["total"], 2) ?></h3>
                <p>Total de Gastos (Todos los medios de pago)</p>
            </div>
            <div class="icon">
                <i class="ion ion-arrow-graph-down-right"></i>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <h3>Desglose de Gastos por Medio de Pago</h3>
    <hr>

    <?php
        $gastosPorMedioPago = ControladorContabilidad::ctrSumaGastosPorMedioPago($fechaInicial, $fechaFinal);
        $colores = ["bg-maroon", "bg-purple", "bg-orange", "bg-blue-active", "bg-green-active"];
        $colorIndex = 0;

        foreach ($gastosPorMedioPago as $key => $value) {
            if($value["total_gastos"] > 0){
                echo '<div class="col-lg-3 col-xs-6">
                        <div class="small-box ' . $colores[$colorIndex] . '">
                            <div class="inner">
                                <h3>$' . number_format($value["total_gastos"], 2) . '</h3>
                                <p>' . $value["medio_pago"] . '</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                        </div>
                      </div>';
                $colorIndex = ($colorIndex + 1) % count($colores);
            }
        }
    ?>

</div>
    </section>
    <section class="content">
        <br>

<div class="box box-primary">

    <div class="box-header with-border">
        <h3 class="box-title">Total de Ventas por Vendedor</h3>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Vendedor</th>
                    <th>Total Vendido</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Leemos las fechas de la URL (si ya lo tienes arriba, puedes omitir estas dos líneas)
                    $fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
                    $fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;

                    // Llamamos a la nueva función del controlador
                    $ventasPorVendedor = ControladorVentas::ctrSumaVentasPorVendedor($fechaInicial, $fechaFinal);
                    
                    foreach ($ventasPorVendedor as $key => $value) {
                        echo '<tr>
                                <td>'.($key + 1).'</td>
                                <td>'.$value["vendedor"].'</td>
                                <td>$ '.number_format($value["total_vendido"], 2).'</td>
                              </tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>

</div>
        </section>
      <div class="box-body">
        
        <div class="row">

          <div class="col-xs-12">
            
            <?php

            include "reportes/grafico-ventas.php";

            ?>

          </div>

           <div class="col-md-6 col-xs-12">
             
            <?php

            include "reportes/productos-mas-vendidos.php";

            ?>

           </div>

            <div class="col-md-6 col-xs-12">
             
            <?php

            include "reportes/vendedores.php";

            ?>

           </div>

           <div class="col-md-6 col-xs-12">
             
            <?php

            include "reportes/compradores.php";

            ?>

           </div>
          
        </div>

      </div>
      
    </div>

  </section>
 
 </div>
