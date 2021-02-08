<?php

    require ('connect.php');
    
    $db = new db\connect();
    $conn = $db->open();

    $key = '';

    if(isset($_POST['key'])){
        $key = $_POST['key'];
    }else{
        echo "Error invalid value.";
        exit;
    }

    //Remover sql injection
    $key = mysqli_real_escape_string($conn,$key);

    //Puxar dados da capa
    $sql = "SELECT * FROM tbcotacao WHERE chave = '$key'";
    $result = mysqli_query($conn,$sql) or die(mysqli_error($conn));
    $header = mysqli_fetch_assoc($result);

    echo '{
        "codigo":"'.$header['codigo'].'",
        "parceiro":"'.$header['parceiro'].'",
        "data_criacao":"'.$header['data_criacao'].'",
        "status":"'.$header['status'].'",
        "data_fechamento":"'.$header['data_fechamento'].'",
        "observacao_empresa":"'.$header['observacao_empresa'].'",
        "valor_total_itens":"'.$header['valor_total_itens'].'",
        "desconto":"'.$header['desconto'].'",
        "icms":"'.$header['icms'].'",
        "ipi":"'.$header['ipi'].'",
        "pis":"'.$header['pis'].'",
        "cofins":"'.$header['cofins'].'",
        "frete":"'.$header['frete'].'",
        "valor_total":"'.$header['valor_total'].'",
        "chave":"'.$header['chave'].'",
        "criador":"'.$header['criador'].'",
        "produtos":{';

    //Puxar dados dos itens
    $sql = "SELECT * FROM tbprodutos WHERE chave = '$key'";
    $result = mysqli_query($conn,$sql) or die(mysqli_error($conn));
    
    $i = 0;
    while ($row = mysqli_fetch_array($result)) {
        
        if ($i > 0){
            echo ',';
        }

        echo '"'.$i.'":{
                        "codigo":"'.$row['codigo'].'",
                        "produto":"'.$row['produto'].' 1",
                        "quantidade":"'.$row['quantidade'].'",
                        "unidade":"'.$row['unidade'].'",
                        "valor_unitario":"'.$row['valor_unitario'].'",
                        "valor_total":"'.$row['valor_total'].'",
                        "chave":"'.$row['chave'].'"
                        }';
            
        $i += 1;
    }

    echo '}
}';
?>