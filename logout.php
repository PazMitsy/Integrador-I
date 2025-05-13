<?php
session_start();
session_destroy(); // Elimina la sesión activa
header("Location: login.php"); // Redirige al usuario al login
exit();
?>