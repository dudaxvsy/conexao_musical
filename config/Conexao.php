<?php

class Conexao
{
    private static $conexao = null;

    private function __construct()
    {
    }

    public static function conectar()
    {
        if (self::$conexao === null) {
            $host = "localhost";
            $usuario = "root";
            $senha = "";
            $banco = "conexao_musical";

            try {
                self::$conexao = new PDO(
                    "mysql:host=$host;dbname=$banco;charset=utf8mb4",
                    $usuario,
                    $senha
                );

                self::$conexao->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$conexao->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

            } catch (PDOException $e) {
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        }

        return self::$conexao;
    }
}