<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../../controllers/UsuarioController.php";
require_once __DIR__ . "/../../controllers/PerfilController.php";

$idUsuario = (int) $_SESSION["id_usuario"];
$usuarioController = new UsuarioController();
$perfilController = new PerfilController();

$usuario = $usuarioController->buscarPorEmail($_SESSION["email_usuario"] ?? "");
$perfil = $perfilController->buscarPorUsuario($idUsuario);

if (!$usuario) {
    header("Location: login.php");
    exit;
}

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senhaAtual = $_POST["senha_atual"] ?? "";
    $novaSenha = $_POST["nova_senha"] ?? "";
    $confirmarSenha = $_POST["confirmar_senha"] ?? "";

    try {
        if ($nome === "" || $email === "") {
            throw new Exception("Nome e e-mail são obrigatórios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Informe um e-mail válido.");
        }

        if ($novaSenha !== $confirmarSenha) {
            throw new Exception("A nova senha e a confirmação não coincidem.");
        }

        $alterou = $usuarioController->atualizarDadosConta(
            $idUsuario,
            $nome,
            $email,
            $senhaAtual,
            $novaSenha
        );

        if ($alterou) {
            $_SESSION["nome_usuario"] = $nome;
            $_SESSION["email_usuario"] = $email;

            $mensagem = "Dados da conta atualizados com sucesso!";
            $tipoMensagem = "sucesso";
            $usuario = $usuarioController->buscarPorEmail($email);
        } else {
            $mensagem = "Não foi possível atualizar os dados da conta.";
            $tipoMensagem = "erro";
        }
    } catch (Exception $e) {
        $mensagem = "Erro ao atualizar os dados da conta: " . $e->getMessage();
        $tipoMensagem = "erro";
    }
}

$nome = $usuario->getNome();
$email = $usuario->getEmail();
$tipoUsuario = $_SESSION["tipo_usuario"] ?? $usuario->getTipo();
$foto = $perfil->getFoto();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dados da conta - Conexão Musical</title>
    <link rel="stylesheet" href="../../public/css/perfil.css">
    <style>
        .conta-card { max-width: 850px; margin: 0 auto; }
        .descricao-conta { color:#9A8EAA; font-size:14px; line-height:1.5; margin-top:-2px; margin-bottom:6px; }
        .acoes-conta { display:flex; justify-content:flex-end; gap:12px; margin-top:28px; }
        .voltar-conta { color:#746580; background:#E9E1F7; }
        .voltar-conta:hover { background:#DDD5E8; }
        .foto-mini { width:58px; height:58px; border-radius:50%; object-fit:cover; border:2px solid #E9E1F7; }
        .foto-mini-placeholder { width:58px; height:58px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#E9E1F7; color:#A996E8; font-size:25px; font-weight:bold; }
        .identificacao-conta { display:flex; align-items:center; gap:16px; margin-bottom:26px; }
        .identificacao-conta h2 { color:#5E4A85; font-size:20px; margin-bottom:5px; }
        .identificacao-conta p { color:#9A8EAA; font-size:13px; }
        @media (max-width:650px) { .acoes-conta { flex-direction:column; } .acoes-conta .botao { width:100%; text-align:center; } }
    </style>
</head>
<body>

<header class="topo">
    <div class="logo">
        <span class="icone-musical">♫</span>
        <div><span>Conexão</span><span>Musical</span></div>
    </div>
    <a href="../../public/index.php" class="voltar">VOLTAR</a>
</header>

<main class="conteudo">
    <div class="cabecalho">
        <h1>Dados da conta</h1>
        <p>Altere seu nome, e-mail ou senha de acesso à plataforma.</p>
    </div>

    <?php if (!empty($mensagem)): ?>
        <div class="mensagem <?= $tipoMensagem ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <section class="card conta-card">
        <div class="identificacao-conta">
            <?php if (!empty($foto)): ?>
                <img src="../../public/uploads/perfis/<?= htmlspecialchars($foto) ?>" alt="Foto de perfil" class="foto-mini">
            <?php else: ?>
                <div class="foto-mini-placeholder">♫</div>
            <?php endif; ?>
            <div>
                <h2><?= htmlspecialchars($nome) ?></h2>
                <p><?= $tipoUsuario === "musico" ? "Músico" : "Cantor" ?></p>
            </div>
        </div>

        <div class="linha-campos">
            <div class="campo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" maxlength="100" form="form-conta" required>
            </div>

            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" maxlength="100" form="form-conta" required>
            </div>
        </div>

        <form method="POST" id="form-conta">
            <div class="campo">
                <label for="senha_atual">Senha atual</label>
                <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual para alterar os dados da conta">
            </div>

            <div class="linha-campos">
                <div class="campo">
                    <label for="nova_senha">Nova senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" minlength="6" placeholder="Deixe em branco para não alterar">
                </div>

                <div class="campo">
                    <label for="confirmar_senha">Confirmar nova senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="6" placeholder="Repita a nova senha">
                </div>
            </div>

            <div class="acoes-conta">
                <a href="../../public/index.php" class="botao voltar-conta">CANCELAR</a>
                <button type="submit" class="botao salvar">SALVAR ALTERAÇÕES</button>
            </div>
        </form>
    </section>
</main>

<footer>© 2026 CONEXÃO MUSICAL</footer>

</body>
</html>
