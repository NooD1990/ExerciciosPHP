<?php
//conectar com banco de dados
$conectar = mysql_connect('localhost', 'root', '');
$banco    = mysql_select_db('loja');

if(isset($_POST['gravar']))
{
//receber as variaveis do HTML
    $codigo         = $_POST['codigo'];
    $nome           = $_POST['nome'];

    $sql = "insert into marca (codigo, nome) values ('$codigo','$nome')";

    $resultado = mysql_query($sql);

    if ($resultado == TRUE)
    {
        echo "Dados gravados com sucesso.";
    }
    else
    {
        echo "Erro ao gravar dados.";
    }
}

if (isset($_POST['alterar']))
{
//receber as variaveis do HTML
    $codigo         = $_POST['codigo'];
    $nome           = $_POST['nome'];

    $sql = "UPDATE marca SET codigo='$codigo', nome='$nome' WHERE codigo = '$codigo'";

    $resultado = mysql_query($sql);

    if ($resultado == TRUE)
    {
        echo "Dados alterados com sucesso.";
    }
    else
    {
        echo "Erro ao alterar os dados.";
    }
}

if (isset($_POST['excluir']))
{
//receber as variaveis do HTML
    $codigo         = $_POST['codigo'];
    $nome            = $_POST['nome'];

    $sql = "DELETE FROM marca WHERE codigo = '$codigo'";
    $resultado = mysql_query($sql);

    if ($resultado == TRUE)
    {
        echo "Dados excluídos com sucesso com sucesso.";
    }
    else
    {
        echo "Erro ao excluir os dados.";
    }
}

if (isset($_POST['pesquisar']))
{
   $sql = mysql_query("SELECT codigo,nome FROM marca");
   
   if (mysql_num_rows($sql) == 0)
         {echo "Desculpe, mas sua pesquisa não retornou resultados.";}
   else
        {
        echo "<b>Produtos Cadastrados:</b><br><br>";
        while ($dados = mysql_fetch_object($sql))
 	        {
                echo "Codigo         : ".$dados->codigo." ";
                echo "Nome           : ".$dados->nome."<br>";
            }
        }
}
?>