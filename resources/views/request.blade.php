<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Viaje</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none; /* Oculto por defecto */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Solicitar Viaje</h1>
        <form id="request-form">
            @csrf
            <!-- Campos del formulario -->
            <div class="form-group">
                <label for="pickup_location">Punto de recogida</label>
                <input type="text" class="form-control" id="pickup_location" name="pickup_location" required>
            </div>
            <div class="form-group">
                <label for="dropoff_location">Punto de entrega</label>
                <input type="text" class="form-control" id="dropoff_location" name="dropoff_location" required>
            </div>
            <div class="form-group">
                <label for="pickup_time">Hora de recogida</label>
                <input type="datetime-local" class="form-control" id="pickup_time" name="pickup_time" required>
            </div>
            <div class="form-group">
                <label for="dropoff_time">Hora de entrega</label>
                <input type="datetime-local" class="form-control" id="dropoff_time" name="dropoff_time" required>
            </div>
            <button type="submit" class="btn btn-primary">Solicitar Viaje</button>
        </form>
    </div>

    <div class="loading-spinner">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <h3 class="mt-3">Estamos buscando tu furgoneta...</h3>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#request-form').on('submit', function(e) {
                e.preventDefault(); // Prevenir la recarga de la página
                $('.loading-spinner').show(); // Mostrar el spinner

                $.ajax({
                    url: "{{ route('create_order') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('.loading-spinner').hide(); // Ocultar el spinner
                        alert('Pedido creado exitosamente');
                        // Puedes redirigir a otra página si es necesario
                        // window.location.href = "/ruta-deseada";
                    },
                    error: function(response) {
                        $('.loading-spinner').hide(); // Ocultar el spinner
                        alert('Hubo un error al crear el pedido');
                    }
                });
            });
        });
    </script>
</body>
</html>
