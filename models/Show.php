<?php

class Show
{
    private $id_show;
    private $id_usuario;
    private $titulo;
    private $data;
    private $hora_inicio;
    private $hora_fim;
    private $local;
    private $endereco;
    private $descricao;
    private $data_cadastro;
    private $nome_cantor;

    public function getIdShow()
    {
        return $this->id_show;
    }

    public function setIdShow($id_show)
    {
        $this->id_show = $id_show;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
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

    public function getLocal()
    {
        return $this->local;
    }

    public function setLocal($local)
    {
        $this->local = $local;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDataCadastro()
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro($data_cadastro)
    {
        $this->data_cadastro = $data_cadastro;
    }
    public function getNomeCantor()
    {
        return $this->nome_cantor;
    }

    public function setNomeCantor($nome_cantor)
    {
        $this->nome_cantor = $nome_cantor;
    }

}

