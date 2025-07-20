/*=============================================
CARGAR LA TABLA DINÁMICA DE VENTAS
=============================================*/

$('.tablaVentas').DataTable({
	"ajax": "ajax/datatable-ventas.ajax.php",
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
AGREGANDO PRODUCTOS A LA VENTA DESDE LA TABLA
=============================================*/
$(".tablaVentas tbody").on("click", "button.agregarProducto", function() {
	var idProducto = $(this).attr("idProducto");
	$(this).removeClass("btn-primary agregarProducto");
	$(this).addClass("btn-default");
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
			var descripcion = respuesta["descripcion"];
			var stock = respuesta["stock"];
			var precio = respuesta["precio_venta"];
			if (stock == 0) {
				swal({
					title: "No hay stock disponible",
					type: "error",
					confirmButtonText: "¡Cerrar!"
				});
				$("button[idProducto='" + idProducto + "']").addClass("btn-primary agregarProducto");
				return;
			}
			$(".nuevoProducto").append(
				'<div class="row" style="padding:5px 15px">' +
				'<div class="col-xs-6" style="padding-right:0px">' +
				'<div class="input-group">' +
				'<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProducto" idProducto="' + idProducto + '"><i class="fa fa-times"></i></button></span>' +
				'<input type="text" class="form-control nuevaDescripcionProducto" idProducto="' + idProducto + '" name="agregarProducto" value="' + descripcion + '" readonly required>' +
				'</div>' +
				'</div>' +
				'<div class="col-xs-3">' +
				'<input type="number" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="1" stock="' + stock + '" nuevoStock="' + Number(stock - 1) + '" required>' +
				'</div>' +
				'<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">' +
				'<div class="input-group">' +
				'<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>' +
				'<input type="text" class="form-control nuevoPrecioProducto" precioReal="' + precio + '" name="nuevoPrecioProducto" value="' + precio + '" readonly >' +
				'</div>' +
				'</div>' +
				'</div>');
			sumarTotalPrecios();
			agregarImpuesto();
			listarProductos();
			$(".nuevoPrecioProducto").number(true);
			localStorage.removeItem("quitarProducto");
		}
	})
});

/*=============================================
CUANDO CARGUE LA TABLA CADA VEZ QUE NAVEGUE EN ELLA
=============================================*/
$(".tablaVentas").on("draw.dt", function() {
	if (localStorage.getItem("quitarProducto") != null) {
		var listaIdProductos = JSON.parse(localStorage.getItem("quitarProducto"));
		for (var i = 0; i < listaIdProductos.length; i++) {
			$("button.recuperarBoton[idProducto='" + listaIdProductos[i]["idProducto"] + "']").removeClass('btn-default');
			$("button.recuperarBoton[idProducto='" + listaIdProductos[i]["idProducto"] + "']").addClass('btn-primary agregarProducto');
		}
	}
});

/*=============================================
QUITAR PRODUCTOS DE LA VENTA Y RECUPERAR BOTÓN
=============================================*/
var idQuitarProducto = [];
localStorage.removeItem("quitarProducto");
$(".formularioVenta").on("click", "button.quitarProducto", function() {
	$(this).parent().parent().parent().parent().remove();
	var idProducto = $(this).attr("idProducto");
	if (localStorage.getItem("quitarProducto") == null) {
		idQuitarProducto = [];
	} else {
		idQuitarProducto.concat(localStorage.getItem("quitarProducto"))
	}
	idQuitarProducto.push({
		"idProducto": idProducto
	});
	localStorage.setItem("quitarProducto", JSON.stringify(idQuitarProducto));
	$("button.recuperarBoton[idProducto='" + idProducto + "']").removeClass('btn-default');
	$("button.recuperarBoton[idProducto='" + idProducto + "']").addClass('btn-primary agregarProducto');
	if ($(".nuevoProducto").children().length == 0) {
		$("#nuevoImpuestoVenta").val(0);
		$("#nuevoDescuentoVenta").val(0);
		$("#nuevoTotalVenta").val(0);
		$("#totalVenta").val(0);
		$("#nuevoTotalVenta").attr("total", 0);
	} else {
		sumarTotalPrecios();
		agregarImpuesto();
		listarProductos();
	}
});

/*=============================================
AGREGANDO PRODUCTOS DESDE EL BOTÓN PARA DISPOSITIVOS
=============================================*/
var numProducto = 0;
$(".btnAgregarProducto").click(function() {
	numProducto++;
	var datos = new FormData();
	datos.append("traerProductos", "ok");
	$.ajax({
		url: "ajax/productos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta) {
			$(".nuevoProducto").append(
				'<div class="row" style="padding:5px 15px">' +
				'<div class="col-xs-6" style="padding-right:0px">' +
				'<div class="input-group">' +
				'<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProducto" idProducto><i class="fa fa-times"></i></button></span>' +
				'<select class="form-control nuevaDescripcionProducto" id="producto' + numProducto + '" idProducto name="nuevaDescripcionProducto" required>' +
				'<option>Seleccione el producto</option>' +
				'</select>' +
				'</div>' +
				'</div>' +
				'<div class="col-xs-3 ingresoCantidad">' +
				'<input type="number" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="0" stock nuevoStock required>' +
				'</div>' +
				'<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">' +
				'<div class="input-group">' +
				'<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>' +
				'<input type="text" class="form-control nuevoPrecioProducto" precioReal="" name="nuevoPrecioProducto" readonly required>' +
				'</div>' +
				'</div>' +
				'</div>');
			respuesta.forEach(funcionForEach);
			function funcionForEach(item, index) {
				if (item.stock != 0) {
					$("#producto" + numProducto).append(
						'<option idProducto="' + item.id + '" value="' + item.descripcion + '">' + item.descripcion + '</option>'
					)
				}
			}
			sumarTotalPrecios();
			agregarImpuesto();
			$(".nuevoPrecioProducto").number(true);
		}
	})
});

/*=============================================
AGREGANDO PRODUCTOS DESDE EL BOTÓN PARA DISPOSITIVOS
=============================================*/
$(document).on("click", ".btnAgregarProducto1", function() {
	$(".nuevoProducto").append(
		'<div class="row" style="padding:5px 15px">' +
		'<div class="col-xs-6" style="padding-right:0px">' +
		'<div class="input-group">' +
		'<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProducto"><i class="fa fa-times"></i></button></span>' +
		'<input type="text" class="form-control nuevaDescripcionProducto" id="producto" name="nuevaDescripcionProducto" required>' +
		'</div>' +
		'</div>' +
		'<div class="col-xs-3 ingresoCantidad">' +
		'<input type="number" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" required>' +
		'</div>' +
		'<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">' +
		'<div class="input-group">' +
		'<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>' +
		'<input type="text" class="form-control nuevoPrecioProducto" precioReal="" name="nuevoPrecioProducto" required>' +
		'</div>' +
		'</div>' +
		'</div>');
});

/*=============================================
SELECCIONAR PRODUCTO
=============================================*/
$(".formularioVenta").on("change", "select.nuevaDescripcionProducto", function() {
	var nombreProducto = $(this).val();
	var nuevaDescripcionProducto = $(this).parent().parent().parent().children().children().children(".nuevaDescripcionProducto");
	var nuevoPrecioProducto = $(this).parent().parent().parent().children(".ingresoPrecio").children().children(".nuevoPrecioProducto");
	var nuevaCantidadProducto = $(this).parent().parent().parent().children(".ingresoCantidad").children(".nuevaCantidadProducto");
	var datos = new FormData();
	datos.append("nombreProducto", nombreProducto);
	$.ajax({
		url: "ajax/productos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta) {
			$(nuevaDescripcionProducto).attr("idProducto", respuesta["id"]);
			$(nuevaCantidadProducto).attr("stock", respuesta["stock"]);
			$(nuevaCantidadProducto).attr("nuevoStock", Number(respuesta["stock"]) - 1);
			$(nuevoPrecioProducto).val(respuesta["precio_venta"]);
			$(nuevoPrecioProducto).attr("precioReal", respuesta["precio_venta"]);
			listarProductos();
		}
	})
});

// MODIFICACIÓN DE LA DESCRIPCIÓN
$(document).on("change", ".nuevaDescripcionProducto", function() {
	sumarTotalPrecios();
	agregarImpuesto();
	listarProductos();
});

/*=============================================
MODIFICAR LA CANTIDAD
=============================================*/
$(".formularioVenta").on("change", "input.nuevaCantidadProducto", function() {
	var precio = $(this).parent().parent().children(".ingresoPrecio").children().children(".nuevoPrecioProducto");
	var precioFinal = $(this).val() * precio.attr("precioReal");
	precio.val(precioFinal);
	var nuevoStock = Number($(this).attr("stock")) - $(this).val();
	$(this).attr("nuevoStock", nuevoStock);
	if (Number($(this).val()) > Number($(this).attr("stock"))) {
		$(this).val(0);
		$(this).attr("nuevoStock", $(this).attr("stock"));
		var precioFinal = $(this).val() * precio.attr("precioReal");
		precio.val(precioFinal);
		sumarTotalPrecios();
		swal({
			title: "La cantidad supera el Stock",
			text: "¡Sólo hay " + $(this).attr("stock") + " unidades!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});
		return;
	}
	sumarTotalPrecios();
	agregarImpuesto();
	listarProductos();
});

// MODIFICACIÓN DEL PRECIO
$(document).on("change", ".nuevoPrecioProducto", function() {
	var precio = $(this).val();
	$(this).attr("precioReal", precio);
	$(this).parent().parent().parent().children(".ingresoCantidad").children(".nuevaCantidadProducto").val(1);
	sumarTotalPrecios();
	agregarImpuesto();
	listarProductos();
});

/*=============================================
SUMAR TODOS LOS PRECIOS
=============================================*/
function sumarTotalPrecios() {
	var precioItem = $(".nuevoPrecioProducto");
	var arraySumaPrecio = [];
	for (var i = 0; i < precioItem.length; i++) {
		arraySumaPrecio.push(Number($(precioItem[i]).val()));
	}
	function sumaArrayPrecios(total, numero) {
		return total + numero;
	}
	var sumaTotalPrecio = arraySumaPrecio.reduce(sumaArrayPrecios);
	$("#nuevoTotalVenta").val(sumaTotalPrecio);
	$("#totalVenta").val(sumaTotalPrecio);
	$("#nuevoTotalVenta").attr("total", sumaTotalPrecio);
}

/*=============================================
FUNCIÓN AGREGAR IMPUESTO
=============================================*/
function agregarImpuesto() {
	var impuesto = $("#nuevoImpuestoVenta").val();
	var precioTotal = $("#nuevoTotalVenta").attr("total");
	let descuento = $("#nuevoDescuentoVenta").val();
	let precioDescuento = Number(precioTotal * descuento / 100);
	let totalConDescuento = Number(precioTotal) - Number(precioDescuento);
	var precioImpuesto = Number(totalConDescuento * impuesto / 100);
	var totalConImpuesto = Number(precioImpuesto) + Number(totalConDescuento);
	$("#nuevoTotalVenta").val(totalConImpuesto);
	$("#totalVenta").val(totalConImpuesto);
	$("#nuevoPrecioImpuesto").val(precioImpuesto);
	$("#nuevoPrecioNeto").val(totalConDescuento);
	$("#nuevoPrecioDescuento").val(precioDescuento);
}

/*=============================================
CUANDO CAMBIA EL IMPUESTO
=============================================*/
$("#nuevoImpuestoVenta").change(function() {
	agregarImpuesto();
});
$("#nuevoDescuentoVenta").change(function() {
	agregarImpuesto();
});

/*=============================================
FORMATO AL PRECIO FINAL
=============================================*/
$("#nuevoTotalVenta").number(true);
$(".nuevoAbono").number(true);

/*=============================================
SELECCIONAR MÉTODO DE PAGO (CON VALIDACIÓN EN TIEMPO REAL)
=============================================*/
$(document).on("change", "#nuevoMetodoPago", function() {
	let medioPago = `
		<div class="input-group">
			<select class="form-control" id="nuevoMedioPago" name="nuevoMedioPago" required>
				<option value="">Seleccione medio de pago</option>
				<option value="Nequi">Nequi</option>
				<option value="DaviPlata">DaviPlata</option>
				<option value="Bancolombia Infinito">Bancolombia Infinito</option>
				<option value="Bancolombia Personal">Bancolombia Personal</option>
				<option value="Davivienda Infinito">Davivienda Infinito</option>
				<option value="Davivienda Personal">Davivienda Personal</option>
				<option value="Efectivo">Efectivo</option>
				<option value="Efectivo Lenin">Efectivo Lenin</option>
				<option value="Efectivo Marcela">Efectivo Marcela</option>
				<option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
				<option value="Datafono">Datafono</option>
			</select>
		</div>
	`;

	if ($(this).val() == "Completo") {
		$(".cajasMetodoPago").empty();
		$(this).parent().parent().parent().children(".cajasMetodoPago").html('<div class="col-xs-6">' +
			'<div class="input-group">' +
			'<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>' +
			'<input type="text" class="form-control nuevoValorEfectivo" value="' + $("#nuevoTotalVenta").val() + '" disabled>' +
			'</div>' +
			'</div>');
		$('.nuevoValorEfectivo').number(true);
		$('.divNuevoMetodoPago').html(medioPago);
	} else if ($(this).val() == "Abono") {
		$(".cajasMetodoPago").empty();
		$(this).parent().parent().parent().children('.cajasMetodoPago').html('<div class="col-xs-6">' +
			'<div class="input-group">' +
			'<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>' +
			'<input type="text" class="form-control nuevoValorEfectivo" name="nuevoValorEfectivo" placeholder="Ingrese el abono" required>' +
			'</div>' +
			'</div>');
		$('.nuevoValorEfectivo').number(true);
		$('.divNuevoMetodoPago').html(medioPago);

        // --- INICIO DE LA VALIDACIÓN AÑADIDA ---
        $(".formularioVenta").off("keyup change", "input.nuevoValorEfectivo").on("keyup change", "input.nuevoValorEfectivo", function(){
            
            // Se leen los valores y se eliminan los puntos de miles
            var abono = Number($(this).val().replace(/\./g, ''));
            var totalVenta = Number($("#totalVenta").val().replace(/\./g, ''));

            // 1. VERIFICA QUE EL ABONO NO SEA NEGATIVO
            if(abono < 0){
                $(this).val(0);
                swal({ title: "Error", text: "El abono no puede ser negativo", type: "error", confirmButtonText: "Cerrar" });
                return;
            }

            // 2. VERIFICA QUE EL ABONO NO SUPERE EL TOTAL
            if(abono > totalVenta){
                // Se corrige el valor al máximo permitido y se le vuelve a dar formato
                $(this).val(totalVenta).number(true, 0, ',', '.');
                swal({ title: "Error", text: "El abono no puede ser mayor que el total", type: "error", confirmButtonText: "Cerrar" });
            }
        });
        // --- FIN DE LA VALIDACIÓN AÑADIDA ---

	} else {
		$(".cajasMetodoPago").empty();
		$('.divNuevoMetodoPago').empty();
        // Se desactiva la validación si se cambia a "Se Debe"
        $(".formularioVenta").off("keyup change", "input.nuevoValorEfectivo");
	}
});
/*=============================================
LISTAR TODOS LOS PRODUCTOS
=============================================*/
function listarProductos() {
	var listaProductos = [];
	var descripcion = $(".nuevaDescripcionProducto");
	var cantidad = $(".nuevaCantidadProducto");
	var precio = $(".nuevoPrecioProducto");
	for (var i = 0; i < descripcion.length; i++) {
		if ($(descripcion[i]).attr("idProducto") != undefined) {
			listaProductos.push({
				"id": $(descripcion[i]).attr("idProducto"),
				"descripcion": $(descripcion[i]).val(),
				"cantidad": $(cantidad[i]).val(),
				"stock": $(cantidad[i]).attr("nuevoStock"),
				"precio": $(precio[i]).attr("precioReal"),
				"total": $(precio[i]).val()
			})
		} else {
			listaProductos.push({
				"id": "libre",
				"descripcion": $(descripcion[i]).val(),
				"cantidad": $(cantidad[i]).val(),
				"stock": 0,
				"precio": $(precio[i]).attr("precioReal"),
				"total": $(precio[i]).val()
			})
		}
	}
	$("#listaProductos").val(JSON.stringify(listaProductos));
}

/*=============================================
BOTON EDITAR VENTA
=============================================*/
$(".tablas").on("click", ".btnEditarVenta", function() {
	var idVenta = $(this).attr("idVenta");
	window.location = "index.php?ruta=editar-venta&idVenta=" + idVenta;
});

/*=============================================
FUNCIÓN PARA DESACTIVAR LOS BOTONES AGREGAR CUANDO EL PRODUCTO YA HABÍA SIDO SELECCIONADO EN LA CARPETA
=============================================*/
function quitarAgregarProducto() {
	var idProductos = $(".quitarProducto");
	var botonesTabla = $(".tablaVentas tbody button.agregarProducto");
	for (var i = 0; i < idProductos.length; i++) {
		var boton = $(idProductos[i]).attr("idProducto");
		for (var j = 0; j < botonesTabla.length; j++) {
			if ($(botonesTabla[j]).attr("idProducto") == boton) {
				$(botonesTabla[j]).removeClass("btn-primary agregarProducto");
				$(botonesTabla[j]).addClass("btn-default");
			}
		}
	}
}

/*=============================================
CADA VEZ QUE CARGUE LA TABLA CUANDO NAVEGAMOS EN ELLA EJECUTAR LA FUNCIÓN:
=============================================*/
$('.tablaVentas').on('draw.dt', function() {
	quitarAgregarProducto();
});

/*=============================================
BORRAR Venta
=============================================*/
$(".tablas").on("click", ".btnEliminarVenta", function() {
	var idVenta = $(this).attr("idVenta");
	swal({
		title: '¿Está seguro de borrar la venta?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar venta!'
	}).then(function(result) {
		if (result.value) {
			window.location = "index.php?ruta=ventas&idVenta=" + idVenta;
		}
	})
});

/*=============================================
IMPRIMIR FACTURA
=============================================*/
$(".tablas").on("click", ".btnImprimirFactura", function() {
	var codigoVenta = $(this).attr("codigoVenta");
	window.open("extensiones/tcpdf/pdf/factura.php?codigo=" + codigoVenta, "_blank");
});

/*=============================================
RANGO DE FECHAS
=============================================*/
$('#daterange-btn').daterangepicker({
	ranges: {
		'Hoy': [moment(), moment()],
		'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
		'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
		'Este mes': [moment().startOf('month'), moment().endOf('month')],
		'Último mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	},
	startDate: moment(),
	endDate: moment()
},
	function(start, end) {
		$('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		var fechaInicial = start.format('YYYY-MM-DD');
		var fechaFinal = end.format('YYYY-MM-DD');
		var capturarRango = $("#daterange-btn span").html();
		localStorage.setItem("capturarRango", capturarRango);
		window.location = "index.php?ruta=ventas&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;
	}
);



/*=============================================
CAPTURAR HOY
=============================================*/
$(".daterangepicker.opensleft .ranges li").on("click", function() {
	var textoHoy = $(this).attr("data-range-key");
	if (textoHoy == "Hoy") {
		var d = new Date();
		var dia = d.getDate();
		var mes = d.getMonth() + 1;
		var anio = d.getFullYear();
		dia = ("0" + dia).slice(-2);
		mes = ("0" + mes).slice(-2);
		var fechaInicial = anio + "-" + mes + "-" + dia;
		var fechaFinal = anio + "-" + mes + "-" + dia;
		localStorage.setItem("capturarRango", "Hoy");
		window.location = "index.php?ruta=ventas&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;
	}
});

/*=============================================
ABRIR ARCHIVO XML EN NUEVA PESTAÑA
=============================================*/
$(".abrirXML").click(function() {
	var archivo = $(this).attr("archivo");
	window.open(archivo, "_blank");
});

// Abonos
$(document).on("click", ".btnAbonar", function() {
	var idVenta = $(this).attr("idVenta");
	var idUsu = $(this).attr("idUsuarioAbo");
	var datos = new FormData();
	datos.append("idVenta", idVenta);
	$.ajax({
		url: "ajax/ventas.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta) {
			var restante = Number(respuesta["total"]) - Number(respuesta["abono"]);
			$(".dinRestante").val(restante);
			$('.dinRestante').number(true);
			$(".idVentaAbo").val(respuesta["id"]);
			$(".idUsuarioAbo").val(idUsu);
		}
	});
});

$('#filter-medioPago').on('change', function() {
	let val = $(this).val();
	if (val === '') {
		window.location = 'ventas'
	} else {
		window.location = `index.php?ruta=ventas&medioPago=${val}`;
	}
});

$('#filter-formaPago').on('change', function() {
	let val = $(this).val();
	if (val === '') {
		window.location = 'ventas'
	} else {
		window.location = `index.php?ruta=ventas&formaPago=${val}`;
	}
});