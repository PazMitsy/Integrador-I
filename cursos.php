<?php
session_start();
require_once "ConexionBD/conexion.php"; 

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

$sqlUsuario = "SELECT nombres FROM usuarios WHERE id_usuario = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

$usuario = ($resultUsuario->num_rows > 0) ? $resultUsuario->fetch_assoc()["nombres"] : "Usuario Desconocido";

$stmtUsuario->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Cursos - EcoFinanzas</title>
  <link rel="stylesheet" href="styles/styles.css" />
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
      <button class="activo" onclick="location.href='cursos.php'">📚 Cursos</button>
    </div>

    <h2 class="titulo">Cursos Actuales</h2>

    <div class="filtros">
      <button onclick="filtrar('popular')">Popular</button>
      <button onclick="filtrar('negocios')">Manejo de Negocios</button>
      <button onclick="filtrar('ahorro')">Ahorro</button>
    </div>

    <div id="cursos" class="contenedor-cursos"></div>
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
      <a href="logout.php" class="logout">Cerrar sesión</a>
    </div>
  </div>

  <script>
    // MENÚ LATERAL
    document.getElementById("menu-btn").addEventListener("click", function () {
      document.getElementById("sidebar-menu").style.transform = "translateX(0)";
    });

    document.getElementById("close-menu").addEventListener("click", function () {
      document.getElementById("sidebar-menu").style.transform = "translateX(100%)";
    });

    window.onclick = function (event) {
      if (event.target == document.getElementById("sidebar-menu")) {
        document.getElementById("sidebar-menu").style.transform = "translateX(100%)";
      }
    };

    // CURSOS
    const cursosData = [
      {
        titulo: "Curso de Ahorro Básico",
        categoria: "ahorro",
        video: "https://www.youtube.com/embed/Zv9GqzcjQXg"
      },
      {
        titulo: "Finanzas Personales Inteligentes",
        categoria: "popular",
        video: "https://www.youtube.com/embed/3YFvNGEaHk4"
      },
      {
        titulo: "Cómo manejar tu negocio",
        categoria: "negocios",
        video: "https://www.youtube.com/embed/BaEHkoWRkds"
      },
      {
        titulo: "Control de Gastos",
        categoria: "ahorro",
        video: "https://www.youtube.com/embed/NmlQU5AgF7M"
      },
      {
        titulo: "Invertir con poco dinero",
        categoria: "popular",
        video: "https://www.youtube.com/embed/0Lcf0QO3H_4"
      }
    ];

    function renderCursos(filtrarCategoria = "") {
      const contenedor = document.getElementById("cursos");
      contenedor.innerHTML = "";

      cursosData.forEach((curso) => {
        if (filtrarCategoria && curso.categoria !== filtrarCategoria) return;

        const cursoDiv = document.createElement("div");
        cursoDiv.className = "curso";

        cursoDiv.innerHTML = `
          <h3>${curso.titulo}</h3>
          <iframe src="${curso.video}" allowfullscreen></iframe>
          <button class="completar" onclick="marcarComoCompletado(this)">Marcar como completado</button>
          <div class="progress-bar"><div class="progress-bar-inner"></div></div>
        `;

        contenedor.appendChild(cursoDiv);
      });
    }

    function filtrar(categoria) {
      renderCursos(categoria);
    }

    function marcarComoCompletado(btn) {
      const bar = btn.nextElementSibling.querySelector(".progress-bar-inner");
      bar.style.width = "100%";
      btn.textContent = "✔ Completado";
      btn.disabled = true;
      btn.style.backgroundColor = "#888";
    }

    renderCursos();
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
