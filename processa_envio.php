<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';


// Load Composer's autoloader


	class Mensagem {
		private $para = null;
		private $assunto = null;
		private $mensagem = null;

		public function __get($atributo) {
			return $this->$atributo;
		}

		public function __set($atributo, $valor) {
			$this->$atributo = $valor;
		}

		public function mensagemValida() {
			if(empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
				return false;
			}

			return true;
		}
	}

	$mensagem = new Mensagem();

	$mensagem->__set('para', $_POST['para']);
	$mensagem->__set('assunto', $_POST['assunto']);
	$mensagem->__set('mensagem', $_POST['mensagem']);

	if(!$mensagem->mensagemValida()) {
		$_SESSION['msg'] = '<div class="alert alert-danger" role="alert">
  			Mensagem não válida!
		</div>';
		header('Location: index.php');
	}

	// Instantiation and passing `true` enables exceptions
	$mail = new PHPMailer(true);

	try {
		//Server settings
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
		$mail->isSMTP();                                            // Send using SMTP
		$mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		$mail->Username   = 'teste@teste.com';                     // SMTP username
		$mail->Password   = '********';                               // SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
		$mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

		//Recipients
		$mail->setFrom($mensagem->__get("para"), 'Gruguieie');
		$mail->addAddress($mensagem->__get("para"), 'Depende de voce');     // Add a recipient
	

		// Attachments
		//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

		// Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $mensagem->__get('assunto');
		$mail->Body    = $mensagem->__get('mensagem');
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		$mail->send();
		$_SESSION['msg'] = "<div class='alert alert-success' role='alert'>
			Messagem enviada!
		</div>";
		header('Location: index.php');
	} catch (Exception $e) {
		$_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>
			A mensagem não foi enviada, detalhes do erro: {$mail->ErrorInfo}
		</div>";
		header('Location: index.php');
	}

