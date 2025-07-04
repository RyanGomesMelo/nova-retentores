<?php
session_start();
require_once __DIR__ . '/../config/db.php'; 

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php'); 
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prod_id = $_POST['prod_id'];
    $prod_name = $_POST['prod_name'];
    $prod_price = $_POST['prod_price'];
    $prod_description = $_POST['prod_description'];
    $cat_id = $_POST['cat_id'];

    // Buscar o caminho da imagem atual
    $stmt = $conn->prepare("SELECT image_path FROM product WHERE prod_id = ?");
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $stmt->bind_result($current_image_path);
    $stmt->fetch();
    $stmt->close();

    $image_path = $current_image_path;

    if (!empty($_FILES['new_image']['name'])) {
        $target_dir = "../imagens/"; // Diretório onde a imagem será salva
        $new_filename = uniqid() . "_" . basename($_FILES["new_image"]["name"]);
        $target_file = $target_dir . $new_filename;
    
        if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $target_file)) {
            // Excluir a imagem antiga se existir
            if (!empty($current_image_path) && file_exists("../imagens/" . $current_image_path)) {
                unlink("../imagens/" . $current_image_path);
            }
            
            $image_path = $new_filename;
        } else {
            die("Erro ao mover o arquivo. Verifique permissões da pasta.");
        }
    }

    // Atualizar os dados no banco de dados
    $stmt = $conn->prepare("UPDATE product SET prod_name = ?, prod_price = ?, prod_description = ?, image_path = ?, cat_id = ? WHERE prod_id = ?");
    $stmt->bind_param("sdssii", $prod_name, $prod_price, $prod_description, $image_path, $cat_id, $prod_id);

    if ($stmt->execute()) {
        header('location: editProduct.php?success=1');
    } else {
        header('location: editProduct.php?error=1');
    }

    $stmt->close();
}

$conn->close();
?>
