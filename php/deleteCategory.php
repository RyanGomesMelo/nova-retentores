<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $cat_id = $_GET['id'];
    
    // Verificar se existem produtos associados
    $check_products = $conn->query("SELECT COUNT(*) as count FROM product WHERE cat_id = $cat_id");
    $product_count = $check_products->fetch_assoc()['count'];
    
    if ($product_count > 0) {
        $_SESSION['error'] = "Não é possível excluir esta categoria pois existem produtos associados a ela.";
        header('location: listCategories.php');
        exit;
    }
    
    $delete_query = "DELETE FROM category WHERE cat_id = $cat_id";
    if ($conn->query($delete_query)) {
        $_SESSION['success'] = "Categoria excluída com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao excluir categoria: " . $conn->error;
    }
    header('location: listCategories.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deletar Categoria</title>
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

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-danger {
            background: #dc3545;
            color: var(--text);
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--primary);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-title {
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .btn-secondary {
            background: var(--glass);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: var(--glass-border);
            transform: translateY(-2px);
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
            <h1 class="page-title">Excluir Categoria</h1>
            <a href="listCategories.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>

        <div class="categories-grid">
            <?php
            $result = $conn->query("SELECT c.*, COUNT(p.prod_id) as product_count 
                                   FROM category c 
                                   LEFT JOIN product p ON c.cat_id = p.cat_id 
                                   GROUP BY c.cat_id");

            while ($category = $result->fetch_assoc()):
            ?>
            <div class="category-card">
                <div class="category-header">
                    <h3 class="category-name">
                        <?= htmlspecialchars($category['cat_name']) ?>
                        <span style="font-size: 0.8em; color: var(--accent); opacity: 0.8;">#<?= $category['cat_id'] ?></span>
                    </h3>
                </div>
                <div style="margin-top: 15px;">
                    <p>Produtos na categoria: <?= $category['product_count'] ?></p>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <?php if ($category['product_count'] == 0): ?>
                        <button onclick="showDeleteModal(<?= $category['cat_id'] ?>, '<?= htmlspecialchars($category['cat_name']) ?>')" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Excluir
                        </button>
                    <?php else: ?>
                        <button class="btn btn-danger" disabled title="Não é possível excluir categorias com produtos">
                            <i class="fas fa-lock"></i>
                            Bloqueado
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-title">Confirmar Exclusão</h2>
            <p>Tem certeza que deseja excluir a categoria <strong id="categoryName"></strong>?</p>
            <p style="margin-top: 10px; color: var(--accent);">Esta ação não pode ser desfeita.</p>
            <div class="modal-buttons">
                <button onclick="hideDeleteModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <a href="#" id="confirmDelete" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Excluir
                </a>
            </div>
        </div>
    </div>

    <script>
        // Menu móvel
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Funções do Modal
        function showDeleteModal(categoryId, categoryName) {
            document.getElementById('categoryName').textContent = categoryName;
            document.getElementById('confirmDelete').href = 'deleteCategory.php?id=' + categoryId;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            if (event.target == document.getElementById('deleteModal')) {
                hideDeleteModal();
            }
        }
    </script>
</body>
</html>
