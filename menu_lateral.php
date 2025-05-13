<div class="barra-menu">
  <button onclick="location.href='dashboard.php'">📊 Dashboard</button>
  <button class="<?= basename($_SERVER['PHP_SELF']) == 'movimientos.php' ? 'activo' : '' ?>" onclick="location.href='movimientos.php'">📋 Movimientos</button>
  <button class="<?= basename($_SERVER['PHP_SELF']) == 'metas.php' ? 'activo' : '' ?>" onclick="location.href='metas.php'">🎯 Metas</button>
  <button class="<?= basename($_SERVER['PHP_SELF']) == 'cursos.php' ? 'activo' : '' ?>" onclick="location.href='cursos.php'">📚 Cursos</button>
</div>
    