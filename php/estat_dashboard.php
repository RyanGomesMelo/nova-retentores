<?php
// Conectar ao banco de dados
include __DIR__ . '/../config/db.php';

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Buscar os dados dos produtos e cliques
$query = "SELECT prod_name, clicks FROM product";
$result = $conn->query($query);

$products = [];
$clicks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row['prod_name'];
        $clicks[] = $row['clicks'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../imgs/suspensao-do-carro (1).png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text);
            opacity: 0.8;
        }

        .chart-container {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            transition: var(--transition);
        }

        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .chart-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--accent);
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
        }

        .time-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-btn {
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            background: transparent;
            color: var(--text);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--accent);
            border-color: var(--accent);
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

            .stats-grid {
                grid-template-columns: 1fr;
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
                <a href="editProduct.php" class="nav-link">
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
                <a href="estat_dashboard.php" class="nav-link active">
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
            <h1 class="page-title">Estatísticas</h1>
            <div class="time-filter">
                <button class="filter-btn active" data-period="day">Hoje</button>
                <button class="filter-btn" data-period="week">Semana</button>
                <button class="filter-btn" data-period="month">Mês</button>
                <button class="filter-btn" data-period="year">Ano</button>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="stat-title">Visualizações</h3>
                </div>
                <div class="stat-value">1.2k</div>
                <div class="stat-label">+12% em relação ao mês anterior</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="stat-title">Vendas</h3>
                </div>
                <div class="stat-value">45</div>
                <div class="stat-label">+8% em relação ao mês anterior</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="stat-title">Usuários</h3>
                </div>
                <div class="stat-value">320</div>
                <div class="stat-label">+15% em relação ao mês anterior</div>
            </div>
        </div>

        <div class="chart-container">
            <h3 class="chart-title">Visualizações por Categoria</h3>
            <div class="chart-wrapper">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <h3 class="chart-title">Vendas por Mês</h3>
            <div class="chart-wrapper">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Gráfico de Categorias
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Retentores', 'Juntas', 'Vedações', 'Outros'],
                datasets: [{
                    data: [300, 250, 200, 150],
                    backgroundColor: [
                        '#ff4d4d',
                        '#00ff9d',
                        '#4d79ff',
                        '#ffd700'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#ffffff',
                            font: {
                                family: 'Space Grotesk'
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Vendas
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Vendas',
                    data: [65, 59, 80, 81, 56, 55],
                    borderColor: '#ff4d4d',
                    backgroundColor: 'rgba(255, 77, 77, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ffffff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ffffff'
                        }
                    }
                }
            }
        });

        // Filtros de tempo
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Aqui você pode adicionar a lógica para atualizar os dados com base no período selecionado
            });
        });
    </script>
</body>
</html>
