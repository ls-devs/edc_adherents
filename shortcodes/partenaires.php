<?php
require_once(dirname(__FILE__) . '/../lib/fonctions_generales.php');
require_once(dirname(__FILE__) . '/../lib/httpful.phar');

require_once(dirname(__FILE__) . '/../lib/guzzle/vendor/autoload.php');

use GuzzleHttp\Client;

add_shortcode('PARTENAIRE_MON_COMPTE', 'partenaireGetMonCompte');
add_shortcode('PARTENAIRE_SECURE_MDP', 'partenaireGetSecureMdp');
add_shortcode('PARTENAIRE_MES_DONNEES', 'partenaireGetMesDonnees');
add_shortcode('PARTENAIRE_CHANGE_ADRESSE', 'partenaireGetChangeAdresse');
add_shortcode('PARTENAIRE_NOUS_CONTACTER', 'partenaireNousContacter');
add_shortcode('PARTENAIRE_MES_CLIENTS', 'partenaireGetMesClients');
add_shortcode('PARTENAIRE_FAIRE_ADHERER', 'partenaireFaireAdherer');
add_shortcode('PARTENAIRE_FAIRE_SIGNER', 'partenaireFaireSigner');
add_shortcode('PARTENAIRE_COORDONNEES_CLIENTS', 'partenaireCoordonneesClients');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

function partenaireNousContacter()
{
  if (is_admin()) {
    return;
  }
  /*
    $sujet = array(
        'Autre' => 'contact@edc.asso.fr',
        'La construction de mon bien' => 'gestion@edc.asso.fr',
        'La gestion locative de mon bien' => 'gestion@edc.asso.fr',
        'La copropriété ou l\'état de mon bien' => 'technique@edc.asso.fr',
        'La location de mon bien' => 'location@edc.asso.fr',
        'La fin de mon opération' => 'findoperation@edc.asso.fr',
        'Mon prêt' => 'banque@edc.asso.fr',
        'Mes assurances' => 'banque@edc.asso.fr',
        'Une information juridique' => 'sea@edc.asso.fr',
        'Ma fiscalité' => 'fiscalite@edc.asso.fr',
        'Les services & la consommation' => 'consommation@edc.asso.fr',
        'Les énergies & l\'environnement' => 'enr@edc.asso.fr',
        'La retraite' => 'retraite@edc.asso.fr',
        'La transmisson & le patrimoine' => 'patrimoine@edc.asso.fr',
        'Ce site internet' => 'communication@edc.asso.fr',
        'Mon adhésion' => 'sea@edc.asso.fr'
    );
    */

  $sujet = array(
    'Autre' => 'sea@edc.asso.fr',
    'La construction de mon bien' => 'sea@edc.asso.fr',
    'La gestion locative de mon bien' => 'sea@edc.asso.fr',
    'La copropriété ou l\'état de mon bien' => 'sea@edc.asso.fr',
    'La location de mon bien' => 'sea@edc.asso.fr',
    'La fin de mon opération' => 'sea@edc.asso.fr',
    'Mon prêt' => 'sea@edc.asso.fr',
    'Mes assurances' => 'sea@edc.asso.fr',
    'Une information juridique' => 'sea@edc.asso.fr',
    'Ma fiscalité' => 'sea@edc.asso.fr',
    'Les services & la consommation' => 'sea@edc.asso.fr',
    'Les énergies & l\'environnement' => 'sea@edc.asso.fr',
    'La retraite' => 'sea@edc.asso.fr',
    'La transmission & le patrimoine' => 'sea@edc.asso.fr',
    'Ce site internet' => 'sea@edc.asso.fr',
    'Mon adhésion' => 'sea@edc.asso.fr'
  );

  $url = get_permalink(get_option('edc_partenaire_id_page_nous_contacter'));

  //Formulaire envoy�
  if (isset($_POST["Message"]) && !empty($_POST["Message"])) {
    $email = $_POST["email"];
    //session_start('EDC');

    $email_to = "";

    if (isset($sujet[stripslashes($_POST['destinataire'])])) {
      $email_to = $sujet[stripslashes($_POST['destinataire'])];
    } else {
      $email_to = 'sea@edc.asso.fr';
    }


    $subject = "[EXTRANET PARTENAIRE EDC] : " . stripslashes($_POST['destinataire']);

    $numAdherent = '';
    if (isset($_POST['num_adherent_contact'])) {
      $numAdherent = 'Concernant l\'adhérent N°' . $_POST['num_adherent_contact'];
    }

    if ($email_to != "") {
      // GF 17-12-2012 Utilisation de la fonction générale envoiMail
      //Envoi d'un mail contenant le message de l'adhérent au service concerné
      $fichierModel = WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_PART_EDC_message.html";

      $t_contenu = array(
        0 => array("search" => "[NumAdhesion]", "replace" => $numAdherent), 1 => array("search" => "[Date]", "replace" => date("d/m/Y H:i:s")), 2 => array("search" => "[Prenom]", "replace" => $_SESSION['adherent_infos']->Prenom), 3 => array("search" => "[Nom]", "replace" => $_SESSION['adherent_infos']->Nom), 4 => array("search" => "[Email]", "replace" => $_SESSION['adherent_infos']->Email), 5 => array("search" => "[Message]", "replace" => nl2br(stripslashes($_POST['Message'])))
      );

      $t_infosMail = array(
        "From" => 'extranet@edc.asso.fr', "To" => $email_to, "Subject" => $subject, "Reply to" => $_SESSION['adherent_infos']->Email
      );

      //Envoi de l'accusé de réception à l'adhérent
      if (envoiMail($fichierModel, $t_contenu, $t_infosMail) == true) {
        $fichierModel = WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_PART_message.html";

        $t_contenu = array(
          0 => array("search" => "[Date]", "replace" => date("d/m/Y H:i:s")), 1 => array("search" => "[Prenom]", "replace" => $_SESSION['adherent_infos']->Prenom), 2 => array("search" => "[Nom]", "replace" => $_SESSION['adherent_infos']->Nom), 3 => array("search" => "[Email]", "replace" => $_SESSION['adherent_infos']->Email), 4 => array("search" => "[Domaine]", "replace" => stripslashes($_POST['destinataire'])), 5 => array("search" => "[Message]", "replace" => nl2br(stripslashes($_POST['Message'])))
        );

        $t_infosMail = array(
          "From" => 'noreply@assoedc.com', "To" => $_SESSION['adherent_infos']->Email, "Subject" => "[EXTRANET PARTENAIRE EDC] : Prise en compte de votre demande"
        );

        envoiMail($fichierModel, $t_contenu, $t_infosMail);

        wp_redirect($url . '?errorse=0');
        /*
                $ret = '<script type="text/javascript">';
                $ret .=  '   location.href= \''.$url . '?errorse=0\';';
                $ret .= '</script>';
                */
        $ret .= '<div style="padding:5px;background-color:white;">Envoi de votre message en cours...</div>';
        return $ret;
      } else {
        $ret = $headers;
        $ret .= $subject;
        $ret .= $message;

        wp_redirect($url . '?errorse=1');
        /*
                $ret .= '<script type="text/javascript">';
                $ret .= '   location.href= \''.$url . '?errorse=1\';';
                $ret .= '</script>';
                */
        return $ret;
      }
    } else {
      $ret = $headers;
      $ret .= $subject;
      $ret .= $message;
      wp_redirect($url . '?errorse=0');
      /*
            $ret .= '<script type="text/javascript">';
            $ret .= '   location.href= \''.$url . '?errorse=1\';';
            $ret .= '</script>';
            */
      return $ret;
    }
  } else {
    //Formulaire d'envoi
    // BM 27/06/2011
    // Ajout d'un test pour sortir la cr�ation du formulaire lorsque l'on affiche un retour d'envoi par email.
    if (isset($_GET['errorse']) && $_GET['errorse'] == "1") {
      // il y a eu une erreur d'envoi
      $result .= '<h6 style="color:red">Votre message n&#146;a pu &ecirc;tre envoy&eacute; suite &agrave; une anomalie technique</h6>';
    } elseif (isset($_GET['errorse']) && $_GET['errorse'] == "0") {
      // il n'y a pas eu d'erreur
      $result .= '<h6 style="color:green; font-size:14px;">Votre message nous a &eacute;t&eacute; adress&eacute;.</h6>';
      $result .= '<h6 style="color:green; font-size:14px;">Un mail r&eacute;capitulant votre demande vous a été envoyé.</h6>';
    } else {
      // on affiche le formulaire
      $result .= '<form name="UpdateDonnees" id="UpdateDonnees" method="post" action="' . $url . '">';

      $result .= '<h6 style="margin-top:5px;margin-bottom:5px;">Votre question concerne : <select name="destinataire" style="margin-bottom:0px"></h6>';

      foreach ($sujet as $s => $dest) {
        $result .= '<option value="' . $s . '">' . $s . '</option>';
      }
      $result .= '</select><br/>';

      $result .= '<h6 style="margin-top:5px;margin-bottom:5px;">Numéro de l\'adhérent (optionnel) :</h6><br/><input type="text" name="num_adherent_contact" /><br/>';

      $result .= '<h6 style="margin-top:5px;margin-bottom:5px;">Votre message :</h6><br/><textarea name="Message" rows="10" cols="72"></textarea><br/>';
      $result .= '<p style="text-align:right"><input type="submit" id="submit" name="submit" value="Envoyer"/></p>';
      $result .= '</form>';
      $result .= '<div id="about"></div>';
    }
  }
  // on revoie l'affichage dans la page
  return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}

function partenaireGetMonCompte()
{
  if (is_admin()) {
    return;
  }
  $html = '';
  if (isset($_POST['posted_password']) && $_POST['posted_password'] == 1) {
    $url = URL_WS_ADH . 'v1.0/json/partenaire/change_mdp_man';

    $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('new_pwd=' . trim($_POST['txtPassword']))->send();

    if ($response->code == 200) {
      header('location:' . get_home_url() . '?deconnect=1');
      die();
    } else {
      $html = '<strong>Erreur WS</strong>';
    }
  } else {
    $html = '';

    $html .= '<p style="font-size: 16px; color: #009900;">Nom : <strong style="color: #E4680C;">' . $_SESSION['adherent_infos']->Prenom . ' ' . $_SESSION['adherent_infos']->Nom . '</strong></p>
		<p style="font-size: 16px; color: #009900;">Mon email : <strong style="color: #E4680C;">' . $_SESSION['adherent_infos']->Email . '</strong></p>';

    $html .= '<div style="clear:both; height:20px;"></div>
		<div style="width:50%; text-align:center; float:left" class="more-link"><a href="javascript:AffDiv(\'form_mdp\');" class="read-more">Modifier mon mot de passe</a></div>
		<div style="clear:both; height:20px;"></div>';

    $html .= '
		<script>
		function AffDiv(nom)
		{
			jQuery(\'#\'+nom).slideToggle();
		}
		
		function checkPassword()
		{
			var error=0;
			var message_erreur;
			if (document.getElementById("txtPassword").value == "" || document.getElementById("txtPasswordConfirm").value == "")
			{
				error++;
				message_erreur="ERREUR : Tous les champs doivent être remplis";
			}
		
			if (document.getElementById("txtPassword").value  != document.getElementById("txtPasswordConfirm").value )
			{
				error++;
				message_erreur="ERREUR : Tous les champs doivent être identiques";
			}
			
			if(document.getElementById("txtPassword").value.length < 6)
			{
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins 6 caractères.";
			}
			
			re = /[0-9]/;
		 	if(!re.test(document.getElementById("txtPassword").value)) {
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins un chiffre.";
			}
			re = /[a-z]/;
		 	if(!re.test(document.getElementById("txtPassword").value)) {
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins une minuscule.";
			}
			re = /[A-Z]/;
		 	if(!re.test(document.getElementById("txtPassword").value)) {
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins une majuscule.";
			}
			
			if (error > 0)
			{
				document.getElementById("posted_password").value=0;
				document.getElementById("error_password").style.display="inline";
				document.getElementById("error_password").innerHTML=message_erreur;
				return (false);
			}
			document.getElementById("posted_password").value=1;
			return(true);
		}
		</script>';

    $html .= '
		
		<form onsubmit="return(checkPassword(this));" action="" method="POST" name="modif_password" id="form_mdp" class="frm_forms with_frm_style frm_style_style-formidable" style="display:none;">
		<input type="hidden" value="0" id="posted_password" name="posted_password">	
		<div id="error_password" style="font-size: 16px; font-weight: bold; color: #ff0000; display: none;"></div>
		<p>Pour modifier votre mot de passe, vous devez préciser le nouveau (et le confirmer).<br>
		Votre mot de passe <u>doit</u> faire au moins 6 caractètes et contenir à minima les caractères suivants :</p>
		<ul style="list-style:disc; margin-left:20px;">
			<li>Une majuscule</li>
			<li>Une minuscule</li>
			<li>Un chiffre</li>
		</ul>
		<p>A défaut, votre nouveau mot de passe ne sera pas enregistré</p>
		
		<p><label for="txtPassword" style="float:left; margin-right:10px; line-height:30px;">Mot de passe :</label><input type="password" name="txtPassword" id="txtPassword" style="font-size: 12px; color: #694F40; width:300px;"></p>
		<p><label for="txtPasswordConfirm" style="float:left; margin-right:10px; line-height:30px;">Confirmez le mot de passe :</label><input type="password" name="txtPasswordConfirm" id="txtPasswordConfirm" style="font-size: 12px; color: #694F40; width:300px;"></p>
		<p style="color:#CC0000;">Lorsque vous aurez enregistré votre demande, vous serez automatiquement déconnecté.<br>
		Il sera alors nécessaire de vous identifier et de renseigner le nouveau mot de passe.</p>
		<p class="submit frm_submit"><input type="submit" value="Enregistrer mon nouveau mot de passe" name="submit_password" class="art-button"></p>
		</form>';


    $html .= '<div style="clear:both; height:20px;"></div>';
  }

  return $html;
}

function partenaireGetSecureMdp()
{
  if (is_admin()) {
    return;
  }
  $html = '';

  if (isset($_POST['posted_password']) && $_POST['posted_password'] == 1) {
    $url = URL_WS_ADH . 'v1.0/json/partenaire/change_mdp_man';

    $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('new_pwd=' . trim($_POST['txtPassword']))->send();

    if ($response->code == 200) {
      header('location:' . get_home_url() . '?deconnect=1');
      die();
    } else {
      $html = '<strong>Erreur WS</strong>';
    }
  } else {
    $html = '';
    $html .= '<p style="font-size: 16px;"><strong style="color:#C00">Pour des raisons de sécurité nous vous recommandons de changer votre mot de passe</strong>, <br />
le nouveau mot de passe devra comporter au moins une lettre Minuscule, une lettre Majuscule et un Chiffre,<br />
il devra comporter au moins 6 caractères.</p>';

    $html .= '<div style="clear:both; height:20px;"></div>';

    $html .= '
		<script>
		
		function checkPassword()
		{
			var error=0;
			var message_erreur;
			if (document.getElementById("txtPassword").value == "" || document.getElementById("txtPasswordConfirm").value == "")
			{
				error++;
				message_erreur="ERREUR : Tous les champs doivent être remplis";
			}
		
			if (document.getElementById("txtPassword").value  != document.getElementById("txtPasswordConfirm").value )
			{
				error++;
				message_erreur="ERREUR : Tous les champs doivent être identiques";
			}
			
			if(document.getElementById("txtPassword").value.length < 6)
			{
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins 6 caractères.";
			}
			
			re = /[0-9]/;
		 	if(!re.test(document.getElementById("txtPassword").value)) {
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins un chiffre.";
			}
			re = /[a-z]/;
		 	if(!re.test(document.getElementById("txtPassword").value)) {
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins une minuscule.";
			}
			re = /[A-Z]/;
		 	if(!re.test(document.getElementById("txtPassword").value)) {
				error++;
				message_erreur="ERREUR : Votre mot de passe doit contenir au moins une majuscule.";
			}
			
			if (error > 0)
			{
				document.getElementById("posted_password").value=0;
				document.getElementById("error_password").style.display="inline";
				document.getElementById("error_password").innerHTML=message_erreur;
				return (false);
			}
			document.getElementById("posted_password").value=1;
			return(true);
		}
		</script>';

    $html .= '
		
		<form onsubmit="return(checkPassword(this));" action="" method="POST" name="modif_password" id="form_mdp" class="frm_forms with_frm_style frm_style_style-formidable">
		<input type="hidden" value="0" id="posted_password" name="posted_password">	
		<div id="error_password" style="font-size: 16px; font-weight: bold; color: #ff0000; display: none;"></div>
		<p>Pour modifier votre mot de passe, vous devez préciser le nouveau (et le confirmer).<br>
		Votre mot de passe <u>doit</u> faire au moins 6 caractètes et contenir à minima les caractères suivants :</p>
		<ul style="list-style:disc; margin-left:20px;">
			<li>Une majuscule</li>
			<li>Une minuscule</li>
			<li>Un chiffre</li>
		</ul>
		<p>A défaut, votre nouveau mot de passe ne sera pas enregistré</p>
		
		<p><label for="txtPassword" style="float:left; margin-right:10px; line-height:30px;">Mot de passe :</label><input type="password" name="txtPassword" id="txtPassword" style="font-size: 12px; color: #694F40; width:300px;"></p>
		<p><label for="txtPasswordConfirm" style="float:left; margin-right:10px; line-height:30px;">Confirmez le mot de passe :</label><input type="password" name="txtPasswordConfirm" id="txtPasswordConfirm" style="font-size: 12px; color: #694F40; width:300px;"></p>
		<p style="color:#CC0000;">Lorsque vous aurez enregistré votre demande, vous serez automatiquement déconnecté.<br>
		Il sera alors nécessaire de vous identifier et de renseigner le nouveau mot de passe.</p>
		<p class="submit frm_submit"><input type="submit" value="Enregistrer mon nouveau mot de passe" name="submit_password" class="art-button"></p>
		</form>';


    $html .= '<div style="clear:both; height:20px;"></div>';
  }

  return $html;
}


function partenaireFaireSignerTest()
{
  global $_conf, $_CONFIG;

  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/DB.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/ConfigurationCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Configuration.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/PartenaireCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Partenaire.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/ProspectCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Prospect.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/CommandeCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Commande.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/config/config.php');

  DB::Connexion($_CONFIG['BD_HOST'], $_CONFIG['BD_USER'], $_CONFIG['BD_PASSWORD'], $_CONFIG['BD_BD']);

  $id_commande = (int)$_GET['c'];
  $commande = new Commande($id_commande);

  if (true) {
    if (file_exists(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf')) {
      @unlink(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf');
    }

    $background = 'https://www.assoedc.com/wp-content/plugins/edc_adherent/bulletins/BULLETIN.png';
    $background_sepa = 'https://www.assoedc.com/wp-content/plugins/edc_adherent/bulletins/Mandat-SEPA.png';

    //$background = 'http://127.0.0.1/edc_preprod/wp-content/plugins/edc_adherent/bulletins/BULLETIN.png';


    include(WP_PLUGIN_DIR . "/edc_adherent/php_to_pdf/html2pdf.class.php");

    ob_start();
?>
    <page backimg="<?php echo $background; ?>" backtop="10mm" backbottom="20mm" backleft="10mm" backright="10mm" orientation="P">
      <div style="margin-left:50mm; margin-top:55mm;"><?php echo $commande->nom; ?></div>
      <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->prenom; ?></div>
      <div style="margin-left:50mm; margin-top:5mm;">&nbsp;</div>
      <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->adresse_1; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:2mm;"><?php echo $commande->adresse_2; ?>&nbsp;</div>

      <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->code_postal; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->ville; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->tel_1; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->email; ?>&nbsp;</div>

      <div style="margin-left:78mm; margin-top:22.5mm; font-family:BebasNeueBold; font-weight:bold; font-size:24px; color:#FFF; text-transform:uppercase;"><?php echo $commande->conseiller_reseau_label; ?></div>
      <div style="margin-left:50mm; margin-top:1mm;"><?php echo $commande->conseiller_nom; ?></div>
      <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->conseiller_prenom; ?></div>
      <div style="margin-left:50mm; margin-top:4mm;">&nbsp;</div>
      <div style="margin-left:50mm; margin-top:3mm;"><?php echo $commande->conseiller_email; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:11mm;"><?php echo $commande->conseiller_reseau; ?></div>

      <div style="margin-left:73.5mm; margin-top:17mm;"><?php echo (isset($_POST['have_acte_notarie']) && $_POST['have_acte_notarie'] == 'on') ? 'X&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_POST['date_acte_notarie'] : ''; ?>&nbsp;</div>
      <div style="margin-left:73.5mm; margin-top:2mm;"><?php echo (true || !isset($_POST['have_acte_notarie']) || $_POST['allow_partenaire_coord'] != 'on') ? 'X' : ''; ?>&nbsp;</div>

      <div style="margin-left:3mm; margin-top:4.8mm;"><?php echo (isset($_POST['allow_partenaire_dossier']) && $_POST['allow_partenaire_dossier'] == 'on') ? 'X' : ''; ?>&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;"><?php echo $commande->conseiller_reseau_label; ?></span>
      </div>
      <div style="margin-left:3mm; margin-top:6mm;"><?php echo (isset($_POST['allow_partenaire_coord']) && $_POST['allow_partenaire_coord'] == 'on') ? 'X' : ''; ?>&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;"><?php echo $commande->conseiller_reseau_label; ?></span></div>
    </page>

    <page backimg="<?php echo $background_sepa; ?>" backtop="10mm" backbottom="20mm" backleft="10mm" backright="10mm" orientation="P">
      <div style="margin-left:50mm; margin-top:137mm;"><?php echo $commande->nom; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->prenom; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->adresse_1; ?>&nbsp;</div>
      <div style="margin-left:50mm; margin-top:2mm;"><?php echo $commande->adresse_2; ?>&nbsp;</div>
      <div style="margin-left:30mm; margin-top:7mm;">
        <table>
          <tr>
            <td style="width:30mm"><?php echo $commande->code_postal; ?>&nbsp;</td>
            <td><?php echo $commande->ville; ?>&nbsp;</td>
          </tr>
        </table>
      </div>
      <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->pays; ?>&nbsp;</div>

      <div style="margin-left:5mm; margin-top:55mm;"><?php echo $commande->iban; ?>&nbsp;</div>
      <div style="margin-left:5mm; margin-top:10mm;"><?php echo $commande->bic; ?>&nbsp;</div>
    </page>

    <?php

    $content = ob_get_clean();

    if (file_exists(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf')) {
      @unlink(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf');
    }

    $html2pdf = new HTML2PDF();
    $html2pdf->addFont('BebasNeueBold', 'B', dirname(__FILE__) . '/../php_to_pdf/fonts/bebasneueb.php');
    $html2pdf->addFont('BebasNeueBold', '', dirname(__FILE__) . '/../php_to_pdf/fonts/bebasneueb.php');
    $html2pdf->setDefaultFont('Helvetica');
    //$html2pdf->setDefaultFont('BebasNeueBold');
    $html2pdf->writeHTML($content);
    $html2pdf->Output('_BULLETIN_ADHESION.pdf', 'I');
    //$html2pdf->Output(__DIR__ .'/../bulletins/generes/'.$commande->id_commande.'_BULLETIN_ADHESION.pdf', 'F');
  }

  die();
}

function partenaireFaireSigner()
{

  error_reporting(E_ALL);
  ini_set("display_errors", 1);


  global $_conf, $_CONFIG;

  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/DB.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/ConfigurationCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Configuration.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/PartenaireCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Partenaire.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/ProspectCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Prospect.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/CommandeCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Commande.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/config/config.php');

  DB::Connexion($_CONFIG['BD_HOST'], $_CONFIG['BD_USER'], $_CONFIG['BD_PASSWORD'], $_CONFIG['BD_BD']);

  $result = '';
  $id_commande = $_GET['c'];
  $commande = new Commande($id_commande);
  if (md5($commande->nom . $id_commande . $commande->prenom . 'EDC') != $_GET['k']) {
    $result = '<div id="donnesPerso">
		<h1 style="margin-bottom:5px">Erreur - lien incorrect</h1>
		</div>';
    return $result;
  } else {
    if (!isset($_POST['signer'])) {
      $result .= '<form method="POST" class="frm_forms with_frm_style frm_style_style-formidable">
			<input type="hidden" name="signer" value="1" />
			
			<div class="row">
			
				<h2>Mon conseiller</h2>
				<p>' . $commande->conseiller_prenom . ' ' . $commande->conseiller_nom . '</p>
				<p>&nbsp;</p>
			';

      if ($commande->bien_residence != 'PAS DE BIEN') {
        $result .= '
				<p><input type="checkbox" name="allow_partenaire_dossier"> <label for="allow_partenaire_dossier" style="display:inline-block;">J’autorise EDC à porter le suivi de mes dossiers concernant mon investissement immobilier à la connaissance du conseiller identifié sur le présent bulletin.</label></p>
				';
      }

      $result .= '				
                <p><input type="checkbox" name="allow_partenaire_coord"> <label for="allow_partenaire_coord" style="display:inline-block;">J’autorise EDC à communiquer mes coordonnées au conseiller identifié ci-dessus.</label></p>
			</div>
			<div style="clear:both;"></div>
			
			<p class="submit frm_submit"><input type="submit" value="Signer" name="submit_password" class="art-button"></p>
			</form>';


      $result .= '</div>';
    } else {

      // On génère le bulletin PDF
      if (true) {
        if (file_exists(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf')) {
          @unlink(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf');
        }

        $background = 'https://www.assoedc.com//wp-content/plugins/edc_adherent/bulletins/BULLETIN.png';
        $background_sepa = 'https://www.assoedc.com//wp-content/plugins/edc_adherent/bulletins/Mandat-SEPA.png';

        include(WP_PLUGIN_DIR . "/edc_adherent/php_to_pdf/html2pdf.class.php");

        $commande->allow_partenaire_dossier = (isset($_POST['allow_partenaire_dossier']) && $_POST['allow_partenaire_dossier'] == 'on') ? 1 : 0;
        $commande->allow_partenaire_coord = (isset($_POST['allow_partenaire_coord']) && $_POST['allow_partenaire_coord'] == 'on') ? 1 : 0;
        $commande->Save();

        ob_start();
    ?>
        <page backimg="<?php echo $background; ?>" backtop="10mm" backbottom="20mm" backleft="10mm" backright="10mm" orientation="P">
          <div style="margin-left:50mm; margin-top:55mm;"><?php echo $commande->nom; ?></div>
          <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->prenom; ?></div>
          <div style="margin-left:50mm; margin-top:5mm;">&nbsp;</div>
          <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->adresse_1; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:2mm;"><?php echo $commande->adresse_2; ?>&nbsp;</div>

          <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->code_postal; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->ville; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->tel_1; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:5mm;"><?php echo $commande->email; ?>&nbsp;</div>

          <div style="margin-left:78mm; margin-top:22.5mm; font-family:BebasNeueBold; font-weight:bold; font-size:24px; color:#FFF; text-transform:uppercase;"><?php echo $commande->conseiller_reseau_label; ?></div>
          <div style="margin-left:50mm; margin-top:1mm;"><?php echo $commande->conseiller_nom; ?></div>
          <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->conseiller_prenom; ?></div>
          <div style="margin-left:50mm; margin-top:4mm;">&nbsp;</div>
          <div style="margin-left:50mm; margin-top:3mm;"><?php echo $commande->conseiller_email; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:11mm;"><?php echo $commande->conseiller_reseau; ?></div>

          <div style="margin-left:73.5mm; margin-top:17mm;"><?php echo ($commande->bien_acte == 1) ? 'X&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $commande->bien_date_acte : ''; ?>&nbsp;</div>
          <div style="margin-left:73.5mm; margin-top:2mm;"><?php echo ($commande->bien_acte != 1) ? 'X' : ''; ?>&nbsp;</div>

          <div style="margin-left:3mm; margin-top:4.8mm;"><?php echo (isset($_POST['allow_partenaire_dossier']) && $_POST['allow_partenaire_dossier'] == 'on') ? 'X' : '&nbsp;'; ?>&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;"><?php echo $commande->conseiller_reseau_label; ?></span>
          </div>
          <div style="margin-left:3mm; margin-top:6mm;"><?php echo (isset($_POST['allow_partenaire_coord']) && $_POST['allow_partenaire_coord'] == 'on') ? 'X' : '&nbsp;'; ?>&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;"><?php echo $commande->conseiller_reseau_label; ?></span></div>
        </page>

        <page backimg="<?php echo $background_sepa; ?>" backtop="10mm" backbottom="20mm" backleft="10mm" backright="10mm" orientation="P">
          <div style="margin-left:50mm; margin-top:137mm;"><?php echo $commande->nom; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->prenom; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->adresse_1; ?>&nbsp;</div>
          <div style="margin-left:50mm; margin-top:2mm;"><?php echo $commande->adresse_2; ?>&nbsp;</div>
          <div style="margin-left:30mm; margin-top:7mm;">
            <table>
              <tr>
                <td style="width:30mm"><?php echo $commande->code_postal; ?>&nbsp;</td>
                <td><?php echo $commande->ville; ?>&nbsp;</td>
              </tr>
            </table>
          </div>
          <div style="margin-left:50mm; margin-top:4mm;"><?php echo $commande->pays; ?>&nbsp;</div>

          <div style="margin-left:5mm; margin-top:55mm;"><?php echo $commande->iban; ?>&nbsp;</div>
          <div style="margin-left:5mm; margin-top:10mm;"><?php echo $commande->bic; ?>&nbsp;</div>
        </page>

<?php

        $content = ob_get_clean();

        if (file_exists(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf')) {
          @unlink(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf');
        }

        $html2pdf = new HTML2PDF();
        $html2pdf->addFont('BebasNeueBold', 'B', dirname(__FILE__) . '/../php_to_pdf/fonts/bebasneueb.php');
        $html2pdf->addFont('BebasNeueBold', '', dirname(__FILE__) . '/../php_to_pdf/fonts/bebasneueb.php');
        $html2pdf->setDefaultFont('Helvetica');
        $html2pdf->writeHTML($content);
        $html2pdf->Output(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf', 'F');
      }

      // Signature electronique
      $client = new Client([
        // You can set any number of default request options.
        'timeout'  => 30.0,
        'verify' => false
      ]);

      // Preprod
      /*
            $token = 'EDCEDC18|fPoz2bWyFLqJwN0oRucsqAhAAsd3GLSBP0lb9YHapHU=';
            $contract_definition_id = 7993;
            $vendor_email = 'fo.edc@calindasoftware.com';
            */

      // Prod
      $token = 'EDC18619|KmZWzY7NhoJYYWNXVWkeapS9IoQD1NUH3bDZC7lGShw=';
      $contract_definition_id = 10471;
      $vendor_email = 'foprod.edc@calindasoftware.com';

      $response = $client->request('POST', 'https://cloud.sellandsign.com/calinda/hub/selling/do?m=sendCommandPacket', [
        'headers' => [
          'j_token' => $token
        ],

        'multipart' => [
          [
            'name'     => 'adhoc_light2.sellsign',
            'contents' => '{
								"customer": {
									"number": "' . $commande->id_commande . '",
									"mode": 3,
									"contractor_id": -1,
									"vendor": "' . $vendor_email . '",
									"fields": [{
											"key": "firstname",
											"value": "' . $commande->prenom . '"
										},
										{
											"key": "lastname",
											"value": "' . $commande->nom . '"
										},
										{
											"key": "email",
											"value": "' . $commande->email . '"
										},
										{
											"key": "cellPhone",
											"value": "' . $commande->tel_1 . '"
										}
									]
								},
								"contractors": [],
								"contract": {
									"contract_definition_id": ' . $contract_definition_id . ' ,
									"pdf_file_path": "' . $commande->id_commande . '_BULLETIN_ADHESION.pdf' . '",
									"contract_id": -1,
									"message_title": "Votre bulleton d\'adhésion à EDC pour signature",
									"message_body": "Vous êtes signataire du bulletin d\'adhésion ci-joint pour l\'association EDC. Merci de bien vouloir le signer électroniquement en cliquant sur le lien ci-dessous.<br>Cordialement,"		
								},
								"contract_properties": [
									{
										"key": "internal_contract_id",
										"value": "' . $commande->id_commande . '",
										"to_fill_by_user": 0
									},
									{
									  "key": "callback:url",
									  "value":"https://www.assoedc.com/seelandsign_success.php?id=<id>"
									}
								],
								"files": [],
								"options": [],
								"to_sign": 1
							}',
            'filename' => 'adhoc_light.sellsign',
            'headers'  => [
              'Content-type' => 'application/json'
            ]
          ],
          [
            'name'     => $commande->id_commande . '_BULLETIN_ADHESION.pdf',
            'contents' => fopen(__DIR__ . '/../bulletins/generes/' . $commande->id_commande . '_BULLETIN_ADHESION.pdf', 'r'),
            'filename' => $commande->id_commande . '_BULLETIN_ADHESION.pdf',
            'headers'  => [
              'Content-type' => 'application/pdf'
            ]
          ]
        ]
      ]);

      $result = '';
      $retour = json_decode($response->getBody()->getContents());
      if (json_last_error() != JSON_ERROR_NONE) {
        // on parse manuellement

        $content = $response->getBody()->getContents();
        $contrat_id = explode(':', $content);
        $contrat_id = explode('}', $contrat_id[count($contrat_id) - 1]);
        $contrat_id = $contrat_id[0];
      } else {
        $contrat_id = $retour->contract_id;
      }

      $commande->contratc_id = $contrat_id;
      $commande->Save();


      $result .= '<h1>Signature de votre bulletin d\'adhesion</h1><br /><br />';
      $result .=  '<p>Un email vient de vous être envoyé pour la signature électronique de votre bulletin d\'adhésion.<br />Merci de cliquer sur le lien présent dans l\'email</p>';
    }
  }

  return $result;
}

function partenaireFaireAdherer()
{
  global $_conf, $_CONFIG;

  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/DB.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/ConfigurationCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Configuration.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/PartenaireCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Partenaire.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/ProspectCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Prospect.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/CommandeCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Commande.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/config/config.php');

  DB::Connexion($_CONFIG['BD_HOST'], $_CONFIG['BD_USER'], $_CONFIG['BD_PASSWORD'], $_CONFIG['BD_BD']);

  // Test Email
  /*
    $fichierModel = WP_PLUGIN_DIR."/edc_adherent/modeles/ADH_Signer_Bulletin.html";
    $t_contenu=array(	0=>array("search"=>"[Prenom]", "replace"=>'TEST send email')
                        ,1=>array("search"=>"[Nom]", "replace"=>'TEST send email')
                        ,2=>array("search"=>"[Lien]", "replace"=>'')
                        );
    $t_infosMail = array(	"From"=>'serviceadhesion@edc.asso.fr'
                            ,"To"=> trim('stephane.morillon@smorillon.com')
                            ,"Subject"=>"[EDC] : Test envoi email"
                            ,"Reply to"=>'serviceadhesion@edc.asso.fr'
                        );

    $t_infosMail['To'] = 'pascal@psalles.fr';
    envoiMail($fichierModel, $t_contenu, $t_infosMail);
    $t_infosMail['To'] = 'salles.p@edc.asso.fr';
    envoiMail($fichierModel, $t_contenu, $t_infosMail);
    $t_infosMail['To'] = 'stephane.morillon@smorillon.com';
    envoiMail($fichierModel, $t_contenu, $t_infosMail);
    */

  $result = '<div id="donnesPerso">
		<h1 style="margin-bottom:5px">Créer une adhésion</h1><br/>';

  if (isset($_POST['nom'])) {
    $commande = new Commande();

    $commande->id_partenaire = $_SESSION['adherent_infos']->IdContact;
    $commande->num_adhesion = "";
    $commande->nom = $_POST['nom'];
    $commande->prenom = $_POST['prenom'];
    $commande->adresse_1 = $_POST['adresse_1'];
    $commande->adresse_2 = $_POST['adresse_2'];
    $commande->code_postal = $_POST['code_postal'];
    $commande->ville = $_POST['ville'];
    $commande->pays = $_POST['pays'];
    $commande->email = $_POST['email'];
    $commande->tel_1 = $_POST['telephone'];
    $commande->tel_2 = "";
    $commande->civilite = "";
    $commande->situation_familiale = ""; // char(20);
    $commande->nb_personne = -1;
    $commande->autorisation = "";
    $commande->transaction = "";
    $commande->date_reponse = date('Y-m-d H:i:s');;
    $commande->origine = "";
    $commande->origine_details = "";

    $commande->conseiller_societe = "";
    $commande->conseiller_adresse_1 = $_SESSION['adherent_infos']->Adresse1;
    $commande->conseiller_adresse_2 = $_SESSION['adherent_infos']->Adresse2;
    $commande->conseiller_codepostal = $_SESSION['adherent_infos']->CP;
    $commande->conseiller_ville = $_SESSION['adherent_infos']->Ville;

    if (isset($_POST['conseiller_nom'])) {
      $commande->conseiller_nom = $_POST['conseiller_nom'];
      $commande->conseiller_prenom = $_POST['conseiller_prenom'];
      $commande->conseiller_email = $_POST['conseiller_email'];
    } else {
      $commande->conseiller_email = $_SESSION['adherent_infos']->Email;
      $commande->conseiller_nom = $_SESSION['adherent_infos']->Nom;
      $commande->conseiller_prenom = $_SESSION['adherent_infos']->Prenom;
    }
    $commande->conseiller_entite_com = "";
    $commande->conseiller_civilite = "";

    $commande->conseiller_reseau = $_POST['groupe'];
    $commande->conseiller_reseau_label = $_POST['groupe_label'];

    if ($_POST['paiement_sepa'] == 1) {
      $commande->iban = $_POST['iban'];
      $commande->bic = $_POST['bic'];
    }

    if ($_POST['bien_have'] == 1) {
      $commande->bien_residence = $_POST['bien_residence'];
      $commande->bien_adresse_1 = $_POST['bien_adresse_1'];
      $commande->bien_adresse_2 = $_POST['bien_adresse_2'];
      $commande->bien_code_postal = $_POST['bien_code_postal'];
      $commande->bien_ville = $_POST['bien_ville'];
      $commande->bien_lot = $_POST['bien_lot'];
      $commande->bien_dispositif = $_POST['bien_dispositif'];
      $commande->bien_administrateur = $_POST['bien_administrateur'];
      $commande->bien_acte = $_POST['bien_acte'];
      if ($commande->bien_acte == 1) {
        $commande->bien_date_acte = $_POST['bien_date_acte'];
      }
    } else {
      $commande->bien_residence = 'PAS DE BIEN';
    }

    $commande->signed = 0;

    $commande->Save();



    if ($_POST['paiement_sepa'] == 1) { // Ne doit pas payer
      $commande->montant = 0.0;
      $commande->date_paiement = date('Y-m-d H:i:s');
      $commande->resultat_cb = "NA";
      $commande->Save();

      // Envoi de l'email à l'adhérent
      //MODELE DE COURRIER POUR L'ASSOCIATION
      $fichierModel = WP_PLUGIN_DIR . "/edc_adherent/modeles/ADH_Signer_Bulletin.html";


      $url_signer = get_permalink(get_option('edc_partenaire_id_page_faire_signer')) . '?c=' . $commande->id_commande . '&k=' . md5($commande->nom . $commande->id_commande . $commande->prenom . 'EDC');

      //JE REMPLACE CERTAINES VARIABLES DANS LE MODELE
      $t_contenu = array(
        0 => array("search" => "[Prenom]", "replace" => $_POST['prenom']), 1 => array("search" => "[Nom]", "replace" => $_POST['nom']), 2 => array("search" => "[Lien]", "replace" => $url_signer)
      );

      //J'ENVOI LE MAIL
      $t_infosMail = array(
        "From" => 'serviceadhesion@edc.asso.fr', "To" => trim($_POST['email']), "Subject" => "[EDC] : Signature de votre bulletin d'adhésion", "Reply to" => 'serviceadhesion@edc.asso.fr'
      );

      // ENVOI DU MAIL A L'PARTENAIRE SI ENVOI A ASSOCIATION OK
      envoiMailSMTP($fichierModel, $t_contenu, $t_infosMail);

      $result = 'Un email a été envoyé à votre client pour la signature du bulletin d\'adhésion';
    } else {  // Doit payer
      $url = URL_WS_ADH . 'v1.0/json/getMontantCotissation';
      $response = \Httpful\Request::get($url)->send();
      if ($response->code == 200) {
        $retval = json_decode($response->body);
      } else {
        return 'Erreur WS';
      }


      $montant = $retval->montant1;
      if ($retval->montant2 > 0) {
        $montant = $retval->montant2;
      }

      $dateFin = explode('-', $retval->date_fin);
      $dateFin = $dateFin[2] . '/' . $dateFin[1] . '/' . $dateFin[0];

      $commande->montant = $montant;
      $commande->date_paiement = date('Y-m-d H:i:s');
      $commande->Save();


      // On affiche le formulaire de paiement

      // --------------- VARIABLES A MODIFIER ---------------

      // Ennonciation de variables
      $pbx_site = '1401683';                  //variable de test 1999888
      $pbx_rang = '01';                  //variable de test 32
      $pbx_identifiant = '292359291';        //variable de test 3
      $pbx_cmd = $commande->id_commande;                //variable de test cmd_test1
      if ($commande->conseiller_email != '') {
        $pbx_porteur = $commande->conseiller_email;
      } else {
        $pbx_porteur = $commande->email;
      }
      $pbx_total = (int)($commande->montant * 100);                  //variable de test 100
      // Suppression des points ou virgules dans le montant
      $pbx_total = str_replace(",", "", $pbx_total);
      $pbx_total = str_replace(".", "", $pbx_total);

      // Paramétrage des urls de redirection après paiement
      $pbx_effectue = $_conf['id_page_cb_ok'];
      $pbx_annule = $_conf['id_page_cb_annule'];
      $pbx_refuse = $_conf['id_page_cb_ko'];
      // Paramétrage de l'url de retour back office site
      $pbx_repondre_a = $_conf['id_page_cb_auto'];
      // Paramétrage du retour back office site
      $pbx_retour = 'ref:R;trans:T;auto:A;montant:M;erreur:E';

      // Connection à la base de données
      // mysql_connect...
      // On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l’on renseigne dans la variable $keyTest;
      //$keyTest = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';
      // DEV $keyTest = '47fc882a87c094faae1e2a4a453909bbc9cc1abce8830ea43be8bed419028974821c95aa8b66751a77f28abfff8dbdad3883016fe94ec553af72bd980df3f8f1';
      $keyTest = 'a4d72d896febad5b0bff3e0f753bb212055f63ca018e58d2c0e007702f5028408497727b98ad997d65b2d8980f95591b94d9e9eb62cec1bf9f159b501f6938e7';

      // --------------- TESTS DE DISPONIBILITE DES SERVEURS ---------------

      $serveurs = array(
        'tpeweb.paybox.com', //serveur primaire
        'tpeweb1.paybox.com'
      ); //serveur secondaire

      //$serveurs = array('preprod-tpeweb.paybox.com');

      $serveurOK = "";
      //phpinfo(); <== voir paybox
      foreach ($serveurs as $serveur) {
        $doc = new DOMDocument();
        $doc->loadHTMLFile('https://' . $serveur . '/load.html');
        $server_status = "";
        $element = $doc->getElementById('server_status');
        if ($element) {
          $server_status = $element->textContent;
        }
        if ($server_status == "OK") {
          // Le serveur est prêt et les services opérationnels
          $serveurOK = $serveur;
          break;
        }
        // else : La machine est disponible mais les services ne le sont pas.
      }
      //curl_close($ch); <== voir paybox
      if (!$serveurOK) {
        return ("Erreur : Aucun serveur n'a été trouvé");
      }
      // Activation de l'univers de préproduction
      //$serveurOK = 'preprod-tpeweb.paybox.com';

      //Création de l'url cgi paybox
      $serveurOK = 'https://' . $serveurOK . '/cgi/MYchoix_pagepaiement.cgi';
      // echo $serveurOK;



      // --------------- TRAITEMENT DES VARIABLES ---------------

      // On récupère la date au format ISO-8601
      $dateTime = date("c");

      // On crée la chaîne à hacher sans URLencodage
      $msg = "PBX_SITE=" . $pbx_site .
        "&PBX_RANG=" . $pbx_rang .
        "&PBX_IDENTIFIANT=" . $pbx_identifiant .
        "&PBX_TOTAL=" . $pbx_total .
        "&PBX_DEVISE=978" .
        "&PBX_CMD=" . $pbx_cmd .
        "&PBX_PORTEUR=" . $pbx_porteur .
        "&PBX_REPONDRE_A=" . $pbx_repondre_a .
        "&PBX_RETOUR=" . $pbx_retour .
        "&PBX_EFFECTUE=" . $pbx_effectue .
        "&PBX_ANNULE=" . $pbx_annule .
        "&PBX_REFUSE=" . $pbx_refuse .
        "&PBX_HASH=SHA512" .
        "&PBX_TIME=" . $dateTime;
      // echo $msg;

      // Si la clé est en ASCII, On la transforme en binaire
      $binKey = pack("H*", $keyTest);

      // On calcule l’empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et //
      // la clé binaire
      // On envoi via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
      // Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne //
      // suivante
      // print_r(hash_algos());
      $hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));

      // La chaîne sera envoyée en majuscule, d'où l'utilisation de strtoupper()
      // On crée le formulaire à envoyer
      // ATTENTION : l'ordre des champs est extrêmement important, il doit
      // correspondre exactement à l'ordre des champs dans la chaîne hachée

      if (isset($dateFin)) {
        $result .= '<p>Cotisation valide jusqu\'au <strong>' . $dateFin . '</strong></p>';
      }

      $result .= '
			<p>Montant : ' . $commande->montant . ' &euro;</p>
			<form method="POST" action="' . $serveurOK . '">
			<input type="hidden" name="PBX_SITE" value="' . $pbx_site . '">
			<input type="hidden" name="PBX_RANG" value="' . $pbx_rang . '">
			<input type="hidden" name="PBX_IDENTIFIANT" value="' . $pbx_identifiant . '">
			<input type="hidden" name="PBX_TOTAL" value="' . $pbx_total . '">
			<input type="hidden" name="PBX_DEVISE" value="978">
			<input type="hidden" name="PBX_CMD" value="' . $pbx_cmd . '">
			<input type="hidden" name="PBX_PORTEUR" value="' . $pbx_porteur . '">
			<input type="hidden" name="PBX_REPONDRE_A" value="' . $pbx_repondre_a . '">
			<input type="hidden" name="PBX_RETOUR" value="' . $pbx_retour . '">
			<input type="hidden" name="PBX_EFFECTUE" value="' . $pbx_effectue . '">
			<input type="hidden" name="PBX_ANNULE" value="' . $pbx_annule . '">
			<input type="hidden" name="PBX_REFUSE" value="' . $pbx_refuse . '">
			<input type="hidden" name="PBX_HASH" value="SHA512">
			<input type="hidden" name="PBX_TIME" value="' . $dateTime . '">
			<input type="hidden" name="PBX_HMAC" value="' . $hmac . '">
			<input type="submit" value="Accéder à l\'interface de paiement de la banque">
			</form>
			';
    }

    return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
  }


  if (isset($reponse) && $reponse != '') {
    $result .= '<p>' . $reponse . '</p>';
  }

  $url_ajax = plugins_url('ajax.php', dirname(__FILE__)) . '?ajax=true';

  if (!isset($_GET['groupe'])) {
    if (count($_SESSION['adherent_infos']->groupes) > 1) {
      $result .= '<h3 style="margin-bottom:50px;">Sélectionnez le réseau pour ce nouvel adhérent</h3>';

      foreach ($_SESSION['adherent_infos']->groupes as $groupe) {
        $result .= '<a style="background-color: #d91733; color:#FFF; padding:10px 20px; margin:20px; display:inline-block;" href="?groupe=' . $groupe->groupe . '&l=' . $groupe->label . '">' . $groupe->groupe . '</a> ';
      }
    } else {
      if (count($_SESSION['adherent_infos']->groupes) == 1) {
        $g = $_SESSION['adherent_infos']->groupes[0];
        $_GET['groupe'] = $g->groupe;
        $_GET['l'] = $g->label;
      } else {
        $_GET['groupe'] = '';
        $_GET['l'] = '';
      }
    }
  }

  if (isset($_GET['groupe'])) {

    $result .= '<form action="" method="POST" name="creer_adherent" id="creer_adherent" class="frm_forms with_frm_style frm_style_style-formidable">
		<input type="hidden" name="update_donnees" value="1" />
		<input type="hidden" name="groupe" value="' . $_GET['groupe'] . '" />
		<input type="hidden" name="groupe_label" value="' . $_GET['l'] . '" />
			
			<div class="row">
			<fieldset style="border:1px solid #EFEFEF; padding:20px; margin-bottom:20px;">
				<legend style="padding:0 10px; background-color:#FFFFFF; color:#cc1632;">Adhérent</legend>
			
				<p><label for="nom" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">NOM* :</label><input type="text" name="nom" id="nom" required="required" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
				<p><label for="prenom" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Prénom* :</label><input type="text" name="prenom" required="required" id="prenom" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
				
				<p><label for="adresse_1" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Adresse 1* :</label><input type="text" name="adresse_1" required="required" id="adresse_1" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
				<p><label for="adresse_2" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Adresse 2 :</label><input type="text" name="adresse_2" id="adresse_2" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
		
				<p>
					<label for="code_postal" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Code postal* :</label>
					<input type="text" id="code_postal" name="code_postal" required="required" value="" style="font-size: 12px; color: #694F40; width:300px;" />
				</p>
				<div>
					<label for="ville" style="float:left; margin-right:10px; line-height:30px; min-width:200px; margin-bottom:20px;">Ville* :</label>
					<span id="ListeVille" data-init_value="" style="font-size: 12px; color: #694F40; width:300px; display:inline-block;"></span>
				</div>
				<div style="clear:both;"></div>
				<p><label for="pays" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Pays* :</label><input type="text" name="pays" required="required" id="pays" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
				
				<p><label for="telephone" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Téléphone portable* :</label><input type="text" name="telephone" id="telephone" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
				
				<p><label for="email" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Email* :</label><input type="text" name="email" id="email" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
			</fieldset>
			
			<fieldset style="border:1px solid #EFEFEF; padding:20px; margin-bottom:20px;">
				<legend style="padding:0 10px; background-color:#FFFFFF; color:#cc1632;">Bien immobilier</legend>
				<p>
					<input type="radio" name="bien_have" id="bien_have_non" value="0" checked="checked"> <label for="bien_have_non" style="margin-right:10px; line-height:30px; display:inline;">Non</label>
					<input type="radio" name="bien_have" id="bien_have_oui" value="1"> <label for="bien_have_oui" style="margin-right:10px; line-height:30px; display:inline;">Oui</label>
				</p>
				<div id="bien_details" style="display:none;">
					<p><label for="bien_residence" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Nom de la résidence :</label><input type="text" name="bien_residence" id="bien_residence" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					<p><label for="bien_adresse_1" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Adresse 1 :</label><input type="text" name="bien_adresse_1" id="bien_adresse_1" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					<p><label for="bien_adresse_2" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Adresse 2 :</label><input type="text" name="bien_adresse_2" id="bien_adresse_2" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
			
					<p>
						<label for="bien_code_postal" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Code postal :</label>
						<input type="text" id="bien_code_postal" name="bien_code_postal" value="" style="font-size: 12px; color: #694F40; width:300px;" />
					</p>
					<div>
						<label for="bien_ville" style="float:left; margin-right:10px; line-height:30px; min-width:200px; margin-bottom:20px;">Ville :</label>
						<span id="bien_ListeVille" data-init_value="" style="font-size: 12px; color: #694F40; width:300px; display:inline-block;"></span>
					</div>
					<div style="clear:both;"></div>
					
					
					<p>
						<label for="bien_lot" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Numéro du lot :</label>
						<input type="text" id="bien_lot" name="bien_lot" value="" style="font-size: 12px; color: #694F40; width:300px;" />
					</p>
					<p>
						<label for="bien_dispositif" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Dispositif fiscal :</label>
						<input type="text" id="bien_dispositif" name="bien_dispositif" value="" style="font-size: 12px; color: #694F40; width:300px;" />
					</p>
					<p>
						<label for="bien_administrateur" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Administrateur de biens :</label>
						<input type="text" id="bien_administrateur" name="bien_administrateur" value="" style="font-size: 12px; color: #694F40; width:300px;" />
					</p>
					
					<p>
						<label for="bien_acte" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Bien act&eacute; :</label>
						<input type="radio" name="bien_acte" id="bien_acte_non" value="0" checked="checked"> <label for="bien_acte_non" style="margin-right:10px; line-height:30px; display:inline;">Non</label>
						<input type="radio" name="bien_acte" id="bien_acte_oui" value="1"> <label for="bien_acte_oui" style="margin-right:10px; line-height:30px; display:inline;">Oui</label>
					</p>
					<div id="bien_acte_details" style="display:none;">
						<p>
							<label for="bien_date_acte" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Date de l\'acte :</label>
							<input type="text" id="bien_date_acte" name="bien_date_acte" value="" style="font-size: 12px; color: #694F40; width:300px;" />
						</p>
					</div>
				</div>
				<div style="clear:both;"></div>
				
			</fieldset>
			';

    $groupeAct = null;
    foreach ($_SESSION['adherent_infos']->groupes as $groupe) {
      if ($groupe->groupe == $_GET['groupe']) {
        $groupeAct = $groupe;
        break;
      }
    }

    $askConseiller = false;
    if ($groupeAct != null && ($groupeAct->ConseillerPropose == 1)) {
      $askConseiller = true;
    }

    if ($askConseiller) {
      $result .= '
				<fieldset style="border:1px solid #EFEFEF; padding:20px; margin-bottom:20px;">
					<legend style="padding:0 10px; background-color:#FFFFFF; color:#cc1632;">Conseiller</legend>
									
					<p><label for="conseiller_nom" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">NOM* :</label><input type="text" name="conseiller_nom" id="conseiller_nom" required="required" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					<p><label for="conseiller_prenom" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Prénom* :</label><input type="text" name="conseiller_prenom" required="required" id="conseiller_prenom" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					<p><label for="conseiller_email" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Email* :</label><input type="text" name="conseiller_email" required="required" id="conseiller_email" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					
				</fieldset>
				';
    }

    $paiementEnLigne = false;
    if ($groupeAct != null && ($groupeAct->PaiementEnLigne == 1)) {
      $paiementEnLigne = true;
    }

    if ($paiementEnLigne) {

      $result .= '
				<fieldset style="border:1px solid #EFEFEF; padding:20px; margin-bottom:20px;">
					<legend style="padding:0 10px; background-color:#FFFFFF; color:#cc1632;">
						Mandat SEPA <input type="radio" class="radio_sepa" name="paiement_sepa" value="1" checked="checked" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Paiement en ligne <input type="radio" class="radio_sepa" name="paiement_sepa" value="0" />
					</legend>
					
					<div id="champs_sepa">					
						<p><label for="iban" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">IBAN* :</label><input type="text" name="iban" id="iban" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
						<p><label for="bic" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">BIC* :</label><input type="text" name="bic" id="bic" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					</div>
					
					<div id="champs_paiement_en_ligne">					
						Paiement en ligne à la prochaine étape.
					</div>
					
				</fieldset>
				';
    } else {
      $result .= '
				<input type="hidden" name="paiement_sepa" value="1" />
				<fieldset style="border:1px solid #EFEFEF; padding:20px; margin-bottom:20px;">
					<legend style="padding:0 10px; background-color:#FFFFFF; color:#cc1632;">Mandat SEPA</legend>
					
					<p><label for="iban" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">IBAN* :</label><input type="text" name="iban" required="required" id="iban" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					<p><label for="bic" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">BIC* :</label><input type="text" name="bic" required="required" id="bic" value="" style="font-size: 12px; color: #694F40; width:300px;"></p>
					
				</fieldset>
				';
    }


    $result .= '
			</div>
			<div style="clear:both;"></div>
			
			<p class="submit frm_submit"><input type="submit" value="Enregistrer l’adhésion" name="submit_password" class="art-button"></p>
			
			<p>* Champs obligatoires</p>
			</form>';
    $result .= '
		<script type="text/javascript">
			var onloadCP = true;
			var bien_onloadCP = true;
			var lastCheck = "";
			var bien_lastCheck = "";
			
			
			function CheckCP()
			{
				//setTimeout(function()
				//{
					var CP=jQuery("#code_postal").val();
					CP.replace(/^\s\s*/, "").replace(/\s\s*$/, "");
					CPint = parseInt(CP);
					var intRegex = /^\d+$/;
					
					if (lastCheck != CP)
					{			
						lastCheck = CP;
						if (CP == "")
						{
							document.getElementById("ListeVille").innerHTML = "<select disabled></select>";
							onloadCP = false;
						}
						else
						{
							if(intRegex.test(CPint))
							{
								if (CP.length > 4)
								{
									//CP = CP.slice(0, -1) //Permet d"enlever le dernier caractère d"une chaine
									jQuery("#ListeVille").load("' . $url_ajax . '&cp_partenaire="+CPint, function( response, status, xhr ) {
										if (onloadCP)
										{
											jQuery("#ville").val(jQuery("#ListeVille").data("init_value"));
											onloadCP = false;
										}
									});
								}
								else
								{
									onloadCP = false;
								}
							}
							else
								onloadCP = false;
						}
					}
				//}, 2000);
			}
			
			function validateIBAN(iban) {
				var newIban = iban.toUpperCase(),
				modulo = function (divident, divisor) {
					var cDivident = "";
					var cRest = "";
					for (var i in divident ) {
					var cChar = divident[i];
					var cOperator = cRest + "" + cDivident + "" + cChar;
					if ( cOperator < parseInt(divisor) ) {
					cDivident += "" + cChar;
					} else {
					cRest = cOperator % divisor;
					if ( cRest == 0 ) {
					cRest = "";
					}
					cDivident = "";
					}
					}
					cRest += "" + cDivident;
					if (cRest == "") {
					cRest = 0;
					}
					return cRest;
				};
				if (newIban.search(/^[A-Z]{2}/gi) < 0) {
					return false;
				}
				newIban = newIban.substring(4) + newIban.substring(0, 4);
				newIban = newIban.replace(/[A-Z]/g, function (match) {
					return match.charCodeAt(0) - 55;
				});
				return parseInt(modulo(newIban, 97), 10) === 1;
			}
			
			function CheckIBAN()
			{
				iban = jQuery("#iban").val()
				
				iban = iban.toUpperCase();
				iban = iban.replace(/\s+/g, "");
				
				
				return validateIBAN(iban);
			}
			
			function isBic(value) {
				return /^([A-Z]{6}[A-Z2-9][A-NP-Z1-9])(X{3}|[A-WY-Z0-9][A-Z0-9]{2})?$/.test( value.toUpperCase() );
			}
			
			function CheckBienCP()
			{
				var CP=jQuery("#bien_code_postal").val();
				CP.replace(/^\s\s*/, "").replace(/\s\s*$/, "");
				CPint = parseInt(CP);
				var intRegex = /^\d+$/;
				
				if (bien_lastCheck != CP)
				{			
					bien_lastCheck = CP;
					if (CP == "")
					{
						document.getElementById("bien_ListeVille").innerHTML = "<select disabled></select>";
						bien_onloadCP = false;
					}
					else
					{
						if(intRegex.test(CPint))
						{
							if (CP.length > 4)
							{
								//CP = CP.slice(0, -1) //Permet d"enlever le dernier caractère d"une chaine
								jQuery("#bien_ListeVille").load("' . $url_ajax . '&cp_partenaire="+CPint, function( response, status, xhr ) {
									jQuery("#bien_ListeVille select").attr("id", "bien_ville");
									jQuery("#bien_ListeVille select").attr("name", "bien_ville");
									
									if (bien_onloadCP)
									{
										jQuery("#bien_ville").val(jQuery("#bien_ListeVille").data("init_value"));
										bien_onloadCP = false;
									}
								});
							}
							else
							{
								bien_onloadCP = false;
							}
						}
						else
							bien_onloadCP = false;
					}
				}
			}
		
			function DisplayBien()
			{
				if (jQuery(\'input[name=bien_have]:checked\').val() == 0)
					jQuery(\'#bien_details\').hide();
				else
					jQuery(\'#bien_details\').show();
			}
			
			function DisplayBienDate()
			{
				if (jQuery(\'input[name=bien_acte]:checked\').val() == 0)
					jQuery(\'#bien_acte_details\').hide();
				else
					jQuery(\'#bien_acte_details\').show();
			}
		
			jQuery("document").ready(
				function(){
					
					jQuery(\'input[type=radio][name=paiement_sepa]\').change(function() {
						if (this.value == 1) {
							jQuery(\'#champs_sepa\').show();
							jQuery(\'#champs_paiement_en_ligne\').hide();
						}
						else{
							jQuery(\'#champs_sepa\').hide();
							jQuery(\'#champs_paiement_en_ligne\').show();
						}
					});
					
					var radioValue = jQuery("input[name=\'paiement_sepa\']:checked"). val();
					if (radioValue == 1) {
							jQuery(\'#champs_sepa\').show();
							jQuery(\'#champs_paiement_en_ligne\').hide();
						}
						else{
							jQuery(\'#champs_sepa\').hide();
							jQuery(\'#champs_paiement_en_ligne\').show();
						}
					
					DisplayBien();
					jQuery(\'input[type=radio][name=bien_have]\').change(function() {
						DisplayBien();
					});
					
					DisplayBienDate();
					jQuery(\'input[type=radio][name=bien_acte]\').change(function() {
						DisplayBienDate();
					});
					
					CheckCP();
					
					//Affiche la liste déroulante des villes en fonction du code postal saisi
					jQuery("#code_postal").keyup(
						function ()
						{
							CheckCP();
						}
					);	
					jQuery("#code_postal").change(
						function ()
						{
							CheckCP();
						}
					);	
					
					CheckBienCP();
					
					//Affiche la liste déroulante des villes en fonction du code postal saisi
					jQuery("#bien_code_postal").keyup(
						function ()
						{
							CheckBienCP();
						}
					);	
					jQuery("#bien_code_postal").change(
						function ()
						{
							CheckBienCP();
						}
					);			
				
					jQuery("#creer_adherent").on(\'submit\', function(e) {
						
						if (jQuery("#telephone").val == "")
						{
							e.preventDefault();
							alert("Téléphone obligatoire");
							return false;
						}
						if (jQuery("#email").val == "")
						{
							e.preventDefault();
							alert("Email obligatoire");
							return false;
						}
						
						var radioValue = jQuery("input[name=\'paiement_sepa\']:checked"). val();
						if (radioValue == 1 || radioValue==undefined)
						{
							if (!CheckIBAN())
							{
								e.preventDefault();
								alert("IBAN incorrect");
								return false;
							}
							
							if (!isBic(jQuery("#bic").val()))
							{
								e.preventDefault();
								alert("BIC incorrect");
								return false;
							}
						}
						
					});
				
				});
		
			</script>';

    $result .= '</div>';
  }



  return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}


function partenaireGetMesDonnees()
{
  if (is_admin()) {
    return;
  }
  $result = '';
  $reponse = '';
  if (isset($_POST['update_donnees']) && $_POST['update_donnees'] == 1) {
    $url = URL_WS_ADH . 'v1.0/json/partenaire/update_donnees';

    $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body(
      'adresse_1=' . trim($_POST['adresse_1']) .
        '&adresse_2=' . trim($_POST['adresse_2']) .
        '&adresse_3=' . trim($_POST['adresse_3']) .
        '&code_postal=' . trim($_POST['code_postal']) .
        '&ville=' . trim($_POST['ville']) .
        '&telephone=' . trim($_POST['telephone'])
    )->send();

    if ($response->code == 200) {
      $_SESSION['adherent_infos'] = json_decode($response->body);
      $reponse = '<strong>Données modifiées</strong>';
    } else {
      $reponse = '<strong>Erreur WS</strong><br />';
    }
  }



  $result .= '<div id="donnesPerso">
		<h1 style="margin-bottom:5px">' . $_SESSION['adherent_infos']->Civilite . ' ' . $_SESSION['adherent_infos']->Prenom . ' ' . $_SESSION['adherent_infos']->Nom . '</h1><br/>';
  // l'adresse principale.

  if ($reponse != '') {
    $result .= '<p>' . $reponse . '</p>';
  }

  $url_ajax = plugins_url('ajax.php', dirname(__FILE__)) . '?ajax=true';

  $result .= '<form action="" method="POST" name="modif_donnees" id="form_donnees" class="frm_forms with_frm_style frm_style_style-formidable">
	<input type="hidden" name="update_donnees" value="1" />
		
		<p><label for="adresse_1" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Adresse 1 :</label><input type="text" name="adresse_1" id="adresse_1" value="' . $_SESSION['adherent_infos']->Adresse1 . '" style="font-size: 12px; color: #694F40; width:300px;"></p>
		<p><label for="adresse_2" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Adresse 2 :</label><input type="text" name="adresse_2" id="adresse_2" value="' . $_SESSION['adherent_infos']->Adresse2 . '" style="font-size: 12px; color: #694F40; width:300px;"></p>
		<p><label for="adresse_3" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Adresse 3 :</label><input type="text" name="adresse_3" id="adresse_3" value="' . $_SESSION['adherent_infos']->Adresse3 . '" style="font-size: 12px; color: #694F40; width:300px;"></p>


		<p>
			<label for="CP" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Code postal :</label>
			<input type="text" id="code_postal" name="code_postal" value="' . $_SESSION['adherent_infos']->CP . '" style="font-size: 12px; color: #694F40; width:300px;" />
		</p>
		<div>
			<label for="ville" style="float:left; margin-right:10px; line-height:30px; min-width:200px; margin-bottom:20px;">Ville :</label>
			<span id="ListeVille" data-init_value="' . $_SESSION['adherent_infos']->Ville . '" style="font-size: 12px; color: #694F40; width:300px; display:inline-block;"></span>
		</div>
		<div style="clear:both;"></div>
		
		<p><label for="telephone" style="float:left; margin-right:10px; line-height:30px; min-width:200px;">Téléphone :</label><input type="text" name="telephone" id="telephone" value="' . $_SESSION['adherent_infos']->Telephone . '" style="font-size: 12px; color: #694F40; width:300px;"></p>
		
		<p class="submit frm_submit"><input type="submit" value="Sauver" name="submit_password" class="art-button"></p>
		</form>';
  $result .= '
    <script type="text/javascript">
		var onloadCP = true;
		var lastCheck = "";
		function CheckCP()
		{
			//setTimeout(function()
			//{
				var CP=jQuery("#code_postal").val();
				CP.replace(/^\s\s*/, "").replace(/\s\s*$/, "");
				CPint = parseInt(CP);
				var intRegex = /^\d+$/;
				
				if (lastCheck != CP)
				{			
					lastCheck = CP;
					if (CP == "")
					{
						document.getElementById("ListeVille").innerHTML = "<select disabled></select>";
						onloadCP = false;
					}
					else
					{
						if(intRegex.test(CPint))
						{
							if (CP.length > 4)
							{
								//CP = CP.slice(0, -1) //Permet d"enlever le dernier caractère d"une chaine
								jQuery("#ListeVille").load("' . $url_ajax . '&cp_partenaire="+CPint, function( response, status, xhr ) {
									if (onloadCP)
									{
										jQuery("#ville").val(jQuery("#ListeVille").data("init_value"));
										onloadCP = false;
									}
								});
							}
							else
							{
								onloadCP = false;
							}
						}
						else
							onloadCP = false;
					}
				}
			//}, 2000);
		}
	
		jQuery("document").ready(
			function(){
				
				CheckCP();
				
				//Affiche la liste déroulante des villes en fonction du code postal saisi
				jQuery("#code_postal").keyup(
					function ()
					{
						CheckCP();
					}
				);			
			});
	
		</script>';

  $result .= '</div>';




  return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}

function partenaireGetChangeAdresse()
{
  if (is_admin()) {
    return;
  }
  if ($_SESSION['adherent_infos']->Adresse1 == '' && $_SESSION['adherent_infos']->CP != '') {
    $_SESSION['adherent_infos']->Adresse1 = '-';
  }

  /*
    if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35')
    {
        $_SESSION['adherent_infos']->Adresse1 = 'route';
        print_r($_SESSION['adherent_infos']);
    }
    */

  $result = '';

  if (isset($_POST['Adresse1'])) {
    $search   = array("\\", "'");
    $replace = array("", "''");

    $IdContact = $_POST["IdContact"];
    $IdAdhesion = $_POST["IdAdhesion"];
    $Adresse = str_replace($search, $replace, $_POST["Adresse"]);
    $Adresse1 = str_replace($search, $replace, $_POST["Adresse1"]);
    $Adresse2 = str_replace($search, $replace, $_POST["Adresse2"]);
    $Adresse3 = str_replace($search, $replace, $_POST["Adresse3"]);
    $IdCommune = $_POST["IdCommune"];
    $CP = $_POST["CP"];
    $ValueIdPays = $_POST["ValueIdPays"];
    $ValuePays = $_POST["ValuePays"];
    $PaysById = $_POST["PaysById"];
    $VilleManuelle = str_replace($search, $replace, $_POST["VilleManuelle"]);
    $AdresseEtranger = str_replace($search, $replace, $_POST["AdresseEtranger"]);
    $CheckVilleManuelle = $_POST["CheckVilleManuelle"];
    $AncienneAdresse = str_replace($search, $replace, $_POST["AncienneAdresse"]);
    $NouvelleAdresse = str_replace($search, $replace, $_POST["NouvelleAdresse"]);
    $ChoixDiffusionAdresse = $_POST["ChoixDiffusionAdresse"];

    $InfoMAJEureka = 0; // ME PERMET DE SAVOIR JE PEUX EFFECTUER UNE MISE A JOUR DE LA BDD.
    $InfoCreationFicheEureka = 0; // ME PERMET DE SAVOIR SI JE DOIS EFFECTUER UNE CREATION DE FICHE POUR LE SERVICE ADMINISTRATIF.

    // SI L'PARTENAIRE A COCHE LA CASE "JE NE TROUVE PAS LA VILLE DANS CETTE LISTE"
    if ($CheckVilleManuelle != 0 && $CheckVilleManuelle != "") {
      $InfoMAJEureka++;
      $InfoCreationFicheEureka++;
    }

    // SI L'PARTENAIRE A CHOISI QUE SA NOUVELLE ADRESSE DEVAIT ETRE DIFFUSEE AUX INTERVENANTS DE SON INVESTISSEMENT
    if ($ChoixDiffusionAdresse != "edc") {
      $InfoCreationFicheEureka++;
    }

    //SI ADRESSE ETRANGER, JE NE DOIS PAS METTRE A JOUR L'ADRESSE EN BDD
    if ($ValuePays == "etranger") {
      $InfoMAJEureka++;
      $InfoCreationFicheEureka++;
    }

    //TEST DE VARIABLE. ME PERMET DE CONNAITRE SI JE PEUX MODIFIER LA BDD.
    //SI = 0, JE PEUX FAIRE UN UPDATE OU UN INSERT
    //SI > 0, JE NE PEUX RIEN FAIRE
    if ($InfoMAJEureka == 0) {
      //SI ADRESSE NON VIDE
      if ($_SESSION['adherent_infos']->Adresse1 != '') {
        //UPDATE ADRESSE

        //if ($_SESSION['adherent_infos']->NumAdhesion == '47749')
        $url = URL_WS_ADH . 'v1.0/json/user/insert_adresse';
        //else
        //	$url = URL_WS_ADH.'v1.0/json/user/update_adresse';

        $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('AdresseComplement1=' . trim($Adresse1) . '&AdresseComplement2=' . trim($Adresse2) . '&AdresseComplement3=' . trim($Adresse3) . '&IdCommune=' . trim($IdCommune))->send();
        if ($response->code == 200) {
        } else {
          //print_r($response);
          mail('laurent@wasabi-artwork.com', 'EDC - Erreur - update_adresse', print_r($response, true) . print_r($_POST, true));
          return '<strong>Erreur WS</strong>';
        }
      } else {
        //SINON ADRESSE VIDE
        //INSERTION DE L'ADRESSE
        $url = URL_WS_ADH . 'v1.0/json/user/insert_adresse';

        $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('AdresseComplement1=' . trim($Adresse1) . '&AdresseComplement2=' . trim($Adresse2) . '&AdresseComplement3=' . trim($Adresse3) . '&IdCommune=' . trim($IdCommune))->send();
        if ($response->code == 200) {
          //echo '1';
        } else {
          mail('stephane.morillon@smorillon.com', 'EDC - Erreur - insert_adresse', print_r($response, true) . print_r($_POST, true));
          //print_r($response);
          return '<strong>Erreur WS</strong>';
        }
      }
    }

    //--------------------------------------------------------------------------------------
    //-------------INSERTION D'UNE ACTION WEB DANS LA FICHE SUIVI DE L'ADHESION-------------
    //--------------------------------------------------------------------------------------

    //RECUPERATION DE L'ID DE LA FICHE SUIVI DE L'ADHESION
    $url = URL_WS_ADH . 'v1.0/json/user/GetIdFicheSuiviAdhesion';
    $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('InfoCreationFicheEureka=' . trim($InfoCreationFicheEureka) . '&AncienneAdresse=' . trim($AncienneAdresse) . '&NouvelleAdresse=' . trim($NouvelleAdresse) . '&IdCommune=' . trim($IdCommune) . '&ValuePays=' . trim($ValuePays) . '&CheckVilleManuelle=' . trim($CheckVilleManuelle) . '&ChoixDiffusionAdresse=' . trim($ChoixDiffusionAdresse))->send();



    if ($response->code == 200) {
      $IdFiche = json_decode($response->body);
    } else {
      //print_r($response);
      return '<strong>Erreur WS</strong>';
    }


    //-------------------------------------------------------
    //-------------DEBUT PARTIE ENVOI DES MAILS -------------
    //-------------------------------------------------------

    //FORMATAGE DES VARIABLES POUR AFFICHAGE CORRECT DANS MAILS
    $AncienneAdresse = str_replace("''", "'", $AncienneAdresse);
    $NouvelleAdresse = str_replace("''", "'", $NouvelleAdresse);

    $UpdateAdresseKO = 0; // Me permet de savoir quand insérer un commentaire pour dire que l'adresse n'a pas été mise à jour

    if (isset($ValuePays) && $ValuePays == "etranger") {
      $Observation .= "L''adresse que l''adhérent a renseigné ne se trouve pas en France Métropolitaine ou DOM-TOM.\n";
      $UpdateAdresseKO++;
    }

    if ($CheckVilleManuelle != 0 && $CheckVilleManuelle != "") {
      $Observation .= "L''adhérent a inséré une ville manuellement. Cela signifie que cette ville n''existe pas sous Eureka. Il sera nécessaire de valider cette commune avant de l''intégrer dans la base de données via le site :http://www.laposte.fr/sna/rubrique.php3?id_rubrique=59.\n";
      $UpdateAdresseKO++;
    }

    if ($UpdateAdresseKO > 0) {
      // VARIABLE SERVANT POUR ACTION DE LA FICHE CHANGEMENT ADRESSE
      //$Observation .="La nouvelle adresse de l''adhérent n''a pas été renseignée dans Eureka.\n";

      //VARIABLE SERVANT POUR LE MAIL POUR LE SERVICE ADMIN (TEMPORAIRE)
      $ActionEDC = '<p style="color:#FF0000;">L\'adresse n\'a pas été automatiquement mise à jour sur Eureka.</p>';
      $ActionEDC .= '<p>' . nl2br(str_replace("''", "'", $Observation)) . '</p>';


      $ActionADH = '<p>Votre adresse n\'a pas été automatiquement mise à jour dans notre base de données.</p>
						<p>Une intervention de notre service administratif est nécessaire afin de valider votre demande.</p>';

      //VARIABLE POUR LE MESSAGE DE FIN AVANT LA REDIRECTION
      $messageFin = '<p style="font-size:12pt; color:#FF0000;">Nous n\'avons pas pu mettre à jour votre adresse dans notre base de données car une intervention manuelle est nécessaire.</p>';
      $messageFin .= '<p style="font-size:12pt; color:#FF0000;">Néanmoins, soyez assurés que votre demande a bien été prise en compte et qu\'un dossier a été ouvert et transféré dans le service concerné afin de mettre à jour votre adresse dans les meilleurs délais.';
      $TimeOut = 10;
    } else {
      $Observation .= "La nouvelle adresse de l''adhérent a déjà été renseignée dans Eureka.\n";
      $ActionEDC = '<p style="color:#009900;">L\'adresse a été automatiquement mise à jour sur Eureka.</p>';
      $ActionADH = '<p style="color:#009900;">Votre adresse a été automatiquement mise à jour dans notre base de données.</p>';
      $TimeOut = 5;
    }

    if ($ChoixDiffusionAdresse != "edc") {
      $ActionEDC .= '<p style="color:#FF0000;">L\'adhérent souhaite qu\'EDC communique sa nouvelle adresse à ses différents interlocuteurs.</p>';
      $ActionADH .= '<p>Nous engageons une proc&eacute;dure d&acute;information aupr&egrave;s des intervenants de votre (ou vos) investissement(s). Celle ci concerne :</p>';
      $ActionADH .= '<p>Pour ce qui est du changement aupr&egrave;s des organismes d&acute;&eacute;tat nous vous invitons &agrave; effectuer la proc&eacute;dure sur le site du gouvernement : <a style="color: #e4680c;" href="https://mdel.mon.service-public.fr/je-change-de-coordonnees.html">https://mdel.mon.service-public.fr/je-change-de-coordonnees.html</a></p>
							<p>Notre d&eacute;lai de traitement de votre demande est actuellement de 15 jours pour les changements d&acute;adresse postale.</p>';
      $Observation .= "L''adhérent souhaite que l''association communique sa nouvelle adresse aux différents intervenants de son (ses) investissement(s).\n";
    } else {
      $Observation .= "Le changement d''adresse ne concerne que l''association.\n";
      $ActionEDC .= '<p style="color:#009900;">Il n\'est pas nécesaire de diffuser la nouvelle adresse de l\'adhérent.</p>';
    }

    //MODELE DE COURRIER POUR L'ASSOCIATION
    $fichierModel = WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_EDC_modification_adresse_postale.html";

    //JE REMPLACE CERTAINES VARIABLES DANS LE MODELE
    $t_contenu = array(
      0 => array("search" => "[NumAdhesion]", "replace" => $_SESSION['adherent_infos']->NumAdhesion), 1 => array("search" => "[DateAction]", "replace" => date("d/m/Y H:i:s")), 2 => array("search" => "[Civilite]", "replace" => $_SESSION['adherent_infos']->Civilite), 3 => array("search" => "[Prenom]", "replace" => $_SESSION['adherent_infos']->Prenom), 4 => array("search" => "[Nom]", "replace" => $_SESSION['adherent_infos']->Nom), 5 => array("search" => "[AncienneAdresse]", "replace" => $AncienneAdresse), 6 => array("search" => "[NouvelleAdresse]", "replace" => $NouvelleAdresse), 7 => array("search" => "[Action]", "replace" => $ActionEDC)
    );


    //J'ENVOI LE MAIL A L'ASSOCIATION
    $t_infosMail = array(
      "From" => 'noreply@assoedc.com', "To" => 'administratif@edc.asso.fr', "Subject" => "[EXTRANET PARTENAIRE EDC] : Changement d'adresse postale", "Reply to" => $_SESSION['adherent_infos']->Email
    );

    // ENVOI DU MAIL A L'PARTENAIRE SI ENVOI A ASSOCIATION OK
    if (envoiMail($fichierModel, $t_contenu, $t_infosMail) == true) {
      $fichierModel = WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_ADH_modification_adresse_postale.html";

      $t_contenu = array(
        0 => array("search" => "[NumAdhesion]", "replace" => $_SESSION['adherent_infos']->NumAdhesion), 1 => array("search" => "[DateAction]", "replace" => date("d/m/Y H:i:s")), 2 => array("search" => "[Civilite]", "replace" => $_SESSION['adherent_infos']->Civilite), 3 => array("search" => "[Prenom]", "replace" => $_SESSION['adherent_infos']->Prenom), 4 => array("search" => "[Nom]", "replace" => $_SESSION['adherent_infos']->Nom), 5 => array("search" => "[AncienneAdresse]", "replace" => $AncienneAdresse), 6 => array("search" => "[NouvelleAdresse]", "replace" => $NouvelleAdresse), 7 => array("search" => "[Action]", "replace" => $ActionADH)
      );

      $t_infosMail = array(
        "From" => 'noreply@assoedc.com', "To" => $_SESSION['adherent_infos']->Email, "Subject" => "[EXTRANET PARTENAIRE EDC] : Prise en compte de votre changement d'adresse postale"
      );

      envoiMail($fichierModel, $t_contenu, $t_infosMail);
    }

    $result .= '<p style="font-size:12pt; color:#006600;">Votre demande de changement d\'adresse a bien été prise en compte.</p>';
    $result .= '<p style="font-size:12pt; color:#006600;">Un email de confirmation vous a été envoyé. Celui-ci contient les informations que vous nous avez communiquées.</p>';
    $result .= $messageFin;

    $url = URL_WS_ADH . 'v1.0/json/user/infos';
    $response = \Httpful\Request::get($url)->authenticateWith($_SESSION['adherent_login'], $_SESSION['adherent_pass'])->send();
    if ($response->code == 200) {
      $_SESSION['adherent_infos'] = json_decode($response->body);
    }
  } elseif (isset($_POST['Cmpl4'])) {
    $search   = array("\\");
    $replace = array("");

    $AncienneAdresse = '';

    if ($_SESSION['adherent_infos']->Adresse1 != '') {
      if ($_SESSION['adherent_infos']->Adresse1 != '') {
        $AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse1 . '<br/>';
      }
      if ($_SESSION['adherent_infos']->Adresse2 != '') {
        $AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse2 . '<br/>';
      }
      if ($_SESSION['adherent_infos']->Adresse3 != '') {
        $AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse3 . '<br/>';
      }
      if ($_SESSION['adherent_infos']->CP != '') {
        $AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->CP . ' ' . $_SESSION['adherent_infos']->Ville . '<br/>';
      }
      if ($_SESSION['adherent_infos']->Pays != '') {
        $AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Pays . '';
      }
    }

    $Adresse = $_SESSION['adherent_infos']->Adresse1; //Me permet de récupérer l'ancienne Adresse1 pour savoir si update ou insert si modif
    $Cmpl4 = str_replace($search, $replace, mb_strtoupper($_POST["Cmpl4"])); //Appt, Etage, Escalier, Chez...
    $Cmpl3 = str_replace($search, $replace, mb_strtoupper($_POST["Cmpl3"])); //Immeuble, batiment, résidence
    $Cmpl2 = str_replace($search, $replace, mb_strtoupper($_POST["Cmpl2"])); //Numéro et nom de la rue
    $Cmpl1 = str_replace($search, $replace, mb_strtoupper($_POST["Cmpl1"])); //Mention spéciale, lieu dit, boite postale
    $CP = $_POST["CP"];
    $IdCommune = $_POST["ValueIdCommune"];
    $ValuePays = $_POST["ValuePays"]; // Permet de savoir si nouvelle adresse en france ou à l'étranger
    $ValueIdPays = $_POST["ValueIdPays"]; // Permet de récupérer l'Id du pays sélectionné
    $AdresseEtranger = str_replace($search, $replace, mb_strtoupper($_POST["AdresseEtranger"])); //Permet de récupérer le textarea qui contient l'adresse dans un pays étranger
    $CheckVilleManuelle = $_POST["CheckVilleManuelle"]; // Permet de savoir si l'adhérent à coché la case "Ma ville n'est pas dans cette liste"
    $VilleManuelle = str_replace($search, $replace, mb_strtoupper($_POST["VilleManuelle"])); // Récupère la valeur de la ville saisie

    //Par défaut, j'ai une ligne d'adresse (numéro et nom de la voie)
    $ligneAdresse = 1;
    $Adresse3 = '';
    $Adresse2 = '';
    $Adresse1 = '';

    $order   = array("ESCALIER", " APT ", "APPARTEMENT", " RES ", " RÉS ", "RÉSIDENCE ", "BATIMENT ", "BÂTIMENT ", " BAT ");
    $replace = array("ESC", " APPT ", "APPT", " RÉSIDENCE ", " RÉSIDENCE ", "RÉSIDENCE ", "BÂT ", "BÂT ", " BÂT ");
    $Cmpl4 = str_replace($order, $replace, $Cmpl4);
    $Cmpl3 = str_replace($order, $replace, $Cmpl3);


    //Me permet de savoir le nombre de ligne d'une adresse
    if ($Cmpl4 != '') {
      $ligneAdresse++;
    }
    if ($Cmpl3 != '') {
      $ligneAdresse++;
    }
    if ($Cmpl1 != '') {
      $ligneAdresse++;
    }

    //Affectation des complément d'adresse
    switch ($ligneAdresse) {
        //Une seule ligne qui correspond au numéro et nom de voie
      case 1:
        $Adresse1 = $Cmpl2;
        break;

        //Si 2 lignes d'adresse
      case 2:
        //Si présence d'un lieu dit
        if ($Cmpl1 != '') {
          $Adresse1 = $Cmpl2;
          $Adresse2 = $Cmpl1;
        }
        //Si présence d'une résidence
        if ($Cmpl3 != '') {
          $Adresse1 = $Cmpl3;
          $Adresse2 = $Cmpl2;
        }
        if ($Cmpl4 != '') {
          $Adresse1 = $Cmpl4;
          $Adresse2 = $Cmpl2;
        }
        break;

        //Si 3 lignes d'adresse
      case 3:
        //Si présence d'un lieu dit
        if ($Cmpl1 != '') {
          $Adresse3 = $Cmpl1;
          $Adresse2 = $Cmpl2;
          if ($Cmpl3 != '') {
            $Adresse1 = $Cmpl3;
          } else {
            $Adresse1 = $Cmpl4;
          }
        } else {
          //Si non présence d'un lieu dit, je peux en déduire toutes les lignes
          $Adresse3 = $Cmpl2;
          $Adresse2 = $Cmpl3;
          $Adresse1 = $Cmpl4;
        }
        break;

        //Si 4 lignes (CAS qui ne doit pas exister normalement)
      case 4:
        $Adresse3 = $Cmpl1;
        $Adresse2 = $Cmpl2;
        $Adresse1 = $Cmpl3 . ', ' . $Cmpl4;
        break;
    }

    $ValueIdPays = 0;

    //Récupération de la ville par l'IdCommune
    $url = URL_WS_ADH . 'v1.0/json/getVille/' . $IdCommune;
    $response = \Httpful\Request::get($url)->send();
    if ($response->code == 200) {
      $villeWS = json_decode($response->body);
      $val = 0;
      if (isset($villeWS->ville->$val)) {
        $VilleById = $villeWS->ville->$val;
      } else {
        $VilleById = $villeWS->ville;
      }

      if ($ValuePays == 'france') {
        $val = 0;
        if (isset($villeWS->pays->$val)) {
          $ValueIdPays = $villeWS->pays->$val;
        } else {
          $ValueIdPays = $villeWS->pays;
        }
      }
    }

    $url = URL_WS_ADH . 'v1.0/json/getPays/' . $ValueIdPays;
    $response = \Httpful\Request::get($url)->send();
    if ($response->code == 200) {
      $p = json_decode($response->body);
      $val = 0;
      if (isset($p->$val)) {
        $PaysById = $p->$val;
      } else {
        $PaysById = $p;
      }
    } else {
      $PaysById = '';
    }
    //NOUVELLE ADRESSE
    //FORMATAGE SPECIFIQUE SI ZONE = FRANCE OU ZONE = ETRANGER
    if ($ValuePays == 'france') {
      $NouvelleAdresse = '';
      if (isset($Adresse1) && $Adresse1 != '') {
        $NouvelleAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $Adresse1;
      }
      if (isset($Adresse2) && $Adresse2 != '') {
        $NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $Adresse2;
      }
      if (isset($Adresse3) && $Adresse3 != '') {
        $NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $Adresse3;
      }


      //Si selection d'une ville dans la liste déroulante
      if (($CheckVilleManuelle == 0) || ($CheckVilleManuelle == "")) {
        $NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $CP . ' ' . $VilleById;
      } else {
        $NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $CP . ' ' . $VilleManuelle;
      }
      $NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $PaysById;
    } else {
      $order   = array("\r\n", "\n", "\r");
      $replace = '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      $AdresseEtranger = str_replace($order, $replace, $AdresseEtranger);
      $NouvelleAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $AdresseEtranger;
      $NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $PaysById;
    }
    $result .= '	<h2>Veuillez trouver ci-après le récapitulatif des données saisies.</h2>';
    $result .= '	<p style="font-size:12pt; font-weight:bold;">' . $NouvelleAdresse . '</p>';
    $result .= '	<form name="ValideDonnees" id="ValideDonnees" method="post" style="width:100%;">
						<input name="Adresse" type="hidden" id="Adresse" value="' . $Adresse . '"/>
						<input name="Adresse1" type="hidden" id="Adresse1" value="' . $Adresse1 . '"/>
						<input name="Adresse2" type="hidden" id="Adresse2" value="' . $Adresse2 . '"/>
						<input name="Adresse3" type="hidden" id="Adresse3" value="' . $Adresse3 . '"/>
						<input name="IdCommune" type="hidden" id="IdCommune" value="' . $IdCommune . '"/>
						<input name="CP" type="hidden" id="CP" value="' . $CP . '"/>
						<input name="ValueIdPays" type="hidden" id="ValueIdPays" value="' . $ValueIdPays . '"/>
						<input name="ValuePays" type="hidden" id="ValuePays" value="' . $ValuePays . '"/>
						<input name="PaysById" type="hidden" id="PaysById" value="' . $PaysById . '"/>
						<input name="VilleManuelle" type="hidden" id="VilleManuelle" value="' . $VilleManuelle . '"/>
						<input name="AdresseEtranger" type="hidden" id="AdresseEtranger" value="' . $AdresseEtranger . '"/>
						<input name="CheckVilleManuelle" type="hidden" id="CheckVilleManuelle" value="' . $CheckVilleManuelle . '"/>
						<input name="NouvelleAdresse" type="hidden" id="NouvelleAdresse" value="' . $NouvelleAdresse . '"/>
						<input name="AncienneAdresse" type="hidden" id="AncienneAdresse" value="' . $AncienneAdresse . '"/>
						<input name="valide" type="hidden" id="valide" value="1"/>
						
						<input type="hidden" name="ChoixDiffusionAdresse" id="ChoixDiffusionAdresse" value="edc" />
						<!--
						<table style="font-size:12pt;">
							<tr>
								<td style="vertical-align:middle;">
									<input class="saisie" type="radio" name="ChoixDiffusionAdresse" id="ChoixDiffusionAdresse" value="edc" checked/>
								</td>
								<td style="vertical-align:middle;">J\'informe uniquement l\'association de mon changement d\'adresse.</td>
							</tr>
							<tr>
								<td style="vertical-align:middle;">
									<input class="saisie" type="radio" name="ChoixDiffusionAdresse" id="ChoixDiffusionAdresse" value="partenaire" />
								</td>
								<td style="vertical-align:middle;">Je souhaite que l\'association transmette mon changement d\'adresse aux différents intervenants de mon ou mes investissement(s).</td>
							</tr>
						</table>
						-->
						
						<table style="width: 100%;">
							<tr>
								<td style="text-align: center; width: 50%;">
									<input type="submit" class="art-button" name="submit_form" value="Valider ma nouvelle adresse">
								</td>
							</tr>
						</table>
					</form>';
    $result .= '	<p style="font-size:12pt;">Si les informations enregistrées sont exactes, nous vous remercions de confirmer votre changement d\'adresse en cliquant sur le bouton ci-dessous. Sinon cliquez <a href="' . $url_mes_donnees . '" style="font-size:12pt;">sur ce lien</a> pour retourner sur la page de vos informations personnelles.</p>';
    return $result;
  } else {

    //Affichage de l'adresse actuelle
    if ($_SESSION['adherent_infos']->Adresse1 != '') {
      $result .= '<p style="font-size:16pt;">Mon adresse actuelle</p>';
      if ($_SESSION['adherent_infos']->Adresse1 != '') {
        $result .= '<span style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse1 . '<br/>';
      }
      if ($_SESSION['adherent_infos']->Adresse2 != '') {
        $result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse2 . '<br/>';
      }
      if ($_SESSION['adherent_infos']->Adresse3 != '') {
        $result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse3 . '<br/>';
      }
      if ($_SESSION['adherent_infos']->CP != '') {
        $result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->CP . ' ' . $_SESSION['adherent_infos']->Ville . '<br/>';
      }
      if ($_SESSION['adherent_infos']->Pays != '') {
        $result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Pays . '</span>';
      }
    }

    //Choix de la zone géographique pour la nouvelle adresse
    $result .= '	<p style="font-size:16pt;">Ma nouvelle adresse se situe</p>
					<table style="font-size:12pt;">
					<tr>
						<td style="vertical-align:middle;">
							<input class="saisie" type="radio" name="zoneGeo" id="zoneGeo" value="france" onClick="ZoneGeoNouvelleAdresse(this.value)"/>
						</td>
						<td style="vertical-align:middle;">En france (Métropolitaine ou DOM-TOM)</td>
					</tr>
					<tr>
						<td style="vertical-align:middle;">
							<input class="saisie" type="radio" name="zoneGeo" id="zoneGeo" value="etranger" onClick="ZoneGeoNouvelleAdresse(this.value)"/>
						</td>
						<td style="vertical-align:middle;">A l\'étranger</td>
					</tr>
					</table>
					<br/>';

    //Affichage du formulaire
    $result .= '	<form name="UpdateDonnees" id="UpdateDonnees" method="post" action="" onSubmit="return(change_adresse(this));" style="width:100%;">
					<input name="ValueIdCommune" type="hidden" id="ValueIdCommune" value="0"/>
					<input name="ValueIdPays" type="hidden" id="ValueIdPays" value="0"/>
					<input name="ValuePays" type="hidden" id="ValuePays" value=""/>
	
					<div id="ZoneGeoFrance" style="display:none;">
					<p style="font-size:12pt; text-decoration:underline;">Les champs marqués d\'un <span style="color:red; font-weight:bold;">*</span> sont obligatoires</p>
					
					<p>
						<label for="Cmpl4">Appartement, étage, escalier, chez ...</label>
						<input style="margin-bottom:0px" type="text" id="Cmpl4" name="Cmpl4" value="" size="40"/>
					</p>
					<p>
						<label for="Cmpl3">Immeuble, bâtiment, résidence</label>
						<input style="margin-bottom:0px" type="text" id="Cmpl3" name="Cmpl3" value="" size="40"/>
					</p>
					<p>
						<label for="Cmpl2">Numéro & nom de la rue <span style="color:red; font-weight:bold;">*</span></label>
						<input style="margin-bottom:0px" type="text" id="Cmpl2" name="Cmpl2" value="" size="40"/>
					</p>
					<p>
						<label for="Cmpl1">Mention spéciale, lieu-dit, boîte postale</label>
						<input style="margin-bottom:0px" type="text" id="Cmpl1" name="Cmpl1" value="" size="40"/>
					</p>
					<p>
						<label for="CP">CP <span style="color:red; font-weight:bold;">*</span></label>
						<input type="text" id="CP" name="CP" value="" maxlength="5" size="4" />
					</p>
					<p id="tr_ville" style="display:none;">
						<label for="Cmpl4">Ville <span style="color:red; font-weight:bold;">*</span></label>
						<div id="ListeVille" name="ListeVille">
						</div>
					</p>
					<p id="tr_checkbox" style="display:none;">
						<input style="margin-bottom:0px; vertical-align:middle; font-size:11pt; text-align:right;" type="checkbox" name="CheckVilleManuelle" id="CheckVilleManuelle" value="0" onClick="ChoixVilleManuelle()" />
						<label for="CheckVilleManuelle">Ma ville n\'est pas dans cette liste</label>
					</p>
					<p id="tr_ville_inconnue" style="display:none;">
						<label for="VilleManuelle">Ville <span style="color:red; font-weight:bold;">*</span></label>
						<input style="margin-bottom:0px" type="text" value="" id="VilleManuelle" name="VilleManuelle" />
					</p>
					</div>			
					<div id="ZoneGeoEtranger" style="display:none;">
					<p style="font-size:12pt; text-decoration:underline;">Adresse</p>
						<table style="font-size:12pt;">
							<tr>
								<td colspan="3" style="vertical-align:top;"><textarea rows="6" cols="50" id="AdresseEtranger" name="AdresseEtranger"></textarea></td>
							</tr>
							<tr id="tr_error_etranger" style="display:none;">	
								<td colspan="3"><div id="div_err_adresse_etranger" style="font-size: 10pt; font-weight:bold; color:red; display:none;"></div><td>
							</tr>					
							<tr>
								<td style="vertical-align:middle;">Pays</td>
								<td style="vertical-align:middle;">' . getListePays() . '</td>
								<td style="vertical-align:middle;"><div id="div_err_pays_list" style="font-size: 10pt; font-weight:bold; color:red; display:none;"></div></td>							
							</tr>
							
						</table>
					</div>
					<br/>
					<div id="ValidationForm" style="display:none;">
						<input name="valide" type="hidden" id="valide" value="0" />
						<table style="width: 100%;">
							<tr>
								<td style="text-align: center; width: 50%;">
									<input type="submit" class="art-button" name="submit_form" value="Enregistrer ma nouvelle adresse">
								</td>
							</tr>
						</table>
					</div>
					
					</form>';

    $url_ajax = plugins_url('ajax.php', dirname(__FILE__)) . '?ajax=true';

    return '<div style="font-size:12pt;">
		<script type="text/javascript">
		jQuery("document").ready(
			function(){
				//Affiche la liste déroulante des villes en fonction du code postal saisi
				jQuery("#CP").keyup(
					function ()
					{
						//setTimeout(function()
						//{
							var CP=jQuery("#CP").val();
							CP.replace(/^\s\s*/, "").replace(/\s\s*$/, "");
							var intRegex = /^\d+$/;
							if (CP == "")
							{
								document.getElementById("ListeVille").innerHTML = "<select disabled></select>";
							}
							else
							{
								if(intRegex.test(CP))
								{
									if (CP.length > 4)
									{
										//CP = CP.slice(0, -1) //Permet d"enlever le dernier caractère d"une chaine
										document.getElementById("tr_ville").style.display="table-row";
										
										
										
										jQuery("#ListeVille").load("' . $url_ajax . '&cp="+CP);
										document.getElementById("tr_checkbox").style.display="table-row";
									}
									else
									{
										document.getElementById("tr_checkbox").style.display="none";
										document.getElementById("tr_ville").style.display="none";
										document.getElementById("tr_ville_inconnue").style.display="none";
										jQuery("#CheckVilleManuelle").attr("checked", false);
									}
								}
							}
						//}, 2000);
					}
				);			
			});
	
		</script>' . $result . '</div>';
  }
}

function partenaireFicheClient()
{
  if (isset($_GET['ficheid'])) {
    $url = URL_WS_ADH . 'v1.0/json/user/getFiche/' . $_GET["ficheid"];
    $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
    if ($response->code == 200) {
      $retval = json_decode($response->body);
    } else {
      return 'Erreur WS';
    }

    //var_dump($retval);

    if (isset($retval->fiche->Gestionnaire) && is_object($retval->fiche->Gestionnaire)) {
      $retval->fiche->Gestionnaire = '';
    }

    $fiche = array(
      'FicheId' => isset($retval->fiche->FicheId) ? (string)$retval->fiche->FicheId : '', 'FicheRef' => isset($retval->fiche->FicheRef) ? (string)$retval->fiche->FicheRef : '', 'DatePriseEnCompte' => isset($retval->fiche->DatePriseEnCompte) ? date_to_string((string)$retval->fiche->DatePriseEnCompte) : '', 'DateCloture' => isset($retval->fiche->DateCloture) ? date_to_string((string)$retval->fiche->DateCloture) : '', 'Domaine' => isset($retval->fiche->Domaine) ? (string)$retval->fiche->Domaine : '', 'SousDomaine' => isset($retval->fiche->SousDomaine) ? (string)$retval->fiche->SousDomaine : '', 'Gestionnaire' => isset($retval->fiche->Gestionnaire) ? (string)$retval->fiche->Gestionnaire : '', 'Statut' => isset($retval->fiche->Statut) ? (string)$retval->fiche->Statut : '', 'Programme' => isset($retval->fiche->Programme) ? (string)$retval->fiche->Programme : '', 'Lot' => isset($retval->fiche->Lot) ? (string)$retval->fiche->Lot : '', 'DateDerniereAction' => isset($retval->fiche->DateDerniereAction) ? date_to_string((string)$retval->fiche->DateDerniereAction) : '', 'lib_statut' => isset($retval->fiche->lib_statut) ? (string)$retval->fiche->lib_statut : '', 'lib_histo' => isset($retval->fiche->lib_histo) ? (string)$retval->fiche->lib_histo : ''
    );
    //Affichage des informations g�n�rales du dossier(fiche)
    //print_r($fiche);
    $result .= '<div id="dossier">';
    if ($fiche['lib_statut'] == 1) {
      $result .= '<div align="center" style="background-color: #eeeeee">Ce dossier a &eacute;t&eacute; cl&ocirc;tur&eacute; le ' . $fiche['DateCloture'] . '</div>';
    } else {
      $result .= '<div style="background-color:#C4DEF1">Dossier en cours de traitement</div>';
    }

    if ($fiche['SousDomaine'] == '') {
      $result .= '<h2>' . $fiche['Domaine'] . '  (ref : ' . $fiche['FicheRef'] . ')</h2>';
    } else {
      $result .= '<h2>' . $fiche['SousDomaine'] . ' (ref : ' . $fiche['FicheRef'] . ')</h2>';
    }

    if ($fiche['DatePriseEnCompte'] != '' || $fiche['DateDerniereAction'] != '' || $fiche['Statut'] != '' || $fiche['Gestionnaire'] != '') {
      $result .= '<div style="font-size:small;margin-left:25px;align:right">
							<ul>';

      if ($fiche['Programme'] != '' &&  $fiche['Lot'] != '') {
        $result .= '<li><em>concerne mon investissement <strong>' . $fiche['Programme'] . ' Lot : ' . $fiche['Lot'] . '</strong></em></li>';
      } else {
        $result .= '<li><em>Ce dossier qui concerne <strong>mon adh&eacute;sion </strong></em></li>';
      }
      if ($fiche['DatePriseEnCompte'] != '') {
        $result .= '<li><em>a &eacute;t&eacute; pris en compte le <strong>' . $fiche['DatePriseEnCompte'] . '</strong></em></li>';
      }
      if ($fiche['DateDerniereAction'] != '') {
        $result .= '<li><em>la derni&egrave;re action r&eacute;alis&eacute;e date du <strong>' . $fiche['DateDerniereAction'] . '</strong></em></li>';
      }

      if ($fiche['Gestionnaire'] != '') {
        $result .= '<li><em>est suivi par <strong>' . $fiche['Gestionnaire'] . '</strong></em></li>';
      }
      $result .= '</ul></div>';
    }
    $result .= '</div>';

    //fin de la table des informations sur la fiche

    //R�cup�ration de l'url donn�e dans la page d'administration WP

    $formulairePostReaction = plugins_url('ajax.php', dirname(__FILE__)) . '?ajax=true';; //searchPageUrl('[postReactionAdherent]');

    $url_ajax = plugins_url('ajax.php', dirname(__FILE__)) . '?ajax=true';
    //Recherche des actions correspondantes � la fiche

    //S'il y a des actions, affichage des actionsv
    //$result .= '<center><hr style="margin-top:20px" width="75%" size="2" noshade/></center>';
    $toujours_ouverte = true;

    /*if(substr_count($fiche['Statut'], 'Clotur')==-1)
        {
            $ts_dateclotur = strtotime($fiche['DateClotur']);
            $ts_now = mktime();
            $ts_datefinreponse = 7*(24*60*60)+$ts_dateclotur;
            if ($ts_now <= $ts_datefinreponse)
            {
                $toujours_ouverte=true;
            }
            else
            {
                $toujours_ouverte=false;
            }
        }*/

    if ($_SESSION['adherent_infos']->NumAdhesion == '47749') {
      $result .= getMesDocuments($_GET["ficheid"], '', false);
    }


    $result .= '<div id="ListeActions">' . displayListeActions($_GET["ficheid"]) . '</div>';

    return $result;
  }
}
// Clé de chiffrement
$phrase_secrete = "M0nT@gne*31&37";
$key = hash('sha256', $phrase_secrete); // Clé de 256 bits (32 octets) obtenue en appliquant la fonction de hachage SHA-256 à la phrase secrète


function client_e($data, $key)
{
  $ivSize = openssl_cipher_iv_length('AES-256-CBC');
  $iv = openssl_random_pseudo_bytes($ivSize);
  $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
  $encoded = base64_encode($iv . $encrypted);
  $safeEncoded = rawurlencode($encoded);
  return $safeEncoded;
}

function client_d($encryptedData, $key)
{
  $decoded = rawurldecode($encryptedData);
  $data = base64_decode($decoded);
  $ivSize = openssl_cipher_iv_length('AES-256-CBC');
  $iv = substr($data, 0, $ivSize);
  $encrypted = substr($data, $ivSize);
  return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

function partenaireGetMesClients()
{
  if (is_admin()) {
    return;
  }
  $url_mes_clients = get_permalink(get_option('edc_partenaire_id_page_mes_clients'));
  if (isset($_GET['client'])) {
    $url = URL_WS_ADH . 'v1.0/json/partenaire/client/' . $_GET['client'] . '/' . strtolower($_GET['i']);
    $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();

    if ($response->code == 403) {
      $result = '';
      $result .= '<div id="donnesPerso">
				<h1 style="margin-bottom:5px">Erreur - vous n\'avez pas accès à ces données</h1><br /><br />
				<a href="' . $url_mes_clients . '">Retour</a>';
      $result .= '</div>';
    } else {
      $data = json_decode($response->body);
      $client = $data->client;
      $fiches = $data->fiches;
      $invest = $data->invest;
      $programmes = $data->programmes;

      if (isset($fiches->Fiches->FicheId)) {
        $fiches->Fiches = array($fiches->Fiches);
      }

      $result = '';
      $result .= '<div id="donnesPerso">
				<h1 style="margin-bottom:5px">' . $client->Nom . ' ' . $client->Prenom . '</h1><br />';

      if (isset($_GET['ficheid'])) {
        foreach ($fiches->Fiches as $fiche) {
          if ($fiche->FicheId == $_GET['ficheid']) {
            $result .= partenaireFicheClient();
            break;
          }
        }
      } else {
        $fiches_ok = array();
        foreach ($fiches->Fiches as $fiche) {
          if (isset($fiche->Programme) && $fiche->Programme == $_GET['p'] && $fiche->Lot = $_GET['b']) {
            $fiches_ok[] = $fiche;
          }
        }

        $result .= displayListeDossiersFromClient($fiches_ok);

        $url = URL_WS_ADH . 'v1.0/json/user/getInvestissements';
        $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
        if ($response->code == 200) {
          $t_programmes = json_decode($response->body);
        } else {
          return 'Erreur WS';
        }

        if (isset($programmes->Invest->Nom)) {
          $programmes->Invest = array($invests->Invest);
        }

        // je boucle sur les programmes retournés jusqu'à trouver les lots de celui demandé
        foreach ($programmes->Invest as $t_programme) {
          // si c'est le programme demandé en paramètre
          if ($t_programme->Nom == $_GET['p']) {
            // C'est le premier invest du programme, je met de coté les infos
            // du programme pour ne pas les perdre
            $t_detail_programme["nom"] = isset($t_programme->Nom) ? $t_programme->Nom : '';
            $t_detail_programme["adresse"] = isset($t_programme->Adresse) ? $t_programme->Adresse : '';
            $t_detail_programme["ville"] = isset($t_programme->Ville) ? $t_programme->Ville : '';
            $t_detail_programme["codepostal"] = isset($t_programme->CodePostal) ? $t_programme->CodePostal : '';
            $t_detail_programme["pays"] = isset($t_programme->Pays) ? $t_programme->Pays : '';
            $t_detail_programme["promoteur"] = isset($t_programme->Promoteur) ? $t_programme->Promoteur : '';
            $t_detail_programme["syndic"] = isset($t_programme->Syndic) ? $t_programme->Syndic : '';
            $t_detail_programme["lien_photo"] = isset($t_programme->lien_photo) ? $t_programme->lien_photo : '';

            $t_detail_programme["adb"] = '';
            if ($t_programme->IdInvestissement == $_GET['i']) {
              if ($t_programme->ADB != '') {
                $t_detail_programme["adb"] = $t_programme->ADB;
              } else {
                $t_detail_programme["adb"] = $t_programme->ADBPROG;
              }

              break;
            }
          }
        }

        $result .= '<h3>Détails du programme</h3><br />' . $t_detail_programme["nom"] . '<br />' . $t_detail_programme["adresse"] . '<br />' . $t_detail_programme["codepostal"] . ' ' . $t_detail_programme["ville"] . '<br /><br />';
        $result .= 'Promoteur : ' . $t_detail_programme["promoteur"] . '<br />';
        $result .= 'ADB : ' . $t_detail_programme["adb"] . '<br />';


        $invest = $invest->Invest;

        $result .= '<br /><br /><h3>Détails du lot</h3><br />';
        //$result .= 'Typologie : '.$invest->Programme.'<br />';
        $result .= 'N&deg; du lot : ' . $invest->RefNum . '<br />';
        $result .= 'Date de livraison : ' . $invest->DateLivraisonReelle . '<br />';
        $result .= 'Date de la 1<sup>ère</sup> location : ' . $invest->DateLocationInitiale . '<br />';
        $result .= 'Loyer initial : ' . ($invest->LoyerMensuelLogement + $invest->LoyerMensuelParking) . ' &euro;<br />';
        $result .= 'Montant de l\'investissement : ' . ($invest->MontantTTCLogement + $invest->MontantTTCParking) . ' &euro;<br />';
        $result .= 'Loi fiscale : ' . $invest->InvestissementProduit . '<br />';

        $result .= '
					<a href="' . $url_mes_clients . '">Retour</a>';
        $result .= '</div>';
      }
    }
  } else {
    $result = '';
    $response = '';

    $result .= '<div id="donnesPerso">
			<h1 style="margin-bottom:5px">Mes clients</h1><br />';

    // Ajout de la barre de recherche
    $result .= '<div style="text-align: right; margin-bottom: 10px;">
			<form method="GET" action="">
				<input type="text" name="search" placeholder="Rechercher mes clients">
				<input class="rouge" type="submit" value="Rechercher">
			</form>
		</div>';

    // l'adresse principale.
    $result .= '
			<table class="rouge" style="width:100%;border-style:none;border-width:0px;">
				<tbody>
					<tr>                
						<th>Information Client </th>
						<th>Nom    </th>
						<th>Prénom</th>
						<th>Investissement</th>
						<th>Intervention(s) en cours liées à cet investissement</th>
						<th>Nombres total d\'interventions(s) en cours</th>
						<th>Date de la dernière action (Toutes fiches confondues)</th>
						<th></th>
					</tr>';

    $url = URL_WS_ADH . 'v1.0/json/partenaire/clients';
    $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();

    $invests = json_decode($response->body);

    // Filtrer les clients en fonction de la recherche
    $filteredInvests = array_filter($invests, function ($invest) {
      if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $_GET['search'];
        $search = strtolower($search);
        $nom = strtolower($invest->Nom);
        $prenom = strtolower($invest->Prenom);
        return (strpos($nom, $search) !== false || strpos($prenom, $search) !== false);
      }
      return true; // Afficher tous les clients si aucune recherche effectuée
    });

    foreach ($filteredInvests as $invest) {

      // Récupération de la valeur de $numero depuis le tableau
      $numero = $invest->Numero;

      // Définition de $_SESSION['numero']
      $_SESSION['numero'] = $numero;

      // Le reste du code reste inchangé

      $encryptedNumero = client_e($numero, $phrase_secrete);
      $result .=
        '<tr>
				<th style="color: white; background-color: white;">
					<a href="https://www.assoedc.com/partenaire-mes-clients/partenaire-clients-coordonnees/?client_I=' . $encryptedNumero . '">
						<img src="' . GESTION_ADH_REP_ICONES . '/balloon-white.png" title="Information client" width="15" />
					</a>
				</th>
				<td>' . $invest->Nom . '</td>
				<td>' . $invest->Prenom . '</td>
				<td>' . $invest->Programme . ' - ' . $invest->Bien . '</td>
				<td>' . $invest->NbInterEnCours . '</td>
				<td>' . $invest->NbInter . '</td>
				<td>' . $invest->DateDernier . '</td>
				<td width="30"><a href="' . $url_mes_clients . '?client=' . ((string)$invest->AdhIdContact) . '&i=' . $invest->IdInvestissement . '&p=' . $invest->Programme . '&b=' . $invest->Bien . '"><img src="' . GESTION_ADH_REP_ICONES . '/eye.png" title="Consultez le détail du dossier" width="16" /></a></td>
			</tr>';
    }

    $result .= '</tbody></table>';

    $result .= '</div>';
  }

  return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}
function partenaireCoordonneesClients()
{
  if (isset($_GET['client_I'])) {
    //var_dump($_GET['client_I']);
    $numero = client_d($_GET['client_I'], $phrase_secrete);
    //echo "Résultat client_d : " . $numero . "<br>";
    $url = URL_WS_ADH . 'v1.0/json/user/getAdherentCoordonnees?id_adherent=' . $numero;
    $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();

    $result = '';
    //echo $numero . "<br>";
    if ($response->code === 200) {
      $data = $response->body;
      //echo $data . "<br>";
      if ($data !== false) {
        $tab = json_decode($data, true);

        if ($tab !== null) {
          $tempArray = array();
          // Fonction qui permet de voir le contenu fourni par la requête slq
          //echo "Contenu JSON : " . $data . "<br>";
          //echo count($tab);

          foreach ($tab as $data) {
            $nom = $data['Nom'];
            $nom = mb_substr($nom, 1);
            //echo "Nom: " . $nom . "<br>";

            $valeurNumerique = intval(substr($data['Nom'], 0, 1));

            if (!isset($tempArray[$valeurNumerique])) {
              $tempArray[$valeurNumerique] = array();
            }

            $tempArray[$valeurNumerique][] = array(
              'nom' => $nom,
              'adresseComplement1' => $data['AdresseComplement1'],
              'adresseComplement2' => $data['AdresseComplement2'],
              'adresseComplement3' => $data['AdresseComplement3'],
              'codePostal' => $data['CodePostal'],
              'ville' => $data['Ville'],
              'telephone' => $data['Telephone'],
              'email' => $data['EmailPrincipal']

            );
          }

          ksort($tempArray);

          $previousNom = '';
          foreach ($tempArray as $values) {
            $email = '';
            $adresse = '';
            $codePostal = '';
            $ville = '';
            $telephones = array();
            foreach ($values as $data) {
              $nom = $data['nom'];
              $nom = mb_substr($nom, 1);

              if ($nom !== $previousNom) {
                $result .= "- Nom: " . $nom . "<br>";

                if (!empty($data['email'])) {
                  $result .= "- E-mail: " . $data['email'] . "<br>";
                }

                if (!empty($data['adresseComplement1']) || !empty($data['adresseComplement2']) || !empty($data['adresseComplement3'])) {
                  $adresse = '';

                  if (!empty($data['adresseComplement1'])) {
                    $adresse .= $data['adresseComplement1'];
                  }

                  if (!empty($data['adresseComplement2'])) {
                    if (!empty($adresse)) {
                      $adresse .= ' ' . $data['adresseComplement2'];
                    } else {
                      $adresse .= $data['adresseComplement2'];
                    }
                  }

                  if (!empty($data['adresseComplement3'])) {
                    if (!empty($adresse)) {
                      $adresse .= ' ' . $data['adresseComplement3'];
                    } else {
                      $adresse .= $data['adresseComplement3'];
                    }
                  }

                  if (!empty($adresse)) {
                    $result .= "- Adresse: " . $adresse . "<br>";
                  }
                }

                if (!empty($data['codePostal']) || !empty($data['ville'])) {
                  $result .= "" . $data['codePostal'] . ", " . $data['ville'] . "<br>";
                }
              }

              if (!empty($data['telephone'])) {
                $result .= "- Téléphone: " . $data['telephone'] . "<br>";
              }

              //echo "Résultat partiel: " . $result . "<br>"; // Ajout de la déclaration echo pour vérifier le résultat partiel

              $previousNom = $nom;
            }

            $result .= "<br>";
          }
        } else {
          $result .= "Erreur lors du décodage des données JSON : " . json_last_error_msg();
        }
      } else {
        $result .= "Erreur lors de la récupération des données depuis le script distant.";
      }
    } else {
      $result .= "Erreur lors de la requête HTTP : " . $response->code;
      echo $response->body;
      // echo $numero . "<br>";
    }
  } else {
    $result .= "La variable client_I n'est pas définie dans l'URL.";
  }

  return $result;
}

function displayListeDossiersFromClient($fiches)
{
  if (is_admin()) {
    return;
  }

  $legende = '<p>
				<img src="' . GESTION_ADH_REP_ICONES . '/status-offline.png" title="Ce dossier a &eacute;t&eacute; clotur&eacute;" alt="Ferm&eacute;" align="absmiddle"> <i>Dossier clotur&eacute;</i> | <img src="' . GESTION_ADH_REP_ICONES . '/status.png" title="Ce dossier est en cours de traitement" alt="Ouvert" align="absmiddle"><i>Dossier en cours de traitement</i> | <img src="' . GESTION_ADH_REP_ICONES . '/new-text.png" title="Une action a &eacute;t&eacute; ajout&eacute;e &agrave; votre dossier" alt="Nouveau !" align="absmiddle"><i>Une action a &eacute;t&eacute; r&eacute;cemment ajout&eacute;e</i></p>';
  $entete_encours = '<table class="rouge" style="width:100%;border-style:none;border-width:0px;">
			<tr>					
				<th>&nbsp;</th>
				<th>Ce dossier concerne</th>
				<th>Ce dossier porte sur</th>
				<th>Ouvert le</th>
				<th>Derni&egrave;re<br>action</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>';
  $entete_traitees = '<table class="rouge" style="width:100%;border-style:none;border-width:0px;">
			<tr>	
				<th>&nbsp;</th>				
				<th>Ce dossier concerne</th>
				<th>Ce dossier porte sur</th>
				<th>Ouvert le</th>
				<th>Cl&ocirc;tur&eacute;<br/>le</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>';

  // corps de
  $t_fiches["encours"] = '';
  $t_fiches["traitees"] = '';
  $t_fiches["histo"] = '';

  $url_details = get_permalink(get_option('edc_partenaire_id_page_mes_clients')) . '?client=' . $_GET['client'] . '&i=' . $_GET['i'];

  $i = 0;
  foreach ($fiches as $fiche) {
    //Pour chaque fiche cr�ation d'un affichage tabulaire
    $bgcolor = '';
    if ($i % 2 == 0) {
      //$bgcolor='#C4DEF1';
    } else {
      $bgcolor = '#FFFFFF';
    }

    $txt_fiche = '<tr bgcolor="' . $bgcolor . '">';

    $txt_fiche .= '<td style="vertical-align:middle; ">';
    if ($fiche->lib_statut == 1) {
      $txt_fiche .= '<img src="' . GESTION_ADH_REP_ICONES . '/status-offline.png" title="Ce dossier a &eacute;t&eacute; clotur&eacute;" alt="Ferm&eacute;">';
    } else {
      $txt_fiche .= '<img src="' . GESTION_ADH_REP_ICONES . '/status.png" title="Ce dossier est en cours" alt="Ce dossier est en cours">';
    }
    $txt_fiche .= '</td>';
    //			$txt_fiche.='<td style="border-style:none;border-width:0px">'.((string)$fiche->FicheRef).'</td>';
    $txt_fiche .= '<td style="vertical-align:middle; ">';

    if (isset($fiche->Programme) && $fiche->Programme != '' && isset($fiche->Lot) && $fiche->Lot != '') {
      $txt_fiche .= (string)$fiche->Programme . ' Lot : ' . (string)$fiche->Lot;
    } else {
      $txt_fiche .= 'Adh&eacute;sion';
    }
    $txt_fiche .= '</td>
		<td style="vertical-align:middle; ">';
    if (isset($fiche->SousDomaine) && $fiche->SousDomaine != '') {
      $txt_fiche .= formatDomaine((string)$fiche->SousDomaine);
    } else {
      $txt_fiche .= formatDomaine((string)$fiche->Domaine);
    }
    /*$txt_fiche .='</td>
                    <td>'.date_to_string((string)$fiche->DatePriseEnCompte).'</td>
                    <td>'.date_to_string((string)$fiche->DateDerniereAction).'</td>
                    <td><a href="'.$url_details.'&ficheid='.((string)$fiche->FicheId).'"><img src="'.WP_PLUGIN_URL.'/GestionAdherent/icones/eye.png" align="absmiddle" style="float:left;"></a></td>
                </tr>';*/
    // on teste la date de dernière action
    list($jour, $mois, $annee) = explode("/", date_to_jjmmaaaa((string)$fiche->DateDerniereAction));
    //$ts_action = time(0,0,0,$mois,$jour,$annee);
    $ts_action = strtotime($annee . '-' . $mois . '-' . $jour);
    $ts_now = time();
    //echo $ts_now .'-'.$ts_action .'='.($ts_now - $ts_action).' / '.(3*3600*24).'<br> ';
    if (($ts_now - $ts_action) < (3 * 3600 * 24)) {
      $icone_new = '<img src="' . GESTION_ADH_REP_ICONES . '/new-text.png" title="Une action a &eacute;t&eacute r&eacute;cemment ajout&eacute;e &aacute; votre dossier" alt="Nouveau !">';
      //$bgcolor_new ='bgcolor="#E4680C"';
    } else {
      $icone_new = '';
      //$bgcolor_new='';
    }
    $bgcolor_new = '';

    $txt_fiche .= '</td>
					<td style="text-align:center; vertical-align:middle;">' . date_to_jjmmaaaa((string)$fiche->DatePriseEnCompte) . '</td>';
    if ($fiche->lib_statut == 1) {
      $txt_fiche .= '<td style="text-align:center;vertical-align:middle;">' . date_to_jjmmaaaa((string)$fiche->DateCloture) . '</td>';
    } else {
      $txt_fiche .= '<td style="text-align:center;vertical-align:middle;">' . date_to_jjmmaaaa((string)$fiche->DateDerniereAction) . '</td>';
    }

    $txt_fiche .= '<td style="vertical-align:middle;" ' . $bgcolor_new . '>' . $icone_new . '</td>
				<td><a href="' . $url_details . '&ficheid=' . ((string)$fiche->FicheId) . '"><img src="' . WP_PLUGIN_URL . '/edc_adherent/icones/eye.png" align="absmiddle" style="float:left;"></a></td>
				</tr>';
    if ($fiche->lib_statut == 1) {
      if ($fiche->lib_histo == 1) {
        $t_fiches["histo"] .= $txt_fiche;
      } else {
        $t_fiches["traitees"] .= $txt_fiche;
      }
    } else {
      $t_fiches["encours"] .= $txt_fiche;
    }

    if (is_object($fiche->Gestionnaire)) {
      $fiche->Gestionnaire = '';
    }

    //Enregistrement en session de la fiche avec pour index son id
    $_SESSION[((string)$fiche->FicheId)] = array(
      'FicheRef' => ((string)$fiche->FicheRef),
      'Lot' => ((string)$fiche->Lot),
      'Programme' => ((string)$fiche->Programme),
      'Domaine' => formatDomaine((string)$fiche->Domaine),
      'SousDomaine' => formatDomaine((string)$fiche->SousDomaine),
      'DatePriseEnCompte' => date_to_string((string)$fiche->DatePriseEnCompte),
      'DateDerniereAction' => date_to_string((string)$fiche->DateDerniereAction),
      'DateCloture' => date_to_string((string)$fiche->DateCloture),
      'Statut' => formatStatut((string)$fiche->Statut),
      'Gestionnaire' => ((string)$fiche->Gestionnaire)
    );

    //Stockage du guid de la fiche dans un tableau
    $guid_fiches[] = ((string)$fiche->FicheId);

    $i++;
  }
  $pied_table = '</table>';

  $result = $legende;
  if (strlen($t_fiches["encours"]) > 0) {
    $result .= '<H2>Dossiers en cours</H2>';
    $result .= $entete_encours;
    $result .= $t_fiches["encours"];
    $result .= $pied_table;
  } else {
    $result .= '<H2>Dossiers en cours</H2>';
    $result .= '<p>Aucun dossier actuellement en cours de traitement par nos services.</p>';
  }

  /*if ($t_fiches["traitees"] != '')
    {
        $result .='<H2>Mes dossiers cloturés dans les 30 derniers jours</H2>';
        $result .=$entete_traitees;
        $result .= $t_fiches["traitees"];
        $result.=$pied_table;
    }*/

  if ($t_fiches["histo"] != '' || $t_fiches["traitees"] != '') {
    $result .= '<H2>Historique des dossiers cl&ocirc;tur&eacute;s</H2>';
    $result .= $entete_traitees;
    $result .= $t_fiches["traitees"];
    $result .= $t_fiches["histo"];
    $result .= $pied_table;
  }
  //print_r($_SESSION);
  return $result;
}

?>
