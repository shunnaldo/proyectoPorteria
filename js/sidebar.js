const ruta = window.location.pathname.includes('/pages') ? 'sidebar.html' : 'html/sidebar.html';

fetch(ruta)
    .then(response => response.text())
    .then(data => {
        document.getElementById('sidebar-container').innerHTML = data;
    })
    .catch(error => console.error('Error al cargar el sidebar:', error));