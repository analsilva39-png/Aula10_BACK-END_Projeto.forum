<?php
const ARQUIVO_USUARIOS = __DIR__ . DIRECTORY_SEPARATOR . 'usuarios.xml';
const ARQUIVO_TOPICOS = __DIR__ . DIRECTORY_SEPARATOR . 'topicos.xml';

function carregarXml(string $arquivo, string $raiz): SimpleXMLElement
{
    if (!is_file($arquivo)) {
        throw new RuntimeException('Arquivo de dados não encontrado: ' . basename($arquivo));
    }

    $conteudo = file_get_contents($arquivo);
    if ($conteudo === false) {
        throw new RuntimeException('Não foi possível ler o arquivo de dados.');
    }
    if (trim($conteudo) === '') {
        return new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $raiz . '/>');
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($conteudo, SimpleXMLElement::class, LIBXML_NONET);
    libxml_clear_errors();
    if ($xml === false || $xml->getName() !== $raiz) {
        throw new RuntimeException('O arquivo de dados está inválido.');
    }
    return $xml;
}

function salvarXml(SimpleXMLElement $xml, string $arquivo): void
{
    $conteudo = $xml->asXML();
    if ($conteudo === false || file_put_contents($arquivo, $conteudo, LOCK_EX) === false) {
        throw new RuntimeException('Não foi possível salvar os dados.');
    }
}

function adicionarTextoXml(SimpleXMLElement $pai, string $nome, string $valor): SimpleXMLElement
{
    $filho = $pai->addChild($nome);
    $filho[0] = $valor;
    return $filho;
}

function escapar($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function obterIndice($valor): ?int
{
    if (!is_string($valor) && !is_int($valor)) {
        return null;
    }
    $indice = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    return $indice === false ? null : $indice;
}

function tokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfValido($token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function exibirEstilos(): void
{
    echo '<style>
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 24px 16px; background: #eaf6ff; color: #20384d;
            font: 16px Arial, sans-serif;
        }
        main, form, header, article {
            max-width: 720px; margin: 20px auto; padding: 22px;
            background: #fff; border: 1px solid #c8e6fa; border-radius: 8px;
        }
        h1, h2, h3 { color: #176b9b; }
        a { color: #087db5; font-weight: bold; }
        label { display: block; margin: 12px 0 5px; }
        input, textarea {
            width: 100%; padding: 10px; border: 1px solid #a9d5ef;
            border-radius: 5px; font: inherit;
        }
        textarea { min-height: 100px; resize: vertical; }
        button {
            margin-top: 12px; padding: 10px 16px; color: #fff;
            background: #168ac2; border: 0; border-radius: 5px;
            font: inherit; cursor: pointer;
        }
        button:hover { background: #106b99; }
        .erro { color: #a32323; }
        .sucesso { color: #176b3a; }
        .comentario { margin: 10px 0; padding: 12px; background: #f2f9fd; border-radius: 5px; }
    </style>';
}

function encerrarComErro(string $mensagem, int $status = 400): void
{
    http_response_code($status);
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Erro</title></head><body>';
    exibirEstilos();
    echo '<main><p class="erro">' . escapar($mensagem) . '</p><a href="lista.php">Voltar ao fórum</a></main></body></html>';
    exit;
}
