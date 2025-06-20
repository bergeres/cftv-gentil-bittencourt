<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "SENHA";
$dbname = "cftv_banco";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Erro de conexão: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4"); // Garantir codificação UTF-8

    // Intervalo de datas padrão: últimos 30 dias
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

    // Validar datas
    if (!DateTime::createFromFormat('Y-m-d', $start_date) || !DateTime::createFromFormat('Y-m-d', $end_date)) {
        throw new Exception("Formato de data inválido.");
    }

    // Top 5 Solicitantes
    $stmt = $conn->prepare("SELECT nome, COUNT(*) as count FROM solicitacoes WHERE data_solicitacao BETWEEN ? AND ? GROUP BY nome ORDER BY count DESC LIMIT 5");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $top_requesters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $top_requesters_labels = array_column($top_requesters, 'nome');
    $top_requesters_data = array_column($top_requesters, 'count');
    $stmt->close();

    // Solicitações por Período (Diário)
    $stmt = $conn->prepare("SELECT DATE(data_solicitacao) as date, COUNT(*) as count FROM solicitacoes WHERE data_solicitacao BETWEEN ? AND ? GROUP BY DATE(data_solicitacao) ORDER BY date");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $requests_over_time = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $requests_over_time_labels = array_column($requests_over_time, 'date');
    $requests_over_time_data = array_column($requests_over_time, 'count');
    $stmt->close();

    // Distribuição de Status
    $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM solicitacoes WHERE data_solicitacao BETWEEN ? AND ? GROUP BY status");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $status_distribution = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $status_labels = array_column($status_distribution, 'status');
    $status_data = array_column($status_distribution, 'count');
    $stmt->close();

    // Solicitações por Setor
    $stmt = $conn->prepare("SELECT setor, COUNT(*) as count FROM solicitacoes WHERE data_solicitacao BETWEEN ? AND ? GROUP BY setor ORDER BY count DESC");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $sector_requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $sector_labels = array_column($sector_requests, 'setor');
    $sector_data = array_column($sector_requests, 'count');
    $stmt->close();

    $conn->close();
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFTV Gentil Bittencourt - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <style>
        body {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
        }
        .header {
            background-color: #1e40af;
        }
        .dashboard-container {
            background: rgba(255, 255, 255, 0.97);
        }
        canvas {
            max-height: 300px;
        }
        .nav-button {
            background-color: #1e40af;
            color: white;
            padding: 8px 16px;
            border-radius: 0.5rem;
            font-weight: medium;
            transition: background-color 0.2s;
        }
        .nav-button:hover {
            background-color: #2563eb;
        }
        .nav-button.active {
            background-color: #2563eb;
            font-weight: bold;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="header text-white py-4 px-4 sm:px-8 flex items-center justify-between shadow-lg sticky top-0 z-10">
        <img src="https://gentilbittencourt.com.br/wp-content/uploads/2024/01/Artboard-1-2.png" alt="Logo Colégio Gentil Bittencourt" class="h-12 sm:h-16">
        <h1 class="text-lg sm:text-2xl font-bold">CFTV Gentil Bittencourt</h1>
        <div class="flex items-center space-x-2">
            <span class="text-sm sm:text-base">Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></span>
            <a href="change_password.php" class="text-sm bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">Alterar Senha</a>
            <a href="logout.php" class="text-sm bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700">Sair</a>
        </div>
    </header>

    <main class="flex-grow py-8 px-4 sm:px-6">
        <div class="dashboard-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-7xl mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-6">Dashboard de Análises</h2>
            <nav class="mb-6">
                <ul class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 justify-center">
                    <li><a href="painel.php" class="nav-button">Gerenciar Usuários</a></li>
                    <li><a href="dashboard.php" class="nav-button active">Dashboard</a></li>
                </ul>
            </nav>
            <?php if (isset($error_message)): ?>
                <p class="text-center text-red-600 bg-red-50 p-3 rounded-lg mb-6"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <!-- Seletor de Intervalo de Datas -->
            <form method="GET" class="mb-8 flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-blue-900">Data Inicial</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required class="mt-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-blue-900">Data Final</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required class="mt-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Filtrar</button>
            </form>
            <!-- Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top 5 Solicitantes -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Top 5 Solicitantes</h3>
                    <canvas id="topRequestersChart"></canvas>
                </div>
                <!-- Solicitações por Período -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Solicitações por Período</h3>
                    <canvas id="requestsOverTimeChart"></canvas>
                </div>
                <!-- Distribuição de Status -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Distribuição de Status</h3>
                    <canvas id="statusDistributionChart"></canvas>
                </div>
                <!-- Solicitações por Setor -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Solicitações por Setor</h3>
                    <canvas id="sectorRequestsChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>

    <script>
        // Inicializar gráficos Chart.js
        const ctxTopRequesters = document.getElementById('topRequestersChart').getContext('2d');
        new Chart(ctxTopRequesters, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($top_requesters_labels, JSON_UNESCAPED_UNICODE); ?>,
                datasets: [{
                    label: 'Número de Solicitações',
                    data: <?php echo json_encode($top_requesters_data); ?>,
                    backgroundColor: ['#1e40af', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                    borderColor: ['#1e40af', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        const ctxRequestsOverTime = document.getElementById('requestsOverTimeChart').getContext('2d');
        new Chart(ctxRequestsOverTime, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($requests_over_time_labels); ?>,
                datasets: [{
                    label: 'Solicitações',
                    data: <?php echo json_encode($requests_over_time_data); ?>,
                    backgroundColor: 'rgba(30, 64, 175, 0.2)',
                    borderColor: '#1e40af',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const ctxStatusDistribution = document.getElementById('statusDistributionChart').getContext('2d');
        new Chart(ctxStatusDistribution, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($status_labels, JSON_UNESCAPED_UNICODE); ?>,
                datasets: [{
                    data: <?php echo json_encode($status_data); ?>,
                    backgroundColor: ['#ef4444', '#10b981', '#3b82f6'],
                    borderColor: ['#dc2626', '#059669', '#2563eb'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const ctxSectorRequests = document.getElementById('sectorRequestsChart').getContext('2d');
        new Chart(ctxSectorRequests, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($sector_labels, JSON_UNESCAPED_UNICODE); ?>,
                datasets: [{
                    label: 'Número de Solicitações',
                    data: <?php echo json_encode($sector_data); ?>,
                    backgroundColor: ['#1e40af', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                    borderColor: ['#1e40af', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => console.log('Service Worker registrado:', registration))
                    .catch(error => console.log('Erro ao registrar Service Worker:', error));
            });
        }
    </script>
</body>
</html>