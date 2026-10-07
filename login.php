<?php
session_start();
require_once __DIR__ . '/funcoes.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($email === '' || $senha === '') {
        $erro = 'Informe seu e-mail e sua senha.';
    } else {
        try {
            $usuarios = carregarXml(ARQUIVO_USUARIOS, 'usuarios');
            foreach ($usuarios->usuario as $usuario) {
                $hash = (string) $usuario->senha;
                if (strtolower((string) $usuario->email) !== $email) {
                    continue;
                }

                $senhaValida = password_verify($senha, $hash);
                $senhaLegada = !$senhaValida && hash_equals($hash, md5($senha));
                if ($senhaValida || $senhaLegada) {
                    if ($senhaLegada) {
                        $usuario->senha = password_hash($senha, PASSWORD_DEFAULT);
                        salvarXml($usuarios, ARQUIVO_USUARIOS);
                    }
                    session_regenerate_id(true);
                    $_SESSION['usuario'] = (string) $usuario->email;
                    header('Location: lista.php');
                    exit;
                }
                break;
            }
            $erro = 'E-mail ou senha incorretos.';
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
    <title>Entrar</title>
</head>
<body>
<?php exibirEstilos(); ?>
<form method="post" action="login.php">
    <h1>Entrar no fórum</h1>
    <?php if (isset($_GET['cadastro'])): ?>
        <p class="sucesso">Conta criada. Agora você já pode entrar.</p>
    <?php endif; ?>
    <?php if ($erro !== ''): ?>
        <p class="erro"><?= escapar($erro) ?></p>
    <?php endif; ?>
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required>
    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" required>
    <button type="submit">Entrar</button>
    <p>Ainda não tem conta? <a href="cadastro.php">Cadastrar</a></p>
    <p><a href="lista.php">Voltar ao fórum</a></p>
</form>
</body>
</html>
