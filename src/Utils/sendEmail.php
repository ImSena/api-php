<?php

namespace App\Utils;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

define('HOST_EMAIL', 'smtp.escalaweb.com.br');
define('USERNAME_MAIL', 'teste@escalaweb.com.br');
define('PASSWORD_MAIL', 'Escalaweb$17');

class SendEmail
{
    public static function sendMail(array $info_user, string $content_subject)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = HOST_EMAIL;
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->SMTPSecure = false;
		$mail->SMTPAutoTLS = false;
        $mail->Username = USERNAME_MAIL;
        $mail->Password = PASSWORD_MAIL;
        $mail->CharSet = 'UTF-8';
        $mail->From     = "no-reply@escalaweb.com.br";
        $mail->FromName = "Escala Web";
        $mail->addAddress($info_user['email']);
        $mail->WordWrap = 50;
        $mail->isHTML(true);

        $subject = $content_subject == 'forget' ? "Recuperação de acesso - Escala Web" : "Ative sua conta - Escala Web";

        $mail->Subject = $subject;
        
        $mail->Body = self::getBody($info_user, $content_subject);

        return $mail->Send();
    } catch (Exception $e) {
        return false;
    }
}


    private static function getInformation($subject, $info_user)
    {

        if ($subject == 'forget') {
            $link = "http://localhost:5173/reset-access?token=".$info_user['token'];
            return [
                "title" => "Recuperar Acesso - Escala Web",
                "message" => "
                    <h2>Recuperar Acesso</h2>
                    <p>Olá, ".$info_user['name']."! </p>
                    <p>Clique no link abaixo para redefinir sua senha: </p>
                    <p><a href='$link' style='color:#007bff;'>Clique aqui para redefinir sua senha</a></p>"
            ];
        }

        if($subject == 'active'){
            $link = "http://localhost:5173/active-account?token=".$info_user['token'];

            return [
                "title" => "Ative sua Conta - Escala Web",
                "message" => "
                    <h2>Ative sua conta</h2>
                    <p>Olá, ".$info_user['name']."!</p>
                    <p><a href='$link' style='color: #007bff;'>Clique aqui para ativar sua conta </a></p>
                "
            ];
        }

        return $subject;
    }

    public static function getBody(array $informacoes, $subject = "forget")
{
    $informacoes = self::getInformation($subject, $informacoes);

    if (is_string($informacoes)) {
        throw new Exception("Erro na criação do corpo do e-mail. O retorno de getInformation não é válido.");
    }

    return (
        "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//PT' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml' style='-webkit-text-size-adjust:none;'>
            <head>
                <meta charset='utf-8'/>
                <meta name='HandheldFriendly' content='true'/>
                <meta name='viewport' content='width=device-width'/>
                <title>" . ucfirst($informacoes['title']) . "</title>
            </head>
            <body style='padding:25px 0 75px 0; margin:0 auto; width:100%; height:100%; font-family:Helvetica,Arial,sans-serif;'>
                <table border='0' cellspacing='0' cellpadding='0' style='border-collapse:collapse; margin:50px 0 0 0;' width='100%' height='100%'>
                    <tbody>
                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td style='padding: 15px'>
                                            <img src='https://escalaweb.com.br/images/logo-nova.png' alt='Logo' style='max-width:200px; margin-bottom:20px;' />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='background-color:#FFF; text-align:left; padding:15px; font-size:14px;'>
                                            ".$informacoes['message']." 
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </body>
        </html>"
    );
}


}