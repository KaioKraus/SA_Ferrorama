<?php
// Encerra a sessão e devolve o navegador à página inicial.

session_start();

$_SESSION = []; // Limpa todos os dados que estavam salvos na sessão

// Quando a sessão usa um cookie, expira o cookie com o mesmo domínio e caminho.
if (ini_get('session.use_cookies')) {

    
    $params = session_get_cookie_params(); // Pega as configurações do cookie da sessão

    // Uma validade no passado faz o navegador remover o cookie.
    setcookie( session_name(), '', time() - 42000, // 'time()' coloca a validade do cookie no passado
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Descarta a sessão armazenada no servidor.
session_destroy();

// Pede ao navegador que descarte a resposta de logout do cache.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

header('Location: ../../index.php');

exit;