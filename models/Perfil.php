<?php

class Perfil
{
    private $id_usuario;
    private $cidade;
    private $instagram;
    private $descricao;
    private $foto;
    private $instrumentos;

    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($valor) { $this->id_usuario = $valor; }

    public function getCidade() { return $this->cidade; }
    public function setCidade($valor) { $this->cidade = $valor; }

    public function getInstagram() { return $this->instagram; }
    public function setInstagram($valor) { $this->instagram = $valor; }

    public function getDescricao() { return $this->descricao; }
    public function setDescricao($valor) { $this->descricao = $valor; }

    public function getFoto() { return $this->foto; }
    public function setFoto($valor) { $this->foto = $valor; }

    public function getInstrumentos() { return $this->instrumentos; }
    public function setInstrumentos($valor) { $this->instrumentos = $valor; }
}
