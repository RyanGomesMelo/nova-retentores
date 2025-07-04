<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php');
    exit;
}

$result = $conn->query("SELECT * FROM category");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Categorias</title>
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

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .category-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            transition: var(--transition);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .category-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--accent);
        }

        .category-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--text);
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: var(--text);
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .category-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--glass-border);
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--accent);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text);
            opacity: 0.8;
        }

        .add-category-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--accent);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);
        }

        .add-category-btn:hover {
            transform: translateY(-5px) scale(1.1);
            background: var(--secondary);
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

            .categories-grid {
                grid-template-columns: 1fr;
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
                <a href="editProduct.php" class="nav-link">
                    <i class="fas fa-edit"></i>
                    Editar Produtos
                </a>
            </li>
            <li class="nav-item">
                <a href="listCategories.php" class="nav-link active">
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
            <h1 class="page-title">Categorias</h1>
            <a href="addCategory.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nova Categoria
            </a>
        </div>

        <div class="categories-grid">
            <?php
            $query = "SELECT c.*, COUNT(p.prod_id) as product_count 
                     FROM category c 
                     LEFT JOIN product p ON c.cat_id = p.cat_id 
                     GROUP BY c.cat_id";
            $result = $conn->query($query);

            while ($category = $result->fetch_assoc()):
            ?>
            <div class="category-card">
                <div class="category-header">
                    <h3 class="category-name">
                        <?= htmlspecialchars($category['cat_name']) ?>
                        <span style="font-size: 0.8em; color: var(--accent); opacity: 0.8;">#<?= $category['cat_id'] ?></span>
                    </h3>
                    <div class="category-actions">
                        <a href="editCategory.php?id=<?= $category['cat_id'] ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="deleteCategory.php?id=<?= $category['cat_id'] ?>" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="category-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?= $category['product_count'] ?></div>
                        <div class="stat-label">Produtos</div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <a href="addCategory.php" class="add-category-btn">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
