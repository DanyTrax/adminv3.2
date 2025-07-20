<?php
require_once "../controladores/ventas.controlador.php";
require_once "../modelos/ventas.modelo.php";

class AjaxVentas{
	public $idVenta;
	public function ajaxDatos(){
		$respuesta=ControladorVentas::ctrMostrarVentas("id",$this->idVenta);
		echo json_encode($respuesta);
	}
}

// OBJETOS
if(isset($_POST["idVenta"])){
	$datos=new AjaxVentas();
	$datos->idVenta=$_POST["idVenta"];
	$datos->ajaxDatos();
}