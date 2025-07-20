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

      Gastos

    </h1>

    <ol class="breadcrumb">

      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>

      <li class="active">Gastos</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <a href="crear-gastos">

          <button class="btn btn-primary">

            Agregar gasto

          </button>

        </a>
        <!-- <---------------------------------------------------------------------- -->



        <?php

        if (count($_GET) === 1) {
        ?>
          <a class="btn btn-success" href="vistas/modulos/reporte-gastos.php">
            Reporte Excel
          </a>
        <?php
        }

        if (isset($_GET["fechaInicial"])) {
        ?>
          <a class="btn btn-success" href="vistas/modulos/reporte-gastos.php?fechaInicial=<?= $_GET["fechaInicial"] ?>&fechaFinal=<?= $_GET["fechaFinal"] ?>">
            Reporte Excel
          </a>
        <?php
        }

        if (isset($_GET["medioPago"])) {
        ?>
          <a class="btn btn-success" href="vistas/modulos/reporte-gastos.php?medioPago=<?= $_GET["medioPago"] ?>">
            Reporte Excel
          </a>
        <?php
        }

        ?>


        <?php
        $medioPago = isset($_GET['medioPago']) ? $_GET['medioPago'] : null;
        ?>
        <select class="btn pull-right" name="filter-medioPago-gasto" id="filter-medioPago-gasto" style="margin-left: 10px;">
          <option value="" <?= $medioPago === null ? 'selected' : '' ?>>Medio de Pago</option>
          <option value="">Todos</option>
          <?php foreach (MedioPago::ALL as $value) : ?>
            <option value="<?= $value ?>" <?= $medioPago === $value ? 'selected' : '' ?>><?= $value ?></option>
          <?php endforeach; ?>
        </select>

        <button type="button" class="btn btn-default pull-right" id="daterange-btn-gastos">


          <span>
            <i class="fa fa-calendar"></i>

            <?php

            if (isset($_GET["fechaInicial"])) {

              echo $_GET["fechaInicial"] . " - " . $_GET["fechaFinal"];
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

              <th style="width:3px">#</th>
              <th>Vendedor</th>
              <th>Detalle</th>
              <th>Valor</th>
              <th>Medio Pago</th>
              <th>Fecha</th>
              <th>Acciones</th>

            </tr>

          </thead>

<tbody>
    <?php
        $fechaInicial = isset($_GET["fechaInicial"]) ? $_GET["fechaInicial"] : null;
        $fechaFinal = isset($_GET["fechaFinal"]) ? $_GET["fechaFinal"] : null;
        $medioPago = isset($_GET["medioPago"]) ? $_GET["medioPago"] : null;

        // Se llama a la función de filtrado para obtener los gastos
        $respuesta = ControladorContabilidad::filterBy($fechaInicial, $fechaFinal, $medioPago, 'Gasto');

        foreach ($respuesta as $key => $value) {

            // Se busca el nombre del vendedor
            $vendedor = ControladorUsuarios::ctrMostrarUsuarios('id', $value["id_vendedor"]);

            echo '<tr>
                    <td>' . ($key + 1) . '</td>
                    <td>' . $vendedor["nombre"] . '</td>
                    <td>' . $value["detalle"] . '</td>
                    <td>$ ' . number_format((float)$value["valor"], 2, ',', '.') . '</td>
                    <td>' . $value["medio_pago"] . '</td>
                    
                    <td>' . date('Y-m-d H:i:s', strtotime($value["fecha"])) . '</td>
                    
                    <td>';
            
            // Lógica para los botones de acciones
            if ($_SESSION["perfil"] == "Administrador") {
                echo '<div class="btn-group">
                        <button class="btn btn-warning btn-xs btnEditarGasto" idGasto="'.$value["id"].'"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btn-xs btnEliminarGasto" idGasto="'.$value["id"].'"><i class="fa fa-times"></i></button>
                      </div>';
            }

            echo '  </td>
                  </tr>';
        }
    ?>
</tbody>
        </table>
        <?php
        ControladorContabilidad::deleteGasto();
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
