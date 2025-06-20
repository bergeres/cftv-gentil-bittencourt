<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['nivel_acesso'], ['Autorização', 'Monitor', 'Admin'])) {
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

    $result = $conn->query("SELECT * FROM solicitacoes ORDER BY data_solicitacao DESC");
    $solicitacoes = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>CFTV Gentil Bittencourt - Lista de Solicitações</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
        }
        .header {
            background-color: #1e40af;
        }
        .card {
            background: rgba(255, 255, 255, 0.97);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .status-button {
            transition: all 0.3s ease;
        }
        .status-button:disabled {
            cursor: not-allowed;
            opacity: 0.6;
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
        <div class="max-w-7xl mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-6">Lista de Solicitações de Visualização de CFTV</h2>
            <?php if ($_SESSION['nivel_acesso'] === 'Admin'): ?>
                <div class="flex justify-center mb-6 space-x-4">
                    <a href="index.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Nova Solicitação</a>
                    <a href="painel.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-medium">Painel de Administração</a>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <p class="text-center text-red-600 bg-red-50 p-4 rounded-lg mb-6"><?php echo htmlspecialchars($error_message); ?></p>
            <?php elseif (empty($solicitacoes)): ?>
                <p class="text-center text-gray-600 bg-gray-50 p-4 rounded-lg">Nenhuma solicitação encontrada.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($solicitacoes as $solicitacao): ?>
                        <div class="card p-6 rounded-2xl shadow-lg" data-solicitacao-id="<?php echo $solicitacao['id']; ?>">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Nome</p>
                                    <p class="text-gray-800"><?php echo htmlspecialchars($solicitacao['nome']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Função</p>
                                    <p class="text-gray-800"><?php echo htmlspecialchars($solicitacao['funcao']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Setor</p>
                                    <p class="text-gray-800"><?php echo htmlspecialchars($solicitacao['setor']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Data Solicitação</p>
                                    <p class="text-gray-800"><?php echo htmlspecialchars($solicitacao['data_solicitacao']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Data Ocorrido</p>
                                    <p class="text-gray-800"><?php echo htmlspecialchars($solicitacao['data_ocorrido']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Local do Fato</p>
                                    <p class="text-gray-800"><?php echo htmlspecialchars($solicitacao['local_fato']); ?></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm font-medium text-blue-900">Descrição</p>
                                    <p class="text-gray-800"><?php echo htmlspecialchars($solicitacao['descricao']); ?></p>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-center space-x-3">
                                <button 
                                    onclick="updateStatus(<?php echo $solicitacao['id']; ?>, 'Aprovado')"
                                    class="status-button px-4 py-2 rounded-lg text-white text-sm flex items-center space-x-2 <?php echo $solicitacao['status'] === 'Aprovado' || $solicitacao['status'] === 'Atendido' ? 'bg-green-500' : 'bg-gray-300 hover:bg-green-400'; ?>"
                                    <?php echo ($solicitacao['status'] !== 'Não Atendido' || !in_array($_SESSION['nivel_acesso'], ['Autorização', 'Admin'])) ? 'disabled' : ''; ?>>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Aprovado</span>
                                </button>
                                <button 
                                    onclick="updateStatus(<?php echo $solicitacao['id']; ?>, 'Atendido')"
                                    class="status-button px-4 py-2 rounded-lg text-white text-sm flex items-center space-x-2 <?php echo $solicitacao['status'] === 'Atendido' ? 'bg-green-500' : 'bg-gray-300 hover:bg-green-400'; ?>"
                                    <?php echo ($solicitacao['status'] !== 'Aprovado' || !in_array($_SESSION['nivel_acesso'], ['Monitor', 'Admin'])) ? 'disabled' : ''; ?>>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Atendido</span>
                                </button>
                                <button 
                                    class="status-button px-4 py-2 rounded-lg text-white text-sm flex items-center space-x-2 <?php echo $solicitacao['status'] === 'Atendido' ? 'bg-gray-300' : 'bg-red-500'; ?>"
                                    disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span>Não Atendido</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Rodapé -->
    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>

    <script>
        function updateStatus(id, status) {
            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&status=${encodeURIComponent(status)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.querySelector(`div[data-solicitacao-id="${id}"]`);
                    const buttons = card.querySelectorAll('.status-button');
                    buttons.forEach(btn => {
                        const btnStatus = btn.textContent.trim().split(' ')[0];
                        if (btnStatus === status) {
                            btn.classList.remove('bg-gray-300', 'hover:bg-green-400');
                            btn.classList.add('bg-green-500');
                        } else if (btnStatus === 'Não' && data.new_status !== 'Atendido') {
                            btn.classList.remove('bg-gray-300');
                            btn.classList.add('bg-red-500');
                        } else if (btnStatus === 'Não' && data.new_status === 'Atendido') {
                            btn.classList.remove('bg-red-500');
                            btn.classList.add('bg-gray-300');
                        } else if (btnStatus !== status && btnStatus !== 'Não') {
                            btn.classList.remove('bg-green-500');
                            btn.classList.add('bg-gray-300', 'hover:bg-green-400');
                        }
                    });

                    const aprovadoBtn = card.querySelector(`button[onclick="updateStatus(${id}, 'Aprovado')"]`);
                    const atendidoBtn = card.querySelector(`button[onclick="updateStatus(${id}, 'Atendido')"]`);
                    const naoAtendidoBtn = card.querySelector(`button:not([onclick])`);

                    if (data.new_status === 'Não Atendido') {
                        aprovadoBtn.disabled = <?php echo $_SESSION['nivel_acesso'] === 'Autorização' || $_SESSION['nivel_acesso'] === 'Admin' ? 'false' : 'true'; ?>;
                        atendidoBtn.disabled = true;
                        naoAtendidoBtn.disabled = true;
                    } else if (data.new_status === 'Aprovado') {
                        aprovadoBtn.disabled = true;
                        atendidoBtn.disabled = <?php echo $_SESSION['nivel_acesso'] === 'Monitor' || $_SESSION['nivel_acesso'] === 'Admin' ? 'false' : 'true'; ?>;
                        naoAtendidoBtn.disabled = true;
                    } else if (data.new_status === 'Atendido') {
                        aprovadoBtn.disabled = true;
                        atendidoBtn.disabled = true;
                        naoAtendidoBtn.disabled = true;
                    }
                } else {
                    alert('Erro: ' + (data.message || 'Falha ao atualizar status.'));
                }
            })
            .catch(error => {
                alert('Erro de conexão: ' + error.message);
            });
        }
    </script>
</body>
</html>