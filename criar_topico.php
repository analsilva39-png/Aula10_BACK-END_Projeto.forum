<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo "Você precisa estar logado para criar um tópico. <a href='login.php'>Fazer login</a>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicos = simplexml_load_file("topicos.xml");
    $novo = $topicos->addChild("topico");
    $novo->addChild("autor", $_SESSION['usuario']);
    $novo->addChild("titulo", $_POST['titulo']);
    $novo->addChild("mensagem", $_POST['mensagem']);
    $novo->addChild("comentarios");
    $topicos->asXML("topicos.xml");

    echo "Tópico criado com sucesso! <a href='listar.php'>Ver Tópicos</a>";
} else {
?>
    <form method="post">
        <h2>Criar Tópico</h2>
        Título: <input type="text" name="titulo" placeholder="Título do tópico" required><br>
        Mensagem: <textarea name="mensagem" placeholder="Escreva sua mensagem..." required></textarea><br>
        <button type="submit">Criar</button>
    </form>
<?php } ?>

<style>
    body {
        background: #fff3e0;
        text-align: center;
    }

    form {
        background: white;
        width: 300px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 10px;
    }

    input,
    textarea {
        padding: 8px;
        margin: 5px;
        width: 90%;
    }

    button {
        background: #f57c00;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background: #e65100;
    }
</style>