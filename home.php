<?php
// Iniciar sessão para poder usar carrinho
session_start();

// Conectar ao banco de dados
$conectar = mysqli_connect('localhost', 'root', '', 'loja');

// Verificar conexão
if (mysqli_connect_errno()) {
    echo "Falha ao conectar ao MySQL: " . mysqli_connect_error();
    exit();
}

// Adicionar produto ao carrinho se o formulário foi submetido
if (isset($_POST['comprar']) && isset($_POST['produto_id'])) {
    $produto_id = (int)$_POST['produto_id'];
    
    // Se o carrinho não existir, crie-o
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = array();
    }
    
    // Adiciona ou incrementa o produto no carrinho
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id]++;
    } else {
        $_SESSION['carrinho'][$produto_id] = 1;
    }
    
    // Redirecionar para evitar reenvio do formulário ao atualizar a página
    header("Location: home.php?adicao=sucesso");
    exit();
}

// Incluir o cabeçalho e início do HTML
include("home.html");

// Mostrar mensagem se um produto foi adicionado com sucesso
if (isset($_GET['adicao']) && $_GET['adicao'] == 'sucesso') {
    echo '<div style="background-color: #d4edda; color: #155724; padding: 10px; margin: 10px; border-radius: 5px; text-align: center; position: fixed; top: 80px; right: 10px; z-index: 1000;">
            Produto adicionado ao carrinho com sucesso!
          </div>';
}

// Montar a consulta SQL para buscar produtos
$sql = "SELECT p.*, m.nome as marca_nome, c.nome as categoria_nome, t.nome as tipo_nome 
        FROM produto p
        LEFT JOIN marca m ON p.codmarca = m.codigo
        LEFT JOIN categoria c ON p.codcategoria = c.codigo
        LEFT JOIN tipo t ON p.codtipo = t.codigo
        WHERE 1=1";

// Verificar se algum filtro foi aplicado
$filtroAplicado = false;

// Filtrar por marca
if (isset($_POST['pesquisarmarcas']) && isset($_POST['marca'])) {
    $filtroAplicado = true;
    $marcas = $_POST['marca'];
    $marcaIds = array();
    
    foreach ($marcas as $marca) {
        switch ($marca) {
            case 'nike': $marcaIds[] = 1; break;
            case 'adidas': $marcaIds[] = 2; break;
            case 'puma': $marcaIds[] = 3; break;
            case 'penalti': $marcaIds[] = 4; break;
            case 'everlast': $marcaIds[] = 5; break;
            case 'fila': $marcaIds[] = 5; break;
        }
    }
    
    if (!empty($marcaIds)) {
        $marcaIdsStr = implode(',', $marcaIds);
        $sql .= " AND p.codmarca IN ($marcaIdsStr)";
    }
}

// Filtrar por categoria
if (isset($_POST['pesquisarcategorias']) && isset($_POST['categoria'])) {
    $filtroAplicado = true;
    $categorias = $_POST['categoria'];
    $categoriaIds = array();
    
    foreach ($categorias as $categoria) {
        switch ($categoria) {
            case 'masculino': $categoriaIds[] = 1; break;
            case 'feminino': $categoriaIds[] = 2; break;
            case 'infantil': $categoriaIds[] = 3; break;
            case 'unissex': $categoriaIds[] = 4; break;
        }
    }
    
    if (!empty($categoriaIds)) {
        $categoriaIdsStr = implode(',', $categoriaIds);
        $sql .= " AND p.codcategoria IN ($categoriaIdsStr)";
    }
}

// Filtrar por tipo
if (isset($_POST['pesquisartipos']) && isset($_POST['tipo'])) {
    $filtroAplicado = true;
    $tipos = $_POST['tipo'];
    $tipoIds = array();
    
    foreach ($tipos as $tipo) {
        switch ($tipo) {
            case 'camisa': $tipoIds[] = 1; break;
            case 'bermuda': $tipoIds[] = 2; break;
            case 'calca': $tipoIds[] = 3; break;
            case 'calcado': $tipoIds[] = 4; break;
            case 'acessorio': $tipoIds[] = 5; break;
        }
    }
    
    if (!empty($tipoIds)) {
        $tipoIdsStr = implode(',', $tipoIds);
        $sql .= " AND p.codtipo IN ($tipoIdsStr)";
    }
}

// Executar a consulta SQL
$resultado = mysqli_query($conectar, $sql);

// Manipular o elemento vitrine com JavaScript
echo "<script>
    // Limpar o conteúdo atual da vitrine
    document.getElementById('vitrine').innerHTML = '';
    
    // Adicionar os produtos dinamicamente
    var vitrine = document.getElementById('vitrine');
</script>";

// Exibir produtos
if (mysqli_num_rows($resultado) == 0) {
    echo "<script>
        vitrine.innerHTML = '<p>Nenhum produto encontrado com os filtros aplicados.</p>';
    </script>";
} else {
    while ($produto = mysqli_fetch_object($resultado)) {
        // Escapar valores para uso seguro em JavaScript
        $codigo = addslashes($produto->codigo);
        $descricao = addslashes($produto->descricao);
        $preco = number_format($produto->preco, 2, ',', '.');
        $foto = addslashes($produto->foto1);
        
        // Criar o elemento de produto via JavaScript
        echo "<script>
            var produtoDiv = document.createElement('div');
            produtoDiv.style = 'width: 30%; border: 1px solid #ccc; border-radius: 10px; padding: 10px; box-shadow: 2px 2px 8px #aaa; text-align: center;';
            
            produtoDiv.innerHTML = `
                <img src='imagens/$foto' width='180' height='180' style='object-fit: cover;'>
                <h3>$descricao</h3>
                <p>R$ $preco</p>
                <form method='POST' action='home.php'>
                    <input type='hidden' name='comprar' value='1'>
                    <input type='hidden' name='produto_id' value='$codigo'>
                    <button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>COMPRAR</button>
                </form>
            `;
            
            vitrine.appendChild(produtoDiv);
        </script>";
    }
}

// Se não tiver nenhum produto no banco de dados e nenhum filtro foi aplicado,
// podemos mostrar os produtos estáticos de exemplo que estavam no HTML original
if (mysqli_num_rows($resultado) == 0 && !$filtroAplicado) {
    echo "<script>
        vitrine.innerHTML = `
            <!-- Produto 1 -->
            <div style='width: 30%; border: 1px solid #ccc; border-radius: 10px; padding: 10px; box-shadow: 2px 2px 8px #aaa; text-align: center;'>
                <img src='imagenssite/camisacorinthiansfeminina1.jpg' width='180' height='180' style='object-fit: cover;'>
                <h3>Camisa Corinthians</h3>
                <p>R$ 180,00</p>
                <form method='POST' action='home.php'>
                    <input type='hidden' name='comprar' value='1'>
                    <input type='hidden' name='produto_id' value='1'>
                    <button type='submit'>COMPRAR</button>
                </form>
            </div>

            <!-- Produto 2 -->
            <div style='width: 30%; border: 1px solid #ccc; border-radius: 10px; padding: 10px; box-shadow: 2px 2px 8px #aaa; text-align: center;'>
                <img src='imagenssite/calca2.jpg' width='180' height='180' style='object-fit: cover;'>
                <h3>Calça Adidas Azul</h3>
                <p>R$ 90,00</p>
                <form method='POST' action='home.php'>
                    <input type='hidden' name='comprar' value='1'>
                    <input type='hidden' name='produto_id' value='2'>
                    <button type='submit'>COMPRAR</button>
                </form>
            </div>

            <!-- Produto 3 -->
            <div style='width: 30%; border: 1px solid #ccc; border-radius: 10px; padding: 10px; box-shadow: 2px 2px 8px #aaa; text-align: center;'>
                <img src='imagenssite/luvaboxe.jpg' width='180' height='180' style='object-fit: cover;'>
                <h3>Luva de Boxe 12oz Everlast</h3>
                <p>R$ 260,00</p>
                <form method='POST' action='home.php'>
                    <input type='hidden' name='comprar' value='1'>
                    <input type='hidden' name='produto_id' value='3'>
                    <button type='submit'>COMPRAR</button>
                </form>
            </div>

            <!-- Produto 4 -->
            <div style='width: 30%; border: 1px solid #ccc; border-radius: 10px; padding: 10px; box-shadow: 2px 2px 8px #aaa; text-align: center;'>
                <img src='imagenssite/tenis3.jpg' width='180' height='180' style='object-fit: cover;'>
                <h3>Têmis Adidas</h3>
                <p>R$ 190,00</p>
                <form method='POST' action='home.php'>
                    <input type='hidden' name='comprar' value='1'>
                    <input type='hidden' name='produto_id' value='4'>
                    <button type='submit'>COMPRAR</button>
                </form>
            </div>
            <!-- Produto 5 -->
            <div style='width: 30%; border: 1px solid #ccc; border-radius: 10px; padding: 10px; box-shadow: 2px 2px 8px #aaa; text-align: center;'>
                <img src='imagenssite/bermuda1.jpg' width='180' height='180' style='object-fit: cover;'>
                <h3>Bermuda Cinza Pênalti</h3>
                <p>R$ 45,00</p>
                <form method='POST' action='home.php'>
                    <input type='hidden' name='comprar' value='1'>
                    <input type='hidden' name='produto_id' value='5'>
                    <button type='submit'>COMPRAR</button>
                </form>
            </div>
            <!-- Produto 6 -->
            <div style='width: 30%; border: 1px solid #ccc; border-radius: 10px; padding: 10px; box-shadow: 2px 2px 8px #aaa; text-align: center;'>
                <img src='imagenssite/bermuda3.jpg' width='180' height='180' style='object-fit: cover;'>
                <h3>Bermuda preta Fila</h3>
                <p>R$ 60,00</p>
                <form method='POST' action='home.php'>
                    <input type='hidden' name='comprar' value='1'>
                    <input type='hidden' name='produto_id' value='6'>
                    <button type='submit'>COMPRAR</button>
                </form>
            </div>
        `;
    </script>";
}
?>