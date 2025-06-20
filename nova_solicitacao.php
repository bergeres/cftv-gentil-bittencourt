<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'Abertura') {
    header("Location: login.php");
    exit();
}

$current_datetime = date('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFTV Gentil Bittencourt - Nova Solicitação</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
        }
        .header {
            background-color: #1e40af;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.97);
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
        <div class="form-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-2xl mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-6">Solicitação de Visualização de Imagens de CFTV</h2>
            
            <!-- Mensagens de Status -->
            <?php if (isset($_GET['status'])): ?>
                <div class="mb-6 p-4 rounded-lg text-center <?php echo $_GET['status'] === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
                    <?php echo htmlspecialchars($_GET['message'] ?? ($_GET['status'] === 'success' ? 'Solicitação enviada com sucesso!' : 'Erro ao enviar solicitação.')); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form action="submit.php" method="POST" class="space-y-4">
                <div>
                    <label for="nome" class="block text-sm font-medium text-blue-900">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($_SESSION['nome']); ?>" readonly required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed">
                </div>
                <div>
                    <label for="funcao" class="block text-sm font-medium text-blue-900">Função</label>
                    <input type="text" id="funcao" name="funcao" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="setor" class="block text-sm font-medium text-blue-900">Setor</label>
                    <input type="text" id="setor" name="setor" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="data_solicitacao" class="block text-sm font-medium text-blue-900">Data da Solicitação</label>
                    <input type="datetime-local" id="data_solicitacao" name="data_solicitacao" value="<?php echo $current_datetime; ?>" readonly required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed">
                </div>
                <div>
                    <label for="data_ocorrido" class="block text-sm font-medium text-blue-900">Data do Ocorrido</label>
                    <input type="date" id="data_ocorrido" name="data_ocorrido" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="local_fato" class="block text-sm font-medium text-blue-900">Local do Fato</label>
                    <input type="text" id="local_fato" name="local_fato" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="descricao" class="block text-sm font-medium text-blue-900">Descrição do Fato</label>
                    <textarea id="descricao" name="descricao" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 h-32"></textarea>
                </div>
                <div class="text-center space-x-4">
                    <a href="index.php" class="inline-block bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition duration-200 font-medium">Voltar</a>
                    <button type="submit" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Enviar Solicitação</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Rodapé -->
    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>
</body>
</html>