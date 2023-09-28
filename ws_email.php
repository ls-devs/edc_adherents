<?php

error_reporting(E_ERROR | E_PARSE);
class Email
{
  public $destinataire = "";
  public $emetteur = "no-reply@assoedc.com";
  public $objet = "";
  public $message = "";

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
    if ($this->objet == "") {
      die("Pas d'objet dans le mail");
    }
    if ($this->message == "") {
      die("Pas de message dans le mail");
    }
    if ($this->destinataire == "") {
      die("Pas de destinataire");
    }

    $this->message = $this->GetHeader() . $this->message . $this->GetFooter();

    $url = 'https://www.assoedc.com/wp-content/plugins/edc_adherent/sendMail.php';
    $data = array('email' => json_encode($this));
    $options = array(
      'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
      )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
  }
}

$e = new Email();
$e->destinataire = $_GET['email'];
$e->objet = "EDC - Renouvellement du mot de passe";
$e->message = "Bonjour,<br /><br />";
$e->message .= "Quelqu'un a demand&eacute; le renouvellement de son mot de passe pour le compte suivant :<br /><br />
						<a href=\"https://www.assoedc.com/\">https://www.assoedc.com/</a><br /><br />
						Identifiant : " . $user_login . "<br /><br />
						S'il s'agit d'une erreur, ignorez ce message et la demande ne sera pas prise en compte.<br /><br />						
						Pour renouveler votre mot de passe, cliquez sur le lien suivant :<br /><br />						
						<a href=\"https://www.assoedc.com/adherent-mot-de-passe_oublie/?key=" . md5($user_login . date('md') . 'qRV~uo}l-oXu;g:Mb{O@uy0|ofZWr`3*hV^H~`)%;Ke-yOR(vh3{e`&G$10`+`&:') . "&login=" . $user_login . "&p=1\">https://www.assoedc.com/adherent-mot-de-passe_oublie/?key=" . md5($user_login . date('md') . 'qRV~uo}l-oXu;g:Mb{O@uy0|ofZWr`3*hV^H~`)%;Ke-yOR(vh3{e`&G$10`+`&:') . "&login=" . $user_login . "&p=1</a>";
$e->Send();

$response = array('statut' => 'OK', 'message' => '');
echo json_encode($response);
