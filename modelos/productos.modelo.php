<?php
require_once "conexion.php";

class ModeloProductos {

    private static function ejecutarQuery($sql, $params = [], $returnType = "all") {
        try {
            $stmt = Conexion::conectar()->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            if ($returnType === "one") return $stmt->fetch();
            if ($returnType === "column") return $stmt->fetchColumn();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }

	static public function mdlMostrarProductos($tabla, $item, $valor, $orden){
		if($item != null){
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
		$stmt -> close();
		$stmt = null;
	}

	static public function mdlIngresarProducto($tabla, $datos) {
		$campos = array_keys($datos);
		$camposSQL = implode(", ", $campos);
		$placeholders = ":" . implode(", :", $campos);
		$sql = "INSERT INTO $tabla ($camposSQL) VALUES ($placeholders)";
		return self::ejecutarQuery($sql, $datos) !== false ? "ok" : "error";
	}

	static public function mdlVerificarCampo($tabla, $campo, $valor) {
		$sql = "SELECT COUNT(*) FROM $tabla WHERE $campo = :$campo";
		return self::ejecutarQuery($sql, [$campo => $valor], "column");
	}

	/*=============================================
	EDITAR PRODUCTO - VERSIÓN FINAL CORREGIDA
	=============================================*/
	static public function mdlEditarProducto($tabla, $datos) {
		// Esta versión se asegura de usar el 'id' para actualizar y
		// nunca incluye los campos de identificación en la parte SET de la consulta.
		
		$identifierField = 'id'; // Siempre usaremos el ID para la máxima precisión

		$updates = [];
		foreach ($datos as $key => $val) {
			// Se construye la parte SET, excluyendo el identificador.
			if ($key !== $identifierField) {
				$updates[] = "$key = :$key";
			}
		}

		if (empty($updates)) { return "ok"; }

		$sql = "UPDATE $tabla SET " . implode(", ", $updates) . " WHERE $identifierField = :$identifierField";
		
		return self::ejecutarQuery($sql, $datos) !== false ? "ok" : "error";
	}

	static public function mdlEliminarProducto($tabla, $id) {
		$sql = "DELETE FROM $tabla WHERE id = :id";
		return self::ejecutarQuery($sql, ["id" => $id]) !== false ? "ok" : "error";
	}

	static public function mdlActualizarCampo($tabla, $campo, $valorCampo, $id) {
		$sql = "UPDATE $tabla SET $campo = :$campo WHERE id = :id";
		return self::ejecutarQuery($sql, [$campo => $valorCampo, "id" => $id]) !== false ? "ok" : "error";
	}

	static public function mdlMostrarSumaVentas($tabla) {
		$sql = "SELECT SUM(ventas) as total FROM $tabla";
		return self::ejecutarQuery($sql, [], "one");
	}

	static public function mdlActualizarStockProducto($tabla, $id, $nuevoStock){
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET stock = :stock WHERE id = :id");
		$stmt->bindParam(":stock", $nuevoStock, PDO::PARAM_INT);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		if($stmt->execute()){ return "ok"; }else{ return "error";	}
		$stmt -> close(); $stmt = null;
	}

	static public function mdlObtenerUltimoCodigo($tabla){
		$stmt = Conexion::conectar()->prepare("SELECT MAX(CAST(codigo AS UNSIGNED)) as ultimo_codigo FROM $tabla");
		$stmt->execute();
		return $stmt->fetch();
		$stmt->close(); $stmt = null;
	}
}