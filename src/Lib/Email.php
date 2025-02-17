<?php
namespace Lib;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Email
{

    public $email;
    public $nombre;
    public $token;

    public function _construct($email, $nombre, $token)
    {

        $this->email - $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

}