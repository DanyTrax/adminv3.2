$(document).ready(function() {

    /*=============================================
    CARGAR LA TABLA DINÁMICA DE PRODUCTOS
    =============================================*/
    var perfilOculto = $("#perfilOculto").val();
    $('.tablaProductos').DataTable({
        "ajax": "ajax/datatable-productos.ajax.php?perfilOculto=" + perfilOculto,
        "deferRender": true,
        "retrieve": true,
        "processing": true,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });

    /*=============================================
    CAMBIO 1: SE ELIMINA TODA LA LÓGICA DE CÁLCULO DE PRECIOS
    =============================================*/
    // $("#nuevoPrecioCompra, #editarPrecioCompra, .nuevoPorcentaje").change(function() { ... });
    // $(".porcentaje").on("ifUnchecked", function() { ... });
    // $(".porcentaje").on("ifChecked", function() { ... });
    // Todo ese bloque ha sido eliminado.

    /*=============================================
    SUBIENDO LA FOTO DEL PRODUCTO (SIN CAMBIOS)
    =============================================*/
    $(".nuevaImagen").change(function() {
        var imagen = this.files[0];
        if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
            $(".nuevaImagen").val("");
            swal({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        } else if (imagen["size"] > 2000000) {
            $(".nuevaImagen").val("");
            swal({
                title: "Error al subir la imagen",
                text: "¡La imagen no debe pesar más de 2MB!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        } else {
            var datosImagen = new FileReader;
            datosImagen.readAsDataURL(imagen);
            $(datosImagen).on("load", function(event) {
                var rutaImagen = event.target.result;
                $(".previsualizar").attr("src", rutaImagen);
            });
        }
    });

    /*=============================================
    EDITAR PRODUCTO
    =============================================*/
    $(".tablaProductos tbody").on("click", "button.btnEditarProducto", function() {
        var idProducto = $(this).attr("idProducto");
        var datos = new FormData();
        datos.append("idProducto", idProducto);
        $.ajax({
            url: "ajax/productos.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(respuesta) {
                
                setTimeout(function() {

                    var datosCategoria = new FormData();
                    datosCategoria.append("idCategoria", respuesta["id_categoria"]);
                    $.ajax({
                        url: "ajax/categorias.ajax.php",
                        method: "POST",
                        data: datosCategoria,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: "json",
                        success: function(respuesta) {
                            $("#editarCategoria").val(respuesta["id"]);
                            $("#editarCategoria").html(respuesta["categoria"]);
                        }
                    });
                    $("#editarCodigo").val(respuesta["codigo"]);
                    $("#editarDescripcion").val(respuesta["descripcion"]);
                    $("#editarStock").val(respuesta["stock"]);

                    /*=============================================
                    CAMBIO 2: SE ELIMINA LA LÍNEA QUE LLENA EL CAMPO "Precio de Compra"
                    =============================================*/
                    // $("#editarPrecioCompra").val(respuesta["precio_compra"]); // <-- LÍNEA ELIMINADA

                    $("#editarPrecioVenta").val(respuesta["precio_venta"]);
                    if (respuesta["imagen"] != "") {
                        $("#imagenActual").val(respuesta["imagen"]);
                        $(".previsualizar").attr("src", respuesta["imagen"]);
                    }

                    /*=============================================
                    CARGAR DATOS DE PRODUCTO DIVISIBLE AL EDITAR (SIN CAMBIOS)
                    =============================================*/
                    if (respuesta["es_divisible"] == 1) {
                        $('#esDivisibleEditar').prop('checked', true);
                        
                        $("#nombreMitadEditar").val(respuesta["nombre_mitad"]);
                        $("#precioMitadEditar").val(respuesta["precio_mitad"]);
                        $("#nombreTercioEditar").val(respuesta["nombre_tercio"]);
                        $("#precioTercioEditar").val(respuesta["precio_tercio"]);
                        $("#nombreCuartoEditar").val(respuesta["nombre_cuarto"]);
                        $("#precioCuartoEditar").val(respuesta["precio_cuarto"]);

                        $('#camposDivisiblesEditar').show();
                    } else {
                        $('#esDivisibleEditar').prop('checked', false);
                        $('#camposDivisiblesEditar').hide();
                        $('#camposDivisiblesEditar input').val('');
                    }

                }, 50);

            }
        });
    });

    /*=============================================
    ELIMINAR PRODUCTO (SIN CAMBIOS)
    =============================================*/
    $(".tablaProductos tbody").on("click", "button.btnEliminarProducto", function() {
        var idProducto = $(this).attr("idProducto");
        var codigo = $(this).attr("codigo");
        var imagen = $(this).attr("imagen");
        swal({
            title: '¿Está seguro de borrar el producto?',
            text: "¡Si no lo está puede cancelar la accíón!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, borrar producto!'
        }).then(function(result) {
            if (result.value) {
                window.location = "index.php?ruta=productos&idProducto=" + idProducto + "&imagen=" + imagen + "&codigo=" + codigo;
            }
        });
    });
    
    /*=============================================
    DIVIDIR PRODUCTO (SIN CAMBIOS)
    =============================================*/
    $(".tablaProductos tbody").on("click", "button.btnDividirProducto", function() {

        var idProducto = $(this).attr("idProducto");

        swal({
            title: '¿Cómo deseas partir el producto?',
            text: 'Se descontará 1 unidad del stock del producto original.',
            type: 'info',
            html:
                '<select id="swal-select1" class="swal2-input">' +
                '<option value="mitad">En 2 Mitades</option>' +
                '<option value="tercio">En 3 Tercios</option>' + 
                '<option value="cuarto">En 4 Cuartos</option>' +
                '</select>',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: '¡Sí, partir!',
            preConfirm: function() {
                return document.getElementById('swal-select1').value
            }
        }).then(function(result) {
            
            if (result.value) {

                var tipoDivision = result.value;

                var datos = new FormData();
                datos.append("idProductoDividir", idProducto);
                datos.append("tipoDivision", tipoDivision);

                $.ajax({
                    url: "ajax/productos.ajax.php",
                    method: "POST",
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(respuesta) {
                        
                        if(respuesta.trim() == "ok"){

                            swal({
                              type: "success",
                              title: "¡El producto ha sido partido correctamente!",
                              showConfirmButton: true,
                              confirmButtonText: "Cerrar"
                            }).then(function(result){
                              if (result.value) {
                                // Recargar la tabla para ver los cambios
                                $('.tablaProductos').DataTable().ajax.reload();
                              }
                            });

                        } else if (respuesta.trim() == "error_stock"){

                            swal({
                              type: "error",
                              title: "¡No hay stock!",
                              text: "No se puede partir un producto con stock en cero.",
                              showConfirmButton: true,
                              confirmButtonText: "Cerrar"
                            });

                        } else {

                            swal({
                              type: "error",
                              title: "Error",
                              text: "No se pudo partir el producto. Verifique que los nombres de las partes estén definidos.",
                              showConfirmButton: true,
                              confirmButtonText: "Cerrar"
                            });
                            
                        }
                    }
                });
            }
        });
    });
});