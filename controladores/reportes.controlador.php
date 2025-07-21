<?php

class ControladorReportes {

    static public function ctrObtenerVentasServerSide($params, $fechaInicial, $fechaFinal) {
        $respuesta = ModeloReportes::mdlObtenerVentasServerSide($params, $fechaInicial, $fechaFinal);
        
        // Formatear la data para DataTables
        $data = [];
        $contador = intval($params['start']) + 1;
        foreach ($respuesta as $row) {
            $data[] = [
                "contador"             => $contador++,
                "fecha_venta"          => $row['fecha_venta'],
                "codigo_factura"       => $row['codigo'],
                "vendedor"             => $row['nombre_vendedor'],
                "cliente"              => $row['nombre_cliente'],
                "producto_descripcion" => $row['producto_descripcion'],
                "producto_cantidad"    => $row['producto_cantidad'],
                "producto_total"       => $row['producto_total'],
                "medio_pago"           => $row['medio_pago']
            ];
        }
        return $data;
    }

    static public function ctrContarVentasFiltradas($params, $fechaInicial, $fechaFinal) {
        return ModeloReportes::mdlContarVentasFiltradas($params, $fechaInicial, $fechaFinal);
    }

    static public function ctrContarTotalVentas($fechaInicial, $fechaFinal) {
        return ModeloReportes::mdlContarTotalVentas($fechaInicial, $fechaFinal);
    }
}