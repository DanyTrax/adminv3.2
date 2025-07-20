<?php

class ControladorCategorias{

	/*=============================================
	CREAR CATEGORIAS
	=============================================*/
	static public function ctrCrearCategoria(){

		if(isset($_POST["nuevaCategoria"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaCategoria"])){

				$tabla = "categorias";
				$datos = $_POST["nuevaCategoria"];
				$respuesta = ModeloCategorias::mdlIngresarCategoria($tabla, $datos);

				if($respuesta == "ok"){
					echo'<script>
					swal({
						  type: "success",
						  title: "La categoría ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {
									window.location = "categorias";
									}
								})
					</script>';
				}

			}else{
				echo'<script>
					swal({
						  type: "error",
						  title: "¡La categoría no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {
							window.location = "categorias";
							}
						})
			  	</script>';
			}
		}
	}

	/*=============================================
	MOSTRAR CATEGORIAS
	=============================================*/
	static public function ctrMostrarCategorias($item, $valor){
		$tabla = "categorias";
		$respuesta = ModeloCategorias::mdlMostrarCategorias($tabla, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	EDITAR CATEGORIA
	=============================================*/
	static public function ctrEditarCategoria(){

		if(isset($_POST["editarCategoria"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarCategoria"])){

				$tabla = "categorias";
				$datos = array("categoria"=>$_POST["editarCategoria"],
							   "id"=>$_POST["idCategoria"]);
				$respuesta = ModeloCategorias::mdlEditarCategoria($tabla, $datos);

				if($respuesta == "ok"){
					echo'<script>
					swal({
						  type: "success",
						  title: "La categoría ha sido cambiada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {
									window.location = "categorias";
									}
								})
					</script>';
				}

			}else{
				echo'<script>
					swal({
						  type: "error",
						  title: "¡La categoría no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {
							window.location = "categorias";
							}
						})
			  	</script>';
			}
		}
	}

	/*=============================================
	BORRAR CATEGORIA (VERSIÓN FINAL PARA AJAX)
	=============================================*/
	static public function ctrBorrarCategoria($idCategoria){

		// 1. Verificar si la categoría tiene productos
		// Esta es la llamada a la función separada que creamos para no causar conflictos.
		$productosAsociados = ModeloProductos::mdlContarProductosPorCategoria("productos", "id_categoria", $idCategoria);

		// 2. Si el array de productos está vacío, proceder a borrar
		if(empty($productosAsociados)){
			$tabla = "categorias";
			$datos = $idCategoria;
			$respuesta = ModeloCategorias::mdlBorrarCategoria($tabla, $datos);
			return $respuesta; // Debería retornar "ok" desde el modelo
		} else {
			// 3. Si tiene productos, retornar un error
			return "error_con_productos";
		}
	}
}