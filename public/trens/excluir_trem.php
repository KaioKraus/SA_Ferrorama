<?php
// Processa a exclusão administrativa de um trem e dos registros vinculados.
session_start();
require_once __DIR__ . '/../acesso/validar_acesso.php';

// Exige POST e valida o ID antes de iniciar a exclusão.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tela_trens.php');
    exit;
}
if (!$usuarioEhAdministrador) {
    header('Location: ../dashboard/dashboard.php');
    exit;
}
if (!isset($_POST['id'])) {
    header('Location: tela_trens.php?error=missing_id');
    exit;
}

$id = filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    header('Location: tela_trens.php?error=invalid_id');
    exit;
}

// Agrupa as exclusões em uma transação para evitar a remoção parcial dos dados.
try {
    if (!$conexao->begin_transaction()) {
        throw new mysqli_sql_exception($conexao->error, $conexao->errno);
    }

    // Bloqueia o trem durante a operação e verifica se existe uma rota ativa.
    $conexao->execute_query('SELECT trem_id FROM trens WHERE trem_id = ? FOR UPDATE', [$id]);
    $consulta = $conexao->execute_query("SELECT rota_id FROM rotas WHERE trem_id = ? AND status = 'Ativo' LIMIT 1 FOR UPDATE", [$id]);
    if ($consulta->fetch_assoc()) {
        $conexao->rollback();
        $_SESSION['mensagem_erro'] = 'Não é possível excluir! Este trem possui rotas ativas. Para excluí-lo, primeiro finalize ou remova as rotas associadas.';
    } else {
        // Remove relatórios, falhas e rotas antes de excluir o trem que eles referenciam.
        $conexao->execute_query('DELETE r FROM relatorio r INNER JOIN falha f ON f.falha_id = r.falha_id WHERE f.trem_id = ?', [$id]);
        $conexao->execute_query('DELETE FROM falha WHERE trem_id = ?', [$id]);
        $conexao->execute_query('DELETE FROM rotas WHERE trem_id = ?', [$id]);
        $conexao->execute_query('DELETE FROM trens WHERE trem_id = ?', [$id]);

        if ($conexao->affected_rows < 1 || !$conexao->commit()) {
            throw new mysqli_sql_exception('Não foi possível concluir a exclusão do trem.');
        }
        $_SESSION['mensagem_sucesso'] = 'Trem excluído com sucesso.';
    }
} catch (mysqli_sql_exception $erro) {
    $conexao->rollback();
    error_log('Erro ao excluir trem: ' . $erro->getMessage());
    $_SESSION['mensagem_erro'] = 'Erro ao excluir o trem. Tente novamente.';
}

header('Location: tela_trens.php');
exit;
