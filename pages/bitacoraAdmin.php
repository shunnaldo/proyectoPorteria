<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Ingresos</title>
    <link rel="stylesheet" href="../css/bitacoraAdmin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>


    <div class="bit-container">
        <h1 class="bit-title">Bitácora de Ingresos</h1>
        
        <!-- Versión para desktop (tabla) -->
        <div class="bit-table-container">
            <table id="tabla-bitacora" class="bit-table">
                <thead>
                    <tr>
                        <th>RUT</th>
                        <th>Fecha y Hora</th>
                        <th>Persona</th>
                        <th>Usuario</th>
                        <th>Portón</th>
                        <th>Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="bit-loading">Cargando datos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Versión para móvil (cards) -->
        <div class="bit-card-container" id="bit-card-container">
            <div class="bit-loading">Cargando datos...</div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.querySelector("#tabla-bitacora tbody");
            const cardContainer = document.getElementById("bit-card-container");
            
            fetch('../php/get_bitacora.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    // Limpiar mensajes de carga
                    tbody.innerHTML = '';
                    cardContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        const noDataMsg = document.createElement('div');
                        noDataMsg.className = 'bit-error';
                        noDataMsg.textContent = 'No hay registros para mostrar';
                        cardContainer.appendChild(noDataMsg);
                        
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" style="text-align: center;">No hay registros para mostrar</td>
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
                })
                .catch(error => {
                    console.error('Error al cargar la bitácora:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="bit-error">Error al cargar los datos. Por favor, intente nuevamente.</td>
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
        <script src="../js/sidebaropen.js"></script>

</body>
</html>