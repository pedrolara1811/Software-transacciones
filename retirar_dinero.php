<!-- retirar_dinero.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retirar Dinero</title>
</head>
<body>
    <h1>Retiro de Dinero</h1>
    <form action="" method="POST">
        <label>ID del usuario:</label><br>
        <input type="number" name="user_id" required><br><br>
        <label>Monto a retirar:</label><br>
        <input type="number" name="amount" required><br><br>
        <button type="submit" name="retirar">Retirar</button>
    </form>

    <?php
    if (isset($_POST['retirar'])) {
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
            // Verificar si el usuario tiene saldo suficiente
            $sql_check = "SELECT saldo FROM usuarios WHERE id = $user_id";
            $result = $conn->query($sql_check);
            $row = $result->fetch_assoc();

            if ($row['saldo'] >= $amount) {
                // Insertar registro de retiro
                $sql_insert = "INSERT INTO retiros (user_id, amount) VALUES ($user_id, $amount)";
                $conn->query($sql_insert);

                // Actualizar saldo del usuario
                $sql_update = "UPDATE usuarios SET saldo = saldo - $amount WHERE id = $user_id";
                $conn->query($sql_update);

                // Confirmar la transacción
                $conn->commit();
                echo "Retiro realizado exitosamente.";
            } else {
                throw new Exception("Saldo insuficiente.");
            }
        } catch (Exception $e) {
            // Si ocurre algún error, hacer ROLLBACK
            $conn->rollback();
            echo "Error en el retiro: " . $e->getMessage();
        }

        $conn->close();
    }
    ?>
</body>
</html>

