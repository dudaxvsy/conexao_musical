<?php

require_once "config/Conexao.php";

try {
    $conexao = Conexao::conectar();

    echo "Conexão com o banco de dados realizada com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}