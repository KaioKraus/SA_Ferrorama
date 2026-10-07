<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

if (!isset($_SESSION['usuario_id']) || !$usuarioEhAdministrador) {
    echo json_encode([
        'success' => false,
        'message' => 'Acesso negado. Somente administradores podem cadastrar trens.'
    ]);
    exit;
}

$nomeTrem = trim((string)($_POST['nome_trem'] ?? ''));
$partida = trim((string)($_POST['partida'] ?? ''));
$chegada = trim((string)($_POST['chegada'] ?? ''));
$horarioPartida = trim((string)($_POST['horario_partida'] ?? ''));
$horarioChegada = trim((string)($_POST['horario_chegada'] ?? ''));
$diasSelecionados = $_POST['dias_semana'] ?? [];

if (is_string($diasSelecionados)) {
    $diasSelecionados = [$diasSelecionados];
}

$diasSemana = array_map('trim', array_filter(array_map('strval', $diasSelecionados), function ($dia) {
    return $dia !== '';
}));

if ($nomeTrem === '' || $partida === '' || $chegada === '' || $horarioPartida === '' || $horarioChegada === '') {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

if (count($diasSemana) === 0) {
    echo json_encode(['success' => false, 'message' => 'Selecione pelo menos um dia da semana.']);
    exit;
}

$horarioPartidaFormatado = date('H:i:s', strtotime($horarioPartida));
$horarioChegadaFormatado = date('H:i:s', strtotime($horarioChegada));
$diasSemanaFormatado = implode(', ', $diasSemana);

if ($horarioPartidaFormatado === '00:00:00' && $horarioPartida !== '00:00') {
    $horarioPartidaFormatado = date('H:i:s', strtotime($horarioPartida));
}

if ($horarioChegadaFormatado === '00:00:00' && $horarioChegada !== '00:00') {
    $horarioChegadaFormatado = date('H:i:s', strtotime($horarioChegada));
}

$verificaColuna = mysqli_query($conexao, "SHOW COLUMNS FROM trens LIKE 'dias_semana'");
if ($verificaColuna && mysqli_num_rows($verificaColuna) > 0) {
    $coluna = mysqli_fetch_assoc($verificaColuna);
    if (stripos($coluna['Type'] ?? '', 'date') !== false) {
        mysqli_query($conexao, "ALTER TABLE trens MODIFY dias_semana VARCHAR(50) NOT NULL");
        mysqli_query($conexao, "ALTER TABLE trens MODIFY horario_partida TIME NOT NULL");
        mysqli_query($conexao, "ALTER TABLE trens MODIFY horario_chegada TIME NOT NULL");
    }
}

$sql = "INSERT INTO trens (nome_trem, dias_semana, horario_partida, horario_chegada) VALUES (?, ?, ?, ?)";

if ($stmt = mysqli_prepare($conexao, $sql)) {
    mysqli_stmt_bind_param($stmt, 'ssss', $nomeTrem, $diasSemanaFormatado, $horarioPartidaFormatado, $horarioChegadaFormatado);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Trem cadastrado com sucesso!']);
    } else {
        $erro = mysqli_error($conexao);
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar trem: ' . $erro]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . mysqli_error($conexao)]);
}

mysqli_close($conexao);
