<?php
require '../php/conexion.php';
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "owner") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Ingresos - Owner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
    <link rel="stylesheet" href="../css/ownervista.css">


</head>

<body>
    <?php include 'sidebarOwner.php'; ?>

    <div class="bit-container">
        <div class="bit-header">
            <h1 class="bit-title">Registro de Accesos - Mis Portones</h1>
            <div class="bit-filter">
                <input type="text" id="bit-date-filter" placeholder="Seleccionar fecha">
                <input type="text" id="bit-patente-filter" placeholder="Filtrar por patente">

                <button id="bit-filter-btn" class="bit-filter-btn">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <button id="bit-clear-filter" class="bit-clear-btn">
                    <i class="fas fa-times"></i>
                </button>
                <div class="tooltip">
                    <button id="bit-download-excel" class="bit-excel-btn">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </button>
                    <span class="tooltiptext">Descargar datos en Excel</span>
                </div>

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

            // Función para calcular la edad a partir de la fecha de nacimiento
            function calcularEdad(fechaNacimiento) {
                if (!fechaNacimiento) return '';

                try {
                    const nacimiento = new Date(fechaNacimiento);
                    const hoy = new Date();

                    let edad = hoy.getFullYear() - nacimiento.getFullYear();
                    const mes = hoy.getMonth() - nacimiento.getMonth();

                    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                        edad--;
                    }

                    return edad;
                } catch (e) {
                    console.error('Error calculando edad:', e);
                    return '';
                }
            }

            // Manejar filtrado
            document.getElementById('bit-filter-btn').addEventListener('click', function() {
                const selectedDate = datePicker.selectedDates[0];
                const patente = document.getElementById('bit-patente-filter').value.trim().toLowerCase();
                filterData(selectedDate, patente);
            });

            document.getElementById('bit-clear-filter').addEventListener('click', function() {
                datePicker.clear();
                document.getElementById('bit-patente-filter').value = '';
                filterData(null, '');
            });

            function filterData(date, patente) {
                let filteredData = bitacoraData;

                if (date) {
                    const dateStr = date.toISOString().split('T')[0];
                    filteredData = filteredData.filter(item => item.fecha_hora && item.fecha_hora.startsWith(dateStr));
                }

                if (patente) {
                    filteredData = filteredData.filter(item => (item.patente || '').toLowerCase().includes(patente));
                }

                renderData(filteredData);
            }

            // Agregar evento para descargar Excel
            document.getElementById('bit-download-excel').addEventListener('click', function() {
                downloadExcel();
            });

async function downloadExcel() {
    const excelBtn = document.getElementById('bit-download-excel');
    const originalText = excelBtn.innerHTML;
    excelBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
    excelBtn.disabled = true;

    try {
        // Obtener datos filtrados (código existente)
        const selectedDate = datePicker.selectedDates[0];
        const patente = document.getElementById('bit-patente-filter').value.trim().toLowerCase();
        let filteredData = bitacoraData;

        if (selectedDate) {
            const dateStr = selectedDate.toISOString().split('T')[0];
            filteredData = filteredData.filter(item => item.fecha_hora && item.fecha_hora.startsWith(dateStr));
        }

        if (patente) {
            filteredData = filteredData.filter(item => (item.patente || '').toLowerCase().includes(patente));
        }

        if (filteredData.length === 0) {
            throw new Error('No hay datos para exportar');
        }

        // Crear workbook
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Registros Portones');

        // Definir estilos
// Definir estilos
const headerStyle = {
    fill: {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FF000000' }  // Fondo negro
    },
    font: {
        color: { argb: 'FFFFFFFF' },    // Letras blancas
        bold: true
    },
    alignment: {
        vertical: 'middle',
        horizontal: 'center'
    },
    border: {
        top: { style: 'thin', color: { argb: 'FFFFFFFF' } },
        left: { style: 'thin', color: { argb: 'FFFFFFFF' } },
        bottom: { style: 'thin', color: { argb: 'FFFFFFFF' } },
        right: { style: 'thin', color: { argb: 'FFFFFFFF' } }
    }
};

const dataStyle = {
    fill: {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FFFFFFFF' }  // Fondo blanco
    },
    font: {
        color: { argb: 'FF000000' }    // Letras negras
    },
    alignment: {
        vertical: 'middle'
    },
    border: {
        top: { style: 'thin', color: { argb: 'FFCCCCCC' } },  // Bordes grises claros
        left: { style: 'thin', color: { argb: 'FFCCCCCC' } },
        bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
        right: { style: 'thin', color: { argb: 'FFCCCCCC' } }
    }
};

        // Añadir encabezados
        const headers = [
            'RUT', 'Fecha', 'Hora Ingreso', 'Hora Salida', 
            'Nombre Completo', 'Género', 'Edad', 'Transporte',
            'Patente', 'Portón', 'Estado', 'Portero', 'Cargo Portero'
        ];
        
        const headerRow = worksheet.addRow(headers);
        headerRow.eachCell(cell => {
            cell.style = headerStyle;
        });

        // Añadir datos
        filteredData.forEach(item => {
            const edad = calcularEdad(item.fecha_nacimiento);
            const row = worksheet.addRow([
                item.rut || '',
                item.fecha || '--/--/----',
                item.hora_ingreso || '--:--',
                item.hora_salida || '--:--',
                `${item.persona_nombre || ''} ${item.persona_apellido || ''}`.trim(),
                item.genero || '',
                edad || '',
                item.medio_transporte || '',
                item.patente || 'N/A',
                item.porton_nombre || '',
                item.estado || '',
                item.usuario_nombre || '',
                item.usuario_alias || ''
            ]);
            
            row.eachCell(cell => {
                cell.style = dataStyle;
            });
        });

        // Ajustar anchos de columnas
        worksheet.columns = [
            { width: 12 }, { width: 10 }, { width: 10 }, { width: 10 },
            { width: 25 }, { width: 8 }, { width: 5 }, { width: 15 },
            { width: 12 }, { width: 15 }, { width: 12 }, { width: 20 }, { width: 15 }
        ];

        // Generar archivo
        const today = new Date();
        const dateStr = today.toLocaleDateString('es-CL').replace(/\//g, '-');
        const fileName = `Registros_Portones_${dateStr}.xlsx`;
        
        const buffer = await workbook.xlsx.writeBuffer();
        saveAs(new Blob([buffer]), fileName);

        // Feedback de éxito
        excelBtn.innerHTML = '<i class="fas fa-check"></i> Descargado!';
        setTimeout(() => {
            excelBtn.innerHTML = originalText;
            excelBtn.disabled = false;
        }, 2000);

    } catch (error) {
        console.error('Error al generar Excel:', error);
        excelBtn.innerHTML = originalText;
        excelBtn.disabled = false;

        Swal.fire({
            icon: error.message.includes('No hay datos') ? 'warning' : 'error',
            title: error.message.includes('No hay datos') ? 'Sin datos' : 'Error',
            text: error.message.includes('No hay datos') 
                ? 'No hay registros para exportar con los filtros actuales' 
                : 'Ocurrió un error al generar el archivo Excel',
            confirmButtonColor: '#4361ee'
        });
    }
}

            function renderData(data) {
                // Limpiar contenedores
                tbody.innerHTML = '';
                cardContainer.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `
                <tr>
                    <td colspan="13">No hay registros para mostrar</td>
                </tr>
            `;
                    cardContainer.innerHTML = '<div class="bit-card">No hay registros para mostrar</div>';
                    return;
                }

                // Renderizar tabla
                data.forEach(item => {
                    const edad = calcularEdad(item.fecha_nacimiento);
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>${item.rut || ''}</td>
                <td>${item.fecha || '--/--/----'}</td>
                <td>${item.hora_ingreso || '--:--'}</td>
                <td>${item.hora_salida || '--:--'}</td>
                <td>${item.persona_nombre || ''} ${item.persona_apellido || ''}</td>
                <td>${item.genero || ''}</td>
                <td>${edad || ''}</td>
                <td>${item.medio_transporte || ''}</td>
                <td>${item.patente || 'N/A'}</td>
                <td>${item.porton_nombre || ''}</td>
                <td class="status-${item.estado ? item.estado.toLowerCase() : ''}">${item.estado || ''}</td>
                <td>${item.nombre_portero || ''}</td>
                <td>${item.usuario_alias || ''}</td>
            `;
                    tbody.appendChild(row);
                });

                // Renderizar cards para móvil
                data.forEach(item => {
                    const edad = calcularEdad(item.fecha_nacimiento);
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
                    <span class="bit-card-label">Edad:</span>
                    <span>${edad || ''}</span>
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
                    <span class="status-${item.estado ? item.estado.toLowerCase() : ''}">${item.estado || ''}</span>
                </div>
                <div class="bit-card-row">
                    <span class="bit-card-label">Portero:</span>
                    <span>${item.nombre_portero || ''}</span>
                </div>
                <div class="bit-card-row">
                    <span class="bit-card-label">Cargo:</span>
                    <span>${item.usuario_alias || ''}</span>
                </div>
            `;
                    cardContainer.appendChild(card);
                });
            }

            // Cargar datos iniciales
            fetch('../php/get_bitacora_owner.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    bitacoraData = data;
                    // Verificar estructura de los datos
                    console.log('Datos recibidos:', data[0]);
                    renderData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = `
                <tr>
                    <td colspan="13" class="bit-error">Error al cargar los datos</td>
                </tr>
            `;
                    cardContainer.innerHTML = '<div class="bit-error">Error al cargar los datos</div>';
                });
        });
    </script>

</body>

</html>