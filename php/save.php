<?php
    session_start();
    require ('connect.php');
    require_once('credential.php');
    require_once('../vendor/autoload.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

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

            //Enviar o email
            //Valida se a chave já existe, se sim, exclui todos e adiciona o novo JSON
            $sql = "SELECT email_resposta,codigo,parceiro FROM tbcotacao WHERE $where_header";
            $result = mysqli_query($conn,$sql) or die(mysqli_error($conn));
            $return = mysqli_fetch_assoc($result);
            
            if ($return['email_resposta'] == ''){
                exit;
            }
            
            //Create a new PHPMailer instance
            $mail = new PHPMailer();
            //Tell PHPMailer to use SMTP
            $mail->isSMTP();
            //Enable SMTP debugging
            //SMTP::DEBUG_OFF = off (for production use)
            //SMTP::DEBUG_CLIENT = client messages
            //SMTP::DEBUG_SERVER = client and server messages
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            //Set the hostname of the mail server
            $mail->Host = MAILHOST;
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = MAILNAME;
            $mail->Password = MAILPASS;
            //Set who the message is to be sent from
            $mail->setFrom(MAILNAME, 'Cotacao');
            //Set an alternative reply-to address
            //$mail->addReplyTo('replyto@example.com', 'First Last');
            //Set who the message is to be sent to
            $mail->addAddress($return['email_resposta'], $return['parceiro']);
            //Set the subject line
            $mail->Subject = 'COTACAO RESPONDIDA';
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            //$mail->Body = 'O fornecedor '.$return['parceiro'].' respondeu sua cotação.';
            $mail->msgHTML('O fornecedor '.$return['parceiro'].' respondeu sua cotação.');
            //Replace the plain text body with one created manually
            $mail->AltBody = 'O fornecedor '.$return['parceiro'].' respondeu sua cotação.';
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');

            //send the message, check for errors
            if (!$mail->send()) {
                //echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                //echo 'Message sent!';
            }
            
        } else {
            echo "Error updating record: " . $conn->error;
            exit;
        }

    }else{
        echo 'Invalid parameters.';
        exit;
    }

?>