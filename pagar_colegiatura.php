<!-- pagar_colegiatura.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar Colegiatura</title>
</head>
<body>
    <h1>Pagar Colegiatura</h1>
    <form action="" method="POST">
        <label>ID del estudiante:</label><br>
        <input type="number" name="student_id" required><br><br>
        <label>Monto del pago:</label><br>
        <input type="number" name="payment" required><br><br>
        <button type="submit" name="pagar">Pagar</button>
    </form>

    <?php
    if (isset($_POST['pagar'])) {
        $student_id = $_POST['student_id'];
        $payment = $_POST['payment'];
        
        // Conexión a la base de datos
        $conn = new mysqli('localhost', 'root', '', 'escuela');
        
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Insertar registro de pago
            $sql_insert = "INSERT INTO pagos (student_id, amount) VALUES ($student_id, $payment)";
            $conn->query($sql_insert);

            // Actualizar balance de colegiatura del estudiante
            $sql_update = "UPDATE estudiantes SET balance = balance - $payment WHERE id = $student_id";
            $conn->query($sql_update);

            // Confirmar la transacción
            $conn->commit();
            echo "Pago de colegiatura realizado exitosamente.";
        } catch (Exception $e) {
            // Si ocurre algún error, hacer ROLLBACK
            $conn->rollback();
            echo "Error en el pago: " . $e->getMessage();
        }

        $conn->close();
    }
    ?>
</body>
</html>
