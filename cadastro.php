<?php
session_start();
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim((string) ($_POST['nome'] ?? ''));
    $celular = trim((string) ($_POST['celular'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($nome === '' || $celular === '' || $email === '' || $senha === '') {
        encerrarComErro('Preencha todos os campos.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        encerrarComErro('Informe um e-mail válido.');
    }

    try {
        $usuarios = carregarXml(ARQUIVO_USUARIOS, 'usuarios');
        foreach ($usuarios->usuario as $usuario) {
            if (strtolower((string) $usuario->email) === $email) {
                encerrarComErro('Este e-mail já está cadastrado.');
            }
        }

        $novo = $usuarios->addChild('usuario');
        adicionarTextoXml($novo, 'nome', $nome);
        adicionarTextoXml($novo, 'celular', $celular);
        adicionarTextoXml($novo, 'email', $email);
        adicionarTextoXml($novo, 'senha', password_hash($senha, PASSWORD_DEFAULT));
        salvarXml($usuarios, ARQUIVO_USUARIOS);
    } catch (RuntimeException $excecao) {
        encerrarComErro($excecao->getMessage(), 500);
    }

    header('Location: login.php?cadastro=sucesso');
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro</title>
</head>
<body>
<?php exibirEstilos(); ?>
<form method="post" action="cadastro.php">
    <h1>Criar conta</h1>
    <label for="nome">Nome</label>
    <input type="text" id="nome" name="nome" required>
    <label for="celular">Celular</label>
    <input type="tel" id="celular" name="celular" required>
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required>
    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" required>
    <button type="submit">Cadastrar</button>
    <p>Já tem uma conta? <a href="login.php">Entrar</a></p>
</form>
</body>
</html>
