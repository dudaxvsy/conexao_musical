<?php

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once __DIR__ . "/../../controllers/UsuarioController.php";

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $whatsapp = trim($_POST["whatsapp"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $tipo = $_POST["tipo"] ?? "";

    if (empty($nome) || empty($email) || empty($whatsapp) || empty($senha) || empty($tipo)) {

        $mensagem = "Preencha todos os campos.";
        $tipoMensagem = "erro";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = "Informe um e-mail válido.";
        $tipoMensagem = "erro";

    } elseif (strlen(preg_replace('/\D/', '', $whatsapp)) < 10) {

        $mensagem = "Informe um WhatsApp válido.";
        $tipoMensagem = "erro";

    } elseif (strlen($senha) < 6) {

        $mensagem = "A senha deve ter pelo menos 6 caracteres.";
        $tipoMensagem = "erro";

    } else {

        try {

            $controller = new UsuarioController();

            $usuarioExistente = $controller->buscarPorEmail($email);

            if ($usuarioExistente !== null) {

                $mensagem = "Este e-mail já está cadastrado.";
                $tipoMensagem = "erro";

            } else {

                $resultado = $controller->cadastrar(
                    $nome,
                    $email,
                    preg_replace("/\D/", "", $whatsapp),
                    $senha,
                    $tipo
                );

                if ($resultado) {

                    $mensagem = "Cadastro realizado com sucesso!";
                    $tipoMensagem = "sucesso";

                } else {

                    $mensagem = "Não foi possível realizar o cadastro.";
                    $tipoMensagem = "erro";
                }
            }

        } catch (Exception $e) {

            $mensagem = "Erro ao realizar o cadastro.";
            $tipoMensagem = "erro";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro - Conexão Musical</title>

    <link rel="stylesheet" href="../../public/css/cadastro.css">

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

        <div class="acesso-login">

            <span>Já possui uma conta?</span>

            <a href="login.php">
                Entrar
            </a>

        </div>

    </header>


    <main class="conteudo">

        <div class="cabecalho-pagina">

            <h1>Criar conta</h1>

            <p class="subtitulo">
                Preencha seus dados para fazer parte do Conexão Musical.
            </p>

        </div>


        <?php if (!empty($mensagem)): ?>

            <div class="mensagem <?= $tipoMensagem ?>">

                <?= htmlspecialchars($mensagem) ?>

            </div>

        <?php endif; ?>


        <form method="POST" class="formulario">

            <div class="campo">

                <label for="nome">Nome completo*</label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    placeholder="Digite seu nome completo"
                    value="<?= htmlspecialchars($_POST["nome"] ?? "") ?>"
                    required
                >

            </div>


            <div class="campo">

                <label for="email">E-mail*</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Digite seu e-mail"
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                    required
                >

            </div>


            <div class="campo">

                <label for="whatsapp">WhatsApp*</label>

                <input
                    type="tel"
                    id="whatsapp"
                    name="whatsapp"
                    placeholder="(31) 99999-9999"
                    value="<?= htmlspecialchars($_POST["whatsapp"] ?? "") ?>"
                    required
                >

            </div>


            <div class="linha-campos">

                <div class="campo">

                    <label for="senha">Senha*</label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="tipo">Tipo de usuário*</label>

                    <select
                        id="tipo"
                        name="tipo"
                        required
                    >

                        <option value="">
                            Selecione
                        </option>

                        <option value="cantor">
                            Cantor
                        </option>

                        <option value="musico">
                            Músico
                        </option>

                    </select>

                </div>

            </div>


            <div class="botoes">

                <button
                    type="reset"
                    class="botao cancelar"
                >
                    CANCELAR
                </button>

                <button
                    type="submit"
                    class="botao cadastrar"
                >
                    CADASTRAR
                </button>

            </div>

        </form>

    </main>


    <footer>

        © 2026 CONEXÃO MUSICAL

    </footer>

</body>

</html>