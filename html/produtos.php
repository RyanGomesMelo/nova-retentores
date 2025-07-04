<?php
session_start();
require('../config/db.php'); // Inclui a conexão com o banco de dados

// Função para construir a árvore de categorias
function categoryTree($parent_id = 0, $sub_mark = '') {
    global $conn; // Use a conexão existente
    $query = $conn->query("SELECT * FROM category WHERE parent_id = $parent_id ORDER BY cat_name ASC");

    if ($query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            echo '<option value="' . $row['cat_id'] . '">' . $sub_mark . $row['cat_name'] . '</option>';
            categoryTree($row['cat_id'], $sub_mark . '---');
        }
    }
}

// Função para obter todas as subcategorias
function getCategoryChildren($parent_id, &$categories = []) {
    global $conn;
    $query = $conn->query("SELECT cat_id FROM category WHERE parent_id = $parent_id");
    while ($row = $query->fetch_assoc()) {
        $categories[] = $row['cat_id'];
        getCategoryChildren($row['cat_id'], $categories);
    }
    return $categories;
}

// Obtém a categoria selecionada (se houver)
$categoryId = isset($_GET['category']) ? $_GET['category'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Monta a consulta para buscar produtos, com base na categoria e na pesquisa, se fornecidos
$query = "SELECT * FROM product";
$whereClauses = [];

if ($categoryId != '') {
    // Busca todas as subcategorias da categoria selecionada
    $subcategories = [];
    function getSubcategories($parent_id) {
        global $conn;
        $subcategories = [];
        // Recupera as subcategorias diretas
        $query = $conn->query("SELECT cat_id FROM category WHERE parent_id = $parent_id");
        
        while ($row = $query->fetch_assoc()) {
            $subcategories[] = $row['cat_id'];
            // Recurssivamente obtém as subcategorias
            $subcategories = array_merge($subcategories, getSubcategories($row['cat_id']));
        }
        
        return $subcategories;
    }
}
    
    // Ao filtrar, certifique-se de pegar todas as subcategorias
    if ($categoryId != '') {
        // Busca todas as subcategorias da categoria selecionada
        $subcategories = getSubcategories($categoryId);
        $subcategories[] = $categoryId; // Inclui a própria categoria
        $whereClauses[] = "cat_id IN (" . implode(',', $subcategories) . ")";
    }

    if ($searchTerm != '') {
        // Se houver um termo de pesquisa, adiciona o filtro em múltiplos campos
        $escapedTerm = '%' . $conn->real_escape_string($searchTerm) . '%';
        $whereClauses[] = "(prod_name LIKE '$escapedTerm' OR prod_application LIKE '$escapedTerm' OR prod_size LIKE '$escapedTerm')";
    }

if (count($whereClauses) > 0) {
    $query .= " WHERE " . implode(' AND ', $whereClauses);
}

// PAGINAÇÃO
$produtosPorPagina = 30;
$paginaAtual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) && $_GET['pagina'] > 0 ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $produtosPorPagina;

// Consulta para contar o total de produtos filtrados
$countQuery = str_replace('SELECT *', 'SELECT COUNT(*) as total', $query);
$countResult = $conn->query($countQuery);
$totalProdutos = 0;
if ($countResult && $row = $countResult->fetch_assoc()) {
    $totalProdutos = (int)$row['total'];
}
$totalPaginas = ceil($totalProdutos / $produtosPorPagina);

// Adiciona LIMIT e OFFSET na query principal
$query .= " LIMIT $produtosPorPagina OFFSET $offset";

// Executa a consulta
$result = $conn->query($query);

if (!$result) {
    die("Consulta falhou: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Nova Retentores</title>
    <meta name="description" content="Empresa especializada no fornecimento de rolamentos e soluções para transmissão de movimento. Venda de Rolamentos para empresas.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for search icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Stylesheet -->
    <style>
        /* --- GERAL & RESET --- */
:root {
    --primary-color: #0056b3; /* Azul escuro principal */
    --secondary-color: #007bff; /* Azul mais claro */
    --accent-color: #ffc107; /* Amarelo/Dourado para botões e destaques */
    --dark-bg: #1a2238; /* Fundo escuro do footer e seções */
    --text-dark: #333;
    --text-light: #f4f4f4;
    --font-family: 'Roboto', sans-serif;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: var(--font-family);
    line-height: 1.6;
    color: var(--text-dark);
    background-color: #fff;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

a {
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}

ul {
    list-style: none;
}

img {
    max-width: 100%;
    height: auto;
    display: block;
}

.section-subtitle {
    color: var(--primary-color);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}
.section-subtitle-light {
    color: #ddd;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.section-title {
    font-size: 2.5rem;
    color: #222;
    margin-bottom: 20px;
}
.section-title-light {
    font-size: 2.5rem;
    color: #fff;
    margin-bottom: 20px;
}
.text-center {
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 12px 28px;
    border-radius: 5px;
    font-weight: 700;
    text-transform: uppercase;
    border: 2px solid transparent;
}

.btn-primary {
    background-color: var(--primary-color);
    color: #fff;
}
.btn-primary:hover {
    background-color: var(--secondary-color);
}
.btn-secondary {
    background-color: transparent;
    color: #fff;
    border: 2px solid #fff;
}
.btn-secondary:hover {
    background-color: #fff;
    color: var(--primary-color);
}


/* --- HEADER --- */
.top-bar {
    background-color: #f8f9fa;
    padding: 8px 0;
    font-size: 0.9rem;
    border-bottom: 1px solid #e7e7e7;
    display: none; /* Oculto no mobile, visível no desktop */
}
.top-bar-content {
    display: flex;
    justify-content: flex-start;
    align-items: center;
}
.email-contact {
    color: #555;
    font-weight: 500;
}
.email-contact:hover {
    color: var(--primary-color);
}

.main-header {
    background-color: #fff;
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.main-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo img {
    max-width: 150px;
}

.nav-links {
    display: flex;
    gap: 30px;
}

.nav-links a {
    font-weight: 500;
    color: #333;
    padding-bottom: 5px;
    border-bottom: 2px solid transparent;
}

.nav-links a:hover, .nav-links a.active {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.cta-button {
    background-color: #25D366;
    color: #fff;
    padding: 10px 20px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
}
.cta-button:hover {
    background-color: #128C7E;
}
.cta-button svg {
    width: 20px;
    height: 20px;
    fill: #fff;
}

/* --- HERO SECTION (HOME PAGE) --- */
.hero {
    position: relative;
    height: 600px;
    background-image: url('https://rolimbras.com.br/wp-content/uploads/2024/11/b1.jpg');
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    color: #fff;
}
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
}
.hero-content {
    position: relative;
    z-index: 2;
    max-width: 700px;
}
.hero h1 {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1rem;
}
.hero p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
}
.hero-buttons {
    display: flex;
    gap: 15px;
}


/* --- PAGE HERO (INTERNAL PAGES) --- */
.page-hero {
    position: relative;
    padding: 60px 0;
    background-image: url('https://rolimbras.com.br/wp-content/uploads/2024/11/b2.jpg');
    background-size: cover;
    background-position: center;
    text-align: center;
    color: #fff;
}
.page-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 42, 92, 0.85);
}
.page-hero-content {
    position: relative;
    z-index: 2;
}
.page-hero h1 {
    font-size: 2.8rem;
    font-weight: 900;
}
.breadcrumb {
    margin-top: 10px;
}
.breadcrumb a {
    color: #fff;
    font-weight: 500;
}
.breadcrumb a:hover {
    text-decoration: underline;
}

/* --- SEARCH & FILTER --- */
.search-filter-section {
    padding: 40px 0;
    background-color: #f7f7f7;
    border-bottom: 1px solid #e7e7e7;
}
.search-form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
}
.search-input, .category-select {
    flex: 1;
    min-width: 200px;
    padding: 12px 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    font-family: inherit;
}
.search-input:focus, .category-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(0, 86, 179, 0.2);
}
.search-button {
    padding: 12px 25px;
    background: var(--primary-color);
    color: #fff;
    border: none;
    border-radius: 5px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}
.search-button:hover {
    background-color: var(--secondary-color);
}

/* --- LIVE SEARCH STYLES --- */
#liveSearchResults {
    display: none;
    margin-top: 20px;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 2px solid var(--primary-color);
    position: relative;
}

#liveSearchResults::before {
    content: 'Resultados da busca em tempo real';
    position: absolute;
    top: -12px;
    left: 20px;
    background: #fff;
    padding: 0 10px;
    color: var(--primary-color);
    font-weight: 700;
    font-size: 0.9rem;
}

#liveSearchResults .product-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}

.live-search-loading {
    text-align: center;
    color: var(--primary-color);
    font-weight: 500;
    font-style: italic;
    padding: 40px 20px;
}

.live-search-loading::before {
    content: '🔍';
    display: block;
    font-size: 2rem;
    margin-bottom: 10px;
}

.live-search-no-results {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 40px 20px;
}

.live-search-no-results::before {
    content: '❌';
    display: block;
    font-size: 2rem;
    margin-bottom: 10px;
}

/* Animação de fade para os resultados */
#liveSearchResults {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Estilo para informações dos resultados */
.search-results-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    border-left: 4px solid var(--primary-color);
}

.search-results-info p {
    margin: 0;
    color: var(--primary-color);
    font-weight: 500;
}

.search-results-info strong {
    color: var(--secondary-color);
}

/* --- ABOUT US SECTION --- */
.about-us {
    padding: 80px 0;
}
.about-us-container {
    display: flex;
    align-items: center;
    gap: 50px;
}
.about-image {
    flex: 1;
}
.about-content {
    flex: 1;
}
.check-list {
    margin: 20px 0;
}
.check-list li {
    padding-left: 30px;
    position: relative;
    margin-bottom: 10px;
    font-weight: 500;
}
.check-list li::before {
    content: '✔';
    color: var(--primary-color);
    position: absolute;
    left: 0;
    font-size: 1.2rem;
}

/* --- PRODUCTS INTRO SECTION --- */
.products-intro {
    padding: 80px 0;
    text-align: center;
    position: relative;
    color: #fff;
    background-image: url('https://rolimbras.com.br/wp-content/uploads/2024/11/b3.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}
.products-intro-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 42, 92, 0.85);
}
.products-intro-content {
    position: relative;
    z-index: 2;
}

/* --- PRODUCTS GRID --- */
.products-grid {
    padding: 60px 0 80px 0;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}
.product-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.product-card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}
.product-card h3 {
    padding: 20px 20px 10px 20px;
    font-size: 1.2rem;
    color: var(--primary-color);
    min-height: 80px; /* Garante alinhamento */
}
.product-card p {
    padding: 0 20px 20px 20px;
    flex-grow: 1;
}
.card-link {
    display: block;
    padding: 15px 20px;
    background-color: #f4f4f4;
    text-align: right;
    font-weight: 700;
    color: var(--primary-color);
}
.card-link:hover {
    background-color: #e9e9e9;
}

/* --- PAGINATION --- */
.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 40px;
}
.pagination a {
    display: block;
    padding: 10px 18px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-weight: 700;
    background-color: #fff;
    color: var(--primary-color);
}
.pagination a:hover {
    background-color: #f4f4f4;
    border-color: #ccc;
}
.pagination a.active {
    background-color: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
}

/* --- TESTIMONIALS SECTION --- */
.testimonials {
    padding: 80px 0;
    background-color: #f0f4f8;
    position: relative;
}
.testimonials-container {
    display: flex;
    align-items: center;
    gap: 50px;
}
.testimonials-image {
    flex: 1;
}
.testimonials-content {
    flex: 1;
}
.testimonial-card {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    margin-top: 20px;
    border-left: 5px solid var(--primary-color);
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
}
.testimonial-quote p {
    font-style: italic;
    margin-bottom: 20px;
}
.testimonial-author {
    display: flex;
    align-items: center;
    gap: 15px;
}
.testimonial-author img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
}
.author-info strong {
    display: block;
    font-size: 1.1rem;
}
.author-info span {
    color: #777;
}

/* --- BRANDS SECTION --- */
.brands {
    padding: 60px 0;
}
.brands .section-title {
    margin-bottom: 40px;
}
.brand-logos {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    flex-wrap: wrap;
}
.brand-logos img {
    max-height: 50px;
    filter: grayscale(100%);
    opacity: 0.7;
    transition: all 0.3s ease;
}
.brand-logos img:hover {
    filter: grayscale(0%);
    opacity: 1;
}

/* --- FOOTER --- */
.main-footer {
    background-color: var(--dark-bg);
    color: var(--text-light);
    padding-top: 60px;
}
.footer-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px;
}
.footer-logo {
    max-width: 150px;
    margin-bottom: 20px;
}
.footer-col h4 {
    font-size: 1.2rem;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 10px;
}
.footer-col h4::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 2px;
    background-color: var(--primary-color);
}
.footer-col p {
    margin-bottom: 10px;
    color: #ccc;
}
.footer-col ul li {
    margin-bottom: 10px;
}
.footer-col a {
    color: #ccc;
}
.footer-col a:hover {
    color: #fff;
    padding-left: 5px;
}
.footer-bottom {
    border-top: 1px solid #444;
    text-align: center;
    padding: 20px 0;
    margin-top: 40px;
    font-size: 0.9rem;
    color: #aaa;
}
.footer-bottom a {
    color: #fff;
}


/* --- RESPONSIVIDADE --- */
@media (max-width: 992px) {
    .section-title { font-size: 2rem; }
    .hero h1 { font-size: 2.8rem; }
    
    .about-us-container, .testimonials-container {
        flex-direction: column;
        text-align: center;
    }
    .check-list li { text-align: left; }
    .footer-container { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 768px) {
    .top-bar { display: block; }
    .cta-button, .nav-links { display: none; } /* Idealmente usaria JS para um menu hamburger */
    .main-nav { justify-content: center; }
    .logo { margin: 0 auto; }

    .hero-content { text-align: center; }
    .hero-buttons { justify-content: center; }

    .search-form { flex-direction: column; }
    .search-input, .category-select, .search-button { width: 100%; }
    
    .footer-container { grid-template-columns: 1fr; }
    .footer-col { text-align: center; }
    .footer-col h4::after { left: 50%; transform: translateX(-50%); }
}

@media (max-width: 480px) {
    .grid { grid-template-columns: 1fr; }
}
        </style>
</head>
<body>

    <!-- Header (idêntico ao da home para consistência) -->
    <header class="main-header">
        <div class="container">
            <nav class="main-nav">
                <a href="index.html" class="logo">
                    <img src="../logos/Logo Preta.png" alt="Nova Retentores Logo">
                </a>
                <ul class="nav-links">
                    <li><a href="../index.html">Home</a></li>
                    <li><a href="../index.html#about-us">Quem somos</a></li>
                    <li><a href="produtos.php" class="active">Produtos</a></li>
                 
                </ul>
                <a href="https://wa.me/5511998353303" target="_blank" class="cta-button">
                    <svg aria-hidden="true" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path></svg>
                    Atendimento
                </a>
            </nav>
        </div>
    </header>

    <main>
        <!-- Page Hero Section -->
        <section class="page-hero">
            <div class="page-hero-overlay"></div>
            <div class="container page-hero-content">
                <h1>Produtos</h1>
                <p class="breadcrumb"><a href="index.html">Home</a> / Produtos</p>
            </div>
        </section>

        <!-- Search and Filter Section -->
        <section class="search-filter-section">
            <div class="container">
                <form class="search-form" method="GET" action="produtos.php">
                    <input type="text" name="search" id="search" placeholder="Buscar por nome do produto..." class="search-input" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <select name="category" id="category" class="category-select">
                        <option value="">Todas as Categorias</option>
                        <?php categoryTree(0, '', $categoryId); // Chama a função para popular as categorias ?>
                    </select>
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </form>
            </div>
        </section>

        <!-- Products Grid Section -->
        <section class="products-grid">
            <div class="container">
                <div class="grid" id="mainProductContainer">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($product = $result->fetch_assoc()): ?>
                            <div class="product-card">
                                <?php 
                                    $imagePath = !empty($product['image_path']) ? '../imagens/' . htmlspecialchars($product['image_path']) : 'imagens/default.jpg';
                                ?>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['prod_name']); ?>">
                                <h3><?php echo htmlspecialchars($product['prod_name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($product['prod_description'], 0, 100)) . '...'; ?></p>
                                <a href="detalhesProduto.php?id=<?php echo $product['prod_id']; ?>" class="card-link">Saiba Mais →</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem;">Nenhum produto encontrado com os filtros aplicados.</p>
                    <?php endif; ?>
                </div>

                <!-- Pagination Section -->
                <?php if ($totalPaginas > 1): ?>
                    <div class="pagination" id="paginationContainer">
                        <?php
                            // Constrói a URL base com os filtros atuais
                            $queryString = $_GET;
                            
                            for ($i = 1; $i <= $totalPaginas; $i++):
                                $queryString['pagina'] = $i;
                                $url = 'produtos.php?' . http_build_query($queryString);
                                $activeClass = ($i == $paginaAtual) ? 'active' : '';
                        ?>
                            <a href="<?php echo $url; ?>" class="<?php echo $activeClass; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <footer class="main-footer">
        <div class="container footer-container">
            <div class="footer-col">
                <img src="../logos/Logo Branca.png" alt="Nova Retentores Logo Branco" class="footer-logo">
                <p>A Nova Retentores é uma empresa especializada no fornecimento de rolamentos e soluções para transmissão de movimento.</p>
            </div>
            <div class="footer-col">
                <h4>Menu</h4>
                <ul>
                    <li><a href="../retentores van new/index.html">Home</a></li>
                    <li><a href="#">Quem somos</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Produtos</h4>
                <ul>
                    <li><a href="../retentores van new - Copy/html/produtos.php"">Rolamentos de rolos cônicos</a></li>
                    <li><a href="../retentores van new - Copy/html/produtos.php"">Mancais Bipartidos</a></li>
                    <li><a href="../retentores van new - Copy/html/produtos.php"">Buchas de Fixação</a></li>
                    <li><a href="../retentores van new - Copy/html/produtos.php"">Retentores</a></li>
                    <li><a href="../retentores van new - Copy/html/produtos.php"">Correias</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contato</h4>
                <p>Rua Lobo de Miranda, 233<br>Vila Maria Alta - SP<br>CEP: 02129-050</p>
                <p><a href="mailto:comercial@rolimbras.com.br">comercial@rolimbras.com.br</a></p>
                <p><a href="tel:11998353303">(11) 99835-3303</a></p>
                <p><a href="tel:1128420555">(11) 2842-0555</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
            <p>Copyright 2025 © Desenvolvido por Ryan Gomes (+55 11 94793-7482) </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript para pesquisa inteligente -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const categorySelect = document.getElementById('category');
            const searchForm = document.querySelector('.search-form');
            
            // Onde os resultados da busca principal e paginação são exibidos
            const mainProductContainer = document.getElementById('mainProductContainer');
            const paginationContainer = document.getElementById('paginationContainer');

            let searchTimeout;

            // Criar o contêiner para os resultados da busca em tempo real
            const liveSearchResultsContainer = document.createElement('div');
            liveSearchResultsContainer.id = 'liveSearchResults';
            // Inserir o novo contêiner logo após o formulário de busca
            searchForm.parentNode.insertBefore(liveSearchResultsContainer, searchForm.nextSibling);

            const performLiveSearch = () => {
                const query = searchInput.value.trim();
                const categoryId = categorySelect.value;

                // Se a busca for curta, esconde os resultados e mostra o conteúdo principal
                if (query.length < 2) {
                    liveSearchResultsContainer.style.display = 'none';
                    mainProductContainer.style.display = 'grid';
                    if (paginationContainer) {
                        paginationContainer.style.display = 'flex';
                    }
                    return;
                }
                
                // Mostra um feedback de "carregando"
                liveSearchResultsContainer.innerHTML = '<p class="live-search-loading">Buscando...</p>';
                liveSearchResultsContainer.style.display = 'block';

                // Esconde o conteúdo principal
                mainProductContainer.style.display = 'none';
                if (paginationContainer) {
                    paginationContainer.style.display = 'none';
                }

                // Faz a chamada AJAX usando a API Fetch
                fetch(`livesearch.php?q=${encodeURIComponent(query)}&category=${encodeURIComponent(categoryId)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro de rede: ' + response.statusText);
                        }
                        return response.text();
                    })
                    .then(html => {
                        // O livesearch.php já retorna os cards, então podemos criar um grid temporário
                        liveSearchResultsContainer.innerHTML = `<div class="product-container">${html}</div>`;
                    })
                    .catch(error => {
                        console.error('Erro na busca:', error);
                        liveSearchResultsContainer.innerHTML = '<p class="live-search-no-results">Ocorreu um erro. Tente novamente.</p>';
                    });
            };

            // Evento para o campo de busca com "debounce"
            searchInput.addEventListener('keyup', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performLiveSearch, 300); // Delay de 300ms
            });

            // Evento para o seletor de categoria
            categorySelect.addEventListener('change', performLiveSearch);

            // Limpar busca quando clicar fora dos resultados
            document.addEventListener('click', function(event) {
                const isClickInsideSearch = searchForm.contains(event.target) || liveSearchResultsContainer.contains(event.target);
                
                if (!isClickInsideSearch && liveSearchResultsContainer.style.display === 'block') {
                    // Se clicou fora e há resultados visíveis, limpa a busca
                    searchInput.value = '';
                    liveSearchResultsContainer.style.display = 'none';
                    mainProductContainer.style.display = 'grid';
                    if (paginationContainer) {
                        paginationContainer.style.display = 'flex';
                    }
                }
            });

            // Prevenir que o clique nos resultados feche a busca
            liveSearchResultsContainer.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        });
    </script>

</body>
</html>