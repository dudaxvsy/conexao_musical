<?php

class Disponibilidade
{
    private $id_disponibilidade;
    private $id_usuario;
    private $data;
    private $hora_inicio;
    private $hora_fim;

    public function getIdDisponibilidade()
    {
        return $this->id_disponibilidade;
    }

    public function setIdDisponibilidade($id_disponibilidade)
    {
        $this->id_disponibilidade = $id_disponibilidade;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getHoraInicio()
    {
        return $this->hora_inicio;
    }

    public function setHoraInicio($hora_inicio)
    {
        $this->hora_inicio = $hora_inicio;
    }

    public function getHoraFim()
    {
        return $this->hora_fim;
    }

    public function setHoraFim($hora_fim)
    {
        $this->hora_fim = $hora_fim;
    }
}