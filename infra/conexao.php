<?php

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "celestial_steel_db";

$conexao = @mysqli_connect($host, $user, $password, $database, 3306);

if (!$conexao) {
    http_response_code(503);
    die("Não foi possível conectar ao banco de dados. Verifique se o MySQL do XAMPP está em execução.");
}

$conexao->set_charset("utf8mb4");

?>