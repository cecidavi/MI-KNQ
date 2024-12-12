$('#formHistorialPrestamos').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    var fechaPrestamo = $('#fecha_prestamo').val();

    if (!fechaPrestamo) {
        alert("Por favor, seleccione una fecha.");
        return;
    }

    $.ajax({
        type: "GET",
        url: "consultar_historial_prestamos.php",
        data: { fecha: fechaPrestamo },
        success: function(data) {
            $('#historialPrestamos').html(data);
        },
        error: function(xhr, status, error) {
            alert("Error al obtener el historial de préstamos: " + xhr.responseText);
        }
    });
});
