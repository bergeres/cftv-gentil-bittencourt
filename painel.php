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

    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
        $new_name = $_POST['name'] ?? '';
        $new_username = $_POST['username'] ?? '';
        $new_password = $_POST['password'] ?? '';
        $nivel_acesso = $_POST['nivel_acesso'] ?? '';

        if (empty($new_name) || empty($new_username) || empty($new_password) || !in_array($nivel_acesso, ['Abertura', 'Autorização', 'Monitor', 'Admin'])) {
            $message = 'Preencha todos os campos corretamente.';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, username, password, nivel_acesso) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $new_name, $new_username, $hashed_password, $nivel_acesso);
            if ($stmt->execute()) {
                $message = 'Usuário criado com sucesso!';
            } else {
                $message = 'Erro ao criar usuário. Username pode já existir.';
            }
            $stmt->close();
        }
    }

    $result = $conn->query("SELECT id, nome, username, nivel_acesso, created_at FROM usuarios ORDER BY created_at DESC");
    $usuarios = $result->fetch_all(MYSQLI_ASSOC);

    if (isset($_GET['status'])) {
        $message = htmlspecialchars($_GET['message'] ?? '');
        $message_class = $_GET['status'] === 'success' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50';
    }

    $conn->close();
} catch (Exception $e) {
    $message = $e->getMessage();
    $message_class = 'text-red-600 bg-red-50';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFTV Gentil Bittencourt - Painel de Administração</title>
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
        .panel-container {
            background: rgba(255, 255, 255, 0.97);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background: rgba(255, 255, 255, 0.97);
            margin: 15% auto;
            padding: 20px;
            border-radius: 1rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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
        <div class="panel-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-4xl mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-6">Painel de Administração</h2>
            <nav class="mb-6">
                <ul class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 justify-center">
                    <li><a href="painel.php" class="nav-button active">Gerenciar Usuários</a></li>
                    <li><a href="dashboard.php" class="nav-button">Dashboard</a></li>
                </ul>
            </nav>
            <?php if ($message): ?>
                <p class="text-center <?php echo $message_class ?? 'text-red-600 bg-red-50'; ?> p-3 rounded-lg mb-6"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="painel.php" method="POST" class="space-y-4 mb-8">
                <input type="hidden" name="create_user" value="1">
                <div>
                    <label for="name" class="block text-sm font-medium text-blue-900">Nome</label>
                    <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="username" class="block text-sm font-medium text-blue-900">Usuário</label>
                    <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-blue-900">Senha</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="nivel_acesso" class="block text-sm font-medium text-blue-900">Nível de Acesso</label>
                    <select id="nivel_acesso" name="nivel_acesso" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="Abertura">Abertura</option>
                        <option value="Autorização">Autorização</option>
                        <option value="Monitor">Monitor</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Criar Usuário</button>
                </div>
            </form>
            <h3 class="text-lg font-semibold text-blue-900 mb-4">Usuários Cadastrados</h3>
            <?php if (empty($usuarios)): ?>
                <p class="text-center text-gray-600 bg-gray-50 p-4 rounded-lg">Nenhum usuário cadastrado.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-800">
                        <thead class="text-xs text-white uppercase bg-blue-600">
                            <tr>
                                <th class="px-4 py-2">Nome</th>
                                <th class="px-4 py-2">Usuário</th>
                                <th class="px-4 py-2">Nível de Acesso</th>
                                <th class="px-4 py-2">Criado Em</th>
                                <th class="px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($usuario['username']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($usuario['nivel_acesso']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($usuario['created_at']); ?></td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <button onclick="openEditModal(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nome']); ?>', '<?php echo htmlspecialchars($usuario['username']); ?>', '<?php echo htmlspecialchars($usuario['nivel_acesso']); ?>')" class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <form action="delete_user.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o usuário <?php echo htmlspecialchars($usuario['username']); ?>?');">
                                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800" <?php echo $usuario['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">Editar Usuário</h3>
            <form id="editForm" action="edit_user.php" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="editId">
                <div>
                    <label for="editName" class="block text-sm font-medium text-blue-900">Nome</label>
                    <input type="text" id="editName" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="editUsername" class="block text-sm font-medium text-blue-900">Usuário</label>
                    <input type="text" id="editUsername" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="editPassword" class="block text-sm font-medium text-blue-900">Nova Senha (opcional)</label>
                    <input type="password" id="editPassword" name="password" placeholder="Deixe em branco para manter a senha atual" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="editNivelAcesso" class="block text-sm font-medium text-blue-900">Nível de Acesso</label>
                    <select id="editNivelAcesso" name="nivel_acesso" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="Abertura">Abertura</option>
                        <option value="Autorização">Autorização</option>
                        <option value="Monitor">Monitor</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>

    <script>
        function openEditModal(id, name, username, nivel_acesso) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            document.getElementById('editNivelAcesso').value = nivel_acesso;
            document.getElementById('editPassword').value = '';
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
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