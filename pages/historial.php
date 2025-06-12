<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cambios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 14px;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .title-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        h1 {
            color: #222;
            margin: 0;
            font-weight: 300;
            font-size: 1.5em;
        }
        
        .btn {
            padding: 6px 12px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            transition: all 0.2s;
        }
        
        .btn:hover {
            background-color: #e0e0e0;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .btn-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            border-color: #45a049;
        }
        
        .btn-primary:hover {
            background-color: #45a049;
        }
        
        .btn-danger {
            background-color: #f44336;
            color: white;
            border-color: #d32f2f;
        }
        
        .btn-danger:hover {
            background-color: #d32f2f;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        
        th {
            background-color: #222;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: 500;
        }
        
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #777;
            font-style: italic;
            font-size: 0.9em;
        }
        
        .error-message {
            color: #d32f2f;
            background-color: #fde0e0;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header-container">
                <div class="title-container">
                    <h1>Historial de Cambios</h1>
                    <a href="dashboard.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="btn-group">
                    <button id="exportPdf" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button id="exportExcel" class="btn btn-primary">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </div>
            </div>
            
            <div class="table-container">
                <?php
                require_once '../php/conexion.php'; // Conectar a la base de datos

                // Consulta para obtener el historial de cambios con el nombre del usuario
                $sql = "SELECT hc.id, u.nombre AS admin, hc.accion, hc.tabla_afectada, hc.registro_id, hc.descripcion, hc.fecha
                        FROM historial_cambios hc
                        JOIN usuarios u ON hc.usuario_id = u.id
                        ORDER BY hc.fecha DESC";

                // Verificar si la consulta devuelve resultados
                if ($result = $conexion->query($sql)) {
                    if ($result->num_rows > 0) {
                        echo "<table id='dataTable'>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Administrador</th>
                                        <th>Acción</th>
                                        <th>Tabla</th>
                                        <th>ID Registro</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>";
                        
                        // Mostrar cada fila de resultados
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row["id"]) . "</td>
                                    <td>" . htmlspecialchars($row["admin"]) . "</td>
                                    <td>" . htmlspecialchars($row["accion"]) . "</td>
                                    <td>" . htmlspecialchars($row["tabla_afectada"]) . "</td>
                                    <td>" . htmlspecialchars($row["registro_id"]) . "</td>
                                    <td>" . htmlspecialchars($row["descripcion"]) . "</td>
                                    <td>" . htmlspecialchars($row["fecha"]) . "</td>
                                  </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<div class='no-data'>No hay cambios registrados.</div>";
                    }
                } else {
                    echo "<div class='error-message'>Error en la consulta: " . $conexion->error . "</div>";
                }

                $conexion->close();  // Cerramos la conexión
                ?>
            </div>
        </div>
    </div>

    <script>
        // Exportar a PDF
        document.getElementById('exportPdf').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Título del documento
            doc.setFontSize(16);
            doc.text('Historial de Cambios', 14, 15);
            
            // Obtener datos de la tabla
            const table = document.getElementById('dataTable');
            const headers = [];
            const rows = [];
            
            // Obtener encabezados
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent);
            });
            
            // Obtener filas de datos
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push(td.textContent);
                });
                rows.push(row);
            });
            
            // Generar tabla en PDF
            doc.autoTable({
                head: [headers],
                body: rows,
                startY: 20,
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                },
                headStyles: {
                    fillColor: [34, 34, 34],
                    textColor: [255, 255, 255]
                }
            });
            
            // Guardar el PDF
            doc.save('historial_cambios.pdf');
        });
        
        // Exportar a Excel
        document.getElementById('exportExcel').addEventListener('click', function() {
            const table = document.getElementById('dataTable');
            const workbook = XLSX.utils.table_to_book(table);
            XLSX.writeFile(workbook, 'historial_cambios.xlsx');
        });
    </script>
</body>
</html>