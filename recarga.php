<!-- recarga.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacer Recarga</title>
</head>
<body>
    <h1>Recarga de Saldo</h1>
    <form action="" method="POST">
        <label>ID del usuario:</label><br>
        <input type="number" name="user_id" required><br><br>
        <label>Monto a recargar:</label><br>
        <input type="number" name="amount" required><br><br>
        <button type="submit" name="recargar">Hacer Recarga</button>
    </form>

    <?php
    if (isset($_POST['recargar'])) {
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];
        
        // Conexión a la base de datos
        $conn = new mysqli('localhost', 'root', '', 'banco');
        
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Insertar registro de recarga
            $sql_insert = "INSERT INTO recargas (user_id, amount) VALUES ($user_id, $amount)";
            $conn->query($sql_insert);

            // Actualizar saldo del usuario
            $sql_update = "UPDATE usuarios SET saldo = saldo + $amount WHERE id = $user_id";
            $conn->query($sql_update);

            // Si ambas consultas son exitosas, hacer COMMIT
            $conn->commit();
            echo "Recarga exitosa y saldo actualizado.";
        } catch (Exception $e) {
            // Si ocurre algún error, hacer ROLLBACK
            $conn->rollback();
            echo "Error en la recarga: " . $e->getMessage();
        }

        $conn->close();
    }
    ?>
</body>
</html>
