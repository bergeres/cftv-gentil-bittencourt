<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['nivel_acesso'], ['Autorização', 'Monitor', 'Admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
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
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !in_array($status, ['Não Atendido', 'Aprovado', 'Atendido'])) {
            throw new Exception("Dados inválidos.");
        }

        // Verificar permissão
        if ($status === 'Aprovado' && !in_array($_SESSION['nivel_acesso'], ['Autorização', 'Admin'])) {
            throw new Exception("Permissão negada para aprovar.");
        }
        if ($status === 'Atendido' && !in_array($_SESSION['nivel_acesso'], ['Monitor', 'Admin'])) {
            throw new Exception("Permissão negada para atender.");
        }

        // Verificar o status atual
        $stmt = $conn->prepare("SELECT status FROM solicitacoes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_status = $result->fetch_assoc()['status'] ?? 'Não Atendido';
        $stmt->close();

        // Validar transição
        if ($status === 'Aprovado' && $current_status !== 'Não Atendido') {
            throw new Exception("A solicitação deve estar 'Não Atendido' para ser 'Aprovado'.");
        }
        if ($status === 'Atendido' && $current_status !== 'Aprovado') {
            throw new Exception("A solicitação deve estar 'Aprovado' para ser 'Atendido'.");
        }
        if ($status === 'Não Atendido' && $current_status !== 'Não Atendido') {
            throw new Exception("Não é permitido voltar para 'Não Atendido'.");
        }

        // Atualizar status
        $stmt = $conn->prepare("UPDATE solicitacoes SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'new_status' => $status]);
        } else {
            throw new Exception("Erro ao atualizar status.");
        }

        $stmt->close();
    } else {
        throw new Exception("Método não permitido.");
    }

    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>