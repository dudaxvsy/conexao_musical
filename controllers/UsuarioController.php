<?php

require_once __DIR__ . "/../models/Usuario.php";
require_once __DIR__ . "/../dao/UsuarioDAO.php";

class UsuarioController
{
    private $usuarioDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function cadastrar($nome, $email, $whatsapp, $senha, $tipo)
    {
        $usuario = new Usuario();

        $usuario->setNome($nome);
        $usuario->setEmail($email);
        $usuario->setWhatsapp($whatsapp);
        $usuario->setSenha(
            password_hash($senha, PASSWORD_DEFAULT)
        );
        $usuario->setTipo($tipo);

        return $this->usuarioDAO->cadastrar($usuario);
    }

    public function buscarPorEmail($email)
    {
        return $this->usuarioDAO->buscarPorEmail($email);
    }

    public function autenticar($email, $senha)
    {
        $usuario = $this->usuarioDAO->buscarPorEmail($email);

        if ($usuario === null) {
            return null;
        }

        if (!password_verify($senha, $usuario->getSenha())) {
            return null;
        }

        return $usuario;
    }

    public function atualizarContato($idUsuario, $whatsapp)
    {
        return $this->usuarioDAO->atualizarContato($idUsuario, $whatsapp);
    }

    public function atualizarDadosConta($idUsuario, $nome, $email, $senhaAtual, $novaSenha)
    {
        return $this->usuarioDAO->atualizarDadosConta(
            $idUsuario,
            $nome,
            $email,
            $senhaAtual,
            $novaSenha
        );
    }

    public function buscarMusicosDisponiveis($data = "", $horaInicio = "", $horaFim = "")
    {
        return $this->usuarioDAO->buscarMusicosDisponiveis(
            $data,
            $horaInicio,
            $horaFim
        );
    }

    public function buscarCantores($nome = "", $idUsuarioAtual = 0)
    {
        return $this->usuarioDAO->buscarCantores($nome, $idUsuarioAtual);
    }
}
