<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["tipo_usuario"] !== "cantor") {
    header("Location: ../../public/index.php");
    exit;
}

require_once __DIR__ . "/../../controllers/UsuarioController.php";
require_once __DIR__ . "/../../controllers/ShowController.php";
require_once __DIR__ . "/../../controllers/ParticipacaoController.php";

$nomeBusca = trim($_GET["nome"] ?? "");
$idShowSelecionado = (int)($_GET["id_show"] ?? 0);

$cantores = [];
$shows = [];
$mensagem = "";
$tipoMensagem = "";

try {
    $usuarioController = new UsuarioController();
    $showController = new ShowController();
    $participacaoController = new ParticipacaoController();

    $shows = $showController->listarPorUsuario($_SESSION["id_usuario"]);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $idShow = (int)($_POST["id_show"] ?? 0);
        $idConvidado = (int)($_POST["id_convidado"] ?? 0);

        if ($idShow <= 0 || $idConvidado <= 0) {
            $mensagem = "Selecione um show e um cantor válido.";
            $tipoMensagem = "erro";
        } elseif ($participacaoController->existeParticipacao($idShow, $idConvidado)) {
            $mensagem = "Esse cantor já possui uma participação ou convite para esse show.";
            $tipoMensagem = "aviso";
        } elseif ($participacaoController->cadastrarConvite($_SESSION["id_usuario"], $_SESSION["id_usuario"], $idConvidado)) {
            $mensagem = "Convite enviado com sucesso.";
            $tipoMensagem = "sucesso";
            $idShowSelecionado = $idShow;
        } else {
            $mensagem = "Não foi possível enviar o convite.";
            $tipoMensagem = "erro";
        }
    }

    $cantores = $usuarioController->buscarCantores(
        $nomeBusca,
        $_SESSION["id_usuario"]
    );
} catch (Exception $e) {
    $mensagem = "Não foi possível carregar os cantores no momento.";
    $tipoMensagem = "erro";
}

function formatarData($data)
{
    return date("d/m/Y", strtotime($data));
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar cantores - Conexão Musical</title>
    <link rel="stylesheet" href="../../public/css/buscar_cantores.css">
</head>
<body>

<header class="topo">
    <div class="logo">
        <span class="icone-musical">♫</span>
        <div>
            <span>Conexão</span>
            <span>Musical</span>
        </div>
    </div>

    <div class="usuario">
        <div class="informacoes-usuario">
            <span class="nome-usuario"><?= htmlspecialchars($_SESSION["nome_usuario"]) ?></span>
            <span class="tipo-usuario">Cantor</span>
        </div>
        <a href="logout.php" class="botao-sair">SAIR</a>
    </div>
</header>

<main class="conteudo">
    <a href="../../public/index.php" class="voltar">← Voltar</a>

    <div class="cabecalho-pagina">
        <h1>Buscar cantores</h1>
        <p>Encontre outro cantor e envie um convite para participar de um dos seus shows.</p>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem <?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($shows)): ?>
        <section class="estado-vazio">
            <div class="icone-vazio">♪</div>
            <h2>Cadastre um show primeiro</h2>
            <p>Você precisa ter pelo menos um show cadastrado para enviar convites.</p>
            <a href="../show/meus.php" class="botao-principal">IR PARA MEUS SHOWS</a>
        </section>
    <?php else: ?>
        <section class="filtro">
            <div class="titulo-filtro">
                <h2>Pesquisar cantor</h2>
                <p>Digite o nome para encontrar um cantor específico.</p>
            </div>

            <form method="GET" class="formulario-filtro">
                <div class="campo">
                    <label for="nome">Nome</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars($nomeBusca) ?>"
                        placeholder="Ex.: João"
                    >
                </div>
                <button type="submit" class="botao-buscar">BUSCAR</button>
            </form>

            <?php if ($nomeBusca !== ""): ?>
                <a href="buscar_cantores.php" class="limpar-filtro">Limpar busca</a>
            <?php endif; ?>
        </section>

        <?php if (empty($cantores)): ?>
            <section class="estado-vazio">
                <div class="icone-vazio">♫</div>
                <h2>Nenhum cantor encontrado</h2>
                <p>Não encontramos outro cantor com esse nome.</p>
            </section>
        <?php else: ?>
            <section class="lista-cantores">
                <?php foreach ($cantores as $cantor): ?>
                    <article class="cantor-card">
                        <div class="cantor-topo">
                            <?php if (!empty($cantor["foto"])): ?>
                                <img src="../../<?= htmlspecialchars($cantor["foto"]) ?>" alt="Foto de <?= htmlspecialchars($cantor["nome"]) ?>" class="foto-cantor">
                            <?php else: ?>
                                <div class="foto-cantor-placeholder">♫</div>
                            <?php endif; ?>

                            <div class="dados-cantor">
                                <h2><?= htmlspecialchars($cantor["nome"]) ?></h2>
                                <?php if (!empty($cantor["cidade"])): ?>
                                    <span><?= htmlspecialchars($cantor["cidade"]) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($cantor["instagram"])): ?>
                                    <span>Instagram: <?= htmlspecialchars($cantor["instagram"]) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($cantor["descricao"])): ?>
                            <p class="descricao"><?= nl2br(htmlspecialchars($cantor["descricao"])) ?></p>
                        <?php endif; ?>

                        <form method="POST" class="form-convite">
                            <input type="hidden" name="id_convidado" value="<?= (int)$cantor["id_usuario"] ?>">

                            <label for="show_<?= (int)$cantor["id_usuario"] ?>">Convidar para o show</label>
                            <select
                                id="show_<?= (int)$cantor["id_usuario"] ?>"
                                name="id_show"
                                required
                            >
                                <option value="">Selecione um show</option>
                                <?php foreach ($shows as $show): ?>
                                    <option
                                        value="<?= (int)$show->getIdShow() ?>"
                                        <?= $idShowSelecionado === (int)$show->getIdShow() ? "selected" : "" ?>
                                    >
                                        <?= htmlspecialchars($show->getTitulo()) ?> — <?= formatarData($show->getData()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit" class="botao-convidar">ENVIAR CONVITE</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<footer>© 2026 CONEXÃO MUSICAL</footer>
</body>
</html>
