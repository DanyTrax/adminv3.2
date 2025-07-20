$(".tablas").on("click", ".btnImprimirCotizacion", function () {

	var codigoVenta = $(this).attr("codigoVenta");

	window.open("pdf/cotizacion.php?codigo=" + codigoVenta, "_blank");

})

$(".tablas").on("click", ".btnEditarCotizacion", function () {

	var idVenta = $(this).attr("idVenta");

	window.location = "index.php?ruta=editar-cotizacion&idCotizacion=" + idVenta;


})

$(".tablas").on("click", ".btnEliminarCotizacion", function () {

	var idVenta = $(this).attr("idCotizacion");

	swal({
		title: '¿Está seguro de borrar la cotizacion?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar cotizacion!'
	}).then(function (result) {
		if (result.value) {

			window.location = "index.php?ruta=cotizacion&idCotizacion=" + idVenta;
		}

	})

})

$('#filter-medioPago-contabilidad').on('change', function () {
	let val = $(this).val();
	if (val === '') {
		window.location = 'contabilidad'
	} else {
		window.location = `index.php?ruta=contabilidad&medioPago=${val}`;
	}
})

$('#filter-formaPago-contabilidad').on('change', function () {
	let val = $(this).val();
	if (val === '') {
		window.location = 'contabilidad'
	} else {
		window.location = `index.php?ruta=contabilidad&formaPago=${val}`;
	}
})




$(".tablas").on("click", ".btnEditarGasto", function () {

	var idVenta = $(this).attr("idGasto");

	window.location = "index.php?ruta=editar-gasto&id=" + idVenta;


})

$(".tablas").on("click", ".btnEliminarGasto", function () {

	var idVenta = $(this).attr("idGasto");

	swal({
		title: '¿Está seguro de borrar la gasto?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar gasto!'
	}).then(function (result) {
		if (result.value) {

			window.location = "index.php?ruta=gastos&id=" + idVenta;
		}

	})

})

$('#filter-medioPago-gasto').on('change', function () {
	let val = $(this).val();
	if (val === '') {
		window.location = 'gastos'
	} else {
		window.location = `index.php?ruta=gastos&medioPago=${val}`;
	}
})

$('#daterange-btn-gasto').daterangepicker(
	{
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
	function (start, end) {
		$('#daterange-btn-gasto span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

		var fechaInicial = start.format('YYYY-MM-DD');

		var fechaFinal = end.format('YYYY-MM-DD');

		var capturarRango = $("#daterange-btn-gasto span").html();

		localStorage.setItem("capturarRango", capturarRango);

		window.location = "index.php?ruta=gastos&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;

	}

)

$(".tablas").on("click", ".btnEditarEntrada", function () {

	var idVenta = $(this).attr("idEntrada");

	window.location = "index.php?ruta=editar-entrada&id=" + idVenta;


})

$(".tablas").on("click", ".btnEliminarEntrada", function () {

	var idVenta = $(this).attr("idEntrada");

	swal({
		title: '¿Está seguro de borrar la entrada?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar entrada!'
	}).then(function (result) {
		if (result.value) {

			window.location = "index.php?ruta=entradas&id=" + idVenta;
		}

	})

})

$('#filter-medioPago-entrada').on('change', function () {
	let val = $(this).val();
	if (val === '') {
		window.location = 'entradas'
	} else {
		window.location = `index.php?ruta=entradas&medioPago=${val}`;
	}
})

$('#daterange-btn-entrada').daterangepicker(
	{
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
	function (start, end) {
		$('#daterange-btn-entrada span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

		var fechaInicial = start.format('YYYY-MM-DD');

		var fechaFinal = end.format('YYYY-MM-DD');

		var capturarRango = $("#daterange-btn-entrada span").html();

		localStorage.setItem("capturarRango", capturarRango);

		window.location = "index.php?ruta=entradas&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;

	}

)