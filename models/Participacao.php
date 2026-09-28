<?php

class Participacao
{
    private $id_participacao;
    private $id_show;
    private $id_usuario;
    private $funcao;
    private $status;
    private $origem;
    private $observacao;
    private $data_cadastro;
    private $titulo_show;
    private $data_show;
    private $hora_inicio;
    private $hora_fim;
    private $local_show;
    private $nome_usuario;
    private $email_usuario;

    public function getIdParticipacao() { return $this->id_participacao; }
    public function setIdParticipacao($valor) { $this->id_participacao = $valor; }
    public function getIdShow() { return $this->id_show; }
    public function setIdShow($valor) { $this->id_show = $valor; }
    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($valor) { $this->id_usuario = $valor; }
    public function getFuncao() { return $this->funcao; }
    public function setFuncao($valor) { $this->funcao = $valor; }
    public function getStatus() { return $this->status; }
    public function setStatus($valor) { $this->status = $valor; }
    public function getOrigem() { return $this->origem; }
    public function setOrigem($valor) { $this->origem = $valor; }
    public function getObservacao() { return $this->observacao; }
    public function setObservacao($valor) { $this->observacao = $valor; }
    public function getDataCadastro() { return $this->data_cadastro; }
    public function setDataCadastro($valor) { $this->data_cadastro = $valor; }
    public function getTituloShow() { return $this->titulo_show; }
    public function setTituloShow($valor) { $this->titulo_show = $valor; }
    public function getDataShow() { return $this->data_show; }
    public function setDataShow($valor) { $this->data_show = $valor; }
    public function getHoraInicio() { return $this->hora_inicio; }
    public function setHoraInicio($valor) { $this->hora_inicio = $valor; }
    public function getHoraFim() { return $this->hora_fim; }
    public function setHoraFim($valor) { $this->hora_fim = $valor; }
    public function getLocalShow() { return $this->local_show; }
    public function setLocalShow($valor) { $this->local_show = $valor; }
    public function getNomeUsuario() { return $this->nome_usuario; }
    public function setNomeUsuario($valor) { $this->nome_usuario = $valor; }
    public function getEmailUsuario() { return $this->email_usuario; }
    public function setEmailUsuario($valor) { $this->email_usuario = $valor; }
}
