<?php
session_start();
include "../infra/conexao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $nome = trim($_POST['nome'] ?? '');
    $cpf = preg_replace('/\D/', '', trim($_POST['cpf'] ?? '')); // Remove formatação
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? '')); // Remove formatação
    $senha = trim($_POST['senha'] ?? '');
    $permissao = trim($_POST['permissao'] ?? ''); // 'admin' ou 'funcionario'

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

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {
        echo json_encode(['success' => false, 'message' => 'Telefone inválido']);
        exit;
    }

    if (strlen($senha) < 8) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter no mínimo 8 caracteres']);
        exit;
    }

    // Verifica se o email já existe
    $sql_check = "SELECT email FROM usuarios WHERE email = ? LIMIT 1";
    if ($stmt_check = mysqli_prepare($conexao, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, 's', $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado']);
            mysqli_stmt_close($stmt_check);
            exit;
        }
        mysqli_stmt_close($stmt_check);
    }

    // Verifica se o CPF já existe
    $sql_check_cpf = "SELECT cpf FROM usuarios WHERE cpf = ? LIMIT 1";
    if ($stmt_check_cpf = mysqli_prepare($conexao, $sql_check_cpf)) {
        mysqli_stmt_bind_param($stmt_check_cpf, 's', $cpf);
        mysqli_stmt_execute($stmt_check_cpf);
        mysqli_stmt_store_result($stmt_check_cpf);
        
        if (mysqli_stmt_num_rows($stmt_check_cpf) > 0) {
            echo json_encode(['success' => false, 'message' => 'Este CPF já está cadastrado']);
            mysqli_stmt_close($stmt_check_cpf);
            exit;
        }
        mysqli_stmt_close($stmt_check_cpf);
    }

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

mysqli_close($conexao);
?>
