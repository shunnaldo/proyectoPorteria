<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora de Ingresos</title>
    
</head>
<body>
    <h1>Bitácora de Ingresos</h1>
    <table id="tabla-bitacora" border="1">
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
        <tbody></tbody>
    </table>

    <script>
        fetch('../php/get_bitacora.php')
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector("#tabla-bitacora tbody");
                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.rut}</td>
                        <td>${item.fecha_hora}</td>
                        <td>${item.persona_nombre} ${item.persona_apellido}</td>
                        <td>${item.usuario_nombre} ${item.usuario_apellido}</td>
                        <td>${item.porton_nombre}</td>
                        <td>${item.ubicacion}</td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error('Error al cargar la bitácora:', error));
    </script>
</body>
</html>
