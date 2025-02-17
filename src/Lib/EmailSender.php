<?php
namespace Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{
    private $mail;
    public $email;
    public $nombre;
    public $token;

    public function __construct($email = null, $nombre = null, $token = null)
    {
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
        $this->mail->CharSet = 'UTF-8';

        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    private function setupSMTP()
    {
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['SMTP_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['SMTP_USERNAME'];
        $this->mail->Password = $_ENV['SMTP_PASSWORD'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $_ENV['SMTP_PORT'];
        $this->mail->CharSet = 'UTF-8';
    }

    public function sendEmail($recipientEmail, $recipientName, $subject, $body)
    {
        try {
            $this->mail->setFrom('tienda@online.com', 'Ortiz Shop');
            $this->mail->addAddress($recipientEmail, $recipientName);

            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            echo "Error enviando el correo: {$this->mail->ErrorInfo}";
            return false;
        }
    }

    public function sendConfirmation(string $recipientEmail, string $recipientName, string $token)
    {
        try {
            // Configuramos el remitente y el destinatario
            $this->mail->setFrom('tienda@online.com', 'Ortiz Shop');
            $this->mail->addAddress($recipientEmail, $recipientName);
            $this->mail->Subject = 'Confirma tu Cuenta';

            // Configuramos el contenido HTML
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';

            $contenido = '<html>';
            $contenido .= "<p>Hola " . $recipientName . ", Has creado tu cuenta en OrtizShop.com. Solo debes confirmarla presionando el siguiente enlace:</p>";
            $contenido .= "<p>Presiona aqui: <a href='" . BASE_URL . "confirmAccount/" . $token . "'>Confirmar Cuenta</a>";
            $contenido .= "<p>Si tú no solicitaste este cambio, puedes ignorar este mensaje.</p>";
            $contenido .= '</html>';

            $this->mail->Body = $contenido;

            // Enviamos el correo
            return $this->mail->send();
        } catch (Exception $e) {
            echo "Error enviando el correo de confirmación: {$this->mail->ErrorInfo}";
            return false;
        }
    }

    public function sendPasswordRecovery($email, $name, $token)
    {
        try {
            $subject = "Recuperación de contraseña";
            $resetLink = BASE_URL . "reset-password/" . $token;
            $message = "Hola $name,\n\nHas solicitado restablecer tu contraseña. Por favor, haz clic en el siguiente enlace para crear una nueva contraseña:\n\n$resetLink\n\nSi no has solicitado este cambio, puedes ignorar este correo.\n\nSaludos,\nTu equipo de soporte";

            // Configuramos el remitente y el destinatario
            $this->mail->setFrom('tienda@online.com', 'Ortiz Shop');
            $this->mail->addAddress($email, $name);
            $this->mail->Subject = 'Recupera tu Cuenta';

            // Configuramos el contenido HTML
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';

            $this->mail->Body = $message;

            // Enviamos el correo
            return $this->mail->send();
        } catch (Exception $e) {
            echo "Error enviando el correo de confirmación: {$this->mail->ErrorInfo}";
            return false;
        }
    }
}
