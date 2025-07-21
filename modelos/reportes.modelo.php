<?php

require_once "conexion.php";

class ModeloReportes {

    /**
     * Obtener los datos de ventas para DataTables (Server-Side)
     */
    static public function mdlObtenerVentasServerSide($params, $fechaInicial, $fechaFinal) {
        
        $db = Conexion::conectar();
        $columns = [
            0 => 'v.id', 1 => 'v.fecha_venta', 2 => 'v.codigo', 3 => 'vendedor.nombre',
            4 => 'cliente.nombre', 5 => 'vp.descripcion', 6 => 'vp.cantidad',
            7 => 'vp.total', 8 => 'v.medio_pago'
        ];

        $sql = "SELECT v.fecha_venta, v.codigo, v.medio_pago,
                    vendedor.nombre as nombre_vendedor, cliente.nombre as nombre_cliente,
                    vp.descripcion as producto_descripcion, vp.cantidad as producto_cantidad,
                    vp.total as producto_total
                FROM ventas AS v
                INNER JOIN usuarios AS vendedor ON v.id_vendedor = vendedor.id
                INNER JOIN clientes AS cliente ON v.id_cliente = cliente.id
                INNER JOIN venta_productos AS vp ON v.id = vp.id_venta";

        $where = "";
        if (!empty($fechaInicial) && !empty($fechaFinal)) {
            $where = " WHERE v.fecha_venta BETWEEN :fechaInicial AND :fechaFinal";
        }

        if (!empty($params['search']['value'])) {
            $where .= ($where == "") ? " WHERE (" : " AND (";
            $where .= " v.codigo LIKE :search_value OR vendedor.nombre LIKE :search_value OR cliente.nombre LIKE :search_value OR vp.descripcion LIKE :search_value )";
        }
        $sql .= $where;

        $sql .= " ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'];

        if ($params['length'] != -1) {
            $sql .= " LIMIT :start, :length";
        }
        
        $stmt = $db->prepare($sql);

        if (!empty($fechaInicial) && !empty($fechaFinal)) {
            // --- CORRECCIÓN AQUÍ ---
            // Añadimos la hora final para cubrir el día completo
            $fechaFinalConHora = $fechaFinal . ' 23:59:59';
            $stmt->bindParam(":fechaInicial", $fechaInicial, PDO::PARAM_STR);
            $stmt->bindParam(":fechaFinal", $fechaFinalConHora, PDO::PARAM_STR);
        }
        if (!empty($params['search']['value'])) {
            $searchValue = "%" . $params['search']['value'] . "%";
            $stmt->bindParam(":search_value", $searchValue, PDO::PARAM_STR);
        }
        if ($params['length'] != -1) {
            $stmt->bindParam(":start", $params['start'], PDO::PARAM_INT);
            $stmt->bindParam(":length", $params['length'], PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar registros filtrados
     */
    static public function mdlContarVentasFiltradas($params, $fechaInicial, $fechaFinal) {
        $db = Conexion::conectar();
        $sql = "SELECT COUNT(v.id) as total FROM ventas AS v 
                INNER JOIN usuarios AS vendedor ON v.id_vendedor = vendedor.id
                INNER JOIN clientes AS cliente ON v.id_cliente = cliente.id
                INNER JOIN venta_productos AS vp ON v.id = vp.id_venta";

        $where = "";
        if (!empty($fechaInicial) && !empty($fechaFinal)) {
            $where = " WHERE v.fecha_venta BETWEEN :fechaInicial AND :fechaFinal";
        }
        if (!empty($params['search']['value'])) {
            $where .= ($where == "") ? " WHERE (" : " AND (";
            $where .= " v.codigo LIKE :search_value OR vendedor.nombre LIKE :search_value OR cliente.nombre LIKE :search_value OR vp.descripcion LIKE :search_value )";
        }
        $sql .= $where;

        $stmt = $db->prepare($sql);
        
        if (!empty($fechaInicial) && !empty($fechaFinal)) {
            // --- CORRECCIÓN AQUÍ ---
            $fechaFinalConHora = $fechaFinal . ' 23:59:59';
            $stmt->bindParam(":fechaInicial", $fechaInicial, PDO::PARAM_STR);
            $stmt->bindParam(":fechaFinal", $fechaFinalConHora, PDO::PARAM_STR);
        }
        if (!empty($params['search']['value'])) {
            $searchValue = "%" . $params['search']['value'] . "%";
            $stmt->bindParam(":search_value", $searchValue, PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Contar el total de registros sin filtros
     */
    static public function mdlContarTotalVentas($fechaInicial, $fechaFinal) {
        $db = Conexion::conectar();
        $sql = "SELECT COUNT(vp.id) as total FROM venta_productos vp
                INNER JOIN ventas v ON vp.id_venta = v.id";
        
        if (!empty($fechaInicial) && !empty($fechaFinal)) {
            $sql .= " WHERE v.fecha_venta BETWEEN :fechaInicial AND :fechaFinal";
        }
        
        $stmt = $db->prepare($sql);

        if (!empty($fechaInicial) && !empty($fechaFinal)) {
            // --- CORRECCIÓN AQUÍ ---
            $fechaFinalConHora = $fechaFinal . ' 23:59:59';
            $stmt->bindParam(":fechaInicial", $fechaInicial, PDO::PARAM_STR);
            $stmt->bindParam(":fechaFinal", $fechaFinalConHora, PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}