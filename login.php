<?php
// Evitar qualquer espaço antes do PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar ao banco de dados
$conectar = mysql_connect('localhost', 'root', '');
if (!$conectar) {
    die('Erro ao conectar ao MySQL: ' . mysql_error());
}
echo "Conexão MySQL OK<br>";

$banco = mysql_select_db('loja');
if (!$banco) {
    die('Erro ao selecionar banco de dados: ' . mysql_error());
}
echo "Banco de dados selecionado<br>";

// Verificar se o formulário foi enviado
if (isset($_POST['entrar'])) {
    echo "Formulário recebido<br>";

    // Receber dados
    $login = $_POST['login'];
    $senha = $_POST['senha'];
    echo "Login recebido: $login<br>";
    echo "Senha recebida: $senha<br>";

    // Montar consulta
    $sql = "SELECT login, senha FROM usuario WHERE login = '$login' AND senha = '$senha'";
    echo "SQL: $sql<br>";

    $resultado = mysql_query($sql);
    if (!$resultado) {
        die('Erro na consulta SQL: ' . mysql_error());
    }

    $linhas = mysql_num_rows($resultado);
    echo "Número de linhas retornadas: $linhas<br>";

    if ($linhas <= 0) {
        echo "<script language='javascript' type='text/javascript'>
            alert('Login e/ou senha incorretos');
            window.location.href='login.html';
        </script>";
    } else {
        setcookie('login', $login);
        echo "Login válido. Redirecionando para menu.html...<br>";
        header('Location: menu.html');
        exit();
    }
} else {
    echo "O formulário não foi enviado corretamente (botão 'entrar' não definido).<br>";
}
?>