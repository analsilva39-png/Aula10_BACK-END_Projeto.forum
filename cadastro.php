<?php 
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $usuarios = simplexml_load_file("usuarios.xml"); 
    $novo = $usuarios->addChild("usuario"); 
    $novo->addChild("nome", $_POST['nome']); 
    $novo->addChild("celular", $_POST['celular']); 
    $novo->addChild("email", $_POST['email']); 
    $novo->addChild("senha", md5($_POST['senha'])); 
 
    $usuarios->asXML("usuarios.xml"); 
 
    echo "Usuário cadastrado com sucesso! <a href='login.php'> Fazer login</a>"; 
 
} else { 
?> 

<style>
body {
    background-color: #fff3e0;
    font-family: Arial;
    text-align: center;
}

form {
    background-color: white;
    width: 300px;
    margin: 60px auto;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 10px #ccc;
}

input {
    width: 90%;
    padding: 9px;
    margin: 5px;
    border: 1px solid #ff9800;
    border-radius: 5px;
}

button {
    background-color: #f57c00;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #e65100;
}
</style>
 
<form method="post"> 

    <h2 style="color: #e65100;">Cadastro</h2>
 
    Nome: 
    <input type="text" name="nome" required><br><br> 
 
    Celular: 
    <input type="text" name="celular" required><br><br> 
 
    Email: 
    <input type="email" name="email" required><br><br> 
 
    Senha: 
    <input type="password" name="senha" required><br><br> 
 
    <button type="submit">Cadastrar</button> 
 
</form> 
 
<?php } ?>