<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "db_sistema";

$conexao = mysqli_connect($host, $user, $passeord, $database);

if (!$conexao) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

$conexao->set_charset("utf8mb4");

?>