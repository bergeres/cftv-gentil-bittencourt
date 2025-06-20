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

    // Buscar solicitações do usuário logado
    $stmt = $conn->prepare("SELECT id, data_solicitacao, data_ocorrido, local_fato, descricao, status FROM solicitacoes WHERE nome = ? ORDER BY data_solicitacao DESC");
    $stmt->bind_param("s", $_SESSION['nome']);
    $stmt->execute();
    $result = $stmt->get_result();
    $solicitacoes = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>CFTV Gentil Bittencourt - Histórico de Atendimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
        }
        .header {
            background-color: #1e40af;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.97);
        }
        .status-aprovado {
            background-color: #10b981;
        }
        .status-atendido {
            background-color: #3b82f6;
        }
        .status-nao-atendido {
            background-color: #ef4444;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Cabeçalho -->
    <header class="header text-white py-4 px-4 sm:px-8 flex items-center justify-between shadow-lg sticky top-0 z-10">
        <img src="https://gentilbittencourt.com.br/wp-content/uploads/2024/01/Artboard-1-2.png" alt="Logo Colégio Gentil Bittencourt" class="h-12 sm:h-16">
        <h1 class="text-lg sm:text-2xl font-bold">CFTV Gentil Bittencourt</h1>
        <div class="flex items-center space-x-2">
            <span class="text-sm sm:text-base">Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></span>
            <a href="change_password.php" class="text-sm bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">Alterar Senha</a>
            <a href="logout.php" class="text-sm bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700">Sair</a>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="flex-grow py-8 px-4 sm:px-6">
        <div class="table-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-5xl mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-6">Histórico de Atendimento</h2>
            <div class="mb-6 text-center">
                <a href="index.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Voltar à Página Inicial</a>
            </div>
            <?php if (isset($error_message)): ?>
                <p class="text-center text-red-600 bg-red-50 p-4 rounded-lg mb-6"><?php echo htmlspecialchars($error_message); ?></p>
            <?php elseif (empty($solicitacoes)): ?>
                <p class="text-center text-gray-600 bg-gray-50 p-4 rounded-lg">Nenhuma solicitação encontrada.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-800">
                        <thead class="text-xs text-white uppercase bg-blue-600">
                            <tr>
                                <th class="px-4 py-2">ID</th>
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
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['data_solicitacao']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['data_ocorrido']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['local_fato']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($solicitacao['descricao']); ?></td>
                                    <td class="px-4 py-2">
                                        <span class="inline-block px-3 py-1 text-white text-xs font-semibold rounded-full <?php
                                            switch ($solicitacao['status']) {
                                                case 'Aprovado':
                                                    echo 'status-aprovado';
                                                    break;
                                                case 'Atendido':
                                                    echo 'status-atendido';
                                                    break;
                                                default:
                                                    echo 'status-nao-atendido';
                                            }
                                        ?>">
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

    <!-- Rodapé -->
    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>
</body>
</html>