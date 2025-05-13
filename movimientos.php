<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Movimientos | EcoFinanzas Familiares</title>
  <link rel="stylesheet" href="styles/styles.css" />

</head>
<body>
  <header>
    <h1>EcoFinanzas Familiares</h1>
    <nav>
      <a href="#">Inicio</a>
      <a href="#">Sobre nosotros</a>
      <a href="#">Soporte</a>
      <a href="#">FAQ</a>
    </nav>
    <div class="acciones1">
      <button>📧</button>
      <button>🔔</button>
      <button>☰</button>
    </div>
  </header>

<main>
    <div class="barra-menu">
        <button onclick="location.href='dashboard.php'">📊 Dashboard</button>
        <button class="activo" onclick="location.href='movimientos.php'">📋 Movimientos</button>
        <button onclick="location.href='metas.php'">🎯 Metas</button>
        <button onclick="location.href='cursos.php'">📚 Cursos</button>
    </div>

    <section class="seccion-movimientos">
      <div class="encabezado">
        <h2>Movimientos</h2>
        <button id="btnAbrirModal">+ Registrar Movimiento</button>
      </div>

      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th># Orden</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Monto</th>
          </tr>
        </thead>
        <tbody id="tablaMovimientos">
          <!-- Se rellenará con JavaScript -->
        </tbody>
      </table>

      <div class="paginacion">
        <button>«</button>
        <button>‹</button>
        <button>1</button>
        <button>2</button>
        <button class="activo">3</button>
        <button>4</button>
        <button>›</button>
        <button>»</button>
      </div>
    </section>

    <!-- Modal de Registro -->
    <div id="modalRegistro" class="modal">
      <div class="modal-contenido">
        <h3>Registrar Movimiento</h3>
        <form id="formularioMovimiento">
          <label>Tipo de Movimiento:
            <select name="tipo">
              <option value="Ingreso">Ingreso</option>
              <option value="Gasto">Gasto</option>
            </select>
          </label>

          <label>Categoría:
            <select name="categoria">
              <option value="Servicios">Servicios</option>
              <option value="Alimentos">Alimentos</option>
              <option value="Transporte">Transporte</option>
            </select>
          </label>

          <label>Monto:
            <input type="number" name="monto" placeholder="Ej: 3000" required>
          </label>

          <label>Fecha:
            <input type="date" name="fecha" required>
          </label>

          <label>Descripción (opcional):
            <textarea name="descripcion"></textarea>
          </label>

          <div class="acciones-modal">
            <button type="button" id="btnCerrarModal">Cancelar</button>
            <button type="submit">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script >
    document.getElementById("btnAbrirModal").addEventListener("click", () => {
  document.getElementById("modalRegistro").style.display = "flex";
});

document.getElementById("btnCerrarModal").addEventListener("click", () => {
  document.getElementById("modalRegistro").style.display = "none";
});

document.getElementById("formularioMovimiento").addEventListener("submit", (e) => {
  e.preventDefault();
  const datos = new FormData(e.target);
  const nuevaFila = document.createElement("tr");
  nuevaFila.innerHTML = `
    <td>${datos.get("fecha")}</td>
    <td>SO${Math.floor(1000 + Math.random() * 9000)}</td>
    <td>${datos.get("categoria")}</td>
    <td>${datos.get("tipo")}</td>
    <td>$${parseFloat(datos.get("monto")).toFixed(2)}</td>
  `;
  document.getElementById("tablaMovimientos").appendChild(nuevaFila);
  document.getElementById("modalRegistro").style.display = "none";
  e.target.reset();
});

  </script>
</body>
</html>
