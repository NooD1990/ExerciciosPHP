<?php
$conectar = mysql_connect('localhost', 'root', '');
$banco = mysql_select_db("loja");

$sql = "SELECT * FROM produto";
$resultado = mysql_query($sql);

if (!$resultado || mysql_num_rows($resultado) == 0) {
    echo "Nenhum produto encontrado ou erro na consulta.";
} else {
    while ($dados = mysql_fetch_object($resultado)) {
        echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
        echo "<img src='imagens/$dados->foto1' width='150'><br>";
        echo "Descrição: $dados->descricao<br>";
        echo "Preço: R$ " . number_format($dados->preco, 2, ',', '.') . "<br>";
        echo "</div>";
    }
}
?>
