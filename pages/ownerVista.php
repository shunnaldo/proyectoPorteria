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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../css/ownervista.css">

    <style>
        /* Estilos para el dropdown */
  .dropdown {
    position: relative;
    display: inline-block;
  }
  
  .dropbtn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    font-size: 14px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
  }
  
  .dropbtn:hover {
    background-color: #3e8e41;
  }
  
  .dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 4px;
    padding: 10px;
  }
  
  .dropdown:hover .dropdown-content {
    display: block;
  }
  
  /* Mantenemos los estilos originales de los botones y tooltips */
  .bit-excel-btn {
    background-color: #1d6f42;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    margin: 5px 0;
    width: 100%;
    text-align: left;
  }
  
  .bit-pdf-btn {
    background-color: #d40f0f;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    margin: 5px 0;
    width: 100%;
    text-align: left;
  }
  
  .tooltip {
    position: relative;
    display: inline-block;
    width: 100%;
  }
  
  .tooltip .tooltiptext {
    visibility: hidden;
    width: 140px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -70px;
    opacity: 0;
    transition: opacity 0.3s;
  }
  
  .tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
  }
  
  .tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
  }
    </style>

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
                <div class="dropdown">
                    <button class="dropbtn">Exportar <i class="fas fa-caret-down"></i></button>
                    <div class="dropdown-content">
                        <div class="tooltip">
                            <button id="bit-download-excel" class="bit-excel-btn">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                            <span class="tooltiptext">Descargar datos en Excel</span>
                        </div>
                        <div class="tooltip">
                            <button id="bit-download-pdf" class="bit-pdf-btn">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </button>
                            <span class="tooltiptext">Descargar datos en PDF</span>
                        </div>
                    </div>
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
                            fgColor: {
                                argb: 'FF000000'
                            } // Fondo negro
                        },
                        font: {
                            color: {
                                argb: 'FFFFFFFF'
                            }, // Letras blancas
                            bold: true
                        },
                        alignment: {
                            vertical: 'middle',
                            horizontal: 'center'
                        },
                        border: {
                            top: {
                                style: 'thin',
                                color: {
                                    argb: 'FFFFFFFF'
                                }
                            },
                            left: {
                                style: 'thin',
                                color: {
                                    argb: 'FFFFFFFF'
                                }
                            },
                            bottom: {
                                style: 'thin',
                                color: {
                                    argb: 'FFFFFFFF'
                                }
                            },
                            right: {
                                style: 'thin',
                                color: {
                                    argb: 'FFFFFFFF'
                                }
                            }
                        }
                    };

                    const dataStyle = {
                        fill: {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: {
                                argb: 'FFFFFFFF'
                            } // Fondo blanco
                        },
                        font: {
                            color: {
                                argb: 'FF000000'
                            } // Letras negras
                        },
                        alignment: {
                            vertical: 'middle'
                        },
                        border: {
                            top: {
                                style: 'thin',
                                color: {
                                    argb: 'FFCCCCCC'
                                }
                            }, // Bordes grises claros
                            left: {
                                style: 'thin',
                                color: {
                                    argb: 'FFCCCCCC'
                                }
                            },
                            bottom: {
                                style: 'thin',
                                color: {
                                    argb: 'FFCCCCCC'
                                }
                            },
                            right: {
                                style: 'thin',
                                color: {
                                    argb: 'FFCCCCCC'
                                }
                            }
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
                    worksheet.columns = [{
                            width: 12
                        }, {
                            width: 10
                        }, {
                            width: 10
                        }, {
                            width: 10
                        },
                        {
                            width: 25
                        }, {
                            width: 8
                        }, {
                            width: 5
                        }, {
                            width: 15
                        },
                        {
                            width: 12
                        }, {
                            width: 15
                        }, {
                            width: 12
                        }, {
                            width: 20
                        }, {
                            width: 15
                        }
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
                        text: error.message.includes('No hay datos') ?
                            'No hay registros para exportar con los filtros actuales' : 'Ocurrió un error al generar el archivo Excel',
                        confirmButtonColor: '#4361ee'
                    });
                }
            }

            document.getElementById('bit-download-pdf').addEventListener('click', function() {
                downloadPDF();
            });

            async function downloadPDF() {
                const pdfBtn = document.getElementById('bit-download-pdf');
                const originalText = pdfBtn.innerHTML;
                pdfBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
                pdfBtn.disabled = true;

                try {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF();

                    // Obtén los datos filtrados (igual que con el Excel)
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

                    // Agregar título al PDF
                    doc.setFontSize(18);
                    doc.text('Registros de Accesos - Mis Portones', 14, 20);

                    // Agregar los encabezados
                    const headers = [
                        'RUT', 'Fecha', 'Hora Ingreso', 'Hora Salida',
                        'Nombre Completo', 'Género', 'Edad', 'Transporte',
                        'Patente', 'Portón', 'Estado', 'Portero', 'Cargo Portero'
                    ];

                    doc.setFontSize(12);
                    doc.autoTable({
                        head: [headers],
                        body: filteredData.map(item => [
                            item.rut || '',
                            item.fecha || '--/--/----',
                            item.hora_ingreso || '--:--',
                            item.hora_salida || '--:--',
                            `${item.persona_nombre || ''} ${item.persona_apellido || ''}`.trim(),
                            item.genero || '',
                            calcularEdad(item.fecha_nacimiento) || '',
                            item.medio_transporte || '',
                            item.patente || 'N/A',
                            item.porton_nombre || '',
                            item.estado || '',
                            item.usuario_nombre || '',
                            item.usuario_alias || ''
                        ]),
                        startY: 30,
                        margin: {
                            top: 20
                        },
                        theme: 'grid',
                    });

                    // Guardar archivo
                    doc.save(`Registros_Portones_${new Date().toLocaleDateString('es-CL').replace(/\//g, '-')}.pdf`);

                    // Feedback de éxito
                    pdfBtn.innerHTML = '<i class="fas fa-check"></i> Descargado!';
                    setTimeout(() => {
                        pdfBtn.innerHTML = originalText;
                        pdfBtn.disabled = false;
                    }, 2000);

                } catch (error) {
                    console.error('Error al generar PDF:', error);
                    pdfBtn.innerHTML = originalText;
                    pdfBtn.disabled = false;

                    Swal.fire({
                        icon: error.message.includes('No hay datos') ? 'warning' : 'error',
                        title: error.message.includes('No hay datos') ? 'Sin datos' : 'Error',
                        text: error.message.includes('No hay datos') ?
                            'No hay registros para exportar con los filtros actuales' : 'Ocurrió un error al generar el archivo PDF',
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