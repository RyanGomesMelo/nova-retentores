<?php
session_start();
require_once __DIR__ . '/../config/db.php'; // Inclua seu arquivo de conexão

// Verifica se o usuário está logado como admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php');
    exit;
}

// Inicializa variáveis
$message = '';
$category = null;

// Se um ID de categoria for passado, tenta carregar os dados
if (isset($_GET['cat_id'])) {
    $cat_id = $_GET['cat_id'];

    // Consulta para obter os dados da categoria
    $stmt = $conn->prepare("SELECT * FROM category WHERE cat_id = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();

    if (!$category) {
        $message = 'Categoria não encontrada.';
    }
} else {
    $message = 'ID da categoria não especificado.';
}

// Atualiza a categoria se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_name'], $cat_id)) {
    $cat_name = $_POST['cat_name'];

    $stmt = $conn->prepare("UPDATE category SET cat_name = ? WHERE cat_id = ?");
    $stmt->bind_param("si", $cat_name, $cat_id);

    if ($stmt->execute()) {
        header('location: admin_dashboard.php?message=Categoria atualizada com sucesso.');
        exit;
    } else {
        $message = 'Erro ao atualizar a categoria: ' . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria</title>
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
            width: 230px;
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

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
            margin-bottom: 2rem;
            display: block;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 12px 15px;
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
            background: var(--glass);
            border: 1px solid var(--glass-border);
        }

        .nav-link:hover, .nav-link.active {
            background: var(--accent);
            transform: translateX(5px);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 230px;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
        }

        .form-container {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            transition: var(--transition);
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-weight: 500;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            background: var(--glass);
            color: var(--text);
            transition: var(--transition);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(255, 77, 77, 0.2);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            background: var(--accent);
            color: var(--text);
        }

        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .message {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: var(--glass);
            border-radius: 8px;
            border: 1px solid var(--glass-border);
        }

        .error {
            color: #dc3545;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Light Mode */
        .light-mode {
            --primary: #ffffff;
            --text: #1a1a1a;
            --glass: rgba(0, 0, 0, 0.1);
            --glass-border: rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <header>
        <h1>Editar Categoria</h1>
        <nav>
        <a href="admin_dashboard.php">Painel Central</a>
            <a href="addCategory.php">Adicionar Categoria</a>
        
            <a href="deleteCategory.php">Deletar Categoria</a>
            <a href="addProduct.php">Adicionar Produto</a>
            <a href="editProduct.php">Editar Produto</a>
            <a href="deleteProduct.php">Deletar Produto</a>
            <a href="listCategories.php">Categorias Existentes</a> 
            <a href="logout.php">Sair</a>
        </nav>
    </header>
    <section>
        <?php if ($message): ?>
            <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if ($category): ?>
            <form action="" method="POST">
                <input type="text" name="cat_name" value="<?= htmlspecialchars($category['cat_name']) ?>" required>
                <button type="submit">Atualizar Categoria</button>
            </form>
        <?php else: ?>
            <p style="color: red;">Categoria não encontrada ou ID inválido.</p>
        <?php endif; ?>
    </section>
</body>
</html>
