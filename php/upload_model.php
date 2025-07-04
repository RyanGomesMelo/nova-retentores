<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/SELO FUNCIONAL DE AMORTECEDORES VAN/TESTE RE MAKE DB/retentores van new/config/db.php');

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php');
    exit;
}


$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    
    if ($product_id && isset($_FILES['model_file'])) {
        $file = $_FILES['model_file'];
        $allowed_types = ['model/gltf-binary', 'model/gltf+json', 'application/octet-stream'];
        $max_size = 50 * 1024 * 1024; // 50MB limit
        
        if ($file['size'] > $max_size) {
            $error = 'O arquivo é muito grande. Tamanho máximo: 50MB';
        } elseif (!in_array($file['type'], $allowed_types) && !preg_match('/\.(glb|gltf)$/', $file['name'])) {
            $error = 'Tipo de arquivo não permitido. Use arquivos .glb ou .gltf';
        } else {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/SELO FUNCIONAL DE AMORTECEDORES VAN/TESTE RE MAKE DB/retentores van new/models/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file['name']);
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Update database
                $query = "UPDATE product SET model_3d_path = ? WHERE prod_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", $filename, $product_id);
                
                if ($stmt->execute()) {
                    $message = 'Modelo 3D enviado com sucesso!';
                } else {
                    $error = 'Erro ao atualizar o banco de dados: ' . $conn->error;
                    // Remove uploaded file if database update fails
                    unlink($filepath);
                }
                $stmt->close();
            } else {
                $error = 'Erro ao fazer upload do arquivo';
            }
        }
    }
}

// Get list of products
$products = [];
$query = "SELECT prod_id, prod_name, model_3d_path FROM product ORDER BY prod_name";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Modelos 3D | Admin</title>
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
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: var(--primary);
            color: var(--text);
            line-height: 1.6;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            background: linear-gradient(45deg, var(--accent), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .upload-form {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        select, input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text);
            font-family: inherit;
        }

        button {
            background: var(--accent);
            color: var(--text);
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            transition: var(--transition);
        }

        button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid var(--secondary);
        }

        .error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid var(--accent);
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .product-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 1rem;
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .model-status {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .has-model {
            color: var(--secondary);
        }

        .no-model {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload de Modelos 3D</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form class="upload-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_id">Selecione o Produto:</label>
                <select name="product_id" id="product_id" required>
                    <option value="">Escolha um produto...</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['prod_id']; ?>">
                            <?php echo htmlspecialchars($product['prod_name']); ?>
                            <?php echo $product['model_3d_path'] ? ' (Tem modelo 3D)' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="model_file">Arquivo do Modelo 3D (.glb ou .gltf):</label>
                <input type="file" name="model_file" id="model_file" accept=".glb,.gltf" required>
            </div>
            
            <button type="submit">
                <i class="fas fa-upload"></i> Enviar Modelo
            </button>
        </form>
        
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <h3><?php echo htmlspecialchars($product['prod_name']); ?></h3>
                    <div class="model-status">
                        <?php if ($product['model_3d_path']): ?>
                            <span class="has-model">
                                <i class="fas fa-check-circle"></i> Tem modelo 3D
                            </span>
                        <?php else: ?>
                            <span class="no-model">
                                <i class="fas fa-times-circle"></i> Sem modelo 3D
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 