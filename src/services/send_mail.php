<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

function sendConfirmationEmail($toEmail, $toName,$phone, $profilePath) {
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP server settings
        $mail->isSMTP();                      
        $mail->Host       = $_ENV['EMAIL_HOST'];  
        $mail->SMTPAuth   = true;               
        $mail->Username   = $_ENV['EMAIL_USERNAME'];
        $mail->Password   = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;

        // Email details
        $mail->setFrom($_SERVER['EMAIL_USERNAME'], 'SmartSites');
        $mail->addAddress($toEmail, $toName);
        
        // Attach the profile picture if it exists
        if ($profilePath) {
            $absolutePath = __DIR__ . '/../' . $profilePath;
            if (file_exists($absolutePath)) {
                $mail->addAttachment($absolutePath); 
            } elseif (file_exists($profilePath)) {
                $mail->addAttachment($profilePath);
            }
        }
        
        $mail->isHTML(true);
        $mail->Subject = 'Form Submitted Successfully';
        $mail->Body    = "<strong>Hello {$toName},</strong><br><br> Your form has been submitted successfully.<br><br>Your Contact Info: {$phone}";
        $mail->AltBody = "Hello {$toName},\n\n Your form has been submitted successfully.\n\nYour Contact Info: {$phone}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return $mail->ErrorInfo;
    }
}
?>