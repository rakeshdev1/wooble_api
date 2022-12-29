<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
if (isset($_POST['email'])) {

    require_once "conn.php";
    require_once "validate.php";
    require 'vendor/autoload.php';
    $email = validate($_POST['email']);
    // $sql = " select id from user_info where email='$email' ";
    // $result = $conn->query($sql);
    date_default_timezone_set("Asia/Calcutta");
    $date = date('Y-m-d H:i:s a', time());

    $stmt = $conn->prepare("select id from user_info where email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id_id);

    $id = null;
    while ($stmt->fetch()) {
        $id = $id_id;
    }


    if ($stmt->num_rows == 1) {
        $otp = rand(1000, 9999);
        $sql = "INSERT INTO `otp_db`(`id`, `otp`, `is_expired`, `created_at`) VALUES ('$id','$otp',0,'$date')";
        $result = $conn->query($sql);
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com;';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@wooble.email';
            $mail->Password = 'Jh05ax-2586INFO';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('info@wooble.email', 'OTP');
            $mail->addAddress($email);
            //$mail->addAddress('receiver2@gfg.com', 'Name');

            $mail->isHTML(true);
            $mail->Subject = 'OTP';
            $mail->Body='<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
            <div style="margin:50px auto;width:70%;padding:20px 0">
              <div style="border-bottom:1px solid #eee">
                <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Wooble</a>
              </div>
              <p style="font-size:1.1em">Hi,</p>
              <p>Use the following OTP to complete your Password Reset procedures. OTP is valid for 10 minutes</p>
              <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'.$otp.'</h2>
              <p style="font-size:0.9em;">Regards,<br />Wooble</p>
              <hr style="border:none;border-top:1px solid #eee" />
              <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
                <p>Wooble Software pvt. Ltd</p>
                <p>Bhubaneswer</p>
                <p>Odisha</p>
              </div>
            </div>
          </div>';
            //$mail->Body = 'Your otp is ' . '<b>' . $otp . '</b>';
            $mail->AltBody = 'Body in plain text for non-HTML mail clients';
            //$mail->send();
            if ($mail->Send()) {
                echo "success";
            } else {
                echo "Message failed to send!";
            }

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "failure";
    }
}
