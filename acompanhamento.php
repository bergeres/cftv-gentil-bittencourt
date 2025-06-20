<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'Abertura') {
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
    $conn->set_charset("utf8mb4");

    // Consultar solicitações do usuário logado
    $stmt = $conn->prepare("SELECT id, nome, funcao, setor, data_solicitacao, data_ocorrido, local_fato, descricao, status FROM solicitacoes WHERE nome = ? ORDER BY data_solicitacao DESC");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $solicitacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    <title>CFTV Gentil Bittencourt - Acompanhamento de Solicitações</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <style>
        body {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
        }
        .header {
            background-color: #1e40af;
        }
        .acompanhamento-container {
            background: rgba(255, 255, 255, 0.97);
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
        .status-nao-atendido {
            color: #ef4444;
            font-weight: bold;
        }
        .status-aprovado {
            color: #10b981;
            font-weight: bold;
        }
        .status-atendido {
            color: #3b82f6;
            font-weight: bold;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="header text-white py-4 px-4 sm:px-8 flex items-center justify-between shadow-lg sticky top-0 z-10">
        <img src="/images/logo.png" alt="Logo Colégio Gentil Bittencourt" class="h-12 sm:h-16">
        <h1 class="text-lg sm:text-2xl font-bold">CFTV Gentil Bittencourt</h1>
        <div class="flex items-center space-x-2">
            <span class="text-sm sm:text-base">Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="text-sm bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700">Sair</a>
        </div>
    </header>

    <main class="flex-grow py-8 px-4 sm:px-6">
        <div class="acompanhamento-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-7xl mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-6">Acompanhamento de Solicitações</h2>
            <nav class="mb-6">
                <ul class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 justify-center">
                    <li><a href="solicitacoes.php" class="nav-button">Nova Solicitação</a></li>
                    <li><a href="acompanhamento.php" class="nav-button active">Acompanhamento</a></li>
                </ul>
            </nav>
            <?php if (isset($error_message)): ?>
                <p class="text-center text-red-600 bg-red-50 p-3 rounded-lg mb-6"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <?php if (empty($solicitacoes)): ?>
                <p class="text-center text-gray-600 bg-gray-50 p-4 rounded-lg">Nenhuma solicitação encontrada.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-800">
                        <thead class="text-xs text-white uppercase bg-blue-600">
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Nome</th>
                                <th class="px-4 py-2">Função</th>
                                <th class="px-4 py-2">Setor</th>
                                <th class="px-4 py-2">Data da Solicitação</th>
                                <th class="px-4 py-2">Data do Ocorrido</th>
                                <th class="px-4 py-2">Local do Fato</th>
                                <th class="px-4 py-2">Descrição</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitacoes as $solicitacao): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['id']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['nome']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['funcao']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['setor']); ?></td>
                                    <td class="px-4 py-2"><?php echo date('d/m/Y H:i', strtotime($solicitacao['data_solicitacao'])); ?></td>
                                    <td class="px-4 py-2"><?php echo date('d/m/Y', strtotime($solicitacao['data_ocorrido'])); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['local_fato']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['descricao']); ?></td>
                                    <td class="px-4 py-2">
                                        <span class="status-<?php echo strtolower(str_replace(' ', '-', $solicitacao['status'])); ?>">
                                            <?php echo htmlspecialchars($solicitacao['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>

    <script>
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