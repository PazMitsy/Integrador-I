<?php
session_start();
require_once "ConexionBD/conexion.php"; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["celular"];
    $correo = $_POST["correo"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_BCRYPT);
    $fecha_registro = date("Y-m-d");

    // Insertar usuario en la base de datos
    $sqlUsuario = "INSERT INTO usuarios (nombres, apellidos, direccion, telefono, correo, contraseña, fecha_registro) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    $stmtUsuario->bind_param("sssssss", $nombres, $apellidos, $direccion, $telefono, $correo, $contrasena, $fecha_registro);

    if ($stmtUsuario->execute()) {
        // Obtener el `id_usuario` recién creado
        $id_usuario = $stmtUsuario->insert_id;

        // Crear automáticamente la billetera para el usuario con saldo inicial de `0.00`
        $sqlBilletera = "INSERT INTO billeteras (id_usuario, saldo) VALUES (?, 0.00)";
        $stmtBilletera = $conn->prepare($sqlBilletera);
        $stmtBilletera->bind_param("i", $id_usuario);
        $stmtBilletera->execute();
        $stmtBilletera->close();

        // **Guardar el usuario en la sesión**
        $_SESSION["id_usuario"] = $id_usuario;

        // Redirigir al dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error en el registro. Inténtalo nuevamente.');</script>";
    }

    $stmtUsuario->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EcoFinanzas Familiares - Registro</title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --secondary-color: #10b981;
            --text-color: #1f2937;
            --light-gray: #f3f4f6;
            --mid-gray: #9ca3af;
            --dark-gray: #4b5563;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%);
        }
        
        .register-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-container:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .register-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-gray);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 1rem;
            background-color: white;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 0.5rem;
        }
        
        .checkbox-group a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .checkbox-group a:hover {
            text-decoration: underline;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 4px;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            width: 100%;
        }
        
        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .login-link {
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--dark-gray);
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .register-container {
                padding: 1.5rem;
            }
        }
    </style>
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
      <button>☰</button>
    </div>
  </header>

    <main>
        <div class="register-container">
            <h2 class="register-title">Registrarse</h2>
            <form action="registro.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" name="nombres" placeholder="Nombres*" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="celular" placeholder="Número de Celular">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" name="apellidos" placeholder="Apellidos*" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="correo" placeholder="Correo Electrónico*" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" name="direccion" placeholder="Dirección*" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="contrasena" placeholder="Contraseña*" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Registrarse</button>
            </form>
        </div>
    </main>
</body>
</html>