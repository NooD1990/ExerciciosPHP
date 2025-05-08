<?php
// Iniciar sessão para acessar o carrinho
session_start();

// Conectar ao banco de dados
$conectar = mysqli_connect('localhost', 'root', '', 'loja');

// Verificar conexão
if (mysqli_connect_errno()) {
    echo "Falha ao conectar ao MySQL: " . mysqli_connect_error();
    exit();
}

// Ação para remover item do carrinho
if (isset($_POST['remover']) && isset($_POST['produto_id'])) {
    $produto_id = (int)$_POST['produto_id'];
    
    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }
    
    // Redirecionar para evitar reenvio do formulário
    header("Location: carrinho.php");
    exit();
}

// Ação para atualizar a quantidade
if (isset($_POST['atualizar'])) {
    foreach ($_POST['quantidade'] as $produto_id => $quantidade) {
        $produto_id = (int)$produto_id;
        $quantidade = (int)$quantidade;
        
        if ($quantidade <= 0) {
            unset($_SESSION['carrinho'][$produto_id]);
        } else {
            $_SESSION['carrinho'][$produto_id] = $quantidade;
        }
    }
    
    // Redirecionar para evitar reenvio do formulário
    header("Location: carrinho.php");
    exit();
}

// Incluir o arquivo HTML do carrinho
include("carrinho.html");

// Gerar conteúdo dinâmico para o carrinho
echo "<script>
    var conteudoCarrinho = document.getElementById('conteudo-carrinho');
</script>";

// Verificar se o carrinho existe e não está vazio
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    echo "<script>
        conteudoCarrinho.innerHTML = `
            <div class='carrinho-vazio'>
                <p>Seu carrinho está vazio!</p>
                <a href='home.php' class='btn btn-continuar'>Continuar Comprando</a>
            </div>
        `;
    </script>";
} else {
    // Iniciar a tabela HTML
    echo "<script>
        conteudoCarrinho.innerHTML = `
            <form method='POST' action='carrinho.php'>
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Descrição</th>
                            <th>Preço Unitário</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    </script>";
    
    $total = 0;
    
    // Para cada produto no carrinho
    foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
        // Buscar informações do produto no banco de dados
        $query = "SELECT * FROM produto WHERE codigo = $produto_id";
        $resultado = mysqli_query($conectar, $query);
        
        if ($produto = mysqli_fetch_object($resultado)) {
            $subtotal = $produto->preco * $quantidade;
            $total += $subtotal;
            
            // Escapar valores para uso seguro em JavaScript
            $descricao = addslashes($produto->descricao);
            $preco_formatado = number_format($produto->preco, 2, ',', '.');
            $subtotal_formatado = number_format($subtotal, 2, ',', '.');
            $foto = addslashes($produto->foto1);
            
            echo "<script>
                conteudoCarrinho.querySelector('tbody').innerHTML += `
                    <tr>
                        <td><img src='imagens/$foto' class='produto-imagem'></td>
                        <td>$descricao</td>
                        <td>R$ $preco_formatado</td>
                        <td>
                            <input type='number' name='quantidade[$produto_id]' value='$quantidade' min='1' class='quantidade-input'>
                        </td>
                        <td>R$ $subtotal_formatado</td>
                        <td>
                            <form method='POST' action='carrinho.php' style='display: inline;'>
                                <input type='hidden' name='produto_id' value='$produto_id'>
                                <button type='submit' name='remover' class='btn btn-remover'>Remover</button>
                            </form>
                        </td>
                    </tr>
                `;
            </script>";
        }
    }
    
    // Formatar o total
    $total_formatado = number_format($total, 2, ',', '.');
    
    // Finalizar a tabela e adicionar os botões
    echo "<script>
        conteudoCarrinho.querySelector('form').innerHTML += `
                    </tbody>
                </table>
                
                <div class='total'>
                    <strong>Total: R$ $total_formatado</strong>
                </div>
                
                <div style='text-align: right;'>
                    <button type='submit' name='atualizar' class='btn btn-atualizar'>Atualizar Carrinho</button>
                    <a href='home.php' class='btn btn-continuar' style='display: inline-block; margin-left: 10px;'>Continuar Comprando</a>
                    <a href='finalizar_compra.php' class='btn' style='margin-left: 10px;'>Finalizar Compra</a>
                </div>
            </form>
        `;
    </script>";
}
?>