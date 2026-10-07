<?php
session_start();
require_once __DIR__ . '/funcoes.php';

try {
    $topicos = carregarXml(ARQUIVO_TOPICOS, 'topicos');
} catch (RuntimeException $excecao) {
    encerrarComErro($excecao->getMessage(), 500);
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fórum</title>
</head>
<body>
<?php exibirEstilos(); ?>
<main>
    <header>
        <h1>Fórum</h1>
        <nav>
            <?php if (isset($_SESSION['usuario'])): ?>
                <span>Conectado como <?= escapar($_SESSION['usuario']) ?></span>
                | <a href="criar_topico.php">Criar tópico</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                | <a href="cadastro.php">Cadastrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php if (count($topicos->topico) === 0): ?>
        <p>Nenhum tópico foi criado ainda.</p>
    <?php endif; ?>

    <?php $id = 0; ?>
    <?php foreach ($topicos->topico as $topico): ?>
        <article>
            <h2><?= escapar($topico->titulo) ?></h2>
            <p><?= nl2br(escapar($topico->mensagem)) ?></p>
            <p><small>Autor: <?= escapar($topico->autor) ?></small></p>

            <h3>Comentários</h3>
            <?php if (count($topico->comentarios->comentario) === 0): ?>
                <p>Este tópico ainda não possui comentários.</p>
            <?php endif; ?>

            <?php $comentarioId = 0; ?>
            <?php foreach ($topico->comentarios->comentario as $comentario): ?>
                <div class="comentario">
                    <p><strong><?= escapar($comentario->nome) ?>:</strong></p>
                    <p><?= nl2br(escapar($comentario->mensagem)) ?></p>
                    <?php if (isset($_SESSION['usuario']) && (string) $_SESSION['usuario'] === (string) $topico->autor): ?>
                        <form method="post" action="excluir.php">
                            <input type="hidden" name="csrf_token" value="<?= escapar(tokenCsrf()) ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="comentario" value="<?= $comentarioId ?>">
                            <button type="submit">Excluir comentário</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php $comentarioId++; ?>
            <?php endforeach; ?>

            <form method="post" action="comentar.php">
                <input type="hidden" name="csrf_token" value="<?= escapar(tokenCsrf()) ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <label for="nome-<?= $id ?>">Seu nome</label>
                <input type="text" id="nome-<?= $id ?>" name="nome" value="<?= isset($_SESSION['usuario']) ? escapar($_SESSION['usuario']) : '' ?>" required>
                <label for="mensagem-<?= $id ?>">Comentário</label>
                <textarea id="mensagem-<?= $id ?>" name="mensagem" required></textarea>
                <button type="submit">Comentar</button>
            </form>
        </article>
        <?php $id++; ?>
    <?php endforeach; ?>
</main>
</body>
</html>
