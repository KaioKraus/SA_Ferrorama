<?php
// Abre a conexão MySQL compartilhada pelas telas e pelos endpoints que acessam o banco.

$host = "localhost";
$user = "root";
$password = "";
$database = "celestial_steel_db";

$conexao = new mysqli($host, $user, $password, $database, 3306);

// Interrompe a execução se a conexão com o banco falhar.
if ($conexao->connect_error) {
    die("Não foi possível conectar ao banco de dados. Verifique se o MySQL do XAMPP está em execução.");
}

// Mantém a codificação da conexão compatível com acentos e outros caracteres Unicode.
$conexao->set_charset("utf8mb4");

?>