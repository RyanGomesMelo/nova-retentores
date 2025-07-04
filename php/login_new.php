<?php
session_start();
require('../config/db.php'); 
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT name, email, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin['name']; 
            $_SESSION['admin_email'] = $admin['email'];

            header('location: admin_dashboard.php');
            exit;
        } else {
            $error = 'Senha incorreta.';
        }
    } else {
        $error = 'Email não encontrado.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Selo Amortecedores Van</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a1a;
            --accent: #ff4d4d;
            --secondary: #00ff9d;
            --text: #ffffff;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Space Grotesk', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary), #2a2a2a);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--accent);
            border-radius: 50%;
            animation: float 4s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.3; }
            50% { transform: translateY(-20px) scale(1.2); opacity: 0.7; }
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem 2rem;
            position: relative;
            z-index: 1;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-logo {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .login-title {
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(255, 77, 77, 0.2);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .input-icon {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
            transition: var(--transition);
        }

        .form-input:focus + .input-icon {
            color: var(--accent);
        }

        .error-message {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.2);
            border-radius: 12px;
            padding: 1rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--accent), #ff6b6b);
            border: none;
            border-radius: 12px;
            color: var(--text);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }

            .login-logo {
                font-size: 2rem;
            }

            .login-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="particles">
        <?php for($i = 0; $i < 50; $i++): ?>
            <div class="particle" style="
                left: <?php echo rand(0, 100); ?>%;
                top: <?php echo rand(0, 100); ?>%;
                animation-delay: <?php echo rand(0, 4000)/1000; ?>s;
                opacity: <?php echo rand(1, 7)/10; ?>;
            "></div>
        <?php endfor; ?>
    </div>

    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">Selo Van</div>
            <h1 class="login-title">Área Administrativa</h1>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="email" 
                       name="email" 
                       class="form-input" 
                       placeholder="Seu email"
                       required
                       autocomplete="email">
                <i class="fas fa-envelope input-icon"></i>
            </div>

            <div class="form-group">
                <input type="password" 
                       name="password" 
                       class="form-input" 
                       placeholder="Sua senha"
                       required
                       autocomplete="current-password">
                <i class="fas fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>
    </div>

    <script>
        // Adiciona efeito de hover nos ícones
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', () => {
                input.nextElementSibling.style.color = 'var(--accent)';
            });
            input.addEventListener('blur', () => {
                input.nextElementSibling.style.color = 'rgba(255, 255, 255, 0.5)';
            });
        });
    </script>
</body>
</html>