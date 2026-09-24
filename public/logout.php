<?php

session_start();

$_SESSION = []; // Limpa todos os dados que estavam salvos na sessão

// Verifica se a sessão está usando cookies
if (ini_get('session.use_cookies')) {

    
    $params = session_get_cookie_params(); // Pega as configurações do cookie da sessão

    // Expira o cookie da sessão
    setcookie( session_name(), '', time() - 42000, // 'time()' coloca a validade do cookie no passado
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destrói a sessão
session_destroy();

// Evita que o navegador mostre uma página antiga pelo cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

header('Location: ../index.php');

exit;