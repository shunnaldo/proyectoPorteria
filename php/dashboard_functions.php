<?php
function getTotalPersonas($conexion) {
    $query = "SELECT COUNT(*) as total FROM personas";
    $result = $conexion->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getIngresosHoy($conexion) {
    $query = "SELECT COUNT(*) as total FROM bitacora_ingresos 
              WHERE DATE(fecha_hora) = CURDATE()";
    $result = $conexion->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getPortonesAbiertos($conexion) {
    $query = "SELECT COUNT(*) as total FROM portones WHERE estado = 'abierto'";
    $result = $conexion->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getPersonasConPatente($conexion) {
    $query = "SELECT COUNT(*) as total FROM personas WHERE patente IS NOT NULL AND patente != ''";
    $result = $conexion->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getUltimosIngresos($conexion, $porton_id = null) {
    $query = "SELECT p.nombre, p.apellido, p.patente, po.nombre as porton, 
                     bi.fecha_hora, u.nombre as registrador
              FROM bitacora_ingresos bi
              JOIN personas p ON bi.persona_id = p.id
              JOIN portones po ON bi.porton_id = po.id
              JOIN usuarios u ON bi.usuario_id = u.id";
    
    if ($porton_id) {
        $query .= " WHERE bi.porton_id = $porton_id";
    }
    
    $query .= " ORDER BY bi.fecha_hora DESC LIMIT 10";
    
    $result = $conexion->query($query);
    $output = '';
    
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>
                      <td>".htmlspecialchars($row['nombre'])." ".htmlspecialchars($row['apellido'])."</td>
                      <td>".htmlspecialchars($row['porton'])."</td>
                      <td>".date('d/m/Y H:i', strtotime($row['fecha_hora']))."</td>
                      <td>".htmlspecialchars($row['patente'])."</td>
                      <td>".htmlspecialchars($row['registrador'])."</td>
                    </tr>";
    }
    
    return $output;
}

function getEstadoPortones($conexion) {
    $query = "SELECT id, nombre, ubicacion, estado FROM portones";
    $result = $conexion->query($query);
    $output = '';
    
    while ($row = $result->fetch_assoc()) {
        $statusClass = $row['estado'] == 'abierto' ? 'gate-open' : 'gate-closed';
        $icon = $row['estado'] == 'abierto' ? 'fa-door-open' : 'fa-door-closed';
        
        $output .= "<div class='gate-card {$statusClass}'>
                      <div class='gate-icon'>
                          <i class='fas {$icon}'></i>
                      </div>
                      <div class='gate-info'>
                          <h3>".htmlspecialchars($row['nombre'])."</h3>
                          <p>".htmlspecialchars($row['ubicacion'])."</p>
                          <span>Estado: ".ucfirst($row['estado'])."</span>
                      </div>
                    </div>";
    }
    
    return $output;
}

function getHorasDelDia() {
    return array_map(function($h) { 
        return str_pad($h, 2, '0', STR_PAD_LEFT) . ':00'; 
    }, range(0, 23));
}

function getIngresosPorHora($conexion, $porton_id = null) {
    $query = "SELECT HOUR(fecha_hora) as hora, COUNT(*) as total 
              FROM bitacora_ingresos 
              WHERE DATE(fecha_hora) = CURDATE()";
    
    if ($porton_id) {
        $query .= " AND porton_id = $porton_id";
    }
    
    $query .= " GROUP BY HOUR(fecha_hora)";
    
    $result = $conexion->query($query);
    $data = array_fill(0, 24, 0);
    
    while ($row = $result->fetch_assoc()) {
        $data[$row['hora']] = $row['total'];
    }
    
    return $data;
}

function getNombresPortones($conexion) {
    $query = "SELECT nombre FROM portones";
    $result = $conexion->query($query);
    $nombres = [];
    
    while ($row = $result->fetch_assoc()) {
        $nombres[] = htmlspecialchars($row['nombre']);
    }
    
    return $nombres;
}

function getUsoPortones($conexion) {
    $query = "SELECT po.nombre, COUNT(bi.id) as total
              FROM portones po
              LEFT JOIN bitacora_ingresos bi ON po.id = bi.porton_id
              GROUP BY po.id";
    
    $result = $conexion->query($query);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['total'];
    }
    
    return $data;
}

function getPortonesParaSelector($conexion) {
    $query = "SELECT id, nombre FROM portones ORDER BY nombre";
    $result = $conexion->query($query);
    
    $options = '';
    while ($row = $result->fetch_assoc()) {
        $options .= '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }
    return $options;
}

function getIngresosHoyPorPorton($conexion, $porton_id) {
    $query = "SELECT COUNT(*) as total FROM bitacora_ingresos 
              WHERE DATE(fecha_hora) = CURDATE() AND porton_id = $porton_id";
    $result = $conexion->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}
?>