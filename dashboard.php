<?php
session_start();
require_once "ConexionBD/conexion.php"; 

// Si el usuario no ha iniciado sesión, redirigir a login.php
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

// Consultar el nombre del usuario
$sqlUsuario = "SELECT nombres FROM usuarios WHERE id_usuario = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows > 0) {
    $row = $resultUsuario->fetch_assoc();
    $usuario = $row["nombres"];
} else {
    $usuario = "Usuario Desconocido"; // **Se evita que se cree otro usuario inesperadamente**
}

$stmtUsuario->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - EcoFinanzas Familiares</title>
    <link rel="stylesheet" href="styles/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <button class="activo"onclick="location.href='dashboard.php'">📊 Dashboard</button>
        <button onclick="location.href='movimientos.php'">📋 Movimientos</button>
        <button onclick="location.href='metas.php'">🎯 Metas</button>
        <button onclick="location.href='cursos.php'">📚 Cursos</button>
    </div>
</main>
    <!-- MENÚ LATERAL ESTILO OUTLOOK -->
    <div id="sidebar-menu" class="sidebar">
        <div class="sidebar-header">
            <span id="close-menu">&times;</span>
            <h2><?= htmlspecialchars($usuario) ?></h2>
        </div>
        <div class="sidebar-content">
            <a href="mi_cuenta.php">Mi Cuenta</a>
            <a href="billetera.php">Mi Billetera</a>
            <a href="ajustes.php">Ajustes</a>
            <a href="logout.php" class="logout">Cerrar sesión</a> <!-- Nueva opción de logout -->
        </div>
    </div>

    <main class="dashboard">
        <div class="stats-grid">
            <div class="card"><h3>Ingresos Mensuales</h3><p>$0</p></div>
            <div class="card"><h3>Gastos Totales</h3><p>$0</p></div>
            <div class="card"><h3>Ahorro Actual</h3><p>$0</p></div>
        </div>
    </main>

    <script>
        document.getElementById("menu-btn").addEventListener("click", function() {
            document.getElementById("sidebar-menu").style.transform = "translateX(0)";
        });

        document.getElementById("close-menu").addEventListener("click", function() {
            document.getElementById("sidebar-menu").style.transform = "translateX(100%)";
        });

        window.onclick = function(event) {
            if (event.target == document.getElementById("sidebar-menu")) {
                document.getElementById("sidebar-menu").style.transform = "translateX(100%)";
            }
        };
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