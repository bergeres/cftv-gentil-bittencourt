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
        $new_name = $_POST['name'] ?? '';
        $new_username = $_POST['username'] ?? '';
        $new_password = $_POST['password'] ?? '';
        $nivel_acesso = $_POST['nivel_acesso'] ?? '';

        if (empty($id) || empty($new_name) || empty($new_username) || !in_array($nivel_acesso, ['Abertura', 'Autorização', 'Monitor', 'Admin'])) {
            throw new Exception("Dados inválidos.");
        }

        // Verificar se o username já existe (exceto para o próprio usuário)
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $new_username, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Username já existe.");
        }
        $stmt->close();

        // Montar query de atualização
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, username = ?, password = ?, nivel_acesso = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $new_name, $new_username, $hashed_password, $nivel_acesso, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, username = ?, nivel_acesso = ? WHERE id = ?");
            $stmt->bind_param("sssi", $new_name, $new_username, $nivel_acesso, $id);
        }

        if ($stmt->execute()) {
            header("Location: painel.php?status=success&message=Usuário atualizado com sucesso!");
        } else {
            throw new Exception("Erro ao atualizar usuário.");
        }

        $stmt->close();
    }

    $conn->close();
} catch (Exception $e) {
    header("Location: painel.php?status=error&message=" . urlencode($e->getMessage()));
}
?>