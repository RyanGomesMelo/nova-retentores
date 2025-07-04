<?php
require('../config/db.php'); 

$email = 'jecatatu'; 
$password = '2906'; 
$name = 'jecatatu';
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Faz o hash da senha

// Insere o email, senha (hashed) e o nome no banco de dados
$stmt = $conn->prepare("INSERT INTO admin (email, password, name) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $hashed_password, $name);
$stmt->execute();

if ($stmt) {
    echo "Administrador criado com sucesso!";
} else {
    echo "Erro ao criar o administrador.";
}
?>
