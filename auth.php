<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

$servername = "localhost";
$username = "root";
$password = "SENHA";
$dbname = "cftv_banco";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Erro de conexão: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            header("Location: login.php?error=Preencha todos os campos");
            exit();
        }

        $stmt = $conn->prepare("SELECT id, nome, username, password, nivel_acesso FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            error_log("Usuário '$username' não encontrado.");
            header("Location: login.php?error=Usuário ou senha inválidos");
            exit();
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nivel_acesso'] = $user['nivel_acesso'];
            error_log("Login bem-sucedido para '$username' com nível {$user['nivel_acesso']}.");

            // Redirecionar com base no nível de acesso
            switch ($user['nivel_acesso']) {
                case 'Abertura':
                    header("Location: index.php");
                    break;
                case 'Autorização':
                case 'Monitor':
                    header("Location: solicitacoes.php");
                    break;
                case 'Admin':
                    header("Location: painel.php");
                    break;
                default:
                    header("Location: login.php?error=Nível de acesso inválido");
            }
        } else {
            error_log("Senha inválida para '$username'.");
            header("Location: login.php?error=Usuário ou senha inválidos");
        }
        $stmt->close();
    }

    $conn->close();
} catch (Exception $e) {
    error_log("Erro no auth.php: " . $e->getMessage());
    header("Location: login.php?error=" . urlencode($e->getMessage()));
}
?>