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
    <link rel="stylesheet" href="../css/ownervista.css">
    <style>
        /* Contenedor del menú desplegable */
        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        /* Botón principal */
        .export-main-btn {
            background-color: #4a6cf7;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(74, 108, 247, 0.2);
            transition: all 0.3s ease;
        }

        .export-main-btn:hover {
            background-color: #3a5ce4;
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.3);
        }

        .export-main-btn i {
            font-size: 16px;
        }

        /* Contenido del desplegable */
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
            overflow: hidden;
            margin-top: 8px;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mostrar el desplegable */
        .show {
            display: block;
        }

        /* Botones de exportación */
        .bit-export-btn {
            width: 100%;
            padding: 10px 16px;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .bit-export-btn:hover {
            background-color: #f0f4ff;
            color: #4a6cf7;
        }

        .bit-export-btn i {
            width: 18px;
            text-align: center;
        }

        #export-excel i {
            color: #217346;
        }

        #export-pdf i {
            color: #e74c3c;
        }

        /* Separador */
        .dropdown-separator {
            height: 1px;
            background-color: #eee;
            margin: 4px 0;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="bit-container">
        <div class="bit-header">
            <h1 class="bit-title">Registro Histórico de Accesos</h1>
            <div class="bit-actions">
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
            <div class="export-dropdown">
                    <button onclick="toggleDropdown()" class="export-main-btn">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                    <div id="exportDropdown" class="dropdown-content">
                        <div class="bit-export">
                            <button id="export-excel" class="bit-export-btn">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <div class="dropdown-separator"></div>
                            <button id="export-pdf" class="bit-export-btn">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Versión para desktop -->
        <div class="bit-table-container">
            <table class="bit-table" id="bitacora-table">
                <thead>
                    <tr>
                        <th>RUT</th>
                        <th>Fecha</th>
                        <th>H. Ingreso</th>
                        <th>H. Salida</th>
                        <th>Nombre</th>
                        <th>Género</th>
                        <th>Fecha Nac.</th>
                        <th>Edad</th>
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
                        <td colspan="14" class="bit-loading">Cargando registros...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Versión para móvil -->
        <div class="bit-card-container" id="bit-card-container">
            <div class="bit-loading">Cargando registros...</div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        // Configura jsPDF
        const {
            jsPDF
        } = window.jspdf;

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
                            <td colspan="14">No hay registros para mostrar</td>
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
                        <td>${item.fecha_nacimiento_formatted || '--/--/----'}</td>
                        <td>${item.edad || 'N/A'}</td>
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
                            <span class="bit-card-label">Fecha Nac.:</span>
                            <span>${item.fecha_nacimiento_formatted || '--/--/----'}</span>
                        </div>
                        <div class="bit-card-row">
                            <span class="bit-card-label">Edad:</span>
                            <span>${item.edad || 'N/A'}</span>
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
                            <td colspan="14" class="bit-error">Error al cargar los datos</td>
                        </tr>
                    `;
                    cardContainer.innerHTML = '<div class="bit-error">Error al cargar los datos</div>';
                });

            // Funciones para exportar
            document.getElementById('export-excel').addEventListener('click', exportToExcel);
            document.getElementById('export-pdf').addEventListener('click', exportToPDF);

            function exportToExcel() {
                // Mostrar mensaje de carga
                const loading = document.createElement('div');
                loading.style.position = 'fixed';
                loading.style.top = '0';
                loading.style.left = '0';
                loading.style.width = '100%';
                loading.style.height = '100%';
                loading.style.backgroundColor = 'rgba(0,0,0,0.5)';
                loading.style.display = 'flex';
                loading.style.justifyContent = 'center';
                loading.style.alignItems = 'center';
                loading.style.zIndex = '1000';
                loading.innerHTML = '<div style="background: white; padding: 20px; border-radius: 5px;"><i class="fas fa-spinner fa-spin"></i> Generando archivo Excel...</div>';
                document.body.appendChild(loading);

                // Crear un nuevo libro de trabajo
                const wb = XLSX.utils.book_new();

                // Convertir la tabla a una hoja de trabajo
                const table = document.getElementById('bitacora-table');
                const ws = XLSX.utils.table_to_sheet(table);

                // Agregar la hoja de trabajo al libro
                XLSX.utils.book_append_sheet(wb, ws, "Bitácora");

                // Generar el archivo Excel
                setTimeout(() => {
                    XLSX.writeFile(wb, 'bitacora_ingresos.xlsx');
                    document.body.removeChild(loading);
                }, 500);
            }

            function exportToPDF() {
                const loading = document.createElement('div');
                loading.style.position = 'fixed';
                loading.style.top = '0';
                loading.style.left = '0';
                loading.style.width = '100%';
                loading.style.height = '100%';
                loading.style.backgroundColor = 'rgba(0,0,0,0.5)';
                loading.style.display = 'flex';
                loading.style.justifyContent = 'center';
                loading.style.alignItems = 'center';
                loading.style.zIndex = '1000';
                loading.innerHTML = '<div style="background: white; padding: 20px; border-radius: 5px;"><i class="fas fa-spinner fa-spin"></i> Generando archivo PDF...</div>';
                document.body.appendChild(loading);

                const table = document.getElementById('bitacora-table');
                const title = "Registro Histórico de Accesos";
                const date = new Date().toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                setTimeout(() => {
                    html2canvas(table, {
                        scale: 2,
                        logging: true,
                        useCORS: true,
                        allowTaint: true
                    }).then(canvas => {
                        const imgData = canvas.toDataURL('image/png');
                        const pdf = new jsPDF({
                            orientation: 'landscape',
                            unit: 'mm'
                        });

                        // Título y fecha
                        pdf.setFontSize(16);
                        pdf.setTextColor(40, 40, 40);
                        pdf.text(title, pdf.internal.pageSize.getWidth() / 2, 15, {
                            align: 'center'
                        });

                        pdf.setFontSize(10);
                        pdf.setTextColor(100, 100, 100);
                        pdf.text(`Generado el: ${date}`, pdf.internal.pageSize.getWidth() / 2, 20, {
                            align: 'center'
                        });

                        // Imagen de la tabla
                        const imgProps = pdf.getImageProperties(imgData);
                        const pdfWidth = pdf.internal.pageSize.getWidth() - 20;
                        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                        pdf.addImage(imgData, 'PNG', 10, 25, pdfWidth, pdfHeight);
                        pdf.save('bitacora_ingresos.pdf');
                        document.body.removeChild(loading);
                    }).catch(err => {
                        console.error('Error al generar PDF:', err);
                        document.body.removeChild(loading);
                        alert('Error al generar el PDF. Por favor, intente nuevamente.');
                    });
                }, 500);
            }
        });
    </script>
    <script>
        // Función para mostrar/ocultar el desplegable
        function toggleDropdown() {
            document.getElementById("exportDropdown").classList.toggle("show");
        }

        // Cerrar el desplegable si se hace clic fuera de él
        window.onclick = function(event) {
            if (!event.target.matches('.export-main-btn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>