<?php

    require ('connect.php');
    
    $db = new db\connect();
    $conn = $db->open();

    //Json modelo
    // $json = '{
    // "codigo":"140",
    // "parceiro":"TESTE",
    // "data_criacao":"08/02/2021",
    // "status":"Aberto",
    // "data_fechamento":"10/10/2021",
    // "observacao_fornecedor":"",
    // "valor_total_itens":"0.00",
    // "desconto":"0.00",
    // "icms":"0.00",
    // "ipi":"0.00",
    // "pis":"0.00",
    // "cofins":"0.00",
    // "frete":"0.00",
    // "valor_total":"0.00",
    // "chave":"12345",
    // "criador":"LUCAS",
    // "produtos":{
    //     "1":{
    //         "codigo":"122",
    //         "produto":"Teste 1",
    //         "quantidade":"10",
    //         "unidade":"UN",
    //         "valor_unitario":"0.00",
    //         "valor_total":"0.00",
    //         "chave":"12345"
    //         },
    //     "2":{
    //         "codigo":"221",
    //         "produto":"Teste 2",
    //         "quantidade":"20",
    //         "unidade":"UN",
    //         "valor_unitario":"0.00",
    //         "valor_total":"0.00",
    //         "chave":"12345"
    //         }
    //     }
    // }';

    $json = '';

    if(isset($_POST['json'])){
        $json = json_decode($_POST['json']);
    }else{
        echo "Error invalid value.";
        exit;
    }

    //$json = json_decode($json);

    $header_columns = '';
    $header_values = '';
    $items_columns = '';
    $items_values = '';
    $sql_items = [];
    $json_key = '';

    $json_produtos = '';

    foreach ($json as $header_fields => $header_value) {

        if ($header_fields != 'qtd_produtos' && $header_fields != 'produtos'){
            
            if ($header_columns == ''){
                $header_columns = $header_fields;
                $header_values = "'" . $header_value . "'";
            }else{
                $header_columns .= ',' . $header_fields;
                $header_values .= ",'" . $header_value . "'";
            }

            if($header_fields == 'chave'){
                $json_key = $header_value;
            }

        }elseif($header_fields == 'produtos'){

            foreach ($header_value as $item_number => $products_array) {
                foreach ($products_array as $items_field => $items_value) {
                    if ($items_columns == ''){
                        $items_columns = $items_field;
                        $items_values = "'" . $items_value . "'";
                    }else{
                        $items_columns .= ',' . $items_field;
                        $items_values .= ",'" . $items_value . "'";
                    }
                }

                $sql = "INSERT INTO tbprodutos ($items_columns) VALUES ($items_values)";
                //echo $sql . '<br>';
                $items_columns = '';
                $items_values = '';

                if ($conn->query($sql) === FALSE) {
                    echo "Error updating record: " . $conn->error;

                    //Excluir produtos adicionados após o erro
                    $sql = "DELETE FROM tbprodutos WHERE chave = '$json_key'";
                    if ($conn->query($sql) === FALSE) {
                        echo "Error delete products: " . $conn->error;
                    }

                    exit;
                }

            }

        }
    }

    $sql = "INSERT INTO tbcotacao ($header_columns) VALUES ($header_values)";
    //echo $sql . '<br>';

    if ($conn->query($sql) === TRUE) {
        echo "Send with success.";
    } else {
        echo "Error updating record: " . $conn->error;

        //Excluir produtos adicionados após o erro
        $sql = "DELETE FROM tbprodutos WHERE chave = '$json_key'";
        
        if ($conn->query($sql) === FALSE) {
            echo "Error delete products: " . $conn->error;
        }

        exit;
    }

?>