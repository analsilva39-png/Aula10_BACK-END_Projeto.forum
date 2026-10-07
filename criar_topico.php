<?php
session_start();
require_once __DIR__ . '/funcoes.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf_token'] ?? null)) {
        encerrarComErro('Formulário expirado. Atualize a página e tente novamente.', 403);
    }

    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $mensagem = trim((string) ($_POST['mensagem'] ?? ''));
    if ($titulo === '' || $mensagem === '') {
        $erro = 'Preencha o título e a mensagem.';
    } else {
        try {
            $topicos = carregarXml(ARQUIVO_TOPICOS, 'topicos');
            $novo = $topicos->addChild('topico');
            adicionarTextoXml($novo, 'autor', (string) $_SESSION['usuario']);
            adicionarTextoXml($novo, 'titulo', $titulo);
            adicionarTextoXml($novo, 'mensagem', $mensagem);
            $novo->addChild('comentarios');
            salvarXml($topicos, ARQUIVO_TOPICOS);
            header('Location: lista.php');
            exit;
        } catch (RuntimeException $excecao) {
            encerrarComErro($excecao->getMessage(), 500);
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar tópico</title>
</head>
<body>
<?php exibirEstilos(); ?>
<form method="post" action="criar_topico.php">
    <h1>Criar tópico</h1>
    <?php if ($erro !== ''): ?><p class="erro"><?= escapar($erro) ?></p><?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= escapar(tokenCsrf()) ?>">
    <label for="titulo">Título</label>
    <input type="text" id="titulo" name="titulo" required>
    <label for="mensagem">Mensagem</label>
    <textarea id="mensagem" name="mensagem" required></textarea>
    <button type="submit">Publicar tópico</button>
    <p><a href="lista.php">Voltar ao fórum</a></p>
</form>
</body>
</html>
