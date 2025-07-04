<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Verifica se o usuário está logado como admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php'); // Redireciona para a página de login se não estiver logado
    exit;
}

// Se um ID de produto for passado, tenta deletá-lo
if (isset($_GET['prod_id'])) {
    $prod_id = $_GET['prod_id'];
    
    // Prepare a consulta SQL para excluir o produto
    $stmt = $conn->prepare("DELETE FROM product WHERE prod_id = ?");
    $stmt->bind_param("i", $prod_id);

    if ($stmt->execute()) {
        header('location: deleteProduct.php?message=Produto excluído com sucesso.');
    } else {
        echo "Erro ao excluir o produto.";
    }
}

// Consulta para obter todos os produtos
$result = $conn->query("SELECT * FROM product");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../imgs/suspensao-do-carro (1).png">
    <title>Deletar Produtos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            transition: background 0.3s, color 0.3s;
        }
        .dark-mode {
            background: rgb(49, 49, 49);
            color: #e0e0e0;
        }
        .light-mode {
            background: #ffffff;
            color: #000000;
        }
        .navbar {
            transition: background 0.3s;
        }
        .dark-mode .navbar {
            background: #222;
        }
        .light-mode .navbar {
            background: #f8f9fa;
        }
        .dark-mode .nav-link {
            color: #e0e0e0 !important;
        }
        .light-mode .nav-link {
            color: #000000 !important;
        }

        /* Estilo da tabela */
        table {
            width: 100%;
            margin-top: 30px;
            border-radius: 8px;
            overflow: hidden;
        }
        table th, table td {
            padding: 12px;
            text-align: center;
        }
        table thead {
            background-color: #333;
            color: white;
        }
        table tbody {
            background-color: #1e1e1e;
        }
        table tbody tr:nth-child(even) {
            background-color: #2c2c2c;
        }

        /* Estilo do botão Deletar */
        #Deletar {
            color: white;
            text-decoration: none;
            background-color: #dc3545;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        #Deletar:hover {
            background-color: #c82333;
        }

        /* Navbar e tema */
        .navbar {
            transition: background 0.3s;
        }
        .dark-mode .navbar {
            background: #222;
        }
        .light-mode .navbar {
            background: #f8f9fa;
        }
        .dark-mode .nav-link {
            color: #e0e0e0 !important;
        }
        .light-mode .nav-link {
            color: #000000 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">Painel de Administração</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="addCategory.php">Adicionar Categoria</a></li>
                    <li class="nav-item"><a class="nav-link" href="deleteCategory.php">Deletar Categoria</a></li>
                    <li class="nav-item"><a class="nav-link" href="addProduct.php">Adicionar Produto</a></li>
                    <li class="nav-item"><a class="nav-link" href="editProduct.php">Editar Produto</a></li>
                    <li class="nav-item"><a class="nav-link" href="deleteProduct.php">Deletar Produto</a></li>
                    <li class="nav-item"><a class="nav-link" href="listCategories.php">Categorias Existentes</a></li>
                    <li class="nav-item"><a class="nav-link" href="estat_dashboard.php">Gráficos de Cliques</a></li>
                </ul>
                <button id="theme-toggle" onclick="toggleTheme()" class="btn btn-outline-light">Trocar Tema</button>
                <a class="btn btn-danger ms-2" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>
    <div class="container text-center mt-5">
        <h2>Deletar Produtos</h2>
        <?php if (isset($_GET['message'])): ?>
            <p style="color: green;"><?= htmlspecialchars($_GET['message']) ?></p>
        <?php endif; ?>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Descrição</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['prod_id']) ?></td>
                        <td><?= htmlspecialchars($row['prod_name']) ?></td>
                        <td><?= htmlspecialchars($row['prod_price']) ?></td>
                        <td><?= htmlspecialchars($row['prod_description']) ?></td>
                        <td>
                            <a id="Deletar" href="deleteProduct.php?prod_id=<?= htmlspecialchars($row['prod_id']) ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Deletar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleTheme() {
        let body = document.body;
        let navbar = document.getElementById('navbar');
        let links = document.querySelectorAll('.nav-link');
        let button = document.getElementById('theme-toggle'); // Adicionado
        
        // Alternar modo claro/escuro
        let isDark = body.classList.toggle('light-mode');
        
        // Aplicar classes no corpo
        body.classList.toggle('dark-mode', !isDark);
        
        // Aplicar classes na navbar
        navbar.classList.toggle('navbar-dark', !isDark);
        navbar.classList.toggle('navbar-light', isDark);
        navbar.classList.toggle('bg-dark', !isDark);
        navbar.classList.toggle('bg-light', isDark);
        
        // Aplicar cores nos links
        links.forEach(link => link.classList.toggle('text-dark', isDark));
        links.forEach(link => link.classList.toggle('text-light', !isDark));
        
        // Alterar classe do botão (nova parte adicionada)
        if (isDark) { // Se estiver em modo claro
            button.classList.replace('btn-outline-light', 'btn-outline-dark');
        } else { // Se estiver em modo escuro
            button.classList.replace('btn-outline-dark', 'btn-outline-light');
        }
        
        // Salvar tema no localStorage
        localStorage.setItem('theme', isDark ? 'light' : 'dark');
    }

    (function() {
        let savedTheme = localStorage.getItem('theme') || 'dark';
        let isLightMode = savedTheme === 'light';
        let button = document.getElementById('theme-toggle'); // Adicionado
        
        // Aplicar tema salvo
        document.body.classList.toggle('light-mode', isLightMode);
        document.body.classList.toggle('dark-mode', !isLightMode);
        
        let navbar = document.getElementById('navbar');
        navbar.classList.toggle('navbar-dark', !isLightMode);
        navbar.classList.toggle('navbar-light', isLightMode);
        navbar.classList.toggle('bg-dark', !isLightMode);
        navbar.classList.toggle('bg-light', isLightMode);
        
        let links = document.querySelectorAll('.nav-link');
        links.forEach(link => link.classList.toggle('text-dark', isLightMode));
        links.forEach(link => link.classList.toggle('text-light', !isLightMode));
        
        // Aplicar classe inicial no botão (nova parte adicionada)
        if (isLightMode) {
            button.classList.replace('btn-outline-light', 'btn-outline-dark');
        } else {
            button.classList.replace('btn-outline-dark', 'btn-outline-light');
        }
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
