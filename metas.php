<?php
session_start();
require_once "ConexionBD/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

// Obtener nombre del usuario
$sqlUsuario = "SELECT nombres FROM usuarios WHERE id_usuario = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows > 0) {
    $row = $resultUsuario->fetch_assoc();
    $usuario = $row["nombres"];
} else {
    $usuario = "Usuario Desconocido";
}

$stmtUsuario->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Metas | EcoFinanzas Familiares</title>
  <link rel="stylesheet" href="styles/styles.css"/>

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
        <button class="activo"onclick="location.href='metas.php'">🎯 Metas</button>
        <button onclick="location.href='cursos.php'">📚 Cursos</button>
    </div>

    <h1>Metas de Ahorro</h1>
    <button class="btn-primary" onclick="abrirFormulario()">+ Registrar Meta</button>

    <div id="formularioMeta" class="modal">
      <div class="modal-content">
        <h2>Crea Nueva Meta</h2>
        <label>Nombre:</label>
        <input type="text" id="nombreMeta" placeholder="Ej: Laptop nueva"/>
        <label>Monto objetivo:</label>
        <input type="number" id="montoMeta" placeholder="Ej: 3000"/>
        <label>Fecha límite:</label>
        <input type="date" id="fechaMeta"/>
        <label>Prioridad:</label>
        <select id="prioridadMeta">
          <option>Alta</option>
          <option>Media</option>
          <option>Baja</option>
        </select>
        <label>Descripción:</label>
        <textarea id="descripcionMeta" placeholder="Detalles..."></textarea>
        <div class="modal-buttons">
          <button onclick="guardarMeta()">Guardar</button>
          <button onclick="cerrarFormulario()">Cancelar</button>
        </div>
      </div>
    </div>

    <section id="listaMetas"></section>
  </main>

  <!-- MENÚ LATERAL -->
  <div id="sidebar-menu" class="sidebar">
    <div class="sidebar-header">
      <h2><?= htmlspecialchars($usuario) ?></h2>
      <span id="close-menu">&times;</span>
    </div>
    <div class="sidebar-content">
      <a href="mi_cuenta.php">Mi Cuenta</a>
      <a href="billetera.php">Mi Billetera</a>
      <a href="ajustes.php">Ajustes</a>
      <a href="logout.php" class="logout">Cerrar sesión</a>
    </div>
  </div>

  <script>
    // Abrir/Cerrar menú lateral
    document.getElementById("menu-btn").addEventListener("click", function() {
      document.getElementById("sidebar-menu").style.transform = "translateX(0)";
    });

    document.getElementById("close-menu").addEventListener("click", function() {
      document.getElementById("sidebar-menu").style.transform = "translateX(100%)";
    });

    // Modal para registrar metas
    function abrirFormulario() {
      document.getElementById("formularioMeta").style.display = "flex";
    }

    function cerrarFormulario() {
      document.getElementById("formularioMeta").style.display = "none";
    }

    function guardarMeta() {
      const nombre = document.getElementById("nombreMeta").value;
      const monto = document.getElementById("montoMeta").value;
      const fecha = document.getElementById("fechaMeta").value;
      const prioridad = document.getElementById("prioridadMeta").value;
      const descripcion = document.getElementById("descripcionMeta").value;

      const contenedor = document.getElementById("listaMetas");
      const tarjeta = document.createElement("div");
      tarjeta.className = "meta-card";
      tarjeta.innerHTML = `
        <h3>${nombre}</h3>
        <p>Monto objetivo: S/ ${monto}</p>
        <p>Fecha límite: ${fecha}</p>
        <p>Prioridad: ${prioridad}</p>
        <p>${descripcion}</p>
      `;
      contenedor.appendChild(tarjeta);
      cerrarFormulario();
    }
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

