<?php
session_start();
include "../infra/conexao.php";

// Habilitar exibição de erros para depuração (remover em produção)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $nome = trim($_POST['nome'] ?? '');
    $cpf = preg_replace('/\D/', '', trim($_POST['cpf'] ?? '')); // Remove formatação
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $telefone = preg_replace('/\D/', '', trim($_POST['telefone'] ?? '')); // Remove formatação
    $senha = trim($_POST['senha'] ?? '');
    $id = intval($_POST['id'] ?? 0);
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

    if (!empty($telefone) && (strlen($telefone) < 10 || strlen($telefone) > 11)) {
        echo json_encode(['success' => false, 'message' => 'Telefone inválido']);
        exit;
    }

    if (strlen($senha) < 8) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter no mínimo 8 caracteres']);
        exit;
    }

    // Verifica se o email já existe (ignora o próprio usuário em edição)
    if ($id > 0) {
        $sql_check = "SELECT usuario_id FROM usuarios WHERE email = ? AND usuario_id != ? LIMIT 1";
    } else {
        $sql_check = "SELECT usuario_id FROM usuarios WHERE email = ? LIMIT 1";
    }
    if ($stmt_check = mysqli_prepare($conexao, $sql_check)) {
        if ($id > 0) {
            mysqli_stmt_bind_param($stmt_check, 'si', $email, $id);
        } else {
            mysqli_stmt_bind_param($stmt_check, 's', $email);
        }
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado']);
            mysqli_stmt_close($stmt_check);
            exit;
        }
        mysqli_stmt_close($stmt_check);
    }

    // Verifica se o CPF já existe (ignora o próprio usuário em edição)
    if ($id > 0) {
        $sql_check_cpf = "SELECT usuario_id FROM usuarios WHERE cpf = ? AND usuario_id != ? LIMIT 1";
    } else {
        $sql_check_cpf = "SELECT usuario_id FROM usuarios WHERE cpf = ? LIMIT 1";
    }
    if ($stmt_check_cpf = mysqli_prepare($conexao, $sql_check_cpf)) {
        if ($id > 0) {
            mysqli_stmt_bind_param($stmt_check_cpf, 'si', $cpf, $id);
        } else {
            mysqli_stmt_bind_param($stmt_check_cpf, 's', $cpf);
        }
        mysqli_stmt_execute($stmt_check_cpf);
        mysqli_stmt_store_result($stmt_check_cpf);
        
        if (mysqli_stmt_num_rows($stmt_check_cpf) > 0) {
            echo json_encode(['success' => false, 'message' => 'Este CPF já está cadastrado']);
            mysqli_stmt_close($stmt_check_cpf);
            exit;
        }
        mysqli_stmt_close($stmt_check_cpf);
    }

    // Define o cargo_id baseado na permissão
    $cargo_id = ($permissao === 'admin') ? 1 : 2;

    // Verifica se o cargo existe na tabela Cargos; se não existir, define como NULL para evitar violação de FK
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
        $cargo_id = null; // será tratado como NULL nas queries
    }

    if ($id > 0) {
        // Atualização de usuário
        if (!empty($senha)) {
            // atualiza senha também
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            if ($cargo_id === null) {
                $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, senha = ?, cargo_id = NULL WHERE usuario_id = ?";
                $stmt = mysqli_prepare($conexao, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $telefone, $cpf, $email, $senha_hash, $id);
                }
            } else {
                $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, senha = ?, cargo_id = ? WHERE usuario_id = ?";
                $stmt = mysqli_prepare($conexao, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sssssii', $nome, $telefone, $cpf, $email, $senha_hash, $cargo_id, $id);
                }
            }
        } else {
            // não altera a senha
            if ($cargo_id === null) {
                $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, cargo_id = NULL WHERE usuario_id = ?";
                $stmt = mysqli_prepare($conexao, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $telefone, $cpf, $email, $id);
                }
            } else {
                $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, cargo_id = ? WHERE usuario_id = ?";
                $stmt = mysqli_prepare($conexao, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssii', $nome, $telefone, $cpf, $email, $cargo_id, $id);
                }
            }
        }

        if ($stmt) {
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar usuário: ' . mysqli_error($conexao)]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar atualização: ' . mysqli_error($conexao)]);
        }
    } else {
        // Inserção de novo usuário
        // Hash da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Insere o usuário no banco
        $sql = "INSERT INTO usuarios (nome, telefone, cpf, email, senha, cargo_id) VALUES (?, ?, ?, ?, ?, ?)";
        if ($cargo_id === null) {
            $sql = "INSERT INTO usuarios (nome, telefone, cpf, email, senha, cargo_id) VALUES (?, ?, ?, ?, ?, NULL)";
            if ($stmt = mysqli_prepare($conexao, $sql)) {
                mysqli_stmt_bind_param($stmt, 'sssss', $nome, $telefone, $cpf, $email, $senha_hash);
                if (mysqli_stmt_execute($stmt)) {
                    echo json_encode(['success' => true, 'message' => 'Usuário cadastrado com sucesso!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . mysqli_error($conexao)]);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . mysqli_error($conexao)]);
            }
        } else {
            if ($stmt = mysqli_prepare($conexao, $sql)) {
                mysqli_stmt_bind_param($stmt, 'sssssi', $nome, $telefone, $cpf, $email, $senha_hash, $cargo_id);
                if (mysqli_stmt_execute($stmt)) {
                    echo json_encode(['success' => true, 'message' => 'Usuário cadastrado com sucesso!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . mysqli_error($conexao)]);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . mysqli_error($conexao)]);
            }
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

mysqli_close($conexao);
?>
