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
    <link rel="stylesheet" href="../css/bitacora.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>

    <div class="bit-container">
        <div class="bit-header">
            <h1 class="bit-title">Bitácora de Ingresos</h1>
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

            <div class="bit-estado-filter">
                <select id="estado-filter">
                    <option value="todos">Todos</option>
                    <option value="ingresada">Ingresada</option>
                    <option value="finalizada">Finalizada</option>
                </select>
            </div>
        </div>
        
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
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="11" class="bit-loading">Cargando datos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="bit-card-container" id="bit-card-container">
            <div class="bit-loading">Cargando datos...</div>
        </div>

        <?php include 'botom-nav.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.querySelector("#tabla-bitacora tbody");
        const cardContainer = document.getElementById("bit-card-container");
        let bitacoraData = [];

        // Función para formatear fecha
        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return '';
            const date = new Date(dateTimeStr);
            return date.toLocaleString('es-CL');
        }

        // Configuración de Flatpickr con cambio dinámico
        const datePicker = flatpickr("#bit-date-filter", {
            locale: "es",
            dateFormat: "Y-m-d",
            allowInput: true,
            maxDate: "today",
            defaultDate: new Date(),
            onChange: function(selectedDates) {
                filterData();
            }
        });

        // Event listeners
        document.getElementById('bit-filter-btn').addEventListener('click', function() {
            datePicker.open();
        });

        document.getElementById('bit-clear-filter').addEventListener('click', function() {
            datePicker.clear();
            filterData();
        });

        document.getElementById('estado-filter').addEventListener('change', filterData);

        // Filtrado combinado
        function filterData() {
            const selectedDate = datePicker.selectedDates[0];
            const estado = document.getElementById('estado-filter').value;
            
            let filtered = bitacoraData;
            
            // Filtrar por fecha si hay una seleccionada
            if (selectedDate) {
                const filterDate = selectedDate.toISOString().split('T')[0];
                filtered = filtered.filter(item => {
                    if (!item.fecha_hora) return false;
                    const itemDate = item.fecha_hora.split(' ')[0];
                    return itemDate === filterDate;
                });
            }
            
            // Filtrar por estado
            if (estado !== 'todos') {
                filtered = filtered.filter(item => item.opciones === estado);
            }
            
            renderData(filtered);
        }

        // Renderizado de datos optimizado
        function renderData(data) {
            // Limpiar contenedores
            tbody.innerHTML = '';
            cardContainer.innerHTML = '';

            if (data.length === 0) {
                const noDataMsg = document.createElement('div');
                noDataMsg.className = 'bit-error';
                noDataMsg.textContent = 'No hay registros para los filtros seleccionados';
                cardContainer.appendChild(noDataMsg);

                tbody.innerHTML = `
                    <tr>
                        <td colspan="11" class="bit-error">No hay registros para los filtros seleccionados</td>
                    </tr>
                `;
                return;
            }

            // Renderizar tabla
            const tableFragment = document.createDocumentFragment();
            data.forEach(item => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${item.rut || ''}</td>
                    <td>${formatDateTime(item.fecha_hora)}</td>
                    <td>${item.persona_nombre || ''} ${item.persona_apellido || ''}</td>
                    <td>${item.genero || ''}</td>
                    <td>${item.medio_transporte || ''}</td>
                    <td>${item.patente || 'N/A'}</td>
                    <td>${item.usuario_nombre || ''} ${item.usuario_apellido || ''}</td>
                    <td>${item.porton_nombre || ''}</td>
                    <td>${item.ubicacion || ''}</td>
                    <td class="${item.opciones === 'ingresada' ? 'bit-estado-ingresada' : 'bit-estado-finalizada'}">
                        ${item.opciones === 'ingresada' ? 'Ingresada' : 'Finalizada'}
                    </td>
                    <td>
                        ${item.opciones === 'ingresada' ? 
                            `<button class="bit-action-btn" onclick="cambiarEstado(${item.id}, 'finalizada')">Finalizar</button>` : 
                            '<span class="bit-finalizado">Completado</span>'}
                    </td>
                `;
                tableFragment.appendChild(row);
            });
            tbody.appendChild(tableFragment);

            // Renderizar cards móviles
            const cardsFragment = document.createDocumentFragment();
            data.forEach((item, index) => {
                const card = document.createElement("div");
                card.className = "bit-card";
                card.innerHTML = `
                    <div class="bit-card-header" onclick="toggleBitCard(${index})">
                        <h3>${formatDateTime(item.fecha_hora)} - ${item.persona_nombre || ''} ${item.persona_apellido || ''}</h3>
                        <span class="bit-arrow">▼</span>
                    </div>
                    <div class="bit-card-content" id="bit-card-content-${index}">
                        <div class="bit-card-row">
                            <span class="bit-card-label">RUT:</span>
                            <span class="bit-card-value">${item.rut || 'N/A'}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Fecha y Hora:</span>
                            <span class="bit-card-value">${formatDateTime(item.fecha_hora)}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Persona:</span>
                            <span class="bit-card-value">${item.persona_nombre || ''} ${item.persona_apellido || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Género:</span>
                            <span class="bit-card-value">${item.genero || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Transporte:</span>
                            <span class="bit-card-value">${item.medio_transporte || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Patente:</span>
                            <span class="bit-card-value">${item.patente || 'N/A'}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Usuario:</span>
                            <span class="bit-card-value">${item.usuario_nombre || ''} ${item.usuario_apellido || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Portón:</span>
                            <span class="bit-card-value">${item.porton_nombre || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Ubicación:</span>
                            <span class="bit-card-value">${item.ubicacion || ''}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Estado:</span>
                            <span class="bit-card-value ${item.opciones === 'ingresada' ? 'bit-estado-ingresada' : 'bit-estado-finalizada'}">
                                ${item.opciones === 'ingresada' ? 'Ingresada' : 'Finalizada'}
                            </span>
                        </div>
                        <div class="bit-card-row bit-action-row">
                            ${item.opciones === 'ingresada' ? 
                                `<button class="bit-action-btn" onclick="cambiarEstado(${item.id}, 'finalizada')">Finalizar</button>` : 
                                '<span class="bit-finalizado">Registro completado</span>'}
                        </div>
                    </div>
                `;
                cardsFragment.appendChild(card);
            });
            cardContainer.appendChild(cardsFragment);
        }

        // Cargar datos iniciales
        function loadData() {
            fetch('../php/get_bitacora.php')
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar los datos');
                    return response.json();
                })
                .then(data => {
                    bitacoraData = data;
                    filterData(); // Aplicar filtros iniciales
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError();
                });
        }

        function showError() {
            const errorMsg = document.createElement('div');
            errorMsg.className = 'bit-error';
            errorMsg.textContent = 'Error al cargar los datos. Por favor, intente nuevamente.';
            cardContainer.innerHTML = '';
            cardContainer.appendChild(errorMsg);
            
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="bit-error">Error al cargar los datos. Por favor, intente nuevamente.</td>
                </tr>
            `;
        }

        // Iniciar carga de datos
        loadData();
    });

    // Funciones globales
    function cambiarEstado(id, nuevoEstado) {
        if (confirm('¿Está seguro de marcar este registro como finalizado?')) {
            fetch('../php/actualizar_estado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    id: id, 
                    estado: nuevoEstado 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registro actualizado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al comunicarse con el servidor');
            });
        }
    }

    function toggleBitCard(index) {
        const cardContent = document.getElementById(`bit-card-content-${index}`);
        const cardHeader = cardContent.previousElementSibling;
        
        cardContent.classList.toggle('bit-active');
        cardHeader.classList.toggle('bit-active');
    }
    </script>
</body>
</html>