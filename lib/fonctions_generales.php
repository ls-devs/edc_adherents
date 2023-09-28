<?php

date_default_timezone_set('Europe/Paris');

function notifyEDC($IdFiche, $Message)
{
  $url = URL_WS_ADH . 'v1.0/json/user/getFiche/' . $IdFiche;
  $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
  if ($response->code == 200) {
    $retval = json_decode($response->body);
  } else {
    return 'Erreur WS';
  }

  try {

    if (!isset($retval->fiche->DatePriseEnCompte)) {
      $retval->fiche->DatePriseEnCompte = '';
    }
    if (!isset($retval->fiche->Gestionnaire)) {
      $retval->fiche->Gestionnaire = '';
    }
    if (!isset($retval->fiche->NomGestionnaire)) {
      $retval->fiche->NomGestionnaire = '';
    }
    if (!isset($retval->fiche->Domaine)) {
      $retval->fiche->Domaine = '';
    }
    if (!isset($retval->fiche->SousDomaine)) {
      $retval->fiche->SousDomaine = '';
    }
    if (!isset($retval->fiche->DateCloture)) {
      $retval->fiche->DateCloture = '';
    }
    if (!isset($retval->fiche->Programme)) {
      $retval->fiche->Programme = '';
    }
    if (!isset($retval->fiche->Lot)) {
      $retval->fiche->Lot = '';
    }
    if (!isset($retval->fiche->DateDerniereAction)) {
      $retval->fiche->DateDerniereAction = '';
    }
    if (!isset($retval->fiche->lib_statut)) {
      $retval->fiche->lib_statut = '';
    }
    if (!isset($retval->fiche->lib_histo)) {
      $retval->fiche->lib_histo = '';
    }
    if (!isset($retval->fiche->emailService)) {
      $retval->fiche->emailService = '';
    }

    $fiche = array(
      'FicheId' => (string)$retval->fiche->FicheId, 'FicheRef' => (string)$retval->fiche->FicheRef, 'DatePriseEnCompte' => date_to_string((string)$retval->fiche->DatePriseEnCompte), 'DateCloture' => date_to_string((string)$retval->fiche->DateCloture), 'Domaine' => (string)$retval->fiche->Domaine, 'SousDomaine' => (string)$retval->fiche->SousDomaine, 'Gestionnaire' => (string)$retval->fiche->Gestionnaire, 'NomGestionnaire' => (string)$retval->fiche->NomGestionnaire, 'Statut' => (string)$retval->fiche->Statut, 'Programme' => (string)$retval->fiche->Programme, 'Lot' => (string)$retval->fiche->Lot, 'DateDerniereAction' => date_to_string((string)$retval->fiche->DateDerniereAction), 'lib_statut' => (string)$retval->fiche->lib_statut, 'lib_histo' => (string)$retval->fiche->lib_histo, 'emailService' => (string)$retval->fiche->emailService
    );



    // GF 17-12-2012 Utilisation de la fonction générale envoiMail
    $InfoLotMail = '';

    $fichierModel = dirname(__FILE__) . "/../modeles/AR_EDC_action_web.html";


    $t_contenu = array(
      0 => array("search" => "[NumAdhesion]", "replace" => $_SESSION['adherent_infos']->NumAdhesion), 1 => array("search" => "[DateAction]", "replace" => date("d/m/Y") . " &agrave; " . date("H:i")), 2 => array("search" => "[Civilite]", "replace" => $_SESSION['adherent_infos']->Civilite), 3 => array("search" => "[Nom]", "replace" => $_SESSION['adherent_infos']->Nom), 4 => array("search" => "[Prenom]", "replace" => $_SESSION['adherent_infos']->Prenom), 5 => array("search" => "[Domaine]", "replace" => $fiche['Domaine']), 6 => array("search" => "[SousDomaine]", "replace" => $fiche['SousDomaine']), 7 => array("search" => "[GestionnaireFiche]", "replace" => $fiche['Gestionnaire'] . ' ' . $fiche['NomGestionnaire']), 8 => array("search" => "[InfoLot]", "replace" => $InfoLotMail), 9 => array("search" => "[Message]", "replace" => nl2br(stripslashes($Message)))
    );

    $t_infosMail = array(
      "From" => 'noreply@assoedc.com', "To" => $fiche["emailService"], "Subject" => "[" . $fiche["NomGestionnaire"] . "] - " . $fiche["SousDomaine"]
    );

    envoiMail($fichierModel, $t_contenu, $t_infosMail);
  } catch (Exception $e) {
    @mail('stephane.morillon@smorillon.com', 'EDC plugin erreur', 'function notifyEDC ligne 66 ' . $e->getMessage());
    print_r($e);
  }
}

function displayListeActions($IdFiche)
{
  $url = 'http://localhost:3000/listeActions';
  $data = array(
    "fiche_id" => $IdFiche,
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  $data_string = json_encode($data);
  curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    array(
      'Content-Type:application/json',
      'Content-Length: ' . strlen($data_string)
    )
  );
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_URL, $url);
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  /* $url = URL_WS_ADH . 'v1.0/json/user/GetListeActionsFiche/' . $IdFiche; */

  $result = '';


  /* $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send(); */
  if ($httpcode == 200) {

    $retval2 = json_decode($response);
    
  } else {

    return '<strong>Erreur WS</strong>';
  }

  if (isset($retval2)) {
    $result .= '
		<table class="rouge" style="width:100%;border-style:none;border-width:0px; margin-top:10px; margin-bottom:5px;">
			<tr>				
				<th>Actions</th>
			</tr>
		</table>';

    if (isset($retval2->Actions->Observations)) {
      $action = $retval2->Actions;

      $classeQuote = 'style="color:#002c52"';
      //var_dump($action);
      if (date_to_string(date("c")) == date_to_string((string)$action->DateCreation)) {
        $result .= 'Aujourd\'hui';
      } else {
        if (date_to_string(date('c', strtotime("yesterday"))) ==  date_to_string((string)$action->DateCreation)) {
          $result .= 'Hier';
        } else {
          $result .= 'le ' . date_to_string((string)$action->DateCreation);
        }
      }

      if (is_string($action->Expediteur) && trim($action->Expediteur) != '') {
        $result .= ', de ' . ((string)$action->Expediteur);
      } else {
        if ((string)$action->ExpediteurIntitule == 'ASSOCIATION EDC') {
          $result .= ', de ' . ((string)$action->Createur) . ' (EDC)';
        } else {
          $result .= ', de ' . ((string)$action->ExpediteurIntitule);
        }
      }

      if (is_string($action->Destinataire) && trim((string)$action->Destinataire) != '') {
        $result .= ' &agrave; ' . ((string)$action->Destinataire) . ' ';
      } else {
        if (is_string($action->DestinataireIntitule) && (string)$action->DestinataireIntitule == 'ASSOCIATION EDC') {
          if (is_object($action->Gestionnaire)) {
            $action->Gestionnaire = '';
          }

          switch ($action->ReponseWeb_bool) {
            case 1:
              // c'est dans le sens EDC -> Adhérent
              $result .= ' &agrave; ' . ((string)$action->Gestionnaire) . ' (EDC) ';
              break;
            case 2:
              $classeQuote = ' style="color:#C69; background-image: url(\'/wp-content/plugins/edc_adherent/images/postquote.png\'); background-repeat:no-repeat;"';
              $result .= ' &agrave; ' . ((string)$action->Gestionnaire) . ' (EDC) ';
              //$result .= ' &agrave; '.((string)$action->Gestionnaire) . ' (EDC) ';
              break;
            default:
              $result .= ' &agrave; ' . ((string)$action->Gestionnaire) . ' (EDC) ';
              break;
          }
        } else {
          if (is_string($action->DestinataireIntitule)) {
            $result .= ' &agrave; ' . ((string)$action->DestinataireIntitule) . ' ';
          }
        }
      }

      switch ($action->Type) {
        case '1 - Téléphone':
          $result .= 'par téléphone ';
          break;

        case '2 - Mail':
          $result .= 'par courrier éléctronique ';
          break;
        case 'Web':
          $result .= 'via l&rsquo;espace Adh&eacute;rent';
          break;
      }
      //$result .= '' . ((string)$action->Type) . '</td>';

      if (is_string($action->Observations)) {
        $result .= '<blockquote ' . $classeQuote . '>' . replaceNbyBR((string)$action->Observations) . '</blockquote>';
      }
    } else {
      foreach ($retval2->Actions as $action) {
        try {
          $classeQuote = 'style="color:#002c52"';
          //var_dump($action);
          if (date_to_string(date("c")) == date_to_string((string)$action->DateCreation)) {
            $result .= 'Aujourd\'hui';
          } else {
            if (date_to_string(date('c', strtotime("yesterday"))) ==  date_to_string((string)$action->DateCreation)) {
              $result .= 'Hier';
            } else {
              $result .= 'le ' . date_to_string((string)$action->DateCreation);
            }
          }

          if (is_string($action->Expediteur) && trim($action->Expediteur) != '') {
            $result .= ', de ' . ((string)$action->Expediteur);
          } else {
            if ((string)$action->ExpediteurIntitule == 'ASSOCIATION EDC' && is_string($action->Createur) && trim($action->Createur) != '') {
              $result .= ', de ' . ((string)$action->Createur) . ' (EDC)';
            } else {
              $result .= ', de ' . ((string)$action->ExpediteurIntitule);
            }
          }

          if (is_string($action->Destinataire) && trim((string)$action->Destinataire) != '') {
            $result .= ' &agrave; ' . ((string)$action->Destinataire) . ' ';
          } else {
            if (!isset($action->Gestionnaire)) {
              $action->Gestionnaire = '';
            }

            if (is_string($action->DestinataireIntitule) && (string)$action->DestinataireIntitule == 'ASSOCIATION EDC') {
              switch ($action->ReponseWeb_bool) {
                case 1:
                  // c'est dans le sens EDC -> Adhérent
                  $result .= ' &agrave; ' . ((string)$action->Gestionnaire) . ' (EDC) ';
                  break;
                case 2:
                  $classeQuote = ' style="color:#C69; background-image: url(\'/wp-content/plugins/edc_adherent/images/postquote.png\'); background-repeat:no-repeat;"';
                  $result .= ' &agrave; ' . ((string)$action->Gestionnaire) . ' (EDC) ';
                  //$result .= ' &agrave; '.((string)$action->Gestionnaire) . ' (EDC) ';
                  break;
                default:
                  $result .= ' &agrave; ' . ((string)$action->Gestionnaire) . ' (EDC) ';
                  break;
              }
            } else {
              if (is_string($action->DestinataireIntitule)) {
                $result .= ' &agrave; ' . ((string)$action->DestinataireIntitule) . ' ';
              }
            }
          }

          switch ($action->Type) {
            case '1 - Téléphone':
              $result .= 'par téléphone ';
              break;

            case '2 - Mail':
              $result .= 'par courrier éléctronique ';
              break;
            case 'Web':
              $result .= 'via l&rsquo;espace Adh&eacute;rent';
              break;
          }
          //$result .= '' . ((string)$action->Type) . '</td>';


          if (is_string($action->Observations)) {
            $result .= '<blockquote ' . $classeQuote . '>' . replaceNbyBR((string)$action->Observations) . '</blockquote>';
          }
        } catch (Exception $e) {

          $result .= 'Erreur internet<br />';
          @mail('stephane.morillon@smorillon.com', 'EDC asso erreur', 'function_generales ligne 291' . $e->getMessage());
        }
      }
    }
  } else {
    $result .= '<div>Aucune action publi&eacute;e.</div>';
  }
  return ($result);
}

if (!function_exists("utf2latin")) :
  function utf2latin($text)
  {
    $text = htmlentities($text, ENT_COMPAT, 'UTF-8');
    return $text;
    //return html_entity_decode($text,ENT_COMPAT,'ISO-8859-1');
  }
endif;

if (!function_exists("replaceNbyBR")) :
  function replaceNbyBR($text)
  {
    return str_ireplace("\n", '<br/>', str_ireplace("\n\r", '<br/>', $text));
  }
endif;

if (!function_exists("date_to_string")) :
  function date_to_string($date)
  {
    if ($date != '' && strtotime($date) != false) {
      try {
        date_default_timezone_set('Europe/Paris');
        $result = date('j', strtotime($date));
        if ($result == '1') {
          $result = '1er';
        }
        $mois = date('n', strtotime($date));
        switch ($mois) {
          case 1:
            $mois = 'Janvier';
            break;
          case 2:
            $mois = 'F&eacute;vrier';
            break;
          case 3:
            $mois = 'Mars';
            break;
          case 4:
            $mois = 'Avril';
            break;
          case 5:
            $mois = 'Mai';
            break;
          case 6:
            $mois = 'Juin';
            break;
          case 7:
            $mois = 'Juillet';
            break;
          case 8:
            $mois = 'Aout';
            break;
          case 9:
            $mois = 'Septembre';
            break;
          case 10:
            $mois = 'Octobre';
            break;
          case 11:
            $mois = 'Novembre';
            break;
          case 12:
            $mois = 'D&eacute;cembre';
            break;
        }
        $result = $result . ' ' . $mois . ' ' . date('Y', strtotime($date));
        date_default_timezone_set('UTC');
        return $result;
      } catch (Exception $e) {
        return '';
      }
    } else {
      return '';
    }
  }
endif;

if (!function_exists("format_db_date")) :
  function format_db_date($date)
  {
    if ($date != '') {
      try {
        $result = substr($date, 6, 4) . '-' . substr($date, 3, 2) . '-' . substr($date, 0, 2) . 'T00:00:00+01:00';

        return ($result);
      } catch (Exception $e) {
        return '';
      }
    } else {
      return '';
    }
  }
endif;


if (!function_exists("date_to_jjmmaaaa")) :
  function date_to_jjmmaaaa($date)
  {
    if ($date != '') {
      try {
        //date_default_timezone_set('Europe/Paris');
        $result = date('d/m/Y', strtotime($date));
        return utf2latin($result);
      } catch (Exception $e) {
        return '';
      }
    } else {
      return '';
    }
  }
endif;

if (!function_exists("format_date_action_ouinon")) :
  function format_date_action_ouinon($val)
  {
    if ($val == '') {
      return 'non';
    } else {
      return 'oui';
    }
  }
endif;

if (!function_exists("test_empty")) :
  function test_empty($val)
  {
    if ($val != '') {
      return $val;
    } else {
      return '<i><font style="color:red">Information non renseign&eacute;e</font></i>';
    }
  }
endif;


if (!function_exists("formatSurface")) :
  function formatSurface($str)
  {
    if ($str !=  '') {
      return $str . ' m&#178;';
    } else {
      return test_empty($str);
    }
  }
endif;


if (!function_exists("formatDomaine")) :
  function formatDomaine($str)
  {
    return ucfirst(str_ireplace('Domaine ', '', str_ireplace('Sous-domaine ', '', $str)));
  }
endif;

if (!function_exists("formatStatut")) :
  function formatStatut($str)
  {
    return lcfirst(str_replace('A ', '&agrave; ', $str));
  }
endif;

if (!function_exists("formatCurrency")) :
  function formatCurrency($str)
  {
    if ($str == '0.0000' || $str == '0.00' || $str == '') {
      return test_empty('');
    } else {
      if (strpos($str, '.') !== false) {
        $integer = substr($str, 0, strpos($str, '.'));
        $float = substr($str, strpos($str, '.'), 3);
      } else {
        $integer = $str;
        $float = '';
      }
      $len = strlen($integer);
      $result = '';
      while ($len >= 3) {
        $result = ' ' . substr($integer, $len - 3, 3) . $result;
        $len -= 3;
      }
      $result = $result . $float . ' &euro;';
      return $result;
      //$currency = substr($str,0,strpos($str,'.')+3);

      //return substr($str,0,strpos($str,'.')+3) . ' &euro;';
    }
  }
endif;

if (!function_exists("formatPrice")) :
  function formatPrice($str)
  {
    if ($str == '0.0000' || $str == '0.00' || $str == '') {
      return '';
    } else {
      return substr($str, 0, strpos($str, '.') + 3);
    }
  }
endif;

if (!function_exists("formatH1H2Bool")) :
  function formatH1H2Bool($str)
  {
    if ($str == '1') {
      return 'envoy&eacute';
    } else {
      if ($str == '0') {
        return 'non envoy&eacute';
      } else {
        return '';
      }
    }
  }
endif;

function envoiMail($fichierModele, $t_contenu, $t_infosMail)
{
  return envoiMailSMTP($fichierModele, $t_contenu, $t_infosMail);

  /*
      if (!file_exists($fichierModele))
      {
          return(false);
      }

      //J'ouvre le fichier et je récupère le contenu.
      $fic = fopen($fichierModele,"r");
      $fcontent = fread($fic,filesize($fichierModele));
      fclose($fic);

      // J'attends un paramètre $t_contenu qui est un tableau de tableaux
      // array(0=>array("search"=>"[blabla]", "replace"=>"bloblo")
      //		,1=>array("search", "replace"))
      if (count($t_contenu) > 0)
      {
          foreach ($t_contenu as $t_recherche)
          {
              $fcontent = str_replace($t_recherche["search"],$t_recherche["replace"],$fcontent);
          }
      }
      // le tableau de contenu du mail se présente :
      // $t_infoMails = array("From", "To", "Subject");

      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
      $headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";

      // En-t�tes additionnels
      $headers .= "From: ".$t_infosMail["From"]. "\r\n";
      if (isset($t_infosMail["Reply to"]) && $t_infosMail["Reply to"] != '')
      {
          $headers .= "Reply-to:".$t_infosMail["Reply to"]. "\r\n";
      }
      $to = $t_infosMail["To"];
      $subject = $t_infosMail["Subject"];

      //@mail( 'stephane.morillon@smorillon.com', $subject, $to.'<br />'.$headers.'<br />'.$fcontent, $headers);
      return(@mail( $to, $subject, $fcontent, $headers));
      */
}

function envoiMailSMTP($fichierModele, $t_contenu, $t_infosMail)
{
  require_once(dirname(__FILE__) . '/EmailSMTP.php');

  switch ($t_infosMail["From"]) {
    case 'noreply@assoedc.com':
      $t_infosMail["From"] = "no-reply@edc.asso.fr";
      break;
    case 'extranet@edc.asso.fr':
      $t_infosMail["From"] = "no-reply@edc.asso.fr";
      break;
    case 'serviceadhesion@edc.asso.fr':
      $t_infosMail["From"] = "serviceadhesion@edc.asso.fr";
      break;
    default:
      $t_infosMail["From"] = "no-reply@edc.asso.fr";
      break;
  }

  if (!file_exists($fichierModele)) {
    return (false);
  }




  //J'ouvre le fichier et je récupère le contenu.
  $fic = fopen($fichierModele, "r");
  $fcontent = fread($fic, filesize($fichierModele));
  fclose($fic);

  // J'attends un paramètre $t_contenu qui est un tableau de tableaux
  // array(0=>array("search"=>"[blabla]", "replace"=>"bloblo")
  //		,1=>array("search", "replace"))
  if (count($t_contenu) > 0) {
    foreach ($t_contenu as $t_recherche) {
      $fcontent = str_replace($t_recherche["search"], $t_recherche["replace"], $fcontent);
    }
  }
  // le tableau de contenu du mail se présente :
  // $t_infoMails = array("From", "To", "Subject");

  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
  $headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";

  // En-t�tes additionnels
  $headers .= "From: " . $t_infosMail["From"] . "\r\n";
  if (isset($t_infosMail["Reply to"]) && $t_infosMail["Reply to"] != '') {
    $headers .= "Reply-to:" . $t_infosMail["Reply to"] . "\r\n";
  }
  $to = $t_infosMail["To"];
  $subject = $t_infosMail["Subject"];

  $e = new EmailSMTP();

  $e->emetteur = $t_infosMail["From"];
  $e->destinataire = $to;
  $e->objet = utf8_decode($subject);
  $e->message = utf8_decode($fcontent);

  $e->Send();
  return true;
}
