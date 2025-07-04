<?php
session_start();
require_once '../config/db.php'; 


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $category_name = $_POST['category_name'];
    $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;

    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO category (cat_name, parent_id) VALUES (?, ?)");
        $stmt->bind_param("si", $category_name, $parent_id);
        
        if ($stmt->execute()) {
            header("Location: addCategory.php?message=Categoria adicionada com sucesso");
        } else {
            echo "Erro ao adicionar categoria: " . $stmt->error;
        }
    } else {
        echo "Por favor, preencha o nome da categoria.";
    }
} else {
    header('location: addCategory.php');
}
?>
