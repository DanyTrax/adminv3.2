<?php

session_start();
date_default_timezone_set('America/Bogota');

// ===================================
// DEFINIR URL BASE DEL SITIO
// ===================================
// NOTA: Recuerda ajustar esta URL cuando subas el proyecto a tu servidor real.
// Ejemplo para servidor real: $url = "https://tusitio.com/";
// =================================================
// DEFINIR URL BASE DINÁMICA (SOLUCIÓN AUTOMÁTICA)
// =================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name = str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);

$url = $protocol . $host . $script_name;


?>

<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>Chapinero</title>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="icon" href="<?php echo $url; ?>vistas/img/plantilla/icono-negro.png ">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo $url; ?>vistas/bower_components/Ionicons/css/ionicons.min.css">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/dist/css/AdminLTE.css">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/dist/css/skins/_all-skins.min.css">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo $url; ?>vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/plugins/iCheck/all.css">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/bower_components/bootstrap-daterangepicker/daterangepicker.css">

  <link rel="stylesheet" href="<?php echo $url; ?>vistas/bower_components/morris.js/morris.css">

  <script src="<?php echo $url; ?>vistas/bower_components/jquery/dist/jquery.min.js"></script>

  <script src="<?php echo $url; ?>vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

  <script src="<?php echo $url; ?>vistas/bower_components/fastclick/lib/fastclick.js"></script>

  <script src="<?php echo $url; ?>vistas/dist/js/adminlte.min.js"></script>

  <script src="<?php echo $url; ?>vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo $url; ?>vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="<?php echo $url; ?>vistas/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="<?php echo $url; ?>vistas/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>

  <script src="<?php echo $url; ?>vistas/plugins/sweetalert2/sweetalert2.all.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>

  <script src="<?php echo $url; ?>vistas/plugins/iCheck/icheck.min.js"></script>

  <script src="<?php echo $url; ?>vistas/plugins/input-mask/jquery.inputmask.js"></script>
  <script src="<?php echo $url; ?>vistas/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="<?php echo $url; ?>vistas/plugins/input-mask/jquery.inputmask.extensions.js"></script>

  <script src="<?php echo $url; ?>vistas/plugins/jqueryNumber/jquerynumber.min.js"></script>

  <script src="<?php echo $url; ?>vistas/bower_components/moment/min/moment.min.js"></script>
  <script src="<?php echo $url; ?>vistas/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

  <script src="<?php echo $url; ?>vistas/bower_components/raphael/raphael.min.js"></script>
  <script src="<?php echo $url; ?>vistas/bower_components/morris.js/morris.min.js"></script>

  <script src="<?php echo $url; ?>vistas/bower_components/Chart.js/Chart.js"></script>

</head>

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">

  <?php

  if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {

    echo '<div class="wrapper">';

    /*=============================================
    CABEZOTE
    =============================================*/
    include "modulos/cabezote.php";

    /*=============================================
    MENU
    =============================================*/
    include "modulos/menu.php";

    /*=============================================
    CONTENIDO
    =============================================*/
    if (isset($_GET["ruta"])) {

      $routes = [
        "inicio" => ["Administrador"],
        "usuarios" => ["Administrador"],
        "categorias" => ["Administrador", "Especial"],
        "productos" => ["Administrador", "Especial", "Vendedor"], // <-- AQUÍ ESTÁ LA CORRECCIÓN
        "clientes" => ["Administrador", "Vendedor", "Contador"],
        "ventas" => ["Administrador", "Vendedor", "Contador"],
        "crear-venta" => ["Administrador", "Vendedor", "Contador"],
        "editar-venta" => ["Administrador", "Vendedor", "Contador"],
        "reportes" => ["Administrador"],
        "reporte-detallado" => ["Administrador"],
        "contabilidad" => ["Administrador", "Contador", "Vendedor"],
        "gastos" => ["Administrador", "Contador", "Vendedor"],
        "crear-gastos" => ["Administrador", "Contador", "Vendedor"],
        "editar-gasto" => ["Administrador", "Contador"],
        "entradas" => ["Administrador", "Contador"],
        "crear-entradas" => ["Administrador", "Contador"],
        "editar-entrada" => ["Administrador", "Contador"],
        "cotizacion" => ["Administrador", "Vendedor", "Contador"],
        "crear-cotizacion" => ["Administrador", "Vendedor", "Contador"],
        "editar-cotizacion" => ["Administrador", "Vendedor", "Contador"],
        "salir" => ["Administrador", "Especial", "Vendedor", "Contador"] // Permitir a todos salir
      ];

      $route = $_GET["ruta"];
      $profile = $_SESSION['perfil'];

      // Validar si la ruta existe en la lista de rutas válidas.
      if (array_key_exists($route, $routes)) {
        
        // Validar si el perfil del usuario está en la lista de perfiles permitidos para esa ruta.
        if (in_array($profile, $routes[$route])) {
          include "modulos/" . $route . ".php";
        } else {
            // Si el perfil no tiene permiso, lo mandamos al inicio (o a una página 403 de "sin permiso")
             include "modulos/inicio.php";
        }

      } else {
        // Si la ruta no existe en la lista, mostramos un 404.
        include "modulos/404.php";
      }

    } else {
      // Si no se especifica ninguna ruta, cargamos el inicio.
      include "modulos/inicio.php";
    }

    /*=============================================
    FOOTER
    =============================================*/
    include "modulos/footer.php";

    echo '</div>';

  } else {
    // Si no ha iniciado sesión, mostramos el login.
    include "modulos/login.php";
  }

  ?>


  <script src="<?php echo $url; ?>vistas/js/plantilla.js"></script>
  <script src="<?php echo $url; ?>vistas/js/usuarios.js"></script>
  <script src="<?php echo $url; ?>vistas/js/categorias.js"></script>
  <script src="<?php echo $url; ?>vistas/js/productos.js?v=1.2"></script> 
  <script src="<?php echo $url; ?>vistas/js/clientes.js"></script>
  <script src="<?php echo $url; ?>vistas/js/ventas.js"></script>
  <script src="<?php echo $url; ?>vistas/js/reportes.js"></script>
  <script src="<?php echo $url; ?>vistas/js/contabilidad.js"></script>

</body>

</html>