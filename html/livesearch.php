<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---- FIM DO CÓDIGO DE DEBUG --


require('../config/db.php'); // Inclui a sua conexão com o banco de dados

// Função para obter todas as subcategorias (copiada de catalogo.php para ser autossuficiente)
function getSubcategories($parent_id) {
    global $conn;
    $subcategories = [];
    $query = $conn->query("SELECT cat_id FROM category WHERE parent_id = $parent_id");
    
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $subcategories[] = $row['cat_id'];
            $subcategories = array_merge($subcategories, getSubcategories($row['cat_id']));
        }
    }
    return $subcategories;
}

// Pega os parâmetros da requisição AJAX
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoryId = isset($_GET['category']) ? $_GET['category'] : '';

// A busca só é executada se houver um termo de pesquisa com 2 ou mais caracteres
if (strlen($searchTerm) >= 2) {

    $whereClauses = [];
    $params = [];
    $types = '';

    // --- Monta a cláusula WHERE ---

    // 1. Filtro pelo termo de busca (em múltiplos campos)
    $searchQuery = '%' . $searchTerm . '%';
    $whereClauses[] = "(prod_name LIKE ? OR prod_application LIKE ? OR prod_size LIKE ? OR prod_description LIKE ?)";

    $params = [$searchQuery, $searchQuery, $searchQuery, $searchQuery];
    $types = 'ssss';

    // 2. Filtro por categoria (se selecionada)
    if ($categoryId != '' && is_numeric($categoryId)) {
        $subcategories = getSubcategories($categoryId);
        $subcategories[] = $categoryId; // Inclui a categoria pai
        
        $inClause = implode(',', array_fill(0, count($subcategories), '?'));
        $whereClauses[] = "cat_id IN ($inClause)";
        
        foreach ($subcategories as $subcat_id) {
            $params[] = $subcat_id;
            $types .= 'i'; // 'i' para integer
        }
    }

    // --- Constrói e executa a query com Prepared Statements para segurança ---
    $sql = "SELECT prod_id, prod_name, prod_description, image_path FROM product";
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }
    $sql .= " LIMIT 20"; // Limita o número de resultados na busca em tempo real

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Mostra quantos resultados foram encontrados
            echo '<div class="search-results-info">';
            echo '<p><strong>' . $result->num_rows . '</strong> produto(s) encontrado(s) para "' . htmlspecialchars($searchTerm) . '"</p>';
            echo '</div>';
            
            // Monta os cards de produto
            while ($product = $result->fetch_assoc()) {
                $image_path = !empty($product['image_path']) ? '../imagens/' . htmlspecialchars($product['image_path']) : '../imagens/default.jpg';
                $description = htmlspecialchars(substr($product['prod_description'], 0, 100)) . '...';
                echo '
                <div class="product-card">
                    <img src="' . $image_path . '" alt="' . htmlspecialchars($product['prod_name']) . '">
                    <h3>' . htmlspecialchars($product['prod_name']) . '</h3>
                    <p>' . $description . '</p>
                    <a href="detalhesProduto.php?id=' . $product['prod_id'] . '" class="card-link">Saiba Mais →</a>
                </div>';
            }
        } else {
            echo '<p class="live-search-no-results">Nenhum produto encontrado para "' . htmlspecialchars($searchTerm) . '".</p>';
        }
        $stmt->close();
    } else {
        echo '<p class="live-search-no-results">Erro na consulta ao banco de dados.</p>';
    }
} else {
    echo '<p class="live-search-no-results">Digite pelo menos 2 caracteres para buscar.</p>';
}

$conn->close();
?>