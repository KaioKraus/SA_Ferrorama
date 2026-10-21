<?php
session_start();
require_once __DIR__ . '/validar_acesso.php';
require_once __DIR__ . '/../infra/conexao.php';

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

if (!$usuarioEhAdministrador) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Somente administradores podem gerenciar usuários.']);
    exit;
}

// Recebe os dados do formulário
$nome = trim($_POST['nome'] ?? '');
$cpf = preg_replace('/\D/', '', trim($_POST['cpf'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? ''));
$senha = trim($_POST['senha'] ?? '');
$id = intval($_POST['id'] ?? 0);
$permissao = trim($_POST['permissao'] ?? '');

// Validações básicas
if (empty($nome) || strlen($nome) < 3) {
    echo json_encode(['success' => false, 'message' => 'Nome inválido']);
    exit;
}

if (strlen($cpf) !== 11) {
    echo json_encode(['success' => false, 'message' => 'CPF inválido']);
    exit;
}

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

if (!empty($telefone) && (strlen($telefone) < 10 || strlen($telefone) > 11)) {
    echo json_encode(['success' => false, 'message' => 'Telefone inválido']);
    exit;
}

// Validação de senha
if ($id > 0) {
    if (!empty($senha) && strlen($senha) < 8) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter no mínimo 8 caracteres']);
        exit;
    }
} else {
    if (strlen($senha) < 8) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter no mínimo 8 caracteres']);
        exit;
    }
}

// Verifica se o email já existe
if ($id > 0) {
    $sql_check = "SELECT usuario_id FROM usuarios WHERE email = ? AND usuario_id != ? LIMIT 1";
    $stmt_check = mysqli_prepare($conexao, $sql_check);
    mysqli_stmt_bind_param($stmt_check, 'si', $email, $id);
} else {
    $sql_check = "SELECT usuario_id FROM usuarios WHERE email = ? LIMIT 1";
    $stmt_check = mysqli_prepare($conexao, $sql_check);
    mysqli_stmt_bind_param($stmt_check, 's', $email);
}

mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if (mysqli_stmt_num_rows($stmt_check) > 0) {
    mysqli_stmt_close($stmt_check);
    echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado']);
    exit;
}
mysqli_stmt_close($stmt_check);

// Verifica se o CPF já existe
if ($id > 0) {
    $sql_check_cpf = "SELECT usuario_id FROM usuarios WHERE cpf = ? AND usuario_id != ? LIMIT 1";
    $stmt_check_cpf = mysqli_prepare($conexao, $sql_check_cpf);
    mysqli_stmt_bind_param($stmt_check_cpf, 'si', $cpf, $id);
} else {
    $sql_check_cpf = "SELECT usuario_id FROM usuarios WHERE cpf = ? LIMIT 1";
    $stmt_check_cpf = mysqli_prepare($conexao, $sql_check_cpf);
    mysqli_stmt_bind_param($stmt_check_cpf, 's', $cpf);
}

mysqli_stmt_execute($stmt_check_cpf);
mysqli_stmt_store_result($stmt_check_cpf);

if (mysqli_stmt_num_rows($stmt_check_cpf) > 0) {
    mysqli_stmt_close($stmt_check_cpf);
    echo json_encode(['success' => false, 'message' => 'Este CPF já está cadastrado']);
    exit;
}
mysqli_stmt_close($stmt_check_cpf);

// Define o cargo_id
$cargo_id = ($permissao === 'admin') ? 1 : 2;

// Verifica se o cargo existe
$cargo_exists = false;
$sql_check_cargo = "SELECT id FROM Cargos WHERE id = ? LIMIT 1";
if ($stmt_check_cargo = mysqli_prepare($conexao, $sql_check_cargo)) {
    mysqli_stmt_bind_param($stmt_check_cargo, 'i', $cargo_id);
    mysqli_stmt_execute($stmt_check_cargo);
    mysqli_stmt_store_result($stmt_check_cargo);
    if (mysqli_stmt_num_rows($stmt_check_cargo) > 0) {
        $cargo_exists = true;
    }
    mysqli_stmt_close($stmt_check_cargo);
}

if (!$cargo_exists) {
    $cargo_id = null;
}

$sucesso = false;

if ($id > 0) {
    // Atualização
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        if ($cargo_id === null) {
            $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, senha = ?, cargo_id = NULL WHERE usuario_id = ?";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssi', $nome, $telefone, $cpf, $email, $senha_hash, $id);
        } else {
            $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, senha = ?, cargo_id = ? WHERE usuario_id = ?";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssii', $nome, $telefone, $cpf, $email, $senha_hash, $cargo_id, $id);
        }
    } else {
        if ($cargo_id === null) {
            $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, cargo_id = NULL WHERE usuario_id = ?";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $telefone, $cpf, $email, $id);
        } else {
            $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, cargo_id = ? WHERE usuario_id = ?";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssii', $nome, $telefone, $cpf, $email, $cargo_id, $id);
        }
    }
    
    if ($stmt && mysqli_stmt_execute($stmt)) {
        $sucesso = true;
        $_SESSION['mensagem_sucesso'] = 'Usuário atualizado com sucesso!';
    }
    if ($stmt) mysqli_stmt_close($stmt);
} else {
    // Inserção
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    if ($cargo_id === null) {
        $sql = "INSERT INTO usuarios (nome, telefone, cpf, email, senha, cargo_id) VALUES (?, ?, ?, ?, ?, NULL)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, 'sssss', $nome, $telefone, $cpf, $email, $senha_hash);
    } else {
        $sql = "INSERT INTO usuarios (nome, telefone, cpf, email, senha, cargo_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, 'sssssi', $nome, $telefone, $cpf, $email, $senha_hash, $cargo_id);
    }
    
    if ($stmt && mysqli_stmt_execute($stmt)) {
        $sucesso = true;
        $_SESSION['mensagem_sucesso'] = 'Usuário cadastrado com sucesso!';
    }
    if ($stmt) mysqli_stmt_close($stmt);
}

if ($sucesso) {
    echo json_encode(['success' => true, 'reload' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao processar operação']);
}

mysqli_close($conexao);
?>
