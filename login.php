<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: painel.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFTV Gentil Bittencourt - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
        }
        .login-container {
            background: rgba(255, 255, 255, 0.97);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="login-container p-6 sm:p-8 rounded-2xl shadow-xl w-full max-w-md">
        <div class="text-center mb-6">
            <img src="https://gentilbittencourt.com.br/wp-content/uploads/2024/01/Artboard-1-2.png" alt="Logo Colégio Gentil Bittencourt" class="h-12 mx-auto">
            <h2 class="text-xl sm:text-2xl font-semibold text-blue-900 mt-4">CFTV Gentil Bittencourt</h2>
        </div>
        <?php if (isset($_GET['error'])): ?>
            <p class="text-center text-red-600 bg-red-50 p-3 rounded-lg mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <form action="auth.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-blue-900">Usuário</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-blue-900">Senha</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="text-center">
                <button type="submit" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Entrar</button>
            </div>
        </form>
    </div>
</body>
</html>