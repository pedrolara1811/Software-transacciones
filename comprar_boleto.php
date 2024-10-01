<!-- comprar_boleto.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Boleto</title>
</head>
<body>
    <h1>Compra de Boleto</h1>
    <form action="" method="POST">
        <label>ID del usuario:</label><br>
        <input type="number" name="user_id" required><br><br>
        <label>ID del evento:</label><br>
        <input type="number" name="event_id" required><br><br>
        <button type="submit" name="comprar">Comprar Boleto</button>
    </form>

    <?php
    if (isset($_POST['comprar'])) {
        $user_id = $_POST['user_id'];
        $event_id = $_POST['event_id'];
        
        // Conexión a la base de datos
        $conn = new mysqli('localhost', 'root', '', 'eventos');

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Verificar si hay boletos disponibles
            $sql_check = "SELECT boletos_disponibles FROM eventos WHERE id = $event_id";
            $result = $conn->query($sql_check);
            $row = $result->fetch_assoc();

            if ($row['boletos_disponibles'] > 0) {
                // Insertar compra de boleto
                $sql_insert = "INSERT INTO compras (user_id, event_id) VALUES ($user_id, $event_id)";
                $conn->query($sql_insert);

                // Disminuir cantidad de boletos disponibles
                $sql_update = "UPDATE eventos SET boletos_disponibles = boletos_disponibles - 1 WHERE id = $event_id";
                $conn->query($sql_update);

                // Confirmar la transacción
                $conn->commit();
                echo "Boleto comprado exitosamente.";
            } else {
                throw new Exception("No hay boletos disponibles.");
            }
        } catch (Exception $e) {
            // Si ocurre algún error, hacer ROLLBACK
            $conn->rollback();
            echo "Error en la compra: " . $e->getMessage();
        }

        $conn->close();
    }
    ?>
</body>
</html>
