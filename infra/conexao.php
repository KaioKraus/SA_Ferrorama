<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "celestial_steel_db";

$conexao = mysqli_connect($host, $user, $password, $database);

if (!$conexao) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

$conexao->set_charset("utf8mb4");

?>