<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "portero") {
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
    <link rel="stylesheet" href="../css/bitacoraAdmin.css">
    <!-- Agregar Font Awesome para el icono del calendario -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Agregar Flatpickr para el selector de fechas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>

    <div class="bit-container">
        <div class="bit-header">
            <h1 class="bit-title">Bitácora de Ingresos</h1>
            <!-- Filtro por fecha -->
            <div class="bit-filter">
                <div class="bit-date-filter">
                    <input type="text" id="bit-date-filter" placeholder="Seleccionar fecha" readonly>
                    <button id="bit-filter-btn" class="bit-filter-button">
                        <i class="fas fa-calendar-alt"></i>
                    </button>
                    <button id="bit-clear-filter" class="bit-clear-button" title="Limpiar filtro">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Versión para desktop (tabla) -->
        <div class="bit-table-container">
            <table id="tabla-bitacora" class="bit-table">
                <thead>
                    <tr>
                        <th>RUT</th>
                        <th>Fecha y Hora</th>
                        <th>Persona</th>
                        <th>Género</th>
                        <th>Transporte</th>
                        <th>Patente</th>
                        <th>Usuario</th>
                        <th>Portón</th>
                        <th>Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="9" class="bit-loading">Cargando datos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Versión para móvil (cards) -->
        <div class="bit-card-container" id="bit-card-container">
            <div class="bit-loading">Cargando datos...</div>
        </div>

        <?php include 'botom-nav.php'; ?>
    </div>

    <!-- Scripts adicionales -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.querySelector("#tabla-bitacora tbody");
            const cardContainer = document.getElementById("bit-card-container");
            let bitacoraData = []; // Almacenar todos los datos para filtrar
            
            // Obtener fecha actual en formato YYYY-MM-DD
            function getCurrentDate() {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            
            // Configurar Flatpickr con la fecha actual por defecto
            const currentDate = getCurrentDate();
            const datePicker = flatpickr("#bit-date-filter", {
                locale: "es",
                dateFormat: "Y-m-d",
                allowInput: true,
                maxDate: "today",
                defaultDate: currentDate, // Establecer fecha actual por defecto
                onClose: function(selectedDates) {
                    if(selectedDates.length > 0) {
                        filterByDate(selectedDates[0]);
                    }
                }
            });
            
            // Botón para abrir el calendario
            document.getElementById('bit-filter-btn').addEventListener('click', function() {
                datePicker.open();
            });
            
            // Botón para limpiar el filtro (vuelve a mostrar el día actual)
            document.getElementById('bit-clear-filter').addEventListener('click', function() {
                datePicker.setDate(currentDate); // Restablecer a fecha actual
                filterByDate(new Date(currentDate));
            });
            
            // Función para filtrar por fecha (ahora filtra por el día actual por defecto)
            function filterByDate(date) {
                // Si no hay fecha, usa la fecha actual por defecto
                const filterDate = date ? date.toISOString().split('T')[0] : currentDate;
                
                const filteredData = bitacoraData.filter(item => {
                    return item.fecha_hora && item.fecha_hora.startsWith(filterDate);
                });
                
                renderData(filteredData);
            }
            
            // Función para renderizar los datos
            function renderData(data) {
                // Limpiar mensajes de carga
                tbody.innerHTML = '';
                cardContainer.innerHTML = '';
                
                if (data.length === 0) {
                    const noDataMsg = document.createElement('div');
                    noDataMsg.className = 'bit-error';
                    noDataMsg.textContent = 'No hay registros para la fecha seleccionada';
                    cardContainer.appendChild(noDataMsg);
                    
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9" style="text-align: center;">No hay registros para la fecha seleccionada</td>
                        </tr>
                    `;
                    return;
                }
                
                // Generar tabla para desktop
                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.rut || ''}</td>
                        <td>${item.fecha_hora || ''}</td>
                        <td>${(item.persona_nombre || '')} ${(item.persona_apellido || '')}</td>
                        <td>${item.genero || ''}</td>
                        <td>${item.medio_transporte || ''}</td>
                        <td>${item.patente || ''}</td>
                        <td>${(item.usuario_nombre || '')} ${(item.usuario_apellido || '')}</td>
                        <td>${item.porton_nombre || ''}</td>
                        <td>${item.ubicacion || ''}</td>
                    `;
                    tbody.appendChild(row);
                });
                
                // Generar cards para móvil
                data.forEach((item, index) => {
                    const card = document.createElement("div");
                    card.className = "bit-card";
                    card.innerHTML = `
                        <div class="bit-card-header" onclick="toggleBitCard(${index})">
                            <h3>${item.fecha_hora || 'Registro'} - ${(item.persona_nombre || '')} ${(item.persona_apellido || '')}</h3>
                            <span class="bit-arrow">▼</span>
                        </div>
                        <div class="bit-card-content" id="bit-card-content-${index}">
                            <div class="bit-card-row">
                                <span class="bit-card-label">RUT:</span>
                                <span class="bit-card-value">${item.rut || ''}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Fecha y Hora:</span>
                                <span class="bit-card-value">${item.fecha_hora || ''}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Persona:</span>
                                <span class="bit-card-value">${(item.persona_nombre || '')} ${(item.persona_apellido || '')}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Género:</span>
                                <span class="bit-card-value">${item.genero || ''}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Medio de Transporte:</span>
                                <span class="bit-card-value">${item.medio_transporte || ''}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Patente:</span>
                                <span class="bit-card-value">${item.patente || 'No aplica'}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Usuario:</span>
                                <span class="bit-card-value">${(item.usuario_nombre || '')} ${(item.usuario_apellido || '')}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Portón:</span>
                                <span class="bit-card-value">${item.porton_nombre || ''}</span>
                            </div>
                            <div class="bit-card-row">
                                <span class="bit-card-label">Ubicación:</span>
                                <span class="bit-card-value">${item.ubicacion || ''}</span>
                            </div>
                        </div>
                    `;
                    cardContainer.appendChild(card);
                });
            }
            
            // Cargar datos iniciales
            fetch('../php/get_bitacora.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    bitacoraData = data;
                    // Al cargar los datos, filtrar por el día actual
                    filterByDate(null);
                })
                .catch(error => {
                    console.error('Error al cargar la bitácora:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9" class="bit-error">Error al cargar los datos. Por favor, intente nuevamente.</td>
                        </tr>
                    `;
                    
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'bit-error';
                    errorMsg.textContent = 'Error al cargar los datos. Por favor, intente nuevamente.';
                    cardContainer.innerHTML = '';
                    cardContainer.appendChild(errorMsg);
                });
        });
        
        function toggleBitCard(index) {
            const cardContent = document.getElementById(`bit-card-content-${index}`);
            const cardHeader = cardContent.previousElementSibling;
            
            cardContent.classList.toggle('bit-active');
            cardHeader.classList.toggle('bit-active');
        }
    </script>
</body>
</html>