<?php
include __DIR__ . '/../config/db.php';

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
    $product_id = intval($_POST['id']);

    // Prepare a consulta SQL
    $stmt = $conn->prepare("UPDATE product SET clicks = clicks + 1 WHERE prod_id = ?");
    
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // Vincule o parâmetro
    $stmt->bind_param("i", $product_id);

    // Execute a consulta
    if ($stmt->execute()) {
        echo "Sucesso! Produto ID $product_id atualizado.";
    } else {
        echo "Erro ao atualizar produto: " . $stmt->error;
    }

    // Feche a declaração
    $stmt->close();
} else {
    echo "Nenhum ID recebido.";
}

$conn->close();
?>
