<?php

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/Disponibilidade.php";

class DisponibilidadeDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function cadastrar(Disponibilidade $disponibilidade)
    {
        $sql = "INSERT INTO disponibilidades
                (id_usuario, data, hora_inicio, hora_fim)
                VALUES
                (:id_usuario, :data, :hora_inicio, :hora_fim)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ":id_usuario",
            $disponibilidade->getIdUsuario()
        );

        $stmt->bindValue(
            ":data",
            $disponibilidade->getData()
        );

        $stmt->bindValue(
            ":hora_inicio",
            $disponibilidade->getHoraInicio()
        );

        $stmt->bindValue(
            ":hora_fim",
            $disponibilidade->getHoraFim()
        );

        return $stmt->execute();
    }

    public function listarPorUsuario($id_usuario)
    {
        $sql = "SELECT *
                FROM disponibilidades
                WHERE id_usuario = :id_usuario
                ORDER BY data ASC, hora_inicio ASC";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ":id_usuario",
            $id_usuario
        );

        $stmt->execute();

        $resultados = $stmt->fetchAll();

        $disponibilidades = [];

        foreach ($resultados as $dados) {

            $disponibilidade = new Disponibilidade();

            $disponibilidade->setIdDisponibilidade(
                $dados["id_disponibilidade"]
            );

            $disponibilidade->setIdUsuario(
                $dados["id_usuario"]
            );

            $disponibilidade->setData(
                $dados["data"]
            );

            $disponibilidade->setHoraInicio(
                $dados["hora_inicio"]
            );

            $disponibilidade->setHoraFim(
                $dados["hora_fim"]
            );

            $disponibilidades[] = $disponibilidade;
        }

        return $disponibilidades;
    }
}