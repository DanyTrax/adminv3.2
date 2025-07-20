<?php

class ControladorProductos {

    static public function ctrMostrarProductos($item, $valor, $orden = "id") {
        return ModeloProductos::mdlMostrarProductos("productos", $item, $valor, $orden);
    }

    static public function ctrCrearProducto() {
        if (isset($_POST["nuevaDescripcion"])) {
            $tabla = "productos";
            $ruta = "vistas/img/productos/default/anonymous.png";
            if (isset($_FILES["nuevaImagen"]["tmp_name"]) && !empty($_FILES["nuevaImagen"]["tmp_name"])) {
                list($ancho, $alto) = getimagesize($_FILES["nuevaImagen"]["tmp_name"]);
                $nuevoAncho = 500; $nuevoAlto = 500;
                $directorio = "vistas/img/productos/" . $_POST["nuevoCodigo"];
                if(!is_dir($directorio)){ mkdir($directorio, 0755); }
                if ($_FILES["nuevaImagen"]["type"] == "image/jpeg") {
                    $aleatorio = mt_rand(100, 999);
                    $ruta = "vistas/img/productos/" . $_POST["nuevoCodigo"] . "/" . $aleatorio . ".jpg";
                    $origen = imagecreatefromjpeg($_FILES["nuevaImagen"]["tmp_name"]);
                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                    imagejpeg($destino, $ruta);
                }
                if ($_FILES["nuevaImagen"]["type"] == "image/png") {
                    $aleatorio = mt_rand(100, 999);
                    $ruta = "vistas/img/productos/" . $_POST["nuevoCodigo"] . "/" . $aleatorio . ".png";
                    $origen = imagecreatefrompng($_FILES["nuevaImagen"]["tmp_name"]);
                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                    imagepng($destino, $ruta);
                }
            }
            
            $esDivisible = isset($_POST["esDivisible"]) ? 1 : 0;
            $datos = [
                "id_categoria"  => $_POST["nuevaCategoria"], "codigo" => $_POST["nuevoCodigo"],
                "descripcion"   => $_POST["nuevaDescripcion"], "stock" => $_POST["nuevoStock"],
                "precio_compra" => 0, "precio_venta"  => $_POST["nuevoPrecioVenta"],
                "imagen" => $ruta, "ventas" => 0, "es_divisible" => $esDivisible,
                "nombre_mitad"  => $esDivisible && !empty($_POST["nombreMitad"]) ? $_POST["nombreMitad"] : "", 
                "precio_mitad"  => $esDivisible && !empty($_POST["precioMitad"]) ? $_POST["precioMitad"] : 0,
                "nombre_tercio" => $esDivisible && !empty($_POST["nombreTercio"]) ? $_POST["nombreTercio"] : "", 
                "precio_tercio" => $esDivisible && !empty($_POST["precioTercio"]) ? $_POST["precioTercio"] : 0,
                "nombre_cuarto" => $esDivisible && !empty($_POST["nombreCuarto"]) ? $_POST["nombreCuarto"] : "", 
                "precio_cuarto" => $esDivisible && !empty($_POST["precioCuarto"]) ? $_POST["precioCuarto"] : 0
            ];
            $respuesta = ModeloProductos::mdlIngresarProducto($tabla, $datos);
            if ($respuesta == "ok") {
                self::mostrarAlerta("success", "El producto ha sido guardado correctamente", "productos");
            } else {
                self::mostrarAlerta("error", "Error al guardar el producto", "productos");
            }
        }
    }

    static public function ctrEditarProducto() {
        if (isset($_POST["editarCodigo"])) {

            $tabla = "productos";
            
            // 1. OBTENEMOS LOS DATOS DEL PADRE ANTES DE CUALQUIER CAMBIO
            $productoActual = ModeloProductos::mdlMostrarProductos($tabla, "codigo", $_POST["editarCodigo"], "DESC");
            
            $ruta = $_POST["imagenActual"];
            if (isset($_FILES["editarImagen"]["tmp_name"]) && !empty($_FILES["editarImagen"]["tmp_name"])) {
                // ... Lógica de imagen ...
            }
            
            $esDivisible = isset($_POST["esDivisible"]) ? 1 : 0;
            
            // 2. PREPARAMOS LOS DATOS PARA ACTUALIZAR AL PADRE
            $datosPadre = [
                "id"            => $productoActual["id"], // Usamos el ID para la actualización segura
                "codigo"        => $_POST["editarCodigo"],
                "id_categoria"  => $_POST["editarCategoria"],
                "descripcion"   => $_POST["editarDescripcion"],
                "stock"         => $_POST["editarStock"],
                "precio_compra" => $productoActual["precio_compra"], // Mantenemos el precio de compra original
                "precio_venta"  => $_POST["editarPrecioVenta"],
                "imagen"        => $ruta,
                "es_divisible"  => $esDivisible,
                "nombre_mitad"  => $esDivisible ? $_POST["nombreMitad"] : "",
                "precio_mitad"  => $esDivisible && !empty($_POST["precioMitad"]) ? $_POST["precioMitad"] : 0,
                "nombre_tercio" => $esDivisible ? $_POST["nombreTercio"] : "",
                "precio_tercio" => $esDivisible && !empty($_POST["precioTercio"]) ? $_POST["precioTercio"] : 0,
                "nombre_cuarto" => $esDivisible ? $_POST["nombreCuarto"] : "",
                "precio_cuarto" => $esDivisible && !empty($_POST["precioCuarto"]) ? $_POST["precioCuarto"] : 0
            ];
            
            // 3. ACTUALIZAMOS AL PADRE
            $respuestaPadre = ModeloProductos::mdlEditarProducto($tabla, $datosPadre, "id");
            
            if ($respuestaPadre == "ok") {
                // 4. SI EL PADRE SE ACTUALIZÓ BIEN, PROCEDEMOS CON LOS HIJOS
                if ($esDivisible == 1) {
                    $partes = [
                        ['nombre' => $_POST["nombreMitad"], 'precio' => $_POST["precioMitad"]],
                        ['nombre' => $_POST["nombreTercio"], 'precio' => $_POST["precioTercio"]],
                        ['nombre' => $_POST["nombreCuarto"], 'precio' => $_POST["precioCuarto"]]
                    ];

                    foreach ($partes as $parte) {
                        if (!empty($parte['nombre'])) {
                            $productoHijo = ModeloProductos::mdlMostrarProductos($tabla, "descripcion", $parte['nombre'], "DESC");
                            if ($productoHijo) {
                                // Preparamos un array solo con los datos a cambiar del hijo
                                $datosHijo = [
                                    "id" => $productoHijo["id"], // Identificador del hijo
                                    "precio_venta" => $parte['precio']
                                ];
                                // Llamamos al modelo para actualizar solo el precio de venta del hijo
                                ModeloProductos::mdlEditarProducto($tabla, $datosHijo, "id");
                            }
                        }
                    }
                }
                
                // 5. MOSTRAMOS LA ALERTA DE ÉXITO
                self::mostrarAlerta("success", "¡El producto ha sido modificado correctamente!", "productos");
            
            } else {
                self::mostrarAlerta("error", "Error al editar el producto", "productos");
            }
        }
    }
    
    static public function ctrEliminarProducto() {
        if (isset($_GET["idProducto"])) {
            $tabla = "productos";
            $datos = $_GET["idProducto"];
            if (!empty($_GET["imagen"]) && $_GET["imagen"] !== "vistas/img/productos/default/anonymous.png") {
                @unlink($_GET["imagen"]);
                @rmdir("vistas/img/productos/" . $_GET["codigo"]);
            }
            $respuesta = ModeloProductos::mdlEliminarProducto($tabla, $datos);
            if ($respuesta === "ok") {
                self::mostrarAlerta("success", "¡Producto eliminado correctamente!", "productos");
            }
        }
    }

    static public function ctrMostrarSumaVentas() {
        return ModeloProductos::mdlMostrarSumaVentas("productos");
    }

    static public function ctrDividirProducto(){
        if(isset($_POST["idProductoDividir"])){
            $tabla = "productos";
            $idProductoOriginal = $_POST["idProductoDividir"];
            $tipoDivision = $_POST["tipoDivision"];
            $productoOriginal = ModeloProductos::mdlMostrarProductos($tabla, "id", $idProductoOriginal, "DESC");
            if((int)$productoOriginal["stock"] < 1){
                echo "error_stock"; return; 
            }
            if($tipoDivision == "mitad"){
                $nuevaDescripcion = $productoOriginal["nombre_mitad"];
                $nuevoPrecioVenta = $productoOriginal["precio_mitad"];
                $cantidadResultante = 2;
            } else if($tipoDivision == "tercio"){
                $nuevaDescripcion = $productoOriginal["nombre_tercio"];
                $nuevoPrecioVenta = $productoOriginal["precio_tercio"];
                $cantidadResultante = 3;
            } else {
                $nuevaDescripcion = $productoOriginal["nombre_cuarto"];
                $nuevoPrecioVenta = $productoOriginal["precio_cuarto"];
                $cantidadResultante = 4;
            }
            if(empty($nuevaDescripcion)){
                echo "error_descripcion"; return;
            }
            $productoExistente = ModeloProductos::mdlMostrarProductos($tabla, "descripcion", $nuevaDescripcion, "DESC");
            if($productoExistente){
                $stockFinal = $productoExistente["stock"] + $cantidadResultante;
                ModeloProductos::mdlActualizarStockProducto($tabla, $productoExistente["id"], $stockFinal);
            } else {
                $ultimoCodigo = ModeloProductos::mdlObtenerUltimoCodigo($tabla);
                $nuevoCodigo = $ultimoCodigo["ultimo_codigo"] + 1;
                $datosNuevoProducto = array(
                    "parent_id"     => $idProductoOriginal,
                    "id_categoria"  => $productoOriginal["id_categoria"],
                    "codigo"        => $nuevoCodigo,
                    "descripcion"   => $nuevaDescripcion,
                    "imagen"        => $productoOriginal["imagen"],
                    "stock"         => $cantidadResultante,
                    "precio_compra" => $productoOriginal["precio_compra"] / $cantidadResultante,
                    "precio_venta"  => $nuevoPrecioVenta,
                    "es_divisible"  => 0,
                    "ventas"        => 0
                );
                ModeloProductos::mdlIngresarProducto($tabla, $datosNuevoProducto);
            }
            $stockOriginalFinal = $productoOriginal["stock"] - 1;
            ModeloProductos::mdlActualizarStockProducto($tabla, $idProductoOriginal, $stockOriginalFinal);
            echo "ok";
        }
    }

    private static function mostrarAlerta($tipo, $mensaje, $redir = "productos") {
         echo "<script>
            swal({
                type: '$tipo', title: '$mensaje',
                showConfirmButton: true, confirmButtonText: 'Cerrar'
            }).then(function(result){
                if (result.value) { window.location = '$redir'; }
            });
        </script>";
    }
}