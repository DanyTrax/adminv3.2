<?php

$item = null;
$valor = null;
$orden = "id";

// Llamadas a los controladores corregidos
$ventasInfinito = ControladorVentas::ctrSumaTotalVentas();
$ventasLema = ControladorVentas::ctrSumaTotalVentas1();
$ventasEpico = ControladorVentas::ctrSumaTotalVentas2();
$totalVentas = ControladorVentas::ctrSumaTotalVentas3();

// NOTA: Estas funciones de Contabilidad probablemente tengan el mismo problema.
// Podremos arreglarlas después si siguen mostrando cero.
$totalEntradas = ControladorContabilidad::sumEntradas();
$totalEntradasEfectivo = ControladorContabilidad::sumEntradasBy();
$totalGastos = ControladorContabilidad::sumGastos();
$totalGastosEfectivo = ControladorContabilidad::sumGastosBy();

?>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-aqua">
    <div class="inner">
      <h3>$<?php echo number_format($ventasInfinito, 2, ',', '.'); ?></h3>
      <p>Ventas Infinito</p>
    </div>
    <div class="icon"><i class="ion ion-social-usd"></i></div>
    <a href="ventas" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-green">
    <div class="inner">
      <h3>$<?php echo number_format($totalEntradasEfectivo - $totalGastosEfectivo, 2, ',', '.'); ?></h3>
      <p>Arqueo de efectivo</p>
    </div>
    <div class="icon"><i class="ion ion-social-usd"></i></div>
    <a href="ventas" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-red">
    <div class="inner">
      <h3>$<?php echo number_format($ventasEpico, 2, ',', '.'); ?></h3>
      <p>Ventas Epico</p>
    </div>
    <div class="icon"><i class="ion ion-social-usd"></i></div>
    <a href="ventas" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-yellow">
    <div class="inner">
      <h3>$<?php echo number_format($totalVentas, 2, ',', '.'); ?></h3>
      <p>Total Ventas</p>
    </div>
    <div class="icon"><i class="ion ion-bank"></i></div>
    <a href="ventas" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-yellow">
    <div class="inner">
      <h3>$<?php echo number_format($totalEntradas, 2, ',', '.'); ?></h3>
      <p>Total Entradas</p>
    </div>
    <div class="icon"><i class="ion ion-social-usd"></i></div>
    <a href="ventas" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-red">
    <div class="inner">
      <h3>$<?php echo number_format($totalEntradasEfectivo, 2, ',', '.'); ?></h3>
      <p>Total Entradas Efectivo</p>
    </div>
    <div class="icon"><i class="ion ion-social-usd"></i></div>
    <a href="ventas" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-green">
    <div class="inner">
      <h3>$<?php echo number_format($totalGastos, 2, ',', '.'); ?></h3>
      <p>Total Gastos</p>
    </div>
    <div class="icon"><i class="ion ion-social-usd"></i></div>
    <a href="ventas" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-xs-6">
  <div class="small-box bg-blue">
    <div class="inner">
      <h3>$<?php echo number_format($totalGastosEfectivo, 2, ',', '.'); ?></h3>
      <p>Total Gastos Efectivo</p>
    </div>
    <div class="icon"><i class="ion ion-bank"></i></div>
    <a href="clientes" class="small-box-footer">Más info <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>