<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Ingresos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --success-color: #4bb543;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: var(--dark-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .bit-container {
            margin-left: 250px;
            padding: 2rem;
            transition: var(--transition);
        }

        .bit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .bit-title {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin: 0;
            font-weight: 600;
        }

        .bit-filter {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        #bit-date-filter {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            min-width: 150px;
            transition: var(--transition);
        }

        #bit-date-filter:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }

        .bit-filter-btn,
        .bit-clear-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .bit-filter-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .bit-filter-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }

        .bit-clear-btn {
            background-color: var(--gray-color);
            color: white;
            padding: 0.5rem;
        }

        .bit-clear-btn:hover {
            background-color: #5a6268;
        }

        .bit-table-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow-x: auto;
            margin-bottom: 2rem;
        }

        .bit-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .bit-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 500;
            position: sticky;
            top: 0;
        }

        .bit-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
        }

        .bit-table tr:last-child td {
            border-bottom: none;
        }

        .bit-table tr:hover td {
            background-color: #f8f9fa;
        }

        .bit-card-container {
            display: none;
            gap: 1rem;
            flex-direction: column;
        }

        .bit-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            transition: var(--transition);
        }

        .bit-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .bit-card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .bit-card-row:last-child {
            margin-bottom: 0;
        }

        .bit-card-label {
            font-weight: 600;
            color: var(--primary-color);
            min-width: 120px;
        }

        .bit-loading,
        .bit-error {
            padding: 2rem;
            text-align: center;
            color: var(--gray-color);
        }

        .bit-error {
            color: #dc3545;
        }

        .status-ingresada {
            color: #28a745;
            font-weight: 500;
        }

        .status-finalizada {
            color: #6c757d;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .bit-container {
                margin-left: 0;
                padding: 1rem;
            }

            .bit-table-container {
                display: none;
            }

            .bit-card-container {
                display: flex;
            }

            .bit-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .bit-filter {
                width: 100%;
            }

            #bit-date-filter {
                flex-grow: 1;
            }
        }

        /* Animación de carga */
        @keyframes pulse {
            0% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.6;
            }
        }

        .bit-loading {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="bit-container">
        <div class="bit-header">
            <h1 class="bit-title">Registro Histórico de Accesos</h1>
            <div class="bit-filter">
                <input type="text" id="bit-date-filter" placeholder="Seleccionar fecha">
                <button id="bit-filter-btn" class="bit-filter-btn">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <button id="bit-clear-filter" class="bit-clear-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Versión para desktop -->
        <div class="bit-table-container">
            <table class="bit-table">
                <thead>
                    <tr>
                        <th>RUT</th>
                        <th>Fecha</th>
                        <th>H. Ingreso</th>
                        <th>H. Salida</th>
                        <th>Nombre</th>
                        <th>Género</th>
                        <th>Transporte</th>
                        <th>Patente</th>
                        <th>Portón</th>
                        <th>Estado</th>
                        <th>Portero</th>
                        <th>Cargo</th>

                    </tr>
                </thead>
                <tbody id="tabla-bitacora">
                    <tr>
                        <td colspan="12" class="bit-loading">Cargando registros...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Versión para móvil -->
        <div class="bit-card-container" id="bit-card-container">
            <div class="bit-loading">Cargando registros...</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById("tabla-bitacora");
            const cardContainer = document.getElementById("bit-card-container");
            let bitacoraData = [];

            // Configurar Flatpickr
            const datePicker = flatpickr("#bit-date-filter", {
                locale: "es",
                dateFormat: "d/m/Y",
                allowInput: true,
                maxDate: "today"
            });

            // Manejar filtrado
            document.getElementById('bit-filter-btn').addEventListener('click', function() {
                const selectedDate = datePicker.selectedDates[0];
                filterByDate(selectedDate);
            });

            document.getElementById('bit-clear-filter').addEventListener('click', function() {
                datePicker.clear();
                filterByDate(null);
            });

            function filterByDate(date) {
                if (!date) {
                    renderData(bitacoraData);
                    return;
                }

                const dateStr = date.toISOString().split('T')[0];
                const filteredData = bitacoraData.filter(item => {
                    return item.fecha_hora && item.fecha_hora.startsWith(dateStr);
                });

                renderData(filteredData);
            }

            function renderData(data) {
                // Limpiar contenedores
                tbody.innerHTML = '';
                cardContainer.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="12">No hay registros para mostrar</td>
                        </tr>
                    `;
                    cardContainer.innerHTML = '<div class="bit-card">No hay registros para mostrar</div>';
                    return;
                }

                // Renderizar tabla
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.rut || ''}</td>
                        <td>${item.fecha || '--/--/----'}</td>
                        <td>${item.hora_ingreso || '--:--'}</td>
                        <td>${item.hora_salida || '--:--'}</td>
                        <td>${item.persona_nombre || ''} ${item.persona_apellido || ''}</td>
                        <td>${item.genero || ''}</td>
                        <td>${item.medio_transporte || ''}</td>
                        <td>${item.patente || 'N/A'}</td>
                        <td>${item.porton_nombre || ''}</td>
                        <td class="status-${item.estado.toLowerCase()}">${item.estado || ''}</td>
                        <td>${item.nombre_portero || ''}</td>
                        <td>${item.alias || ''}</td>

                    `;
                    tbody.appendChild(row);
                });

                // Renderizar cards para móvil
                data.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'bit-card';
                    card.innerHTML = `
                        <div class="bit-card-row">
                            <span class="bit-card-label">RUT:</span>
                            <span>${item.rut || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Fecha:</span>
                            <span>${item.fecha || '--/--/----'}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">H. Ingreso:</span>
                            <span>${item.hora_ingreso || '--:--'}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">H. Salida:</span>
                            <span>${item.hora_salida || '--:--'}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Nombre:</span>
                            <span>${item.persona_nombre || ''} ${item.persona_apellido || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Género:</span>
                            <span>${item.genero || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Transporte:</span>
                            <span>${item.medio_transporte || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Patente:</span>
                            <span>${item.patente || 'N/A'}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Portón:</span>
                            <span>${item.porton_nombre || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Estado:</span>
                            <span class="status-${item.estado.toLowerCase()}">${item.estado || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Portero:</span>
                            <span>${item.nombre_portero || ''}</span>
                        </div>
                    `;
                    cardContainer.appendChild(card);
                });
            }

            // Cargar datos iniciales
            fetch('../php/get_bitacora.php')
                .then(response => response.json())
                .then(data => {
                    bitacoraData = data;
                    renderData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="12" class="bit-error">Error al cargar los datos</td>
                        </tr>
                    `;
                    cardContainer.innerHTML = '<div class="bit-error">Error al cargar los datos</div>';
                });
        });
    </script>
</body>

</html>