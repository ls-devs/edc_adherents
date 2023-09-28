<?php
require_once dirname(__FILE__)."/ews/autoload.php";
use garethp\ews\API\Type;
use garethp\ews\MailAPI;

class EmailSMTP
{ 
	public $destinataire = "";
	public $emetteur = "sea@edc.asso.fr";
	public $objet = "";
	public $message = "";
	
	private $host = '176.162.183.219:443';
	private $user = 'edc\\scan';
	private $pass = 'PokeSCAN';
	
	public function __construct() 
	{
		
	}
	
	private function GetHeader()
	{
		$texte = "";
		
		return $texte;
	}
	
	private function GetFooter()
	{
		$texte = "";
		
		return $texte;
	}
	
	public function Send()
	{

		if ($this->objet == "")
			die ("Pas d'objet dans le mail");
		if ($this->message == "")
			die ("Pas de message dans le mail");
		if ($this->destinataire == "")
			die ("Pas de destinataire");

		$subject = $this->objet;	
		
		$texte = "";
		
		$texte .= $this->GetHeader();
		
		$texte .= $this->message;
		
		$texte .= $this->GetFooter();
		
		$api = MailAPI::withUsernameAndPassword($this->host, $this->user, $this->pass);
		
		$message = new Type\MessageType();
		
		$ea =  new Type\EmailAddressType();
		$ea->setEmailAddress($this->emetteur);
		
		$sender = new Type\SingleRecipientType();
		$sender->setMailbox($ea);
		
		$message->setFrom($sender);
		$message->setBody(utf8_encode($texte));
		$message->setSubject(utf8_encode($subject));
		
		$message->setToRecipients($this->destinataire);
		
		if(!$api->sendMail($message)) {
			//echo 'Message could not be sent.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			//echo 'Message has been sent';
		}
		
		
		//$message->setToRecipients('production@edc.asso.fr');
		//$api->sendMail($message);
	}
}
?>