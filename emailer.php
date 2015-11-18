<?php
function email_types(){
	return array('sendmail'=>'Default','smtp'=>'SMTP mail server');
}

function gmail_send($p,&$error){
	$mail=mailer_setup($error);
	if (empty($mail)){
		return false;
	}
	$mail->Host='smtp.gmail.com';
	$mail->Port=465;
	$mail->SMTPSecure='ssl';
	$mail->SMTPAuth=true;
	if (function_exists('send_user')){
		$mail=send_user($mail,'gmail');
	}
	if (!smtp_send_email($mail,$p,$error)){
		return false;
	}
	return true;
}

function log_email($email,$subject,$message,$headers){
	$file=LOG.'email-sent.log';
	$content='---- Email sent on '.date('r').' ----'."\r\n";
	$content.="\r\n".'Recipient: '.$email;
	$content.="\r\n".'Subject: '.$subject;
	$content.="\r\n".$message;
	$content.="\r\n".$headers."\r\n\r\n";
	$fh=fopen($file,'a+');
	if (!empty($fh)){
		fwrite($fh,$content);
		fclose($fh);
	}
	elseif (!empty($_SERVER['SHELL'])){
		echo 'Email log failed to '.$file.PHP_EOL;
	}
	return true;
}

function mail_headers(&$p=null,&$extra=null){
	if (function_exists('mail_headers_config')){
		$p=mail_headers_config($p);
	}
	$headers=[];
	$headers[]='From: '.$p['from'].' ('.$p['name'].')';
	$headers[]='Return-Path: <'.$p['from'].'>';
	$headers[]='Reply-to: "'.$p['name'].'" <'.$p['reply'].'> ';
	$headers[]='X-Mailer: php';
	$headers[]='MIME-Version: 1.0';
	if (!$extra['multi']){
		$headers[]='Content-type: text/html; charset=utf-8';
	}
	elseif (!$extra['boundary']){
		$extra['boundary']=md5(date('U'));
		$headers[]='Content-Type: multipart/alternative; boundary="'.$extra['boundary'].'"';
	}
	$headers=implode("\r\n",$headers);
	return $headers;
}

/**
 * @param $error
 *
 * @return bool|PHPMailer
 */
function mailer_setup(&$error){
	if (!class_exists('PHPMailer')){
	   $error='The mailing system cannot be loaded to send this email.';
	   return false;
	}
	$mail=new PHPMailer();
	$mail->IsSMTP();
	if (defined('SMTP_DEBUG')){
		$mail->SMTPDebug=1;
	}
	return $mail;
}

function send_email($p,&$error=null,$mail_type=null){
	if (empty($mail_type) and function_exists('send_email_type')){
		$mail_type=send_email_type();
	}
	if (function_exists('send_email_subject')){
		$p['subject']=send_email_subject($p);
	}
	$n=0;
	if (!isset($p['emails']) or !is_array_full(array_keys($p['emails']))){
		$error='You must send emails to the emailer as an array, even for single email addresses. If you don&#39;t know what this means, contact your website manager.';
		return false;
	}

	// this bit is only needed until we've updated all other sites to use new email assoc format
	$first=reset($p['emails']);
	if (make_email($first)){
		$temp=array();
		foreach ($p['emails'] as $name => $email){
			$temp[$email]=$name;
		}
		$p['emails']=$temp;
		unset($temp);
	}
	//

	if (defined('BETA') && function_exists('send_email_whitelist')){
		$p['emails'] = send_email_whitelist($p['emails']);
	}

	if (!defined('EMAIL_SEND')){
		if (!isset($p['headers'])){
			$headers=mail_headers();
		}
		foreach ($p['emails'] as $email => $name){
			log_email($name.' <'.$email.'>',$p['subject'],$p['message'],$headers);
			$n++;
		}
	}
	else {
		switch ($mail_type){
			case 'func':
				$func=send_email_func();
				if (!$func($p,$error)){
					return false;
				}
			break;
			case 'gmail':
				if (!gmail_send($p,$error)){
					return false;
				}
			break;
			case 'sendgrid':
				if (!sendgrid_send($p,$error)){
					return false;
				}
			break;
			case 'smtp':
				if (!smtp_send($p,$error)){
					return false;
				}
			break;
			case 'sendmail':
			default:
				if (!is_array_full($p['emails'])){
					$error='You must send emails to the emailer as an array, even for single email addresses. If you don&#39;t know what this means, contact your website manager.';
					return false;
				}
				if (!isset($p['headers'])){
					$p['headers']=mail_headers();
				}
				foreach ($p['emails'] as $email => $name){
					if (@mail($email,$p['subject'],$p['message'],$p['headers'])){
						$n++;
					}
					else {
						$errors[]=$email;
					}
				}
				if (!empty($errors)){
					$error='The email message could not be sent to the following addresses.</p><ul><li>'.implode('</li><li>',$errors).'</li></ul><p>';
					return false;
				}
			break;
		}
	}
	return true;
}

function sendgrid_send($p,&$error){
	$mail=mailer_setup($error);
	if (empty($mail)){
		return false;
	}
	$mail->Host='smtp.sendgrid.net';
	$mail->Port=587;
	$mail->SMTPAuth=true;
	if (function_exists('send_user')){
		$mail=send_user($mail,'sendgrid');
	}
	$mail->Encoding='base64';
	if (function_exists('sendgrid_send_api')){
		$mail = sendgrid_send_api($mail);
	}
	if (!smtp_send_email($mail,$p,$error)){
		return false;
	}
	return true;
}

function smtp_send($p,&$error){
	$mail=mailer_setup($error);
	if (empty($mail)){
		return false;
	}
	if (function_exists('smtp_send_details')){
		$mail=smtp_send_details($mail);
	}
	if (function_exists('send_user')){
		$mail=send_user($mail,'smtp');
	}
	if (!smtp_send_email($mail,$p,$error)){
		return false;
	}
	return true;
}

function smtp_send_email($mail,$p,&$error){
	if (function_exists('smtp_send_config')){
		$p=smtp_send_config($p);
	}
	$mail->FromName=$p['from-name'];
	$mail->AddReplyTo($p['replyto'],$p['replyto-name']);
	$mail->Subject=$p['subject'];
	$mail->Body=$p['message']; //HTML Body
	$mail->IsHTML(true); // send as HTML
	$mail->AltBody=strip_tags($p['message']); //Text Body
	if (!empty($p['cc'])){
		foreach ($p['cc'] as $email => $name){
			$mail->AddCC($email,$name);
		}
	}
	if (!empty($p['bcc'])){
		foreach ($p['bcc'] as $email => $name){
			$mail->AddBCC($email,$name);
		}
	}
	if (!empty($p['attach'])){
		foreach($p['attach'] as $attach){
			$mail->AddAttachment($attach['file'],$attach['name']);
		}
	}
	if (!empty($p['emails'])){
		foreach ($p['emails'] as $email => $name){
			$mail->AddAddress($email,$name);
			if (is_on('EMAIL_SERIALIZE')){
				$mail_serialize = serialize($mail);
				file_save(LOG.'emails/by-date/'.date('Y-m-d-H:i:s').'_'.$email, $mail_serialize);
			}
			if (!$mail->Send()){
				$errors[]=$mail->ErrorInfo.' occured for address '.$email;
			}
			$mail->ClearAddresses();
		}
	}
	elseif (!$mail->Send()){
		$errors[]='The error '.$mail->ErrorInfo.' occured for address '.$email;
	}
	if (count($errors)>0){
		$error='The following SMTP errors were reported:<ul><li>'.implode('</li><li>',$errors).'</li></ul>';
		return false;
	}
	return true;
}
