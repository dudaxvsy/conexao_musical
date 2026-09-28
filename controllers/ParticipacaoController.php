<?php

require_once __DIR__ . "/../models/Participacao.php";
require_once __DIR__ . "/../dao/ParticipacaoDAO.php";

class ParticipacaoController
{
    private $participacaoDAO;

    public function __construct()
    {
        $this->participacaoDAO = new ParticipacaoDAO();
    }

    public function listarPorUsuario($id_usuario, $tipo_usuario)
    {
        if ($tipo_usuario === "musico") {
            return $this->participacaoDAO->listarPorMusico($id_usuario);
        }

        return $this->participacaoDAO->listarPorShowsDoCantor($id_usuario);
    }

    public function listarMinhasParticipacoes($id_usuario)
    {
        return $this->participacaoDAO->listarPorParticipante($id_usuario);
    }

    public function listarOfertasDoParticipante($id_usuario)
    {
        return $this->participacaoDAO->listarOfertasDoParticipante($id_usuario);
    }

    public function listarConvitesRecebidos($id_usuario)
    {
        return $this->participacaoDAO->listarConvitesRecebidos($id_usuario);
    }

    public function listarOfertasPorShow($id_show, $id_cantor)
    {
        return $this->participacaoDAO->listarOfertasPorShow($id_show, $id_cantor);
    }

    public function atualizarStatus($id_participacao, $id_cantor, $status)
    {
        return $this->participacaoDAO->atualizarStatus(
            $id_participacao,
            $id_cantor,
            $status
        );
    }

    public function responderConvite($id_participacao, $id_usuario, $status)
    {
        return $this->participacaoDAO->responderConvite(
            $id_participacao,
            $id_usuario,
            $status
        );
    }

    public function existeParticipacao($id_show, $id_usuario)
    {
        return $this->participacaoDAO->existeParticipacao($id_show, $id_usuario);
    }

    public function cadastrarOferta($id_show, $id_usuario)
    {
        if ($this->participacaoDAO->existeParticipacao($id_show, $id_usuario)) {
            return false;
        }

        return $this->participacaoDAO->cadastrarOferta($id_show, $id_usuario);
    }

    public function cadastrarConvite($id_show, $id_cantor, $id_convidado)
    {
        if ($this->participacaoDAO->existeParticipacao($id_show, $id_convidado)) {
            return false;
        }

        return $this->participacaoDAO->cadastrarConvite(
            $id_show,
            $id_cantor,
            $id_convidado
        );
    }
}
