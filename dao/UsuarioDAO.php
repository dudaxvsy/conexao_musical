<?php

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/Usuario.php";

class UsuarioDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
        $this->garantirColunaWhatsapp();
    }

    private function garantirColunaWhatsapp()
    {
        $stmt = $this->conexao->query("SHOW COLUMNS FROM usuarios LIKE 'whatsapp'");

        if (!$stmt->fetch()) {
            $this->conexao->exec("ALTER TABLE usuarios ADD COLUMN whatsapp VARCHAR(20) NULL AFTER email");
        }
    }

    public function cadastrar(Usuario $usuario)
    {
        $sql = "INSERT INTO usuarios (nome, email, whatsapp, senha, tipo)
                VALUES (:nome, :email, :whatsapp, :senha, :tipo)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(":nome", $usuario->getNome());
        $stmt->bindValue(":email", $usuario->getEmail());
        $stmt->bindValue(":whatsapp", $usuario->getWhatsapp());
        $stmt->bindValue(":senha", $usuario->getSenha());
        $stmt->bindValue(":tipo", $usuario->getTipo());

        return $stmt->execute();
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios
                WHERE email = :email";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        $dados = $stmt->fetch();

        if ($dados) {
            $usuario = new Usuario();

            $usuario->setIdUsuario($dados["id_usuario"]);
            $usuario->setNome($dados["nome"]);
            $usuario->setEmail($dados["email"]);
            $usuario->setWhatsapp($dados["whatsapp"] ?? "");
            $usuario->setSenha($dados["senha"]);
            $usuario->setTipo($dados["tipo"]);
            $usuario->setDataCadastro($dados["data_cadastro"]);

            return $usuario;
        }

        return null;
    }

    public function atualizarContato($idUsuario, $whatsapp)
    {
        $sql = "UPDATE usuarios
                SET whatsapp = :whatsapp
                WHERE id_usuario = :id_usuario";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":whatsapp", $whatsapp);
        $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function atualizarDadosConta($idUsuario, $nome, $email, $senhaAtual, $novaSenha)
    {
        $stmtUsuario = $this->conexao->prepare(
            "SELECT id_usuario, senha FROM usuarios WHERE id_usuario = :id_usuario"
        );
        $stmtUsuario->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmtUsuario->execute();
        $usuario = $stmtUsuario->fetch();

        if (!$usuario) {
            throw new Exception("Usuário não encontrado.");
        }

        $contaFoiAlterada = ($nome !== $this->buscarNomeAtual($idUsuario)) || ($email !== $this->buscarEmailAtual($idUsuario)) || !empty($novaSenha);

        if (!$contaFoiAlterada) {
            return true;
        }

        if (empty($senhaAtual) || !password_verify($senhaAtual, $usuario["senha"])) {
            throw new Exception("Informe sua senha atual corretamente para alterar os dados da conta.");
        }

        $stmtEmail = $this->conexao->prepare(
            "SELECT id_usuario FROM usuarios WHERE email = :email AND id_usuario <> :id_usuario"
        );
        $stmtEmail->bindValue(":email", $email);
        $stmtEmail->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmtEmail->execute();

        if ($stmtEmail->fetch()) {
            throw new Exception("Este e-mail já está cadastrado em outra conta.");
        }

        if (!empty($novaSenha)) {
            if (strlen($novaSenha) < 6) {
                throw new Exception("A nova senha deve ter pelo menos 6 caracteres.");
            }

            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios
                    SET nome = :nome, email = :email, senha = :senha
                    WHERE id_usuario = :id_usuario";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":senha", $senhaHash);
        } else {
            $sql = "UPDATE usuarios
                    SET nome = :nome, email = :email
                    WHERE id_usuario = :id_usuario";

            $stmt = $this->conexao->prepare($sql);
        }

        $stmt->bindValue(":nome", $nome);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function buscarNomeAtual($idUsuario)
    {
        $stmt = $this->conexao->prepare("SELECT nome FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return (string) $stmt->fetchColumn();
    }

    private function buscarEmailAtual($idUsuario)
    {
        $stmt = $this->conexao->prepare("SELECT email FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return (string) $stmt->fetchColumn();
    }

    public function buscarMusicosDisponiveis($data = "", $horaInicio = "", $horaFim = "")
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.nome,
                    u.email,
                    u.whatsapp,
                    d.data,
                    d.hora_inicio,
                    d.hora_fim
                FROM usuarios u
                INNER JOIN disponibilidades d
                    ON d.id_usuario = u.id_usuario
                WHERE u.tipo = 'musico'";

        $parametros = [];

        if (!empty($data)) {
            $sql .= " AND d.data = :data";
            $parametros[":data"] = $data;
        } else {
            $sql .= " AND d.data >= CURDATE()";
        }

        if (!empty($horaInicio) && !empty($horaFim)) {
            $sql .= "
                AND d.hora_inicio <= :hora_inicio
                AND d.hora_fim >= :hora_fim";

            $parametros[":hora_inicio"] = $horaInicio;
            $parametros[":hora_fim"] = $horaFim;
        }

        $sql .= " ORDER BY d.data ASC, d.hora_inicio ASC, u.nome ASC";

        $stmt = $this->conexao->prepare($sql);

        foreach ($parametros as $parametro => $valor) {
            $stmt->bindValue($parametro, $valor);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function buscarCantores($nome = "", $idUsuarioAtual = 0)
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.nome,
                    u.email,
                    u.whatsapp,
                    p.cidade,
                    p.instagram,
                    p.descricao,
                    p.foto
                FROM usuarios u
                LEFT JOIN perfis p ON p.id_usuario = u.id_usuario
                WHERE u.tipo = 'cantor'
                  AND u.id_usuario <> :id_usuario";

        $parametros = [":id_usuario" => $idUsuarioAtual];

        if (!empty($nome)) {
            $sql .= " AND u.nome LIKE :nome";
            $parametros[":nome"] = "%" . $nome . "%";
        }

        $sql .= " ORDER BY u.nome ASC";

        $stmt = $this->conexao->prepare($sql);

        foreach ($parametros as $parametro => $valor) {
            $stmt->bindValue($parametro, $valor);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
