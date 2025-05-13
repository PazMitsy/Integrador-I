<?php
session_start();
require_once "ConexionBD/conexion.php"; 

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
// Consultar el nombre del usuario antes de usar la variable
$sqlUsuario = "SELECT nombres FROM usuarios WHERE id_usuario = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows > 0) {
    $rowUsuario = $resultUsuario->fetch_assoc();
    $usuario = $rowUsuario["nombres"];
} else {
    $usuario = "Usuario Desconocido"; // Previene errores si no se encuentra el usuario
}

$stmtUsuario->close();
// Obtener `id_billetera` del usuario
$sqlBilletera = "SELECT id_billetera, saldo FROM billeteras WHERE id_usuario = ?";
$stmtBilletera = $conn->prepare($sqlBilletera);
$stmtBilletera->bind_param("i", $id_usuario);
$stmtBilletera->execute();
$resultBilletera = $stmtBilletera->get_result();
$row = $resultBilletera->fetch_assoc();
$id_billetera = $row["id_billetera"] ?? null;
$saldo = $row["saldo"] ?? 0;
$stmtBilletera->close();

// Si el usuario no tiene billetera, crearla
if (!$id_billetera) {
    $sqlCrearBilletera = "INSERT INTO billeteras (id_usuario, saldo) VALUES (?, 0)";
    $stmtCrear = $conn->prepare($sqlCrearBilletera);
    $stmtCrear->bind_param("i", $id_usuario);
    $stmtCrear->execute();
    $stmtCrear->close();

    // Obtener nuevamente `id_billetera`
    $stmtBilletera = $conn->prepare($sqlBilletera);
    $stmtBilletera->bind_param("i", $id_usuario);
    $stmtBilletera->execute();
    $resultBilletera = $stmtBilletera->get_result();
    $row = $resultBilletera->fetch_assoc();
    $id_billetera = $row["id_billetera"] ?? null;
    $saldo = $row["saldo"] ?? 0;
    $stmtBilletera->close();
}

// Procesar depósito si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deposito"])) {
    $monto = floatval($_POST["deposito"]);

    if ($monto > 0 && $id_billetera) {
        // Actualizar saldo en la billetera
        $nuevoSaldo = $saldo + $monto;
        $sqlUpdate = "UPDATE billeteras SET saldo = ? WHERE id_billetera = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("di", $nuevoSaldo, $id_billetera);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Registrar movimiento
        $sqlMovimiento = "INSERT INTO movimientos_billetera (id_billetera, tipo_movimiento, monto, descripcion) 
                          VALUES (?, 'deposito', ?, 'Depósito realizado')";
        $stmtMovimiento = $conn->prepare($sqlMovimiento);
        $stmtMovimiento->bind_param("id", $id_billetera, $monto);
        $stmtMovimiento->execute();
        $stmtMovimiento->close();

        // Redirigir para evitar reenvío del formulario
        header("Location: billetera.php");
        exit();
    } else {
        $error = "Error al procesar el depósito.";
    }
}

// Consultar historial de transacciones
$sqlHistorial = "SELECT fecha_movimiento, tipo_movimiento, monto, descripcion FROM movimientos_billetera
                 WHERE id_billetera = ?
                 ORDER BY fecha_movimiento DESC LIMIT 10";
$stmtHistorial = $conn->prepare($sqlHistorial);
$stmtHistorial->bind_param("i", $id_billetera);
$stmtHistorial->execute();
$resultHistorial = $stmtHistorial->get_result();
$stmtHistorial->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Billetera Digital - EcoFinanzas Familiares</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header>
        <h1>EcoFinanzas Familiares</h1>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="nosotros.php">Sobre nosotros</a>
            <a href="soporte.php">Soporte</a>
            <a href="faq.php">FAQ</a>
        </nav>
        <div class="acciones1">
            <button>📧</button>
            <button>🔔</button>
            <button id="menu-btn">☰</button>
        </div>
    </header>
<main>
    <div class="barra-menu">
        <button onclick="location.href='dashboard.php'">📊 Dashboard</button>
        <button onclick="location.href='movimientos.php'">📋 Movimientos</button>
        <button onclick="location.href='metas.php'">🎯 Metas</button>
        <button onclick="location.href='cursos.php'">📚 Cursos</button>
    </div>

    <!-- Menú lateral estilo Outlook -->
    <div id="sidebar-menu" class="sidebar">
        <div class="sidebar-header">
            <span id="close-menu">&times;</span>
            <h2><?= htmlspecialchars($usuario) ?></h2>
        </div>
        <div class="sidebar-content">
            <a href="mi_cuenta.php">Mi Cuenta</a>
            <a href="billetera.php">Mi Billetera</a>
            <a href="ajustes.php">Ajustes</a>
            <a href="logout.php" class="logout">Cerrar sesión</a>
        </div>
    </div>

    <main class="main-content">
        <h2>Mi Billetera Digital</h2>

        <div class="wallet-box">
            <p><strong>Saldo actual:</strong> S/ <span><?= number_format($saldo, 2) ?></span></p>

            <form action="billetera.php" method="POST">
                <label for="deposito">Monto a depositar:</label>
                <input type="number" name="deposito" placeholder="Ej. 100.00" required>
                <button type="submit" class="btn-action">💰 Depositar</button>
            </form>

            <?php if (isset($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="qr-section">
                <p>Escanea el código QR para depositar (Yape / Plin):</p>
                <img src="img/yape.jpg" alt="QR EcoFinanzas">
            </div>
        </div>

        <section class="acciones">
            <h3>Historial de transacciones</h3>
            <ul>
                <?php while ($row = $resultHistorial->fetch_assoc()): ?>
                    <li>
                        <strong><?= $row["tipo_movimiento"] ?></strong> - S/ <?= number_format($row["monto"], 2) ?>
                        <br><?= $row["descripcion"] ?> (<?= $row["fecha_movimiento"] ?>)
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>

    <script>
        document.getElementById("menu-btn").addEventListener("click", function() {
            document.getElementById("sidebar-menu").style.transform = "translateX(0)";
        });

        document.getElementById("close-menu").addEventListener("click", function() {
            document.getElementById("sidebar-menu").style.transform = "translateX(100%)";
        });
    </script>

    <style>
        /* MENÚ LATERAL DESDE LA DERECHA */
        .sidebar {
            position: fixed;
            top: 0; right: 0;
            width: 250px;
            height: 100%;
            background: white;
            box-shadow: -2px 0px 10px rgba(0,0,0,0.2);
            transform: translateX(100%);
            transition: 0.3s;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .sidebar-header span {
            cursor: pointer;
            font-size: 22px;
        }

        .sidebar-content a {
            display: block;
            padding: 15px;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
        }

        .sidebar-content a:hover {
            background: #f3f3f3;
        }

        .logout {
            color: red;
            font-weight: bold;
        }
    </style>
</body>
</html>