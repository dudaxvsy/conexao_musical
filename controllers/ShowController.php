<?php

require_once __DIR__ . "/../models/Show.php";
require_once __DIR__ . "/../dao/ShowDAO.php";

class ShowController
{
    private $showDAO;

    public function __construct()
    {
        $this->showDAO = new ShowDAO();
    }

    public function cadastrar(
        $id_usuario,
        $titulo,
        $data,
        $hora_inicio,
        $hora_fim,
        $local,
        $endereco,
        $descricao
    ) {
        $show = new Show();

        $show->setIdUsuario($id_usuario);
        $show->setTitulo($titulo);
        $show->setData($data);
        $show->setHoraInicio($hora_inicio);
        $show->setHoraFim($hora_fim);
        $show->setLocal($local);
        $show->setEndereco($endereco);
        $show->setDescricao($descricao);

        return $this->showDAO->cadastrar($show);
    }

    public function listarPorUsuario($id_usuario)
    {
        return $this->showDAO->listarPorUsuario($id_usuario);
    }

    public function listarDisponiveisParaMusico($id_usuario)
    {
        return $this->showDAO->listarDisponiveisParaMusico($id_usuario);
    }

    public function listarParticipantesConfirmados($id_show, $id_cantor)
    {
        return $this->showDAO->listarParticipantesConfirmados($id_show, $id_cantor);
    }
}
