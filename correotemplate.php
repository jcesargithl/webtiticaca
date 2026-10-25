<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $mail = isset( $_POST["mail"] )? $_POST["mail"] : "";
    $msgx = isset( $_POST["msgx"] )? $_POST["msgx"] : "";

    // captcha
    //$capt = $_POST["captcha"];
    //if( $capt != "my token" ) exit;


    function Send4Mail( $arrMail="", $subject="", $content="", $sign="Orale" )
    {
        // as array allways
        //
        $arrMail = is_array($arrMail)? $arrMail : [ $arrMail ];

        $from = $arrMail[0];
        $cc = "";

        // send 1 or many mails in batch
        //
        foreach( $arrMail as $item ){

            // concat the header mime type
            //
            $header = "MIME-Version: 1.0\r\n"
                . "Content-type: text/html; charset=UTF-8\r\n"
                ////. "To: $item\r\n"
                . "From: $sign <$from>\r\n"      // Signature <dest>
                . ( (!$cc)? "" : "Cc: $cc\r\n")  // when CC exists
                . "X-Mailer: PHP/" . phpversion();


            $res = mail( $item, $subject, $content, $header, "-f ".$from );
        }

        if( ! $res )
            echo "LxMail: it wasn't sent";
    }

?>

<?php if( $_POST == null ): ?>
    <form  action="" method=post>
        <input name="mail" value="rplm.mx@gmail.com"> <br>
        <textarea name="msgx">Anybody's home?</textarea> <br>
        <button type="submit"> Mail it ! </button>
    </form>
<?php else: ?>


<?php

    Send4Mail( $mail, "Saludos cordiales", $msgx );
    echo "Enviado a: $mail";
    echo "<hr><a href=''> Reload </a>";
    //echo "<script> location.href='' </script>";
?>

<?php endif; ?>