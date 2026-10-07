<?php
session_start();
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    encerrarComErro('Método não permitido.', 405);
}
if (!csrfValido($_POST['csrf_token'] ?? null)) {
    encerrarComErro('Formulário expirado. Atualize a página e tente novamente.', 403);
}

$id = obterIndice($_POST['id'] ?? null);
$nome = trim((string) ($_POST['nome'] ?? ''));
$mensagem = trim((string) ($_POST['mensagem'] ?? ''));
if ($id === null || $nome === '' || $mensagem === '') {
    encerrarComErro('Informe seu nome e um comentário válido.');
}

try {
    $topicos = carregarXml(ARQUIVO_TOPICOS, 'topicos');
    if (!isset($topicos->topico[$id])) {
        encerrarComErro('Tópico não encontrado.', 404);
    }

    $topico = $topicos->topico[$id];
    $comentarios = isset($topico->comentarios)
        ? $topico->comentarios
        : $topico->addChild('comentarios');
    $comentario = $comentarios->addChild('comentario');
    adicionarTextoXml($comentario, 'nome', $nome);
    adicionarTextoXml($comentario, 'mensagem', $mensagem);
    salvarXml($topicos, ARQUIVO_TOPICOS);
    header('Location: lista.php');
    exit;
} catch (RuntimeException $excecao) {
    encerrarComErro($excecao->getMessage(), 500);
}
