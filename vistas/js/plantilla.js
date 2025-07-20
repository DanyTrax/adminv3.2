/*=============================================
SideBar Menu
=============================================*/

$('.sidebar-menu').tree()

/*=============================================
Data Table
=============================================*/

$(".tablas").DataTable({

	"language": {

		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
		"sFirst":    "Primero",
		"sLast":     "Último",
		"sNext":     "Siguiente",
		"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}

	}

});

/*=============================================
 //iCheck for checkbox and radio inputs
=============================================*/

$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
  checkboxClass: 'icheckbox_minimal-blue',
  radioClass   : 'iradio_minimal-blue'
})

/*=============================================
 //input Mask
=============================================*/

//Datemask dd/mm/yyyy
$('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
//Datemask2 mm/dd/yyyy
$('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
//Money Euro
$('[data-mask]').inputmask()

/*=============================================
CORRECCIÓN BOTONERAS OCULTAS BACKEND	
=============================================*/

if(window.matchMedia("(max-width:767px)").matches){
	
	$("body").removeClass('sidebar-collapse');

}else{

	$("body").addClass('sidebar-collapse');
}
/*=============================================
LIMPIAR CAMPO DE VALOR ANTES DE ENVIAR FORMULARIO
=============================================*/
// Esto se aplica a cualquier formulario con la clase .form-clean-valor
$("form.form-clean-valor").on("submit", function() {

    // Busca el campo de valor dentro del formulario que se está enviando
    var valorInput = $(this).find(".input-valor");

    // Si el campo existe...
    if (valorInput.length) {
        // Obtiene el valor formateado (ej: "1,000")
        var valorFormateado = valorInput.val();

        // Le quita TODAS las comas (usando una expresión regular global)
        var valorLimpio = valorFormateado.replace(/,/g, "");

        // Pone el valor limpio de vuelta en el campo antes de que el formulario se envíe
        valorInput.val(valorLimpio);
    }
});
/*=============================================
VALIDAR EL MONTO MÁXIMO DEL ABONO EN EL MODAL
=============================================*/
// Se activa cuando el modal de abonos se muestra en pantalla
$('#modalAbonar').on('shown.bs.modal', function() {

  // Capturamos el formulario del modal
  var form = $(this).find('form');

  // Capturamos los campos de valor
  var inputRestante = form.find('.dinRestante');
  var inputAbono = form.find('.nuevoAbono');

  // Guardamos el valor restante (limpio, sin comas)
  var valorRestante = parseFloat(inputRestante.val().replace(/,/g, ''));

  // Añadimos un evento al envío del formulario
  form.off('submit').on('submit', function(e) {

    // Obtenemos el valor del abono (limpio, sin comas)
    var valorAbono = parseFloat(inputAbono.val().replace(/,/g, ''));

    // Validamos que el abono no sea mayor a lo que se debe
    if (valorAbono > valorRestante) {

      // Prevenimos que el formulario se envíe
      e.preventDefault();

      swal({
        title: "Error en el Monto",
        text: "¡El abono no puede ser mayor al dinero restante!",
        type: "error",
        confirmButtonText: "¡Cerrar!"
      });

    } else if (valorAbono <= 0) {

        // Prevenimos que el formulario se envíe
        e.preventDefault();

        swal({
            title: "Error en el Monto",
            text: "¡El abono debe ser un valor mayor a cero!",
            type: "error",
            confirmButtonText: "¡Cerrar!"
        });
    }
  });
});
/*=============================================
Date range picker para el Dashboard
=============================================*/
$('#daterange-btn-dashboard').daterangepicker(
  {
    ranges   : {
      'Hoy'       : [moment(), moment()],
      'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días' : [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este mes'  : [moment().startOf('month'), moment().endOf('month')],
      'Mes anterior'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment(),
    endDate  : moment()
  },
  function (start, end) {
    $('#daterange-btn-dashboard span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    var fechaInicial = start.format('YYYY-MM-DD');
    var fechaFinal = end.format('YYYY-MM-DD');

    // Recargamos la página con los parámetros de fecha
    window.location = "index.php?ruta=inicio&fechaInicial="+fechaInicial+"&fechaFinal="+fechaFinal;
  }
)
$(document).ready(function() {

    /*=============================================
    FUNCIÓN GENÉRICA PARA FILTROS DE FECHA (BASADA EN TU CÓDIGO)
    =============================================*/
    function activarDateRangePicker(idBoton, ruta, localStorageKey) {

        // Si el botón con el ID específico existe en la página actual...
        if ($('#' + idBoton).length) {

            // Se lee el rango guardado para mantener el estado del botón
            if (localStorage.getItem(localStorageKey) != null) {
                $('#' + idBoton + ' span').html(localStorage.getItem(localStorageKey));
            } else {
                $('#' + idBoton + ' span').html('<i class="fa fa-calendar"></i> Rango de fecha');
            }

            // Se inicializa el calendario en el botón
            $('#' + idBoton).daterangepicker({
                ranges: {
                    'Hoy'           : [moment(), moment()],
                    'Ayer'          : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 días'  : [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
                    'Este mes'      : [moment().startOf('month'), moment().endOf('month')],
                    'Mes anterior'    : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment(),
                endDate: moment()
            },
            function(start, end) {
                var capturarRango = start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY');
                $('#' + idBoton + ' span').html(capturarRango);
                
                var fechaInicial = start.format('YYYY-MM-DD');
                var fechaFinal = end.format('YYYY-MM-DD');

                localStorage.setItem(localStorageKey, capturarRango);
                window.location = "index.php?ruta=" + ruta + "&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;
            });

            // Se maneja el botón de cancelar
            $('#' + idBoton).on('cancel.daterangepicker', function() {
                localStorage.removeItem(localStorageKey);
                window.location = ruta;
            });

            // Se captura el clic en los rangos predefinidos (como "Hoy")
            // Este es el bloque que te funcionó, ahora adaptado para ser genérico
            $('.daterangepicker').on('click', '.ranges li', function() {
                if ($('#' + idBoton).is(':visible')) { // Se asegura de que solo afecte al calendario activo
                    var textoOpcion = $(this).text();
                    var fechaInicial, fechaFinal;
                    
                    if (textoOpcion == "Hoy") {
                        var d = new Date();
                        fechaInicial = moment(d).format('YYYY-MM-DD');
                        fechaFinal = moment(d).format('YYYY-MM-DD');
                        localStorage.setItem(localStorageKey, "Hoy");
                        window.location = "index.php?ruta=" + ruta + "&fechaInicial=" + fechaInicial + "&fechaFinal=" + fechaFinal;
                    }
                }
            });
        }
    }

    // --- SE LLAMA A LA FUNCIÓN PARA CADA PÁGINA ---
    activarDateRangePicker('daterange-btn', 'ventas', 'capturarRangoVentas');
    activarDateRangePicker('daterange-btn-reportes', 'reportes', 'capturarRangoReportes');
    activarDateRangePicker('daterange-btn-gastos', 'gastos', 'capturarRangoGastos');
    activarDateRangePicker('daterange-btn-entrada', 'entradas', 'capturarRangoEntradas');
    activarDateRangePicker('daterange-btn-detallado', 'reporte-detallado', 'capturarRangoDetallado');
    activarDateRangePicker('daterange-btn-contabilidad', 'contabilidad', 'capturarRangoContabilidad');

});