<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

if (!$usuarioEhAdministrador) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Somente administradores podem cadastrar rotas.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ponto_partida = trim($_POST['ponto_partida'] ?? '');
    $ponto_chegada = trim($_POST['ponto_chegada'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $trem_id = intval($_POST['trem_id'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'Ativo');
    $rotaId = null;

    if (isset($_POST['rota_id']) && $_POST['rota_id'] !== '') {
        $rotaId = filter_var($_POST['rota_id'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($rotaId === false) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Rota inválida.']);
            exit;
        }
    }

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

    if ($rotaId !== null) {
        $sql = "UPDATE rotas SET ponto_partida = ?, ponto_chegada = ?, nome = ?, trem_id = ?, descricao = ?, status = ? WHERE rota_id = ? LIMIT 1";
    } else {
        $sql = "INSERT INTO rotas (ponto_partida, ponto_chegada, nome, trem_id, descricao, status) VALUES (?, ?, ?, ?, ?, ?)";
    }
    $stmt = mysqli_prepare($conexao, $sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar gravação da rota.']);
        exit;
    }

    if ($rotaId !== null) {
        mysqli_stmt_bind_param($stmt, 'sssissi', $ponto_partida, $ponto_chegada, $nome, $trem_id, $descricao, $status, $rotaId);
    } else {
        mysqli_stmt_bind_param($stmt, 'sssiss', $ponto_partida, $ponto_chegada, $nome, $trem_id, $descricao, $status);
    }

    if (mysqli_stmt_execute($stmt)) {
        if ($rotaId !== null && mysqli_stmt_affected_rows($stmt) === 0) {
            $stmt_check_rota = mysqli_prepare($conexao, 'SELECT rota_id FROM rotas WHERE rota_id = ? LIMIT 1');
            if ($stmt_check_rota === false) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao verificar a rota.']);
                mysqli_stmt_close($stmt);
                exit;
            }
            mysqli_stmt_bind_param($stmt_check_rota, 'i', $rotaId);
            mysqli_stmt_execute($stmt_check_rota);
            mysqli_stmt_store_result($stmt_check_rota);
            $rotaExiste = mysqli_stmt_num_rows($stmt_check_rota) > 0;
            mysqli_stmt_close($stmt_check_rota);
            if (!$rotaExiste) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'A rota não foi encontrada.']);
                mysqli_stmt_close($stmt);
                exit;
            }
        }
        $_SESSION['mensagem_sucesso'] = $rotaId !== null
            ? 'Rota atualizada com sucesso!'
            : 'Rota cadastrada com sucesso!';
        echo json_encode(['success' => true, 'reload' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $rotaId !== null ? 'Erro ao atualizar rota' : 'Erro ao cadastrar rota',
        ]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexao);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método não permitido']);
exit;
?>
