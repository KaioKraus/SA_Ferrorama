<?php
session_start();
include "../infra/conexao.php";

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ponto_partida = trim($_POST['ponto_partida'] ?? '');
    $ponto_chegada = trim($_POST['ponto_chegada'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $trem_id = intval($_POST['trem_id'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'Ativo');

    if (empty($ponto_partida) || empty($ponto_chegada) || empty($nome) || $trem_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
        exit;
    }

    if ($ponto_partida === $ponto_chegada) {
        echo json_encode(['success' => false, 'message' => 'Ponto de partida e chegada não podem ser iguais']);
        exit;
    }

    if (strlen($nome) < 3) {
        echo json_encode(['success' => false, 'message' => 'Nome da rota deve ter no mínimo 3 caracteres']);
        exit;
    }

    $sql_check_trem = "SELECT trem_id FROM trens WHERE trem_id = ? LIMIT 1";
    $stmt_check = mysqli_prepare($conexao, $sql_check_trem);
    mysqli_stmt_bind_param($stmt_check, 'i', $trem_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    if (mysqli_stmt_num_rows($stmt_check) === 0) {
        echo json_encode(['success' => false, 'message' => 'Trem selecionado não existe']);
        mysqli_stmt_close($stmt_check);
        exit;
    }
    mysqli_stmt_close($stmt_check);

    $status_validos = ['Ativo', 'Manutenção', 'Inativo'];
    if (!in_array($status, $status_validos)) {
        $status = 'Ativo';
    }

    $sql = "INSERT INTO rotas (ponto_partida, ponto_chegada, nome, trem_id, descricao, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, 'sssiss', $ponto_partida, $ponto_chegada, $nome, $trem_id, $descricao, $status);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Rota cadastrada com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar rota']);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexao);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método não permitido']);
exit;
?>
