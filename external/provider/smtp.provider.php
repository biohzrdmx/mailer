<?php

	/**
	 * SmtpMailerProvider class
	 * @uses PHPMailer - Install the PHPMailer library into your project's lib/PHPMailer folder
	 */
	class SmtpMailerProvider extends MailerProvider {

		/**
		 * Process the send request, you must override this in your derived classes
		 * @param  object $message  An instance of MailerMessage
		 * @param  array  $options  Additional or provider-specific options
		 * @return boolean          True if the message was sent, False otherwise
		 */
		public function send($message, $options = null) {
			global $site;
			$ret = false;
			try {
				include_once $site->baseDir('/lib/PHPMailer/PHPMailerAutoload.php');
				# Format body

				$mail = new PHPMailer;

				$mail->isSMTP();                                 // Set mailer to use SMTP
				$mail->Host = get_item($options, 'host');  		 // Specify main and backup server
				$mail->Port = get_item($options, 'port');  		 // Specify main and backup server
				$mail->SMTPAuth = true;                          // Enable SMTP authentication
				// $mail->SMTPDebug = true;                      // Enable SMTP debug
				$mail->Username = get_item($options, 'user');    // SMTP username
				$mail->Password = get_item($options, 'pass');    // SMTP password
				$mail->SMTPSecure = 'tls';                       // Enable encryption, 'ssl' also accepted

				if ( is_array($message->from) ) {
					foreach ($message->from as $email => $name) {
						$mail->From = $email;
						$mail->FromName = $name;
					}
				} else {
					$mail->From = $message->from;
				}

				if ( is_array($message->to) ) {
					foreach ($message->to as $email => $name) {
						$mail->addAddress($email, $name);
					}
				} else {
					$mail->addAddress($message->to);
				}

				$contents = $message->contents;
				# Load template
				$template = $message->template;
				if ($template) {
					$html = file_get_contents( $template );
					$html = str_replace('%email-site%', $site->urlTo('/'), $html);
					$html = str_replace('%email-body%', $contents, $html);
					foreach ($message->replacements as $shortcode => $value) {
						$html = str_replace($shortcode, $value, $html);
					}
				} else {
					$html = $contents;
				}

				$mail->isHTML(true);
				$mail->Subject = $message->subject;
				$mail->Body    = $html;
				$mail->AltBody = strip_tags($html);

				foreach ($message->attachments as $name => $path) {
					$mail->addAttachment($path, $name);
				}

				foreach ($message->images as $name => $path) {
					$mail->addEmbeddedImage($path, $name, $name);
				}

				$ret = $mail->send();

			} catch (Exception $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

	}

?>