<?php

if ($_SERVER['REMOTE_ADDR'] != '176.162.183.218') exit;

error_reporting(E_ALL);
ini_set('display_errors', '1');
echo 'a';

require_once(dirname(__FILE__).'/lib/EmailSMTP.php');
echo 'b';

$emailToSend = json_decode($_POST['email']);
print_r($emailToSend);

$e = new EmailSMTP();

$e->destinataire = $emailToSend->destinataire;
$e->objet = $emailToSend->objet;
$e->message = $emailToSend->message;

$e->Send();

echo 'c';
?>