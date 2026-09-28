<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../usuario/login.php");
    exit;
}

if ($_SESSION["tipo_usuario"] !== "cantor") {
    header("Location: ../../public/index.php");
    exit;
}

require_once __DIR__ . "/../../controllers/ShowController.php";

$mensagem = "";
$tipoMensagem = "";

$titulo = "";
$data = "";
$hora_inicio = "";
$hora_fim = "";
$local = "";
$endereco = "";
$descricao = "";

try {
    $controller = new ShowController();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $titulo = trim($_POST["titulo"] ?? "");
        $data = $_POST["data"] ?? "";
        $hora_inicio = $_POST["hora_inicio"] ?? "";
        $hora_fim = $_POST["hora_fim"] ?? "";
        $local = trim($_POST["local"] ?? "");
        $endereco = trim($_POST["endereco"] ?? "");
        $descricao = trim($_POST["descricao"] ?? "");

        if (
            empty($titulo) ||
            empty($data) ||
            empty($hora_inicio) ||
            empty($hora_fim) ||
            empty($local)
        ) {
            $mensagem = "Preencha todos os campos obrigatórios.";
            $tipoMensagem = "erro";
        } elseif ($hora_inicio >= $hora_fim) {
            $mensagem = "A hora de início deve ser anterior à hora de término.";
            $tipoMensagem = "erro";
        } elseif ($data < date("Y-m-d")) {
            $mensagem = "A data do show não pode ser anterior à data atual.";
            $tipoMensagem = "erro";
        } else {
            $resultado = $controller->cadastrar(
                $_SESSION["id_usuario"],
                $titulo,
                $data,
                $hora_inicio,
                $hora_fim,
                $local,
                $endereco,
                $descricao
            );

            if ($resultado) {
                $mensagem = "Show cadastrado com sucesso.";
                $tipoMensagem = "sucesso";

                $titulo = "";
                $data = "";
                $hora_inicio = "";
                $hora_fim = "";
                $local = "";
                $endereco = "";
                $descricao = "";
            } else {
                $mensagem = "Não foi possível cadastrar o show.";
                $tipoMensagem = "erro";
            }
        }
    }

    $shows = $controller->listarPorUsuario($_SESSION["id_usuario"]);
    $participantesPorShow = [];

    foreach ($shows as $show) {
        $participantesPorShow[$show->getIdShow()] =
            $controller->listarParticipantesConfirmados(
                $show->getIdShow(),
                $_SESSION["id_usuario"]
            );
    }

} catch (Exception $e) {
    $shows = [];
    $participantesPorShow = [];
    $mensagem = "Não foi possível carregar os shows no momento.";
    $tipoMensagem = "erro";
}

function formatarData($data)
{
    return date("d/m/Y", strtotime($data));
}

function formatarHora($hora)
{
    return date("H:i", strtotime($hora));
}

function textoTipoParticipante($tipo)
{
    return $tipo === "musico" ? "Músico" : "Cantor";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus shows - Conexão Musical</title>
    <link rel="stylesheet" href="../../public/css/show.css">
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
        <a href="../usuario/logout.php" class="botao-sair">SAIR</a>
    </div>
</header>

<main class="conteudo">
    <a href="../../public/index.php" class="voltar">← Voltar</a>

    <div class="cabecalho-pagina">
        <h1>Meus shows</h1>
        <p>Cadastre seus shows e acompanhe as apresentações que você organizou.</p>
    </div>

    <?php if (!empty($mensagem)): ?>
        <div class="mensagem <?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <section class="secao-cadastro">
        <div class="titulo-secao">
            <h2>Cadastrar show</h2>
            <p>Preencha as informações da apresentação.</p>
        </div>

        <form method="POST" class="formulario">
            <div class="campo campo-completo">
                <label for="titulo">Nome do show*</label>
                <input type="text" id="titulo" name="titulo" maxlength="150" value="<?= htmlspecialchars($titulo) ?>" placeholder="Ex.: Show na Praça" required>
            </div>

            <div class="linha-campos">
                <div class="campo">
                    <label for="data">Data*</label>
                    <input type="date" id="data" name="data" value="<?= htmlspecialchars($data) ?>" min="<?= date("Y-m-d") ?>" required>
                </div>

                <div class="campo">
                    <label for="hora_inicio">Horário de início*</label>
                    <input type="time" id="hora_inicio" name="hora_inicio" value="<?= htmlspecialchars($hora_inicio) ?>" required>
                </div>

                <div class="campo">
                    <label for="hora_fim">Horário de término*</label>
                    <input type="time" id="hora_fim" name="hora_fim" value="<?= htmlspecialchars($hora_fim) ?>" required>
                </div>
            </div>

            <div class="linha-campos">
                <div class="campo">
                    <label for="local">Local*</label>
                    <input type="text" id="local" name="local" maxlength="150" value="<?= htmlspecialchars($local) ?>" placeholder="Ex.: Praça Central" required>
                </div>

                <div class="campo campo-endereco">
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" name="endereco" maxlength="255" value="<?= htmlspecialchars($endereco) ?>" placeholder="Rua, número e bairro">
                </div>
            </div>

            <div class="campo campo-completo">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" maxlength="1000" placeholder="Adicione alguma informação importante sobre o show."><?= htmlspecialchars($descricao) ?></textarea>
            </div>

            <div class="botoes">
                <button type="reset" class="botao cancelar">LIMPAR</button>
                <button type="submit" class="botao cadastrar">CADASTRAR SHOW</button>
            </div>
        </form>
    </section>

    <section class="secao-shows">
        <div class="titulo-secao">
            <h2>Shows cadastrados</h2>
            <p>Confira as apresentações que você já registrou.</p>
        </div>

        <?php if (empty($shows)): ?>
            <div class="sem-shows">
                <div class="icone-vazio">♪</div>
                <h3>Nenhum show cadastrado</h3>
                <p>Quando você cadastrar um show, ele aparecerá aqui.</p>
            </div>
        <?php else: ?>
            <div class="lista-shows">
                <?php foreach ($shows as $show): ?>
                    <?php $participantes = $participantesPorShow[$show->getIdShow()] ?? []; ?>

                    <article class="show-card">
                        <div class="show-data">
                            <strong><?= htmlspecialchars(formatarData($show->getData())) ?></strong>
                            <span><?= htmlspecialchars(formatarHora($show->getHoraInicio())) ?></span>
                        </div>

                        <div class="show-info">
                            <h3><?= htmlspecialchars($show->getTitulo()) ?></h3>
                            <p><strong>Local:</strong> <?= htmlspecialchars($show->getLocal()) ?></p>

                            <?php if (!empty($show->getEndereco())): ?>
                                <p><strong>Endereço:</strong> <?= htmlspecialchars($show->getEndereco()) ?></p>
                            <?php endif; ?>

                            <p><strong>Horário:</strong> <?= htmlspecialchars(formatarHora($show->getHoraInicio())) ?> às <?= htmlspecialchars(formatarHora($show->getHoraFim())) ?></p>

                            <?php if (!empty($show->getDescricao())): ?>
                                <p class="descricao-show"><?= nl2br(htmlspecialchars($show->getDescricao())) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="acoes-show">
                            <a href="../usuario/buscar_cantores.php?id_show=<?= (int)$show->getIdShow() ?>" class="botao-convidar-cantor">CONVIDAR CANTOR</a>
                        </div>

                        <?php if (!empty($participantes)): ?>
                            <div class="participantes-confirmados">
                                <div class="participantes-titulo">
                                    <strong>Participantes confirmados</strong>
                                    <span><?= count($participantes) ?></span>
                                </div>

                                <div class="lista-participantes-confirmados">
                                    <?php foreach ($participantes as $participante): ?>
                                        <div class="participante-confirmado">
                                            <span class="icone-participante">♪</span>
                                            <div>
                                                <strong><?= htmlspecialchars($participante["nome"]) ?></strong>
                                                <span><?= htmlspecialchars(textoTipoParticipante($participante["tipo"])) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<footer>© 2026 CONEXÃO MUSICAL</footer>
</body>
</html>
