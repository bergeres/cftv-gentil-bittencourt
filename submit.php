<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'Abertura') {
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
        $nome = $_POST['nome'] ?? '';
        $funcao = $_POST['funcao'] ?? '';
        $setor = $_POST['setor'] ?? '';
        $data_solicitacao = $_POST['data_solicitacao'] ?? '';
        $data_ocorrido = $_POST['data_ocorrido'] ?? '';
        $local_fato = $_POST['local_fato'] ?? '';
        $descricao = $_POST['descricao'] ?? '';

        if (empty($nome) || empty($funcao) || empty($setor) || empty($data_solicitacao) || empty($data_ocorrido) || empty($local_fato) || empty($descricao)) {
            throw new Exception("Preencha todos os campos.");
        }

        $stmt = $conn->prepare("INSERT INTO solicitacoes (nome, funcao, setor, data_solicitacao, data_ocorrido, local_fato, descricao, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Não Atendido')");
        $stmt->bind_param("sssssss", $nome, $funcao, $setor, $data_solicitacao, $data_ocorrido, $local_fato, $descricao);

        if ($stmt->execute()) {
            header("Location: nova_solicitacao.php?status=success");
        } else {
            throw new Exception("Erro ao enviar solicitação.");
        }

        $stmt->close();
    }

    $conn->close();
} catch (Exception $e) {
    header("Location: nova_solicitacao.php?status=error&message=" . urlencode($e->getMessage()));
}
?>