<?php
session_start();
require_once __DIR__ . '/../config/db.php'; 

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php'); 
    exit;
}

if (isset($_GET['prod_id'])) {
    $prod_id = $_GET['prod_id'];
    
    $stmt = $conn->prepare("SELECT * FROM product WHERE prod_id = ?");
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "Produto não encontrado.";
        exit;
    }
    
    $product = $result->fetch_assoc();
} else {
    echo "ID do produto não especificado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
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
            max-width: 800px;
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
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 77, 77, 0.2);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            padding: 12px 24px;
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

        .btn-danger {
            background: #dc3545;
            color: var(--text);
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .image-preview {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            margin-bottom: 15px;
        }

        .image-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: var(--glass);
            border-radius: 12px;
            border: 1px dashed var(--glass-border);
            cursor: pointer;
            transition: var(--transition);
        }

        .image-upload:hover {
            border-color: var(--accent);
        }

        .image-upload i {
            font-size: 2rem;
            color: var(--accent);
        }

        .image-upload input[type="file"] {
            display: none;
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

            .btn {
                padding: 10px 20px;
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
            <h1 class="page-title">Editar Produto</h1>
            <a href="editProduct.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>

        <div class="form-container">
            <form action="process_edit_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="prod_id" value="<?= htmlspecialchars($product['prod_id']) ?>">
                
                <div class="form-group">
                    <label class="form-label" for="prod_name">Nome do Produto</label>
                    <input type="text" class="form-control" id="prod_name" name="prod_name" 
                           value="<?= htmlspecialchars($product['prod_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="prod_price">Preço</label>
                    <input type="number" class="form-control" id="prod_price" name="prod_price" 
                           value="<?= htmlspecialchars($product['prod_price']) ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="prod_description">Descrição</label>
                    <textarea class="form-control" id="prod_description" name="prod_description" 
                              required><?= htmlspecialchars($product['prod_description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cat_id">Categoria</label>
                    <select class="form-control" id="cat_id" name="cat_id" required>
                        <?php
                        $catQuery = "SELECT * FROM category";
                        $catResult = $conn->query($catQuery);
                        while ($cat = $catResult->fetch_assoc()) {
                            $selected = ($cat['cat_id'] == $product['cat_id']) ? 'selected' : '';
                            echo "<option value='" . $cat['cat_id'] . "' " . $selected . ">" . htmlspecialchars($cat['cat_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Imagem Atual</label>
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="../imagens/<?= htmlspecialchars($product['image_path']) ?>" 
                             alt="<?= htmlspecialchars($product['prod_name']) ?>" 
                             class="image-preview">
                    <?php else: ?>
                        <div class="image-upload">
                            <i class="fas fa-image"></i>
                            <span>Nenhuma imagem selecionada</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_image">Nova Imagem (opcional)</label>
                    <div class="image-upload" onclick="document.getElementById('new_image').click()">
                        <i class="fas fa-upload"></i>
                        <span>Clique para selecionar uma nova imagem</span>
                        <input type="file" id="new_image" name="new_image" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        document.getElementById('new_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.image-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        const imageUpload = document.querySelector('.image-upload');
                        imageUpload.innerHTML = `<img src="${e.target.result}" class="image-preview">`;
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
