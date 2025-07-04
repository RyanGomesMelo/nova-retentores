<?php
session_start();
// Ajuste o caminho conforme a estrutura de suas pastas.
// Este exemplo assume que a pasta 'config' está no mesmo nível que este arquivo.
require_once('../config/db.php');

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$product_id) {
    die("ID do produto não fornecido.");
}

// --- BUSCAR DETALHES DO PRODUTO PRINCIPAL ---
$query = "SELECT * FROM product WHERE prod_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Erro na preparação da consulta: ' . $conn->error);
}

$stmt->bind_param("i", $product_id);
if (!$stmt->execute()) {
    die('Erro ao executar a consulta: ' . $stmt->error);
}

$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Produto não encontrado.");
}
$stmt->close();


// --- BUSCAR PRODUTOS RELACIONADOS (MESMA CATEGORIA) ---
$related_products = [];
if (isset($product['cat_id'])) {
    $cat_id = $product['cat_id'];
    
    $sql_related = "SELECT prod_id, prod_name, image_path, prod_description 
                    FROM product 
                    WHERE cat_id = ? AND prod_id != ? 
                    ORDER BY RAND() 
                    LIMIT 3"; // Limitar a 3 produtos relacionados
    
    $stmt_related = $conn->prepare($sql_related);
    if ($stmt_related) {
        $stmt_related->bind_param("ii", $cat_id, $product_id);
        if ($stmt_related->execute()) {
            $result_related = $stmt_related->get_result();
            $related_products = $result_related->fetch_all(MYSQLI_ASSOC);
        }
        $stmt_related->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['prod_name']); ?> - Rolimbras</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Stylesheet -->
    <style>
/* --- ESTILOS PARA A PÁGINA DE DETALHES DO PRODUTO --- */
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





.product-details-page {
    padding: 60px 0;
    background-color: #f9f9f9;
}

.product-main-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr; /* Coluna da imagem menor que a de texto */
    gap: 50px;
    background-color: #fff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    margin-bottom: 80px;
}

.product-visual {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.product-visual img {
    width: 100%;
    height: auto;
    aspect-ratio: 1 / 1;
    object-fit: cover;
}

.product-meta .product-title {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--primary-color);
    line-height: 1.2;
    margin-bottom: 20px;
}

.product-meta .product-description {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #555;
    margin-bottom: 30px;
}

.product-specifications {
    background-color: #f7f7f7;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
    border-left: 4px solid var(--primary-color);
}

.product-specifications h4 {
    font-size: 1.2rem;
    margin-bottom: 15px;
    color: #333;
}

.product-specifications ul {
    list-style: none;
}

.product-specifications li {
    padding: 5px 0;
    border-bottom: 1px dashed #ddd;
}
.product-specifications li:last-child {
    border-bottom: none;
}

.product-specifications li strong {
    color: #333;
}


/* --- Botões de Ação --- */
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}

.action-buttons a {
    padding: 15px 30px;
    border-radius: 5px;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-whatsapp {
    background-color: #25D366;
    color: #fff;
}
.btn-whatsapp:hover {
    background-color: #128C7E;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
}

.btn-back {
    background-color: #6c757d;
    color: #fff;
}
.btn-back:hover {
    background-color: #5a6268;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* --- Produtos Relacionados --- */
.related-products {
    padding-top: 40px;
    border-top: 1px solid #e0e0e0;
}
.related-products .section-title {
    margin-bottom: 40px;
}


/* --- RESPONSIVIDADE PARA A PÁGINA DE DETALHES --- */
@media (max-width: 992px) {
    .product-main-grid {
        grid-template-columns: 1fr;
        gap: 30px;
        padding: 20px;
    }
    .product-meta .product-title {
        font-size: 2rem;
    }
}

        </style>
</head>
<body>

    <!-- Header (consistente com outras páginas) -->
    <header class="main-header">
        <div class="container">
            <nav class="main-nav">
                <a href="index.html" class="logo">
                    <img src="../logos/Logo Preta.png" alt="Rolimbras Logo">
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

    <main class="product-details-page">
        <div class="container">
            <section class="product-main-grid">
                <!-- Coluna da Imagem -->
                <div class="product-visual">
                    <img src="../imagens/<?php echo htmlspecialchars($product['image_path'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($product['prod_name']); ?>">
                </div>

                <!-- Coluna de Detalhes -->
                <div class="product-meta">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['prod_name']); ?></h1>
                    
                    <div class="product-description">
                        <?php echo nl2br(htmlspecialchars($product['prod_description'])); ?>
                    </div>

                    <div class="product-specifications">
                        <h4>Especificações Técnicas:</h4>
                        <ul>
                            <!-- Exemplo de como você poderia exibir especificações -->
                            <li><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></li>
                            <li><strong>Marca:</strong> <?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></li>
                            <li><strong>Dimensões:</strong> <?php echo htmlspecialchars($product['dimensions'] ?? 'N/A'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="https://wa.me/5511980496568?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20produto%3A%20<?php echo urlencode($product['prod_name']); ?>" target="_blank" class="action-btn primary">
                            <i class="fab fa-whatsapp"></i> Fazer Cotação
                        </a>
                        <a href="produtos.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Voltar ao Catálogo
                        </a>
                    </div>
                </div>
            </section>

            <!-- Seção de Produtos Relacionados -->
            <?php if (!empty($related_products)): ?>
                <section class="related-products">
                    <h2 class="section-title text-center">Produtos Relacionados</h2>
                    <div class="grid">
                        <?php foreach ($related_products as $related_product): ?>
                            <div class="product-card">
                                <img src="../imagens/<?php echo htmlspecialchars($related_product['image_path'] ?? 'default.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($related_product['prod_name']); ?>">
                                <h3><?php echo htmlspecialchars($related_product['prod_name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($related_product['prod_description'], 0, 100)) . '...'; ?></p>
                                <a href="detalhesProduto.php?id=<?php echo $related_product['prod_id']; ?>" class="card-link">Saiba Mais →</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </main>
    
    <!-- Footer (consistente com outras páginas) -->
    <footer class="main-footer">
        <!-- O conteúdo do footer é o mesmo das outras páginas, mantido pelo style.css -->
        <div class="container footer-container">
            <div class="footer-col">
                <img src="../logos/Logo Branca.png" alt="Rolimbras Logo Branco" class="footer-logo">
                <p>A Nova Retentores é uma empresa especializada no fornecimento de rolamentos e soluções para transmissão de movimento.</p>
            </div>
            <div class="footer-col">
                <h4>Menu</h4>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="#">Quem somos</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="#">Contato</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Produtos</h4>
                <ul>
                    <li><a href="#">Rolamentos de rolos cônicos</a></li>
                    <li><a href="#">Mancais Bipartidos</a></li>
                    <li><a href="#">Buchas de Fixação</a></li>
                    <li><a href="#">Retentores</a></li>
                    <li><a href="#">Correias</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contato</h4>
                <p>Rua Lobo de Miranda, 233<br>Vila Maria Alta - SP<br>CEP: 02129-050</p>
                <p><a href="mailto:comercial@novaretentores.com.br">comercial@novaretentores.com.br</a></p>
                <p><a href="tel:11998353303">(11) 99835-3303</a></p>
                <p><a href="tel:1128420555">(11) 2842-0555</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>Copyright 2025 © Desenvolvido por <a href="https://www.instagram.com/ryangomesmelo/">Ryan Gomes de Melo</a></p>
            </div>
        </div>
    </footer>

</body>
</html>