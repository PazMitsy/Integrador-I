<?php
session_start();
require_once "ConexionBD/conexion.php"; // Conexión a la BD

// Validar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["usuario"];
    $contrasena = $_POST["contrasena"];

    // Consultar el usuario en la BD
    $sql = "SELECT id_usuario, contraseña FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_usuario, $hash_contrasena);
        $stmt->fetch();

        // Validar contraseña
        if (password_verify($contrasena, $hash_contrasena)) {
            $_SESSION["id_usuario"] = $id_usuario;
            header("Location: dashboard.php"); // Redirige si las credenciales son correctas
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EcoFinanzas Familiares - Login</title>
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
        
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-container:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }
        
        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
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
        
        .forgot-password {
            display: block;
            margin: 1rem 0;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .register-link {
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--dark-gray);
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .login-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
  <header>
    <h1>EcoFinanzas Familiares</h1>
    <nav>
      <a href="index.html">Inicio</a>
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
        <div class="login-container">
            <h2 class="login-title">Iniciar Sesión</h2>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <input type="email" class="form-control" name="usuario" placeholder="Correo*" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="contrasena" placeholder="Contraseña*" required>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar sesión</button>

                <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

                <div class="register-link">
                    ¿No tienes una cuenta? <a href="registro.php">Regístrate</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>