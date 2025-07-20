<?php

class Conexion{

	static public function conectar(){

		$link = new PDO("mysql:host=localhost;dbname=dowgroupcol_chapinero",
			            "dowgroupcol_chapinero",
			            "T7D*,LnFPZ}d");

		$link->exec("set names utf8");

		return $link;

	}

}