<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../usuario/login.php");
    exit;
}

require_once __DIR__ . "/../../controllers/PerfilController.php";
require_once __DIR__ . "/../../controllers/UsuarioController.php";

$idUsuario = (int) $_SESSION["id_usuario"];
$tipoUsuario = $_SESSION["tipo_usuario"];

$perfilController = new PerfilController();
$usuarioController = new UsuarioController();

$perfil = $perfilController->buscarPorUsuario($idUsuario);
$usuario = $usuarioController->buscarPorEmail($_SESSION["email_usuario"] ?? "");

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cidade = trim($_POST["cidade"] ?? "");
    $whatsappNovo = trim($_POST["whatsapp"] ?? "");
    $instagram = trim($_POST["instagram"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $instrumentos = trim($_POST["instrumentos"] ?? "");
    $fotoAtual = $perfil->getFoto();
    $foto = $fotoAtual;

    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
            $mensagem = "Não foi possível enviar a foto.";
            $tipoMensagem = "erro";
        } elseif ($_FILES["foto"]["size"] > 3 * 1024 * 1024) {
            $mensagem = "A foto deve ter no máximo 3 MB.";
            $tipoMensagem = "erro";
        } else {
            $extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];
            $extensao = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));

            if (!in_array($extensao, $extensoesPermitidas, true)) {
                $mensagem = "Use uma imagem JPG, PNG ou WEBP.";
                $tipoMensagem = "erro";
            } else {
                $nomeArquivo = "perfil_" . $idUsuario . "_" . time() . "." . $extensao;
                $pasta = __DIR__ . "/../../public/uploads/perfis/";
                $destino = $pasta . $nomeArquivo;

                if (!is_dir($pasta)) {
                    mkdir($pasta, 0755, true);
                }

                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $destino)) {
                    $foto = $nomeArquivo;
                } else {
                    $mensagem = "Não foi possível salvar a foto.";
                    $tipoMensagem = "erro";
                }
            }
        }
    }

    if (empty($mensagem)) {
        try {
            $contatoAtualizado = $usuarioController->atualizarContato($idUsuario, $whatsappNovo);

            $resultado = $perfilController->salvar(
                $idUsuario,
                $cidade,
                $instagram,
                $descricao,
                $foto,
                $tipoUsuario === "musico" ? $instrumentos : ""
            );

            if ($resultado && $contatoAtualizado) {
                $mensagem = "Perfil atualizado com sucesso!";
                $tipoMensagem = "sucesso";
                $perfil = $perfilController->buscarPorUsuario($idUsuario);
            } else {
                $mensagem = "Não foi possível atualizar o perfil.";
                $tipoMensagem = "erro";
            }
        } catch (Exception $e) {
            $mensagem = "Erro ao atualizar o perfil: " . $e->getMessage();
            $tipoMensagem = "erro";
        }
    }
}

$usuario = $usuarioController->buscarPorEmail($_SESSION["email_usuario"] ?? "");
$nome = $usuario ? $usuario->getNome() : ($_SESSION["nome_usuario"] ?? "");
$email = $usuario ? $usuario->getEmail() : ($_SESSION["email_usuario"] ?? "");
$whatsapp = $usuario ? $usuario->getWhatsapp() : "";
$foto = $perfil->getFoto();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil - Conexão Musical</title>
    <link rel="stylesheet" href="../../public/css/perfil.css">
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
        <h1>Meu perfil</h1>
        <p>Atualize suas informações para facilitar o contato com outros profissionais.</p>
    </div>

    <?php if (!empty($mensagem)): ?>
        <div class="mensagem <?= $tipoMensagem ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="perfil-form">
        <section class="card perfil-topo">
            <div class="foto-area">
                <?php if (!empty($foto)): ?>
                    <img src="../../public/uploads/perfis/<?= htmlspecialchars($foto) ?>" alt="Foto de perfil" class="foto-perfil">
                <?php else: ?>
                    <div class="foto-placeholder">♫</div>
                <?php endif; ?>

                <label for="foto" class="botao-foto">ALTERAR FOTO</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
                <span class="ajuda">JPG, PNG ou WEBP · até 3 MB</span>
            </div>

            <div class="dados-principais">
                <h2><?= htmlspecialchars($nome) ?></h2>
                <span class="tipo"><?= $tipoUsuario === "musico" ? "Músico" : "Cantor" ?></span>
                <p><?= htmlspecialchars($email) ?></p>
                <p><?= htmlspecialchars($whatsapp) ?></p>
            </div>
        </section>

        <section class="card">
            <h2>Informações do perfil</h2>

            <div class="linha-campos">
                <div class="campo">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" maxlength="100" placeholder="Ex.: Conselheiro Lafaiete" value="<?= htmlspecialchars($perfil->getCidade()) ?>">
                </div>

                <div class="campo">
                    <label for="whatsapp">WhatsApp</label>
                    <input type="tel" id="whatsapp" name="whatsapp" maxlength="20" placeholder="Ex.: (31) 99999-9999" value="<?= htmlspecialchars($whatsapp) ?>">
                    <span class="ajuda-campo">Será usado para contato nas solicitações.</span>
                </div>
            </div>

            <div class="campo">
                <label for="instagram">Instagram</label>
                <input type="text" id="instagram" name="instagram" maxlength="150" placeholder="@seuinstagram" value="<?= htmlspecialchars($perfil->getInstagram()) ?>">
                <span class="ajuda-campo">Opcional, para divulgar seu perfil.</span>
            </div>

            <?php if ($tipoUsuario === "musico"): ?>
                <div class="campo">
                    <label for="instrumentos">Instrumento(s)</label>
                    <input type="text" id="instrumentos" name="instrumentos" maxlength="255" placeholder="Ex.: Violão, guitarra e teclado" value="<?= htmlspecialchars($perfil->getInstrumentos()) ?>">
                </div>
            <?php endif; ?>

            <div class="campo">
                <label for="descricao">Sobre você</label>
                <textarea id="descricao" name="descricao" maxlength="1000" placeholder="Conte um pouco sobre você e seu trabalho na música."><?= htmlspecialchars($perfil->getDescricao()) ?></textarea>
            </div>
        </section>



        <div class="botoes">
            <a href="../usuario/dados_conta.php" class="botao conta">DADOS DA CONTA</a>

            <a href="../../public/index.php" class="botao cancelar">CANCELAR</a>
            <button type="submit" class="botao salvar">SALVAR ALTERAÇÕES</button>
        </div>
    </form>
</main>

<footer>© 2026 CONEXÃO MUSICAL</footer>

<script>
const inputFoto = document.getElementById("foto");
if (inputFoto) {
    inputFoto.addEventListener("change", function () {
        const arquivo = this.files && this.files[0];

        if (!arquivo) return;

        if (!arquivo.type.startsWith("image/")) {
            alert("Selecione uma imagem JPG, PNG ou WEBP.");
            this.value = "";
            return;
        }

        if (arquivo.size > 3 * 1024 * 1024) {
            alert("A foto deve ter no máximo 3 MB.");
            this.value = "";
            return;
        }

        const leitor = new FileReader();

        leitor.onload = function (evento) {
            const area = document.querySelector(".foto-area");
            const imagemAtual = area.querySelector(".foto-perfil");
            const placeholder = area.querySelector(".foto-placeholder");

            if (imagemAtual) {
                imagemAtual.src = evento.target.result;
            } else if (placeholder) {
                const imagem = document.createElement("img");
                imagem.src = evento.target.result;
                imagem.alt = "Pré-visualização da foto de perfil";
                imagem.className = "foto-perfil";
                placeholder.replaceWith(imagem);
            }
        };

        leitor.readAsDataURL(arquivo);
    });
}
</script>
</body>
</html>
