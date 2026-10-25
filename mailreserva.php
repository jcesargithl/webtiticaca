<?php
    
    $captcha=$_POST['cf-turnstile-response'];

    if (!$captcha) {
      // What happens when the CAPTCHA was entered incorrectly
      echo '<h2>Revise el formulario que contiene el captcha.</h2>';
                      echo "<script> location.href='/' </script>";
            exit;
    }
 
    $secretKey = "0x4AAAAAAAEh5oKSfOhZBhQy7rXq-oZP5LA";
    $ip = $_SERVER['REMOTE_ADDR'];
 
    $url_path = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data = array('secret' => $secretKey, 'response' => $captcha, 'remoteip' => $ip);
     
     $options = array(
         'http' => array(
         'method' => 'POST',
         'content' => http_build_query($data))
     );
     
     $stream = stream_context_create($options);
     
     $result = file_get_contents(
             $url_path, false, $stream);
     
     $response =  $result;
    
    $responseKeys = json_decode($response,true);
    //print_r ($responseKeys);
       if(intval($responseKeys["success"]) !== 1) {
            echo '<h2>spam?</h2>';
       } else { 
 //Put your mail code here


                    
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

                $name = isset( $_POST["name"] )? $_POST["name"] : "";
                $email = isset( $_POST["email"] )? $_POST["email"] : "";
                $phone = isset( $_POST["phone"] )? $_POST["phone"] : "";
                $fecha = isset( $_POST["fecha"] )? $_POST["fecha"] : "";
                $people = isset( $_POST["people"] )? $_POST["people"] : "";
                $message = isset( $_POST["message"] )? $_POST["message"] : "";
                $tour = isset( $_POST["tour"] )? $_POST["tour"] : "";
                

                $correo_destino="gerencia@titicacaexperiencesperu.com";
                
      

                function Send4Mail( $arrMail="", $subject="", $content="", $sign="Reserva-Titicaca Experiences" )
                {
                    // as array allways
                    //
                    $arrMail = is_array($arrMail)? $arrMail : [ $arrMail ];

                //        $from = $arrMail[0];

                    $from ="noreply@titicacaexperiencesperu.com";
                    $cc = "titicacaexperiences@gmail.com";

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
                        echo "Alerta : El mensaje no pudo ser enviado";
                }


             
                
                $name = isset( $_POST["name"] )? $_POST["name"] : "";
                $email = isset( $_POST["email"] )? $_POST["email"] : "";
                $phone = isset( $_POST["phone"] )? $_POST["phone"] : "";
                $fecha = isset( $_POST["fecha"] )? $_POST["fecha"] : "";
                $people = isset( $_POST["people"] )? $_POST["people"] : "";
                $message = isset( $_POST["message"] )? $_POST["message"] : "";
                $tour = isset( $_POST["tour"] )? $_POST["tour"] : "";




                $cadena = "<h1>Formulario Reserva</h1>
                <br>
                Tour : ".$tour."<br>
                Nombre : " .$name."<br>
                Correo : " .$email."<br>
                Teléfono : ".$phone."<br>
                Fecha : ".$fecha."<br>
                Número de personas : ".$people."<br>
                Mensaje : ".$message;



                Send4Mail( $correo_destino, "Formulario Reserva", $cadena );
                echo "Enviado exitosamente R";
              //  echo "<script> location.href='/tour-amantani.html' </script>";
                //echo "<hr><a href=''> Reload </a>";
                //echo "<script> location.href='' </script>";

                //fin 
 }   
    
    

?>