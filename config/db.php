<?php
$servername = "localhost";
$username = "root"; // seu nome de usuário do MySQL
$password = ""; // senha do MySQL (em geral é vazia no XAMPP)
$dbname = "teste_retentoresvan"; // nome do seu banco de dados

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

?>
