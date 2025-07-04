<?php
session_start();
// Certifique-se de que o caminho para o arquivo db.php está correto
require_once __DIR__ . '/../config/db.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $stmt = $conn->prepare("SELECT name, email, password FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // Verifica a senha
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_name'] = $admin['name']; 
                $_SESSION['admin_email'] = $admin['email'];

                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error = 'Email ou senha incorretos.';
            }
        } else {
            $error = 'Email ou senha incorretos.';
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel de Administração Rolimbras</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Favicon (opcional) -->
    <link rel="icon" type="image/png" href="https://rolimbras.com.br/wp-content/uploads/2024/11/cropped-favicon-32x32.png">

    <style>
        /* --- Tema da Página de Login Rolimbras --- */
        :root {
            --primary-color: #0056b3; /* Azul principal */
            --light-bg: #f4f7fc;      /* Fundo claro */
            --border-color: #dee2e6;  /* Cor da borda */
            --text-dark: #343a40;      /* Texto escuro */
            --text-muted: #6c757d;    /* Texto mais claro */
            --error-color: #dc3545;   /* Vermelho para erros */
            --font-family: 'Roboto', sans-serif;
            --shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-family);
        }

        body {
            min-height: 100vh;
            background-color: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: var(--shadow);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo {
            max-width: 200px;
            margin-bottom: 15px;
        }

        .login-title {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px 12px 40px; /* Espaço para o ícone */
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-dark);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15);
        }

        .form-input::placeholder {
            color: #adb5bd;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ced4da;
            transition: var(--transition);
        }

        .form-input:focus + .input-icon {
            color: var(--primary-color);
        }

        .error-message {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 6px;
            padding: 12px 15px;
            color: var(--error-color);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background-color: #00458e; /* Um tom de azul mais escuro */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 86, 179, 0.25);
        }

        .btn-login:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <header class="login-header">
            <img src="/retentores van new - Copy/logos/Logo Preto.png" alt="Rolimbras Logo" class="login-logo">
            <h1 class="login-title">Acesso Restrito</h1>
            <p class="login-subtitle">Painel de Administração</p>
        </header>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-times-circle"></i> 
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="form-group">
                <input type="email" 
                       name="email" 
                       class="form-input" 
                       placeholder="Endereço de e-mail"
                       required
                       autocomplete="email">
                <i class="fas fa-envelope input-icon"></i>
            </div>

            <div class="form-group">
                <input type="password" 
                       name="password" 
                       class="form-input" 
                       placeholder="Senha"
                       required
                       autocomplete="current-password">
                <i class="fas fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>
    </div>
</body>
</html>