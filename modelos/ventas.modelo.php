<?php

require_once "conexion.php";

class ModeloVentas {

	/*=============================================
	MOSTRAR VENTAS
	=============================================*/
	static public function mdlMostrarVentas($tabla, $item, $valor) {
		if ($item != null) {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id ASC");
			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt -> fetch(PDO::FETCH_ASSOC);
		} else {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id ASC");
			$stmt->execute();
			return $stmt->fetchAll();
		}
		$stmt->close(); $stmt = null;
	}

	/*=============================================
	REGISTRO DE VENTA (SIN CAMBIOS)
	=============================================*/
	static public function mdlIngresarVenta($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(codigo,id_cliente,id_vendedor,productos,impuesto,descuento,neto,total,detalle,metodo_pago,fecha_venta,abono,id_vend_abono,fecha_abono, pago, Ult_abono, medio_pago) VALUES (:codigo,:id_cliente,:id_vendedor,:productos,:impuesto,:descuento,:neto,:total,:detalle,:metodo_pago,:fecha_venta,:abono,:id_vend_abono,:fecha_abono, '', 0, :medio_pago)");
		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_INT);
		$stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_INT);
		$stmt->bindParam(":id_vendedor", $datos["id_vendedor"], PDO::PARAM_INT);
		$stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR);
		$stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
		$stmt->bindParam(":descuento", $datos["descuento"], PDO::PARAM_STR);
		$stmt->bindParam(":neto", $datos["neto"], PDO::PARAM_STR);
		$stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);
		$stmt->bindParam(":detalle", $datos["detalle"], PDO::PARAM_STR);
		$stmt->bindParam(":metodo_pago", $datos["metodo_pago"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_venta", $datos["fecha_venta"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vend_abono", $datos["id_vend_abono"], PDO::PARAM_INT);
		$stmt->bindParam(":abono", $datos["abono"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_abono", $datos["fecha_abono"], PDO::PARAM_STR);
		$stmt->bindParam(":medio_pago", $datos["medio_pago"], PDO::PARAM_STR);
		if ($stmt->execute()) { return "ok"; } else { return "error"; }
		$stmt = null;
	}

	/*=============================================
	EDITAR VENTA (SIN CAMBIOS)
	=============================================*/
	static public function mdlEditarVenta($tabla, $datos) {
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET id_cliente=:id_cliente,id_vendedor=:id_vendedor,productos=:productos,impuesto=:impuesto,neto=:neto,total=:total,detalle=:detalle,metodo_pago=:metodo_pago,fecha_venta=:fecha_venta,abono=:abono,id_vend_abono=:id_vend_abono,fecha_abono=:fecha_abono,pago=:pago, medio_pago = :medio_pago WHERE codigo=:codigo");
		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_INT);
		$stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_INT);
		$stmt->bindParam(":id_vendedor", $datos["id_vendedor"], PDO::PARAM_INT);
		$stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR);
		$stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
		$stmt->bindParam(":neto", $datos["neto"], PDO::PARAM_STR);
		$stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);
		$stmt->bindParam(":detalle", $datos["detalle"], PDO::PARAM_STR);
		$stmt->bindParam(":metodo_pago", $datos["metodo_pago"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_venta", $datos["fecha_venta"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vend_abono", $datos["id_vend_abono"], PDO::PARAM_INT);
		$stmt->bindParam(":abono", $datos["abono"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_abono", $datos["fecha_abono"], PDO::PARAM_STR);
		$stmt->bindParam(":pago", $datos["pago"], PDO::PARAM_STR);
		$stmt->bindParam(":medio_pago", $datos["medio_pago"], PDO::PARAM_STR);
		if ($stmt->execute()) { return "ok"; } else { return "error"; }
		$stmt = null;
	}

	/*=============================================
	ACTUALIZAR ABONO EN LA VENTA (SIN CAMBIOS)
	=============================================*/
	static public function mdlActualizarAbono($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET abono = :abono, Ult_abono = :Ult_abono, id_vend_abono = :id_vend_abono, fecha_abono = :fecha_abono, medio_pago = :medio_pago, metodo_pago = :metodo_pago WHERE id = :id");
		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt->bindParam(":abono", $datos["abono"], PDO::PARAM_STR);
		$stmt->bindParam(":Ult_abono", $datos["Ult_abono"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vend_abono", $datos["id_vend_abono"], PDO::PARAM_INT);
		$stmt->bindParam(":fecha_abono", $datos["fecha_abono"], PDO::PARAM_STR);
		$stmt->bindParam(":medio_pago", $datos["medio_pago"], PDO::PARAM_STR);
		$stmt->bindParam(":metodo_pago", $datos["metodo_pago"], PDO::PARAM_STR);
		if($stmt->execute()){ return "ok"; }else{ return "error"; }
	}

/*=============================================
ELIMINAR VENTA COMPLETA (VERSIÓN FINAL)
=============================================*/
static public function mdlEliminarVenta($tabla, $datos){

	$idVenta = $datos;
	$db = Conexion::conectar();

	$db->beginTransaction();

	try {
		$stmt = $db->prepare("SELECT codigo FROM $tabla WHERE id = :id");
		$stmt->bindParam(":id", $idVenta, PDO::PARAM_INT);
		$stmt->execute();
		$venta = $stmt->fetch();
		$codigoFactura = $venta['codigo'];

		$stmt = $db->prepare("DELETE FROM venta_productos WHERE id_venta = :id_venta");
		$stmt->bindParam(":id_venta", $idVenta, PDO::PARAM_INT);
		$stmt->execute();

		if ($codigoFactura) {
			
			// --- CORRECCIÓN FINAL AQUÍ ---
			// La columna se llama 'factura', no 'codigo_factura'.
			$stmt = $db->prepare("DELETE FROM contabilidad WHERE factura = :factura AND tipo = 'Entrada'");
			$stmt->bindParam(":factura", $codigoFactura, PDO::PARAM_STR);
			$stmt->execute();

		}

		$stmt = $db->prepare("DELETE FROM $tabla WHERE id = :id");
		$stmt->bindParam(":id", $idVenta, PDO::PARAM_INT);
		$stmt->execute();

		$db->commit();
		return "ok";

	} catch (Exception $e) {
		$db->rollBack();
		// Devolvemos el mensaje de error para saber qué pasó
		return "Error de base de datos: " . $e->getMessage();
	}
	
	$stmt = null;
}
	/*=============================================
	RANGO FECHAS (UNIFICADO Y CORREGIDO)
	=============================================*/
	static public function mdlRangoFechasVentas($tabla, $fechaInicial, $fechaFinal){
		if($fechaInicial == null){
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();	
		}else{
			// Se usa DATE() para ignorar la hora y comparar solo el día
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE DATE(fecha_venta) BETWEEN :fechaInicial AND :fechaFinal ORDER BY id DESC");
			$stmt->bindParam(":fechaInicial", $fechaInicial, PDO::PARAM_STR);
			$stmt->bindParam(":fechaFinal", $fechaFinal, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
	}
	
	/*=============================================
	SUMAR VENTAS POR VENDEDOR (CORREGIDO)
	=============================================*/
	static public function mdlSumaVentasPorVendedor($tablaVentas, $tablaUsuarios, $fechaInicial, $fechaFinal){
		$sql_base = "SELECT SUM(v.total) as total_vendido, u.nombre as vendedor FROM $tablaVentas as v INNER JOIN $tablaUsuarios as u ON v.id_vendedor = u.id";
		$sql_end = " GROUP BY v.id_vendedor ORDER BY total_vendido DESC";
		
		if($fechaInicial == null){
			$stmt = Conexion::conectar()->prepare($sql_base . $sql_end);
		} else {
			$stmt = Conexion::conectar()->prepare($sql_base . " WHERE DATE(v.fecha_venta) BETWEEN :fechaInicial AND :fechaFinal" . $sql_end);
			$stmt->bindParam(":fechaInicial", $fechaInicial, PDO::PARAM_STR);
			$stmt->bindParam(":fechaFinal", $fechaFinal, PDO::PARAM_STR);
		}
		$stmt -> execute();
		return $stmt -> fetchAll();
	}

	
	/*=============================================
	FILTRO GENERAL (VERSIÓN FINAL)
	=============================================*/
	public static function mdlFilterBy($tabla, $fechaInicial, $fechaFinal, $medioPago, $formaPago) {
		$sql = "SELECT * FROM $tabla WHERE 1=1";
		$params = [];

		if ($fechaInicial != null && $fechaFinal != null) {
            // Se usa la misma lógica if/else para manejar rangos de un día o varios
            if($fechaInicial == $fechaFinal){
                $sql .= " AND DATE(fecha_venta) = :fecha";
                $params[":fecha"] = $fechaInicial;
            }else{
                $sql .= " AND fecha_venta BETWEEN :fechaInicial AND :fechaFinal";
                $params[":fechaInicial"] = $fechaInicial . " 00:00:00";
                $params[":fechaFinal"] = $fechaFinal . " 23:59:59";
            }
		}
		if ($medioPago != null) {
			$sql .= " AND medio_pago = :medioPago";
			$params[":medioPago"] = $medioPago;
		}
		if ($formaPago != null) {
			$sql .= " AND metodo_pago = :formaPago";
			$params[":formaPago"] = $formaPago;
		}

		$stmt = Conexion::conectar()->prepare("$sql ORDER BY id DESC");
		foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// --- FUNCIONES ORIGINALES RESTANTES SIN CAMBIOS ---
		/*=============================================
	SUMAR EL TOTAL DE VENTAS
	=============================================*/

	static public function mdlSumaTotalVentas3($tabla3)
	{

		$stmt = Conexion::conectar()->prepare("SELECT SUM(total) as total FROM $tabla3");

		$stmt->execute();

		return $stmt->fetch();

		$stmt->close();

		$stmt = null;
	}
		/*=============================================
	SUMAR EL TOTAL DE VENTAS Infinito
	=============================================*/

	static public function mdlSumaTotalVentas($tabla)
	{
		$iniciofc = date("Y-m-01");

		$finfc = date("Y-m-t");

		$stmt = Conexion::conectar()->prepare("SELECT SUM(v.total) as total FROM usuarios u, ventas v WHERE u.id = v.id_vendedor AND u.empresa = 'Infinito' AND (v.fecha_venta) BETWEEN '$iniciofc' AND '$finfc'");

		$stmt->execute();

		return $stmt->fetch();

		$stmt->close();

		$stmt = null;
	}
	//SUMAR EL TOTAL DE VENTAS LEMA
	//=============================================//

	static public function mdlSumaTotalVentas1($tabla1)
	{
		$iniciofc = date("Y-m-01");

		$finfc = date("Y-m-t");

		$stmt = Conexion::conectar()->prepare("SELECT SUM(v.total) as total FROM usuarios u, ventas v WHERE u.id = v.id_vendedor AND u.empresa = 'Lema' AND (v.fecha_venta) BETWEEN '$iniciofc' AND '$finfc'");

		$stmt->execute();

		return $stmt->fetch();

		$stmt->close();

		$stmt = null;
	}
	//SUMAR EL TOTAL DE VENTAS EPICO
	//=============================================//

	static public function mdlSumaTotalVentas2($tabla1)
	{
		$iniciofc = date("Y-m-01");

		$finfc = date("Y-m-t");

		$stmt = Conexion::conectar()->prepare("SELECT SUM(v.total) as total FROM usuarios u, ventas v WHERE u.id = v.id_vendedor AND u.empresa = 'Epico' AND (v.fecha_venta) BETWEEN '$iniciofc' AND '$finfc'");

		$stmt->execute();

		return $stmt->fetch();

		$stmt->close();

		$stmt = null;
	}

	// Ultimo C車digo de Venta
	static public function mdlLastCodVenta($tabla)
	{
		$sql = "SELECT MAX(codigo) FROM $tabla";
		$stmt = Conexion::conectar()->prepare($sql);
		$stmt->execute();
		return $stmt->fetch();
		$stmt = null;
	}
	/*=============================================
	SUMAR TOTAL DE DEUDA (CORREGIDO)
	=============================================*/
	static public function mdlSumaTotalDeuda($tabla, $fechaInicial, $fechaFinal){
		$sql_base = "SELECT SUM(total - abono) as total_deuda FROM $tabla WHERE metodo_pago != 'Completo'";
		if($fechaInicial == null){
			$stmt = Conexion::conectar()->prepare($sql_base);
		} else {
			$stmt = Conexion::conectar()->prepare($sql_base . " AND DATE(fecha_venta) BETWEEN :fechaInicial AND :fechaFinal");
			$stmt->bindParam(":fechaInicial", $fechaInicial, PDO::PARAM_STR);
			$stmt->bindParam(":fechaFinal", $fechaFinal, PDO::PARAM_STR);
		}
		$stmt -> execute();
		return $stmt -> fetch();
	}

    
/*=============================================
SUMAR TOTAL DE VENTAS (GENERAL) (CORREGIDO)
=============================================*/
static public function mdlSumaTotalVentasGeneral($tabla, $fechaInicial, $fechaFinal){

	if($fechaInicial == null){

		$stmt = Conexion::conectar()->prepare("SELECT SUM(total) as total_ventas FROM $tabla");
		$stmt -> execute();
		return $stmt -> fetch();

	} else {
		
		// Corregimos el rango de fechas para que incluya todo el día
		$fechaFinalConHora = $fechaFinal . ' 23:59:59';

		$stmt = Conexion::conectar()->prepare("SELECT SUM(total) as total_ventas FROM $tabla WHERE fecha_venta BETWEEN :fechaInicial AND :fechaFinal");
		
		$stmt->bindParam(":fechaInicial", $fechaInicial, PDO::PARAM_STR);
		$stmt->bindParam(":fechaFinal", $fechaFinalConHora, PDO::PARAM_STR);

		$stmt -> execute();
		return $stmt -> fetch();
	}

	$stmt -> close();
	$stmt = null;
}
}