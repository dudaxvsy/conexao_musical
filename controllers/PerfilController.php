<?php

require_once __DIR__ . "/../models/Perfil.php";
require_once __DIR__ . "/../dao/PerfilDAO.php";

class PerfilController
{
    private $perfilDAO;

    public function __construct()
    {
        $this->perfilDAO = new PerfilDAO();
    }

    public function buscarPorUsuario($idUsuario)
    {
        return $this->perfilDAO->buscarPorUsuario($idUsuario);
    }

    public function salvar($idUsuario, $cidade, $instagram, $descricao, $foto, $instrumentos)
    {
        $perfil = new Perfil();
        $perfil->setIdUsuario($idUsuario);
        $perfil->setCidade($cidade);
        $perfil->setInstagram($instagram);
        $perfil->setDescricao($descricao);
        $perfil->setFoto($foto);
        $perfil->setInstrumentos($instrumentos);

        return $this->perfilDAO->salvar($perfil);
    }
}
