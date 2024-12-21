<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Helper
{
  public function sendEmail($email_info)
  {
    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
      // Server settings
      $mail->isSMTP();
      $mail->Host = env('MAIL_HOST');
      $mail->SMTPAuth = true;
      $mail->Username = env('MAIL_USERNAME');
      $mail->Password = env('MAIL_PASSWORD');
      $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
      $mail->Port = env('MAIL_PORT', 587);

      // Sender details
      $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

      // Recipients
      foreach ($email_info['recipient_email'] as $recipient) {
        $mail->addAddress($recipient);
      }

      // CC Recipients
      if (!empty($email_info['cc'])) {
        foreach ($email_info['cc'] as $cc) {
          $mail->addCC($cc);
        }
      }

      // BCC Recipients
      if (!empty($email_info['bcc'])) {
        foreach ($email_info['bcc'] as $bcc) {
          $mail->addBCC($bcc);
        }
      }

      // Email content
      $mail->isHTML(true);
      $mail->Subject = $email_info['subject'];
      $mail->Body = $email_info['body'];

      // Send the email
      $mail->send();
      return 'Message has been sent';
    } catch (Exception $e) {
      return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}
