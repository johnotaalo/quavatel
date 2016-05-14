<?php

class Mail extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('Mailer');
	}
	
	function send_mail($data)
	{
		$end_data = array();
		$mail = new PHPMailer();
		if(gethostname() == "homestead"){
		        $mail->IsSMTP();
		        $mail->IsHTML(); 
		        $mail->SMTPAuth   = true; 
		        $mail->SMTPSecure = "tls"; 
		        $mail->Host       = "server.penumos.com";      
		        $mail->Port       = 587;                  
		        $mail->Username   = "app@quavatel.co.ke";  
		        $mail->Password   = "@kenya2030#";            
		        $mail->SetFrom('app@quavatel.co.ke', 'Quavatel Mailing System');  
			$mail->AddReplyTo("no-reply@quavatel.co.ke","No Reply");  
		        
		        $mail->Subject    = $data["subject"];
		        $mail->Body      = $data["message"];
		        $destino = $data["email_address"];
		        $mail->AddAddress($destino, "Chrispine Otaalo");
		        
		        if(!$mail->Send()) {
		            $end_data["message"] = "Error: " . $mail->ErrorInfo;
		            $end_data["status"] = FALSE;
		        } else {
		            $end_data["message"] = "Message sent correctly!";
		            $end_data["status"] = TRUE;
		        }
	        
	       
	    }
	    else
	    {
			$mail->IsSMTP(); // Use SMTP
			$mail->IsHTML();
			$mail->Host        = "smtp.gmail.com"; // Sets SMTP server
			$mail->SMTPDebug   = 2; // 2 to enable SMTP debug information
			$mail->SMTPAuth    = TRUE; // enable SMTP authentication
			//$mail->SMTPSecure  = "tls"; //Secure conection
			$mail->Port        = 25; // set the SMTP port
			$mail->Username    = 'chrizota@gmail.com'; // SMTP account username
			$mail->Password    = 'Chrispine-2014'; // SMTP account password
			$mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
			$mail->CharSet     = 'UTF-8';
			$mail->Subject     = $data["subject"];
			$mail->ContentType = 'text/html; charset=utf-8\r\n';
			$mail->From        = 'chrispinethesim@gmail.com';
			$mail->FromName    = 'Quavatel App';

			$destino = $data["email_address"];
			$mail->AddAddress($destino, "Chrispine Otaalo");
			$mail->isHTML( TRUE );
			$mail->Body = $data["message"];
			if(!$mail->Send()) {
				$end_data["message"] = "Error: " . $mail->ErrorInfo;
				$end_data["status"] = FALSE;
			} else {
				$end_data["message"] = "Message sent correctly!";
				$end_data["status"] = TRUE;
			}
			$mail->SmtpClose();
	    }
	     return (object)$end_data;
	}

	function send_email($data)
	{
		$time=date('Y-m-d');
		
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.gmail.com',
			'smtp_port' => 465,
			'smtp_user' => "chrizota@gmail.com",
			'smtp_pass' => "Chrispine-2014"
			);
		
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");

		$this->email->from('chrisrichrads@gmail.com', 'STRATHMORE UNIVERSITY NOTIFICATION');
		$this->email->to($data['email_address']);
		$this->email->subject($data['subject']);
		$this->email->message($data['message']);
		$this->email->set_mailtype("html");
		
		
		if($this->email->send())
			{	
				// $this->admin_model->store_sent_email($recipient, $subject, $message, $time);
				$this->load->view('students_view');
				
			} else 
			{
				show_error($this->email->print_debugger());
			}
	}
}