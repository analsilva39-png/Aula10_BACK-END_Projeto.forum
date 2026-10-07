<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarios = simplexml_load_file("usuarios.xml");

    foreach ($usuarios->usuario as $u) {
        if ($u->email == $_POST['email'] && $u->senha == md5($_POST['senha'])) {
            $_SESSION['usuario'] = (string)$u->email;
            echo "Login realizado com sucesso! <a href='criar_topico.php'>Criar Tópico</a>";
            exit;
        }
    } echo "Login inválido!";
 } else {
?>

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

        input {
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

    <form method="post">

        <h2>Login</h2>

        Email:
        <input type="email" name="email" required><br>

        Senha:
        <input type="password" name="senha" required><br>

        <button type="submit">Entrar</button>

    </form>

<?php } ?>