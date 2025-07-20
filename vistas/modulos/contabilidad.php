<?php

if ($_SESSION["perfil"] == "Especial") {

  echo '<script>

    window.location = "inicio";

  </script>';

  return;
}

$xml = ControladorVentas::ctrDescargarXML();

if ($xml) {

  rename($_GET["xml"] . ".xml", "xml/" . $_GET["xml"] . ".xml");

  echo '<a class="btn btn-block btn-success abrirXML" archivo="xml/' . $_GET["xml"] . '.xml" href="ventas">Se ha creado correctamente el archivo XML <span class="fa fa-times pull-right"></span></a>';
}

?>
<div class="content-wrapper">

  <section class="content-header">

    <h1>

      Contabilidad ventas

    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>

      <li class="active">Contabilidad ventas</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <!-- <---------------------------------------------------------------------- -->
        <!-- <---------------------BOTON EXPORTAR VENTAS------------------------ -->
        <!-- <---------------------------------------------------------------------- -->


      <div class="box-header with-border">

    <?php
        // Lógica para construir la URL de descarga con los filtros activos
        $urlDescarga = "vistas/modulos/reporte-ventas.php?reporte=contabilidad";
        if (isset($_GET["fechaInicial"])) {
            $urlDescarga .= "&fechaInicial=" . $_GET["fechaInicial"] . "&fechaFinal=" . $_GET["fechaFinal"];
        }
        if (isset($_GET["medioPago"]) && !empty($_GET["medioPago"])) {
            $urlDescarga .= "&medioPago=" . $_GET["medioPago"];
        }
        if (isset($_GET["formaPago"]) && !empty($_GET["formaPago"])) {
            $urlDescarga .= "&formaPago=" . $_GET["formaPago"];
        }
    ?>

    <a href="<?= $urlDescarga ?>">
        <button class="btn btn-success">Reporte Excel</button>
    </a>

    <div class="box-tools pull-right">
        
        <button type="button" class="btn btn-default" id="daterange-btn-contabilidad">
            <span>
                <i class="fa fa-calendar"></i> Rango de fecha
            </span>
            <i class="fa fa-caret-down"></i>
        </button>
        
        <select class="btn" name="filter-medioPago-contabilidad" id="filter-medioPago-contabilidad" style="margin-left: 10px;">
            <option value="">Medio de Pago</option>
            <option value="">Todos</option>
            <?php foreach (MedioPago::ALL as $value) : ?>
                <option value="<?= $value ?>"><?= $value ?></option>
            <?php endforeach; ?>
        </select>
        
        <select class="btn" name="filter-formaPago-contabilidad" id="filter-formaPago-contabilidad" style="margin-left: 10px;">
            <option value="">Forma de Pago</option>
            <option value="">Todos</option>
            <?php foreach (FormaPago::ALL as $value) : ?>
                <option value="<?= $value ?>"><?= $value ?></option>
            <?php endforeach; ?>
        </select>

    </div>

</div>





        <!-- <---------------------------------------------------------------------- -->
  


          


      </div>

      <div class="box-body">

        <table class="table table-bordered table-striped dt-responsive tablas" width="100%">

          <thead>

            <tr>

              <th style="width:3px">#</th>
              <th style="width:20px">C&oacute;digo factura</th>
              <th>Cliente</th>
              <th style="width:3px">Emp</th>
              <th>Vendedor</th>
              <th>V_Abono</th>
              <th style="width:80px">Forma de pago</th>
              <th>Neto</th>
              <th>Total</th>
              <th>Fecha Venta</th>
              <th>Abono</th>
              <th>Ult_Abono</th>
              <th>Pago</th>
              <th>Medio Pago</th>
              <th>Acciones</th>

            </tr>

          </thead>

          <tbody>

            <?php
            
            $fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
            $fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;
            $medioPago = isset($_GET["medioPago"]) ? $_GET["medioPago"] : null;
            $formaPago = isset($_GET["formaPago"]) ? $_GET["formaPago"] : null;
            
            // CORRECCIÓN: Si el valor es 0, mostramos todo. Si no, usamos el valor o 150 por defecto.
            $valor_limite = isset($_GET["minimo"]) ? (int)$_GET["minimo"] : 150;

            $respuesta = ControladorVentas::filterBy($fechaInicial, $fechaFinal, $medioPago, $formaPago);
            
            $contador = 0;
            
            foreach ($respuesta as $key => $value) {

                // CORRECCIÓN: Aplicamos el límite solo si es mayor a cero
                if ($valor_limite > 0 && $contador >= $valor_limite) {
                    break; 
                }
                
              echo '<tr>
              <td>' . ($key + 1) . '</td>
              <td>' . $value["codigo"] . '</td>';

              $respuestaCliente = ControladorClientes::ctrMostrarClientes("id", $value["id_cliente"]);
              echo '<td>' . $respuestaCliente["nombre"] . '</td>';
              $respuestaUsuario = ControladorUsuarios::ctrMostrarUsuarios("id", $value["id_vendedor"]);
              $respuestaUsuario_ab = ControladorUsuarios::ctrMostrarUsuarios("id", $value["id_vend_abono"]);

              if ($value["abono"] != 0) {
                $botones = '<div class="btn-group">
                  <button class="btn btn-info btn-xs btnImprimirFactura" codigoVenta="' . $value["codigo"] . '">
                    <i class="fa fa-print"></i>
                  </button>';
                if ($_SESSION["perfil"] == "Administrador") {
                  $botones .= '<button class="btn btn-warning btn-xs btnEditarVenta" idVenta="' . $value["id"] . '"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-xs btnEliminarVenta" idVenta="' . $value["id"] . '"><i class="fa fa-times"></i></button>';
                }
                $botones .= '<button class="btn btn-primary btn-xs btnAbonar" idVenta="' . $value["id"] . '" idUsuarioAbo="' . $_SESSION["id"] . '" data-toggle="modal" data-target="#modalAbonar" title="Abonar"><i class="fa fa-money"></i></button></div>';
              } else {
                $botones = '<div class="btn-group">
                  <button class="btn btn-info btn-xs btnImprimirFactura" codigoVenta="' . $value["codigo"] . '">
                    <i class="fa fa-print"></i>
                  </button>';
                if ($_SESSION["perfil"] == "Administrador") {
                  $botones .= '<button class="btn btn-warning btn-xs btnEditarVenta" idVenta="' . $value["id"] . '"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-xs btnEliminarVenta" idVenta="' . $value["id"] . '"><i class="fa fa-times"></i></button>';
                }
                if ($_SESSION['perfil'] == 'Vendedor' || $_SESSION['perfil'] == 'Contador' || $_SESSION["perfil"] == "Administrador") {
                  if ($value["metodo_pago"] == 'Se Debe') {
                    $botones .= '<button class="btn btn-primary btn-xs btnAbonar" idVenta="' . $value["id"] . '" idUsuarioAbo="' . $_SESSION["id"] . '" data-toggle="modal" data-target="#modalAbonar" title="Abonar"><i class="fa fa-money"></i></button></div>';
                  }
                }
                $botones .= '</div>';
              }

              echo '<td>' . ($respuestaUsuario_ab["empresa"] ?? '') . '</td>
              <td>' . ($respuestaUsuario["nombre"] ?? '') . '</td>
              <td>' . ($respuestaUsuario_ab["nombre"] ?? '') . '</td>
              <td>' . $value["metodo_pago"] . '</td>
              <td>$ ' . number_format($value["neto"] ?? 0, 2, ',', '.') . '</td>
              <td>$ ' . number_format($value["total"] ?? 0, 2, ',', '.') . '</td>
              <td>' . $value["fecha_abono"] . '</td>
              <td>$ ' . number_format($value["abono"] ?? 0, 2, ',', '.') . '</td>
              <td>$ ' . number_format($value["Ult_abono"] ?? 0, 2, ',', '.') . '</td>
              <td>' . $value["pago"] . '</td>
              <td>' . $value["medio_pago"] . '</td>
              <td>' . $botones . '</td>
            </tr>';
                
            $contador++;
            
            }
            ?>
          </tbody>
        </table>
        <?php
        $eliminarVenta = new ControladorVentas();
        $eliminarVenta->ctrEliminarVenta();
        ?>
      </div>
    </div>
  </section>
</div>

<!-- MODAL AGREGAR ABONO -->
<div id="modalAbonar" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Abono</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <div class="input-group col-xs-12">
                <label>Dinero Restante</label>
                <input type="text" class="form-control dinRestante" disabled>
              </div>
            </div>
            <input type="hidden" class="form-control idUsuarioAbo" name="idUsuarioAbo">
            <input type="hidden" class="form-control idVentaAbo" name="idVentaAbo">
            <div class="form-group">
              <div class="input-group col-xs-12">
                <span class="input-group-addon"><i class="fa fa-money"></i></span>
                <input type="text" class="form-control nuevoAbono" name="nuevoAbono" placeholder="Ingrese Nuevo Abono" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar Abono</button>
        </div>
        <?php
        $crearAbono = new ControladorVentas();
        $crearAbono->ctrCrearAbono();
        ?>
      </form>
    </div>
  </div>
</div>