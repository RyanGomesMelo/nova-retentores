<?php
session_start();

// Verifica se o admin está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php');
    exit;
}

// Processa o envio do formulário para adicionar a categoria
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];

    
    header('location: addCategory.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Categoria - Painel Administrativo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../imgs/suspensao-do-carro (1).png">
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
            background: var(--primary);
            color: var(--text);
            overflow-x: hidden;
            transition: var(--transition);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            padding: 20px;
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--glass-border);
            transition: var(--transition);
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px 0;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 20px;
        }

        .sidebar-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            color: var(--text);
            text-decoration: none;
            border-radius: 12px;
            transition: var(--transition);
            background: var(--glass);
            border: 1px solid var(--glass-border);
            font-size: 1.1rem;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--accent);
            transform: translateX(5px);
        }

        .nav-link i {
            width: 25px;
            text-align: center;
            font-size: 1.2rem;
        }

        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--glass);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            transition: var(--transition);
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text);
            font-weight: 500;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            background: var(--glass);
            color: var(--text);
            transition: var(--transition);
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 77, 77, 0.2);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--text);
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: var(--glass);
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .alert-success {
            background: rgba(0, 255, 157, 0.1);
            border: 1px solid var(--secondary);
            color: var(--secondary);
        }

        .alert-danger {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .form-container {
                padding: 20px;
            }
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            left: 20px;
            top: 20px;
            z-index: 1001;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            padding: 10px;
            color: var(--text);
        }

        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../imgs/suspensao-do-carro (1).png" alt="Logo">
            <h4>Painel Admin</h4>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="addProduct.php" class="nav-link">
                    <i class="fas fa-plus"></i>
                    Adicionar Produto
                </a>
            </li>
            <li class="nav-item">
                <a href="addCategory.php" class="nav-link active">
                    <i class="fas fa-folder-plus"></i>
                    Adicionar Categoria
                </a>
            </li>
            <li class="nav-item">
                <a href="listCategories.php" class="nav-link">
                    <i class="fas fa-list"></i>
                    Categorias
                </a>
            </li>
            <li class="nav-item">
                <a href="estat_dashboard.php" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    Estatísticas
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Adicionar Nova Categoria</h1>
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert <?php echo strpos($message, 'sucesso') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="process_add_category.php">
                <div class="form-group">
                    <label for="category_name">Nome da Categoria</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                </div>

                <div class="form-group">
                    <label for="parent_id">Categoria Pai (Opcional)</label>
                    <select class="form-control" id="parent_id" name="parent_id">
                        <option value="0">Nenhuma (Categoria Principal)</option>
                        <?php
                        $catQuery = "SELECT cat_id, cat_name FROM category";
                        $catResult = $conn->query($catQuery);
                        while ($cat = $catResult->fetch_assoc()) {
                            echo "<option value='" . $cat['cat_id'] . "'>" . htmlspecialchars($cat['cat_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Salvar Categoria
                    </button>
                    <a href="listCategories.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i>
                        Ver Categorias
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
