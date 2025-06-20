<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'Abertura') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFTV Gentil Bittencourt - Página Inicial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
        }
        .header {
            background-color: #1e40af;
        }
        .main-container {
            background: rgba(255, 255, 255, 0.97);
        }
        .option-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
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
    <main class="flex-grow py-8 px-4 sm:px-6 flex items-center justify-center">
        <div class="main-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-2xl">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-8">Bem-vindo ao Sistema CFTV</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Opção: Abrir Solicitação -->
                <a href="nova_solicitacao.php" class="option-card bg-white p-6 rounded-xl shadow-md flex flex-col items-center text-center hover:bg-blue-50">
                    <svg class="w-12 h-12 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900">Abrir Solicitação</h3>
                    <p class="text-sm text-gray-600 mt-2">Crie uma nova solicitação de visualização de imagens de CFTV.</p>
                </a>
                <!-- Opção: Histórico de Atendimento -->
                <a href="historico.php" class="option-card bg-white p-6 rounded-xl shadow-md flex flex-col items-center text-center hover:bg-blue-50">
                    <svg class="w-12 h-12 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h6m-6 4h6m-6 4h6"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900">Histórico de Atendimento</h3>
                    <p class="text-sm text-gray-600 mt-2">Acompanhe o status das suas solicitações de atendimento.</p>
                </a>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>
</body>
</html>