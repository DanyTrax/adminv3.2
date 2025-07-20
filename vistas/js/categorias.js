/*=============================================
EDITAR CATEGORIA
=============================================*/
$(".tablas").on("click", ".btnEditarCategoria", function(){

	var idCategoria = $(this).attr("idCategoria");

	var datos = new FormData();
	datos.append("idCategoria", idCategoria);

	$.ajax({
		url: "ajax/categorias.ajax.php",
		method: "POST",
      	data: datos,
      	cache: false,
     	contentType: false,
     	processData: false,
     	dataType:"json",
     	success: function(respuesta){

     		$("#editarCategoria").val(respuesta["categoria"]);
     		$("#idCategoria").val(respuesta["id"]);

     	}

	})


})

/*=============================================
ELIMINAR CATEGORIA (MÉTODO AJAX CORRECTO)
=============================================*/
$(".tablas").on("click", ".btnEliminarCategoria", function(){

	var idCategoria = $(this).attr("idCategoria");

	swal({
		title: '¿Está seguro de borrar la categoría?',
		text: "¡Si no lo está, puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: '¡Sí, borrar categoría!'
	}).then(function(result){

		if(result.value){

			var datos = new FormData();
			datos.append("idCategoriaBorrar", idCategoria);

			$.ajax({
				url: "ajax/categorias.ajax.php",
				method: "POST",
				data: datos,
				cache: false,
				contentType: false,
				processData: false,
				success: function(respuesta){
					if(respuesta == "ok"){
						swal({
						  type: "success",
						  title: "La categoría ha sido borrada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {
									window.location = "categorias";
									}
								})
					} else {
						swal({
						  type: "error",
						  title: "Error: La categoría tiene productos y no puede ser borrada.",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						});
					}
				}
			});
		}
	})

}) // <-- Así debe quedar, con un solo paréntesis cerrando la función.