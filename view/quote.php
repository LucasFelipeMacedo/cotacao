<?php

session_start();
require ('php/connect.php');

$db = new db\connect();
$conn = $db->open();

//Carregar variaveis
$key = '';

$_SESSION['_token'] = hash("sha512",rand(100,1000));

if (isset($_GET["key"])){
    $key = $_GET["key"];
}else{
    echo "Invalid quote.";
    exit;
}

//Remover sql injection
$key = mysqli_real_escape_string($conn,$key);

//Puxar dados da capa
$sql = "SELECT * FROM tbcotacao WHERE chave = '$key'";
$result = mysqli_query($conn,$sql) or die(mysqli_error($conn));
$column = mysqli_fetch_assoc($result);
$status = htmlspecialchars($column['status']);
$codigo = htmlspecialchars($column['codigo']);
$parceiro = htmlspecialchars($column['parceiro']);
$data_criacao = htmlspecialchars($column['data_criacao']);
$criador = htmlspecialchars($column['criador']);
$status = htmlspecialchars($column['status']);
$data_fechamento = htmlspecialchars($column['data_fechamento']);
$observacao_fornecedor = htmlspecialchars($column['observacao_fornecedor']);
$valor_total_itens = htmlspecialchars($column['valor_total_itens']);
$desconto = htmlspecialchars($column['desconto']);
$icms = htmlspecialchars($column['icms']);
$ipi = htmlspecialchars($column['ipi']);
$pis = htmlspecialchars($column['pis']);
$cofins = htmlspecialchars($column['cofins']);
$frete = htmlspecialchars($column['frete']);
$valor_total = htmlspecialchars($column['valor_total']);
$observacao_empresa = htmlspecialchars($column['observacao_empresa']);

?>

<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap Style -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/loader.css">
<!-- Jquery -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
<!-- Popper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
</head>

<body>
<!--Alerta de operação bem sucedida-->
<?php
    if ($status == 'Finalizado'){
        echo '<div class="alert alert-success" role="alert" id="alerta_sucesso">'
                .'<h4 class="alert-heading">Proposta enviada com sucesso!</h4>'
                .'<p>Agora basta aguardar! A empresa entrará em contato com você caso a sua proposta seja aprovada!</p>'
            .'</div>';
        exit;
    }elseif($status == 'Aberto'){
        echo '<div class="alert alert-success" style="display:none" role="alert" id="alerta_sucesso">'
                .'<h4 class="alert-heading">Proposta enviada com sucesso!</h4>'
                .'<p>Agora basta aguardar! A empresa entrará em contato com você caso a sua proposta seja aprovada!</p>'
            .'</div>';
    }elseif($status == ''){
        echo 'Not found.';
        exit;
    }
?>
<!--Formulário-->
<form id="form" style="padding: 2%;">
    <input type="hidden" class="form-control" value = "<?php echo $_SESSION['_token']; ?>" id="_token">
    <h1 class="text-center">COTAÇÃO DE COMPRA</h1>
    <div class="form-row">
        <div class="form-group col-sm-1">
            <label for="txtcodigo" class="visually-hidden">Código</label>
            <input type="text" class="form-control" id="txtcodigo" value="<?= $codigo ?>" readonly>
        </div>
        <div class="form-group col-sm-5">
            <label for="txtparceiro" class="visually-hidden">Parceiro</label>
            <input type="text" class="form-control" id="txtparceiro" value="<?= $parceiro ?>" readonly>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-sm-2">
            <label for="txtdata_criacao" class="visually-hidden">Data Criação</label>
            <!--<input type="text" readonly class="form-control-plaintext" id="txtdata_criacao" value="email@example.com">-->
            <input type="text" class="form-control" id="txtdata_criacao" value="<?= $data_criacao ?>" readonly>
        </div>
        <div class="form-group col-sm-2">
            <label for="txtcriador" class="visually-hidden">Solicitante</label>
            <input type="text" class="form-control" id="txtcriador" value="<?= $criador ?>" readonly>
        </div>
        <div class="form-group col-sm-2">
            <label for="txtstatus" class="visually-hidden">Situação</label>
            <input type="text" class="form-control" id="txtstatus" value="<?= $status ?>" readonly>
        </div>
        <div class="form-group col-sm-2">
            <label for="txtdata_fechamento" class="visually-hidden">Data de Fechamento</label>
            <input type="text" class="form-control" id="txtdata_fechamento" value="<?= $data_fechamento ?>" readonly>
        </div>
        <div class="form-group col-sm-2">
            <label for="txtprazo_entrega" class="visually-hidden">Prazo de Entrega</label>
            <input type="text" class="form-control" id="txtprazo_entrega" value="">
        </div>
        <div class="form-group col-sm-2">
            <label for="txtnumero_orcamento" class="visually-hidden">Número do Orçamento</label>
            <input type="text" class="form-control" id="txtnumero_orcamento" value="">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-sm-12">
            <label for="txtobservacao_fornecedor" class="visually-hidden">Observação para o Fornecedor</label>
            <textarea class="form-control" id="txtobservacao_fornecedor" rows="3" readonly><?= $observacao_fornecedor ?></textarea>
        </div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Cód</th>
                <th scope="col">Produto</th>
                <th scope="col">Qtd</th>
                <th scope="col">Unid</th>
                <th scope="col">Vlr Unitário</th>
                <th scope="col">Vlr Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $sql = "SELECT * FROM tbprodutos WHERE chave = '$key'";
                $result = mysqli_query($conn,$sql);
                
                while($row = mysqli_fetch_assoc($result)){  
                    //Carregar linhas da tabela
                    echo '<tr>'
                    .'<th scope="row">'.$row['id'].'</th>'
                    .'<td>'.$row['codigo'].'</td>'
                    .'<td>'.$row['produto'].'</td>'
                    .'<td><input type="text" class="form-control calc table-qtd" id="txtqtd_'.$row['id'].'" value="'.$row['quantidade'].'" readonly></td>'
                    .'<td>'.$row['unidade'].'</td>'
                    .'<td><input type="text" class="form-control calc table-vlr-unit" id="txtvlr_unit_'.$row['id'].'" value="0,00"></td>'
                    .'<td><input type="text" class="form-control calc table-vlr-total" id="txtvlr_total_'.$row['id'].'" value="0,00" readonly></td>'
                    .'</tr>';
                }
            ?>
        </tbody>
    </table>
    <div class="form-row">
        <div class="form-group col-sm-2">
            <label for="txtvalor_total_itens" class="visually-hidden">Valor total dos Itens</label>
            <input type="text" class="form-control calc" id="txtvalor_total_itens" value="0,00" readonly>
        </div>
        <div class="form-group col-sm-2">
            <label for="txtdesconto" class="visually-hidden">Desconto</label>
            <input type="text" class="form-control calc" id="txtdesconto" value="0,00">
        </div>
        <div class="form-group col-sm-2">
            <label for="txticms" class="visually-hidden">ICMS</label>
            <input type="text" class="form-control calc" id="txticms" value="0,00">
        </div>
        <div class="form-group col-sm-2">
            <label for="txtipi" class="visually-hidden">IPI</label>
            <input type="text" class="form-control calc" id="txtipi" value="0,00">
        </div>
        <div class="form-group col-sm-2">
            <label for="txtpis" class="visually-hidden">PIS</label>
            <input type="text" class="form-control calc" id="txtpis" value="0,00">
        </div>
        <div class="form-group col-sm-2">
            <label for="txtcofins" class="visually-hidden">Cofins</label>
            <input type="text" class="form-control calc" id="txtcofins" value="0,00">
        </div>
        <div class="form-group col-sm-2">
            <label for="txtfrete" class="visually-hidden">Frete</label>
            <input type="text" class="form-control calc" id="txtfrete" value="0,00">
        </div>
        <div class="form-group col-sm-2">
            <label for="txtvalor_total" class="visually-hidden">Valor Total</label>
            <input type="text" class="form-control calc" id="txtvalor_total" value="0,00" readonly>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-sm-12">
            <label for="txtobservacao_empresa" class="visually-hidden">Observações para a empresa</label>
            <textarea class="form-control" id="txtobservacao_empresa" rows="3" placeholder="Ex: Nome do vendedor, telefone celular, informações sobre os produtos..."></textarea>
        </div>
    </div>
    <!--Alerta de operação mal sucedida-->
    <div class="alert alert-danger" style="display:none" role="alert" id="alerta_erro">
        Não foi possivel finalizar o envio da cotação, por favor verifique os dados informados ou faça mais tarde
    </div>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button class="btn btn-primary btn-lg" type="button" id="btnenviar">Enviar</button>
        <div class="loader" style="display:none"></div>
    </div>
    
    <hr>
    <p>Desenvolvido por <mark>Magnus Sistemas</mark>.</p>
</form>

</body>
<script src="js/util.js"></script>
<script src="js/cotacao.js"></script>
<script src="js/callcotacao.js"></script>
</html>