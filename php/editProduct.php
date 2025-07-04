<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php');
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM product";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " WHERE prod_name LIKE '%$search%'";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produtos</title>
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

        .table-container {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            transition: var(--transition);
            overflow-x: auto;
        }

        .table-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        th {
            color: var(--accent);
            font-weight: 600;
            font-size: 1.1rem;
        }

        td {
            font-size: 1rem;
        }

        tr:hover {
            background: var(--glass);
        }

        .btn {
            padding: 10px 20px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
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

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
        }

        .description-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

            .table-container {
                padding: 15px;
            }

            th, td {
                padding: 10px;
                font-size: 0.9rem;
            }

            .btn {
                padding: 8px 15px;
                font-size: 0.9rem;
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

        .search-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-input {
            padding: 10px 15px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            background: var(--glass);
            color: var(--text);
            font-size: 1rem;
            min-width: 250px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent);
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
                <a href="editProduct.php" class="nav-link active">
                    <i class="fas fa-edit"></i>
                    Editar Produtos
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
            <h1 class="page-title">Editar Produtos</h1>
            <div class="search-container">
                <form method="GET" action="editProduct.php" class="search-form">
                    <input type="text" name="search" placeholder="Buscar por nome..." value="<?= htmlspecialchars($search) ?>" class="search-input">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Buscar
                    </button>
                </form>
            </div>
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['prod_id']) ?></td>
                            <td>
                                <?php if (!empty($row['image_path'])): ?>
                                    <img src="../imagens/<?= htmlspecialchars($row['image_path']) ?>" 
                                         alt="<?= htmlspecialchars($row['prod_name']) ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['prod_name']) ?></td>
                            <td>R$ <?= number_format($row['prod_price'], 2, ',', '.') ?></td>
                            <td class="description-cell"><?= htmlspecialchars($row['prod_description']) ?></td>
                            <td>
                                <a href="editProductForm.php?prod_id=<?= $row['prod_id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </a>
                                <a href="deleteProduct.php?prod_id=<?= $row['prod_id'] ?>" class="btn btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                    <i class="fas fa-trash"></i>
                                    Deletar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>