<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 * This uses traditional id & password authentication - look at the gmail_xoauth.phps
 * example to see how to use XOAUTH2.
 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
 */
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
require '/vendor/autoload.php';

function sendMail($name, $email, $comments) {

    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';

    //Used to clean email input
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6
    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 587;

    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'tls';

    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;

    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = "websitecvmail@gmail.com";

    $p = file_get_contents("../etc/p.txt");

    //Password to use for SMTP authentication
    $mail->Password = $p;

    //Set who the message is to be sent from
    $mail->setFrom($email, $name);

    //Set an alternative reply-to address
    $mail->addReplyTo($email, $name);

    //Set who the message is to be sent to
    $mail->addAddress('codymaceachern@gmail.com', 'Cody MacEachern');

    //Set the subject line
    $mail->Subject = 'Mail from resume website';

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    //$mail->msgHTML(file_get_contents('mail.html'), __DIR__);

    $mail->Body = $comments;

    //Replace the plain text body with one created manually
    $mail->AltBody = $comments;

    //Attach an image file
    //$mail->addAttachment('images/phpmailer_mini.png');
    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent! I'll get back to you as soon as possible!";
        //Section 2: IMAP
        //Uncomment these to save your message in the 'Sent Mail' folder.
        #if (save_mail($mail)) {
        #    echo "Message saved!";
        #}
    }
    //Section 2: IMAP
    //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
    //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
    //You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
    //be useful if you are trying to get this working on a non-Gmail IMAP server.
    function save_mail($mail)
    {
        //You can change 'Sent Mail' to any other folder or tag
        $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
        //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
        $imapStream = imap_open($path, $mail->Username, $mail->Password);
        $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
        imap_close($imapStream);
        return $result;
    }
}

function getVariables() {
    if(isset($_POST['email'])) {

        function died($error) {
            // your error code can go here
            echo "We are very sorry, but there were error(s) found with the form you submitted. ";
            echo "These errors appear below.<br /><br />";
            echo $error."<br /><br />";
            echo "Please go back and fix these errors.<br /><br />";
            die();
        }
     
     
        // validation expected data exists
        if(!isset($_POST['name']) ||
            !isset($_POST['email']) ||
            !isset($_POST['comments'])) {
            died('We are sorry, but there appears to be a problem with the form you submitted.');       
        }

         
     
        $name = $_POST['name']; // required
        $email = $_POST['email']; // required
        $comments = $_POST['comments']; // required
     
        $error_message = "";
        $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/';
     
      if(!preg_match($email_exp,$email)) {
        $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
      }
     
        $string_exp = "/^[A-Za-z .'-]+$/";
     
      if(!preg_match($string_exp,$name)) {
        $error_message .= 'The name you entered does not appear to be valid.<br />';
      }
     
      if(strlen($comments) < 2) {
        $error_message .= 'The Comments you entered do not appear to be valid.<br />';
      }
     
      if(strlen($error_message) > 0) {
        died($error_message);
      }
     
        $email_message = "Form details below.\n\n";
     
         
        function clean_string($string) {
          $bad = array("content-type","bcc:","to:","cc:","href");
          return str_replace($bad,"",$string);
        }

        $postInfo = array($name, $email, $comments);
        $count = count($postInfo);

        for($i = 0; $i < $count; $i++) {
            $postInfo[$i] = clean_string($postInfo[$i]);
        }

        return $postInfo;
    }
    else {
        echo "Please provide an email address";
    }
}

$postInfo = getVariables();

sendMail($postInfo[0], $postInfo[1], $postInfo[2]);

?>