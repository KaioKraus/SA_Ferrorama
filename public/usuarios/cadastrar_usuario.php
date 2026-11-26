<?php
// Recebe o formulário administrativo para criar ou atualizar uma conta.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

header('Content-Type: application/json; charset=utf-8');

// Envia uma mensagem de validação ou de acesso no formato esperado pelo formulário.
function erroUsuario(string $mensagem, int $status = 200): void
{
    http_response_code($status);
    exit(json_encode(['success' => false, 'message' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

// Exige POST e restringe a alteração de contas a administradores.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    erroUsuario('Método não permitido', 405);
}

if (!$usuarioEhAdministrador) {
    erroUsuario('Acesso negado. Somente administradores podem gerenciar usuários.', 403);
}

// Normaliza os dados recebidos e converte o perfil escolhido no ID do cargo.
$id = (int) ($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
$cep = preg_replace('/\D/', '', $_POST['cep'] ?? '');
$logradouro = trim($_POST['logradouro'] ?? '');
$bairro = trim($_POST['bairro'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$estado = strtoupper(trim($_POST['estado'] ?? ''));
$senha = trim($_POST['senha'] ?? '');
$cargoId = trim($_POST['permissao'] ?? '') === 'admin' ? 1 : 2;

// Confere os campos obrigatórios, os tamanhos esperados e a senha quando informada.
if (strlen($nome) < 3) {
    erroUsuario('Nome inválido');
}
if (strlen($cpf) !== 11) {
    erroUsuario('CPF inválido');
}
if (!$email) {
    erroUsuario('Email inválido');
}
if (!empty($telefone) && !in_array(strlen($telefone), [10, 11], true)) {
    erroUsuario('Telefone inválido');
}
if (!empty($cep) && strlen($cep) !== 8) {
    erroUsuario('CEP inválido');
}
if (!empty($estado) && !preg_match('/^[A-Z]{2}$/', $estado)) {
    erroUsuario('UF inválida');
}
if (($id <= 0 || !empty($senha)) && strlen($senha) < 8) {
    erroUsuario('A senha deve ter no mínimo 8 caracteres');
}

// Impede que um administrador remova o próprio acesso administrativo.
if ($id === (int) $_SESSION['usuario_id'] && $cargoId !== 1) {
    erroUsuario('Você não pode alterar seu próprio perfil de Administrador para Funcionário.', 403);
}

// Verifica a unicidade de e-mail e CPF sem contar a própria conta em uma edição.
try {
    $consulta = $conexao->execute_query('SELECT usuario_id FROM usuarios WHERE email = ? AND usuario_id <> ? LIMIT 1', [$email, $id]);
    if ($consulta->fetch_assoc()) {
        erroUsuario('Este email já está cadastrado');
    }

    $consulta = $conexao->execute_query('SELECT usuario_id FROM usuarios WHERE cpf = ? AND usuario_id <> ? LIMIT 1', [$cpf, $id]);
    if ($consulta->fetch_assoc()) {
        erroUsuario('Este CPF já está cadastrado');
    }

    // Confere o cargo e prepara os dados que serão enviados ao banco.
    $consulta = $conexao->execute_query('SELECT id FROM Cargos WHERE id = ?', [$cargoId]);
    $cargoId = $consulta->fetch_assoc()['id'] ?? null;
    $hash = empty($senha) ? null : password_hash($senha, PASSWORD_DEFAULT);
    $dadosUsuario = [$nome, $telefone, $cpf, $email, $cep, $logradouro, $bairro, $cidade, $estado, $cargoId, $hash];

    // Atualiza a conta identificada ou cria uma conta nova quando o formulário não traz ID.
    if ($id > 0) {
        // COALESCE mantém o hash atual quando a senha é deixada em branco na edição.
        $sql = 'UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, cep = ?, logradouro = ?,
                bairro = ?, cidade = ?, estado = ?, cargo_id = ?, senha = COALESCE(?, senha)
                WHERE usuario_id = ?';
        $dadosUsuario[] = $id;
        $conexao->execute_query($sql, $dadosUsuario);
        $_SESSION['mensagem_sucesso'] = 'Usuário atualizado com sucesso!';
    } else {
        $sql = 'INSERT INTO usuarios (nome, telefone, cpf, email, cep, logradouro, bairro, cidade, estado, cargo_id, senha)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $conexao->execute_query($sql, $dadosUsuario);
        $_SESSION['mensagem_sucesso'] = 'Usuário cadastrado com sucesso!';
    }
} catch (mysqli_sql_exception $erro) {
    error_log('Erro ao salvar usuário: ' . $erro->getMessage());
    erroUsuario('Erro ao processar operação');
}

echo json_encode(['success' => true, 'reload' => true], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
