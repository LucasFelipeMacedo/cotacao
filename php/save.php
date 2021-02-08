<?php
    session_start();
    require ('connect.php');

    function right($valor,$numero_caracteres){
        return substr($valor,strlen($valor)-$numero_caracteres,strlen($valor));
    }

    function left($valor,$numero_caracteres){
        return substr($valor,0,$numero_caracteres);
    }

    function cnum($value,$decimal = false){
		if ($decimal == true){
			$value = number_format($value,2);
		}
		return str_replace(',', '.', str_replace('.','',$value));
    }
    
    //Verifica se o token esta correto
    if(isset($_POST['_token'])){
        //Diferente então para a execução do código e devolve erro
        if ($_POST['_token'] != $_SESSION['_token']){
            echo 'Error invalid token.';
            exit;
        }else{
            //Apaga se estiver correto e da sequência
            unset($_POST['_token']);
        }
    }else{
        exit;
    }

    $key = '';

    if (isset($_POST['key'])){
        $key = $_POST['key'];

        $columns_header = '';
        $where_header = "chave = '$key'";
        $columns_items = '';
        $columns_items_array = [];
        $where_items = [];

        $db = new db\connect();
        $conn = $db->open();

        foreach ($_POST as $key => $value) {

            //SQL Items
            if(left($key,7) == 'txtqtd_' || left($key,12) == 'txtvlr_unit_' || left($key,13) == 'txtvlr_total_'){
                
                $field = right($key,strlen($key) - 3);

                //Clear field
                if (left($field,3) == 'qtd'){
                    $field = 'quantidade'; 
                }elseif(left($field,8) == 'vlr_unit'){
                    $field = 'valor_unitario';
                }elseif(left($field,9) == 'vlr_total'){
                    $field = 'valor_total';
                }

                if ($columns_items == '') {
                    $columns_items = "$field = '".cnum($value)."'";
                }else{
                    $columns_items .= ",$field = '".cnum($value)."'";
                }

                if (left($key,13) == 'txtvlr_total_'){
                    array_push($columns_items_array,$columns_items);
                    array_push($where_items,'id = '.right($key,strlen($key)-13));
                    $columns_items = '';
                }

            //SQL header
            }elseif($key != 'key' && $key != 'status'){
                if ($columns_header == '') {
                    $columns_header = right($key,strlen($key) - 3)." = '".cnum($value)."'";
                }else{
                    $columns_header .= ",".right($key,strlen($key) - 3)." = '".cnum($value)."'";
                }
            }
        }

        //Contruct sql items
        foreach ($columns_items_array as $key => $value) {
            $sql = "UPDATE tbprodutos SET $value WHERE ".$where_items[$key];
            //echo $sql;
            if ($conn->query($sql) === TRUE) {
                //echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
                exit;
            }
        }
        
        $sql = "UPDATE tbcotacao SET $columns_header,status='Finalizado' WHERE $where_header";
        
        if ($conn->query($sql) === TRUE) {
            echo "Send with success.";
        } else {
            echo "Error updating record: " . $conn->error;
            exit;
        }

    }else{
        echo 'Invalid parameters.';
        exit;
    }

?>