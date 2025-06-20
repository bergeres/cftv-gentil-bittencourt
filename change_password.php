<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id'])) {
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
    $message_class = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            throw new Exception("Preencha todos os campos.");
        }

        if ($new_password !== $confirm_password) {
            throw new Exception("As novas senhas não coincidem.");
        }

        if (strlen($new_password) < 6) {
            throw new Exception("A nova senha deve ter pelo menos 6 caracteres.");
        }

        // Verificar senha atual
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!password_verify($current_password, $user['password'])) {
            throw new Exception("Senha atual incorreta.");
        }

        // Atualizar senha
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $message = "Senha alterada com sucesso!";
            $message_class = "text-green-600 bg-green-50";
        } else {
            throw new Exception("Erro ao atualizar a senha.");
        }
        $stmt->close();
    }

    $conn->close();
} catch (Exception $e) {
    $message = $e->getMessage();
    $message_class = "text-red-600 bg-red-50";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFTV Gentil Bittencourt - Alterar Senha</title>
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
    <header class="header text-white py-4 px-4 sm:px-8 flex items-center justify-between shadow-lg sticky top-0 z-10">
        <img src="https://gentilbittencourt.com.br/wp-content/uploads/2024/01/Artboard-1-2.png" alt="Logo Colégio Gentil Bittencourt" class="h-12 sm:h-16">
        <h1 class="text-lg sm:text-2xl font-bold">CFTV Gentil Bittencourt</h1>
        <div class="flex items-center space-x-2">
            <span class="text-sm sm:text-base">Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="text-sm bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700">Sair</a>
        </div>
    </header>

    <main class="flex-grow py-8 px-4 sm:px-6">
        <div class="form-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-md mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 text-center mb-6">Alterar Senha</h2>
            <?php if ($message): ?>
                <p class="text-center <?php echo $message_class; ?> p-3 rounded-lg mb-6"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="change_password.php" method="POST" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-blue-900">Senha Atual</label>
                    <input type="password" id="current_password" name="current_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-blue-900">Nova Senha</label>
                    <input type="password" id="new_password" name="new_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-blue-900">Confirmar Nova Senha</label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="text-center">
                    <button type="submit" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Alterar Senha</button>
                </div>
            </form>
            <div class="text-center mt-4">
                <a href="<?php echo $_SESSION['nivel_acesso'] === 'Admin' ? 'painel.php' : ($_SESSION['nivel_acesso'] === 'Abertura' ? 'index.php' : 'solicitacoes.php'); ?>" class="text-blue-600 hover:underline">Voltar</a>
            </div>
        </div>
    </main>

    <footer class="bg-blue-800 text-white text-center py-4">
        <p class="text-sm">© 2025 Colégio Gentil Bittencourt. Todos os direitos reservados.</p>
    </footer>
</body>
</html>