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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            throw new Exception("ID inválido.");
        }

        // Impedir exclusão do próprio usuário
        if ($id == $_SESSION['user_id']) {
            throw new Exception("Você não pode excluir seu próprio usuário.");
        }

        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: painel.php?status=success&message=Usuário excluído com sucesso!");
        } else {
            throw new Exception("Erro ao excluir usuário.");
        }

        $stmt->close();
    }

    $conn->close();
} catch (Exception $e) {
    header("Location: painel.php?status=error&message=" . urlencode($e->getMessage()));
}
?>