<?php

require_once __DIR__ . "/../models/Disponibilidade.php";
require_once __DIR__ . "/../dao/DisponibilidadeDAO.php";

class DisponibilidadeController
{
    private $disponibilidadeDAO;

    public function __construct()
    {
        $this->disponibilidadeDAO = new DisponibilidadeDAO();
    }

    public function cadastrar(
        $id_usuario,
        $data,
        $hora_inicio,
        $hora_fim
    ) {
        $disponibilidade = new Disponibilidade();

        $disponibilidade->setIdUsuario($id_usuario);
        $disponibilidade->setData($data);
        $disponibilidade->setHoraInicio($hora_inicio);
        $disponibilidade->setHoraFim($hora_fim);

        return $this->disponibilidadeDAO->cadastrar(
            $disponibilidade
        );
    }

    public function listarPorUsuario($id_usuario)
    {
        return $this->disponibilidadeDAO->listarPorUsuario(
            $id_usuario
        );
    }
}