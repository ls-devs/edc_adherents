<?php

if (!isset($_SESSION)) {
  // session_set_cookie_params(0, '/');
  session_start();
}
add_shortcode('ADHERENT_FormPreInscription', 'GetFormPreInscription');
add_shortcode('ADHERENT_FormInscription', 'FormInscription');
add_shortcode('CONSEILLER_FormInscription', 'FormInscriptionConseiller');
add_shortcode('ADHERENT_FormCB', 'GetFormCB');


add_shortcode('ADHERENT_FormInscription2018', 'FormInscription2018');

function GetFormPreInscription()
{
  if (is_admin()) {
    return;
  }
  global $_conf, $_CONFIG;

  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/DB.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/ProspectCore.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/classes/Prospect.php');
  require_once(dirname(__FILE__) . '/../../../../_admin_/config/config.php');

  DB::Connexion($_CONFIG['BD_HOST'], $_CONFIG['BD_USER'], $_CONFIG['BD_PASSWORD'], $_CONFIG['BD_BD']);

  $id_prospect = $_GET['idp'];
  $checkSum = $_GET['c'];

  $resultat = '';

  $prospect = new Prospect($id_prospect);
  if ($prospect->GetCheckSum() != $_GET['c']) {
    return 'Vous n\'avez pas accès à cette page.';
  } else {
    $resultat .= '
        <script>
		function CheckFormPreInscription()
		{
			if (jQuery("#nom").val()=="")
			{
				alert("Vous devez indiquer un nom");
				return false;
			}
			if (jQuery("#prenom").val()=="")
			{
				alert("Vous devez indiquer un prénom");
				return false;
			}
			if (jQuery("#adresse_1").val()=="")
			{
				alert("Vous devez indiquer une adresse");
				return false;
			}
			if (jQuery("#code_postal").val()=="")
			{
				alert("Vous devez indiquer un code postal");
				return false;
			}
			if (jQuery("#ville").val()=="")
			{
				alert("Vous devez indiquer une ville");
				return false;
			}
			if (jQuery("#tel_1").val()=="" && jQuery("#tel_2").val()=="")
			{
				alert("Vous devez indiquer un numéro de téléphone");
				return false;
			}
			if (jQuery("#email").val()=="")
			{
				alert("Vous devez indiquer une adresse email");
				return false;
			}
			if (jQuery("#situation_familiale").val()=="")
			{
				alert("Vous devez indiquer une situation familiale");
				return false;
			}
			if (jQuery("#nb_personne").val()=="")
			{
				alert("Vous devez indiquer un nombre de personnes rattachées au foyer fiscal ");
				return false;
			}
			
			return true;
		}
		</script>
        <form action="' . $_conf['id_page_cb'] . '" method="post" onsubmit="return CheckFormPreInscription();">
        	<input type="hidden" name="idp" value="' . $id_prospect . '" />
        	<input type="hidden" name="c" value="' . $checkSum . '" />
		
			<p><label for="civilite">Civilité :</label></p>
			<p><input type="radio" id="civilite_monsieur" class="text" value="Monsieur" name="civilite"> <label for="civilite_monsieur">Monsieur</label> <input type="radio" id="civilite_madame" class="text" value="Madama" name="civilite"> <label for="civilite_madame">Madame</label></p>
			<p><label for="nom"><strong>Nom :</strong></label> <input type="text" id="nom" class="text" name="nom" value="' . $prospect->nom . '"></p>
			<p><label for="prenom"><strong>Prénom :</strong></label> <input type="text" id="prenom" class="text" name="prenom" value="' . $prospect->prenom . '"></p>
			<p><label for="adresse_1"><strong>Adresse 1 :</strong></label> <input type="text" id="adresse_1" class="text" name="adresse_1" value="' . $prospect->adresse_1 . '"></p>
			<p><label for="adresse_2">Adresse 2 :</label> <input type="text" id="adresse_2" class="text" name="adresse_2" value="' . $prospect->adresse_2 . '"></p>
			<p><label for="code_postal"><strong>Code postal :</strong></label> <input type="text" id="code_postal" class="text" name="code_postal" value="' . $prospect->code_postal . '"></p>
			<p><label for="ville"><strong>Ville :</strong></label> <input type="text" id="ville" class="text" name="ville" value="' . $prospect->ville . '"></p>
			<p><label for="tel_1"><strong>Téléphone 1 :</strong></label> <input type="text" id="tel_1" class="text" name="tel_1" value="' . $prospect->tel_1 . '"></p>
			<p><label for="tel_2">Téléphone 2 :</label> <input type="text" id="tel_2" class="text" name="tel_2" value="' . $prospect->tel_2 . '"></p>
			<p><label for="email"><strong>Email :</strong></label> <input type="text" id="email" class="text" name="email" value="' . $prospect->email . '"></p>
			<p><label for="situation_familiale"><strong>Situation familiale :</strong></label> <select name="situation_familiale">
            	<option value="">--</option>
            	<option value="marie">Marié</option>
            	<option value="divorce">Divorcé</option>
            	<option value="celibataire">Célibataire</option>
            	<option value="veuf">Veuf</option>
            	<option value="pacse">Pacsé</option>
            	<option value="concubinage">Concubinage</option>
            </select></p>
			<p><label for="nb_personne"><strong>Nombre de personnes rattachées au foyer fiscal :</strong></label> <select name="nb_personne">
            	<option value="">--</option>
            	<option value="1">1</option>
            	<option value="2">2</option>
            	<option value="3">3</option>
            	<option value="4">4</option>
            	<option value="5">5</option>
            	<option value="6">6</option>
            	<option value="7">7</option>
            	<option value="8">8</option>
            	<option value="9">9</option>
            </select></p>
	
						
			<p class="rememberme"><input type="checkbox" id="cgv" class="checkbox" name="cgv"> <label for="cgv">Je reconnais avoir pris connaissance des <a href="' . $_conf['URL_statuts'] . '" target="_blank">statuts et du règlement intérieur</a> que je m’engage à respecter.</label></p>
			
			<p class="submit">
				<input type="submit" value="Je confirmer mon inscription" id="wp-submit" name="wp-submit">
			</p>
			
					
		</form>';
  }

  return $resultat;
}


function FormInscriptionConseiller()
{
  if (is_admin()) {
    return;
  }
  global $_conf;

  $resultat .= '
	<script>
	
	function CheckFormPreInscription()
	{
		if (jQuery("#nom").val()=="")
		{
			alert("Vous devez indiquer un nom");
			return false;
		}
		if (jQuery("#prenom").val()=="")
		{
			alert("Vous devez indiquer un prénom");
			return false;
		}
		if (jQuery("#adresse_1").val()=="")
		{
			alert("Vous devez indiquer une adresse");
			return false;
		}
		if (jQuery("#code_postal").val()=="")
		{
			alert("Vous devez indiquer un code postal");
			return false;
		}
		if (jQuery("#ville").val()=="")
		{
			alert("Vous devez indiquer une ville");
			return false;
		}
		if (jQuery("#tel_1").val()=="" && jQuery("#tel_2").val()=="")
		{
			alert("Vous devez indiquer un numéro de téléphone");
			return false;
		}
		if (jQuery("#email").val()=="")
		{
			alert("Vous devez indiquer une adresse email");
			return false;
		}
		if (jQuery("#email").val()!=jQuery("#cemail").val())
		{
			alert("Confirmation de l\'email invalide");
			return false;
		}
		
		if (jQuery("#situation_familiale").val()=="")
		{
			alert("Vous devez indiquer une situation familiale");
			return false;
		}
		if (jQuery("#nb_personne").val()=="")
		{
			alert("Vous devez indiquer un nombre de personnes rattachées au foyer fiscal ");
			return false;
		}
		
		if (jQuery("#conseiller_nom").val() == "")
		{
			alert("Vous devez indiquer votre nom de conseiller");
			return false;
		}
		
		if (jQuery("#conseiller_societe").val() == "")
		{
			alert("Vous devez indiquer votre nom de société");
			return false;
		}
		
		if (jQuery("#conseiller_adresse_1").val() == "")
		{
			alert("Vous devez indiquer votre adresse");
			return false;
		}
		
		if (jQuery("#conseiller_codepostal").val() == "")
		{
			alert("Vous devez indiquer votre code postal");
			return false;
		}
		
		if (jQuery("#conseiller_ville").val() == "")
		{
			alert("Vous devez indiquer votre ville");
			return false;
		}
		
		if (jQuery("#conseiller_email").val()=="")
		{
			alert("Vous devez indiquer votre adresse email");
			return false;
		}
		if (jQuery("#conseiller_email").val()!=jQuery("#cconseiller_email").val())
		{
			alert("Confirmation de votre email invalide");
			return false;
		}
		
		if (!jQuery("#cgv").is(\':checked\'))
		{
			alert("Vous devez valider les statuts et le règlement intérieur");
			return false;
		}
		
		return true;
	}
	</script>
	<form action="' . $_conf['id_page_cb'] . '" method="post" onsubmit="return CheckFormPreInscription();">
		<input type="hidden" name="new_adh_from_c" value="1" />
	
		<fieldset style="padding-left:10px; border-left:1px solid #545454;">
			<legend style="color:#DA1834;">Informations sur l\'adhérent</legend>
		<p>
			<label for="civilite">Civilité :</label></p>
		<p>
			<span style="display:inline-block; margin-right:20px;"><input type="radio" id="civilite_monsieur" class="text" value="Monsieur" name="civilite" style="display:inline-block;"> <label for="civilite_monsieur" style="display:inline-block;">Monsieur</label></span>
			<span style="display:inline-block;"><input type="radio" id="civilite_madame" class="text" value="Madame" name="civilite"  style="display:inline-block;"> <label for="civilite_madame" style="display:inline-block;">Madame</label></span>
		</p>
		<p style="float:left; width:50%;"><label for="prenom"><strong>Prénom :</strong></label> <input type="text" id="prenom" class="text" name="prenom" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="nom"><strong>Nom :</strong></label> <input type="text" id="nom" class="text" name="nom" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p><label for="adresse_1"><strong>Adresse 1 :</strong></label> <input type="text" id="adresse_1" class="text" name="adresse_1" style="width:95%"></p>
		<p><label for="adresse_2">Adresse 2 :</label> <input type="text" id="adresse_2" class="text" name="adresse_2" style="width:95%"></p>
		<p style="float:left; width:50%;"><label for="code_postal"><strong>Code postal :</strong></label> <input type="text" id="code_postal" class="text" name="code_postal" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="ville"><strong>Ville :</strong></label> <input type="text" id="ville" class="text" name="ville" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="tel_1"><strong>Téléphone 1 :</strong></label> <input type="text" id="tel_1" class="text" name="tel_1" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="tel_2">Téléphone 2 :</label> <input type="text" id="tel_2" class="text" name="tel_2" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="email"><strong>Email :</strong></label> <input type="text" id="email" class="text" name="email" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="cemail"><strong>Confirmation email :</strong></label> <input type="text" id="cemail" class="text" name="cemail" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="situation_familiale"><strong>Situation familiale :</strong></label> <select name="situation_familiale">
			<option value="">--</option>
			<option value="marie">Marié</option>
			<option value="divorce">Divorcé</option>
			<option value="celibataire">Célibataire</option>
			<option value="veuf">Veuf</option>
			<option value="pacse">Pacsé</option>
			<option value="concubinage">Concubinage</option>
		</select></p>
		<p style="float:left; width:50%;"><label for="nb_personne"><strong>Nombre de personnes rattachées au foyer fiscal :</strong></label> <select name="nb_personne">
			<option value="">--</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
		</select></p>
		<div style="clear:both;"></div>
		
		<input type="hidden" name="origine" value="conseiller">
		
		</fieldset>
		
		<fieldset style="padding-left:10px; border-left:1px solid #545454;">
			<legend style="color:#DA1834;">Vos informations</legend>
			
		<p style="float:left; width:50%;"><label for="conseiller_nom">Nom Prénom</label> <input type="text" name="conseiller_nom" id="conseiller_nom" class="text" style="width:90%; margin-bottom:0;" /></p>
		
		<p style="float:left; width:50%;"><label for="conseiller_societe">Société</label> <input type="text" name="conseiller_societe" id="conseiller_societe" class="text" style="width:90%; margin-bottom:0;" /></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><label for="conseiller_adresse_1">Adresse 1</label> <input type="text" name="conseiller_adresse_1" id="conseiller_adresse_1" class="text" style="width:90%; margin-bottom:0;" /></p>
		
		<p style="float:left; width:50%;"><label for="conseiller_adresse_2">Adresse 2</label> <input type="text" name="conseiller_adresse_2" id="conseiller_adresse_2" class="text" style="width:90%; margin-bottom:0;" /></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><label for="conseiller_codepostal">Code postal</label> <input type="text" name="conseiller_codepostal" id="conseiller_codepostal" class="text" style="width:90%; margin-bottom:0;" /></p>
		
		<p style="float:left; width:50%;"><label for="conseiller_ville">Ville</label> <input type="text" name="conseiller_ville" id="conseiller_ville" class="text" style="width:90%; margin-bottom:0;" /></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><label for="conseiller_email"><strong>Email :</strong></label> <input type="text" id="conseiller_email" class="text" name="conseiller_email" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="cconseiller_email"><strong>Confirmation email :</strong></label> <input type="text" id="cconseiller_email" class="text" name="cconseiller_email" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		
		</fieldset>
		
		
		<div style="clear:both;"></div>
		
		
		<p><a href="' . $_conf['URL_statuts'] . '" target="_blank">Télécharger les statuts</a></p>
		
		<p class="rememberme"><input type="checkbox" id="cgv" class="checkbox" name="cgv" style="float:left;"> <label for="cgv">Je reconnais avoir pris connaissance des <a href="' . $_conf['URL_statuts'] . '" target="_blank">statuts et du règlement intérieur</a> que je m’engage à respecter.</label></p>
		
		<p class="submit">
			<input type="submit" value="Je valide l\'inscription" id="wp-submit" name="wp-submit">
		</p>
		
				
	</form>';

  return $resultat;
}

function FormInscription()
{
  if (is_admin()) {
    return;
  }
  global $_conf;

  $resultat .= '
	<script>
	
	jQuery(document).ready(function(e) {
        
		jQuery("#origine_parrainage").change(function(e) {
            if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
				jQuery("#adherent_parain").prop( "disabled", false );
			}
        });
		jQuery("#origine_conseiller").change(function(e) {
            if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
				jQuery("#conseiller_nom").prop( "disabled", false );
			}
        });
		jQuery("#origine_autre").change(function(e) {
            if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
				jQuery("#autre_texte").prop( "disabled", false );
			}
        });
		
		jQuery("#origine_internet").change(function(e) {
            if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
			}
        });
		
    });
	
	function CheckFormPreInscription()
	{
		if (jQuery("#nom").val()=="")
		{
			alert("Vous devez indiquer un nom");
			return false;
		}
		if (jQuery("#prenom").val()=="")
		{
			alert("Vous devez indiquer un prénom");
			return false;
		}
		if (jQuery("#adresse_1").val()=="")
		{
			alert("Vous devez indiquer une adresse");
			return false;
		}
		if (jQuery("#code_postal").val()=="")
		{
			alert("Vous devez indiquer un code postal");
			return false;
		}
		if (jQuery("#ville").val()=="")
		{
			alert("Vous devez indiquer une ville");
			return false;
		}
		if (jQuery("#tel_1").val()=="" && jQuery("#tel_2").val()=="")
		{
			alert("Vous devez indiquer un numéro de téléphone");
			return false;
		}
		if (jQuery("#email").val()=="")
		{
			alert("Vous devez indiquer une adresse email");
			return false;
		}
		if (jQuery("#email").val()!=jQuery("#cemail").val())
		{
			alert("Confirmation de l\'email invalide");
			return false;
		}
		
		if (jQuery("#situation_familiale").val()=="")
		{
			alert("Vous devez indiquer une situation familiale");
			return false;
		}
		if (jQuery("#nb_personne").val()=="")
		{
			alert("Vous devez indiquer un nombre de personnes rattachées au foyer fiscal ");
			return false;
		}
		
		if (jQuery("#origine_parrainage").is(":checked") && jQuery("#adherent_parain").val() == "")
		{
			alert("Vous devez indiquer le nom de votre parrain");
			return false;
		}
		
		if (jQuery("#origine_conseiller").is(":checked") && jQuery("#conseiller_nom").val() == "")
		{
			alert("Vous devez indiquer le nom de votre conseiller");
			return false;
		}
		
		if (jQuery("#origine_autre").is(":checked") && jQuery("#autre_texte").val() == "")
		{
			alert("Vous devez préciser l\'origine de votre adhésion");
			return false;
		}
		
		
		if (!jQuery("#cgv").is(\':checked\'))
		{
			alert("Vous devez valider les statuts et le règlement intérieur");
			return false;
		}
		
		return true;
	}
	</script>
	<form action="' . $_conf['id_page_cb'] . '" method="post" onsubmit="return CheckFormPreInscription();">
		<input type="hidden" name="new_adh" value="1" />
	
		<p>
			<label for="civilite">Civilité :</label></p>
		<p>
			<span style="display:inline-block; margin-right:20px;"><input type="radio" id="civilite_monsieur" class="text" value="Monsieur" name="civilite" style="display:inline-block;"> <label for="civilite_monsieur" style="display:inline-block;">Monsieur</label></span>
			<span style="display:inline-block;"><input type="radio" id="civilite_madame" class="text" value="Madame" name="civilite"  style="display:inline-block;"> <label for="civilite_madame" style="display:inline-block;">Madame</label></span>
		</p>
		<p style="float:left; width:50%;"><label for="prenom"><strong>Prénom :</strong></label> <input type="text" id="prenom" class="text" name="prenom" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="nom"><strong>Nom :</strong></label> <input type="text" id="nom" class="text" name="nom" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p><label for="adresse_1"><strong>Adresse 1 :</strong></label> <input type="text" id="adresse_1" class="text" name="adresse_1" style="width:95%"></p>
		<p><label for="adresse_2">Adresse 2 :</label> <input type="text" id="adresse_2" class="text" name="adresse_2" style="width:95%"></p>
		<p style="float:left; width:50%;"><label for="code_postal"><strong>Code postal :</strong></label> <input type="text" id="code_postal" class="text" name="code_postal" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="ville"><strong>Ville :</strong></label> <input type="text" id="ville" class="text" name="ville" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="tel_1"><strong>Téléphone 1 :</strong></label> <input type="text" id="tel_1" class="text" name="tel_1" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="tel_2">Téléphone 2 :</label> <input type="text" id="tel_2" class="text" name="tel_2" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="email"><strong>Email :</strong></label> <input type="text" id="email" class="text" name="email" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="cemail"><strong>Confirmation email :</strong></label> <input type="text" id="cemail" class="text" name="cemail" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="situation_familiale"><strong>Situation familiale :</strong></label> <select name="situation_familiale">
			<option value="">--</option>
			<option value="marie">Marié</option>
			<option value="divorce">Divorcé</option>
			<option value="celibataire">Célibataire</option>
			<option value="veuf">Veuf</option>
			<option value="pacse">Pacsé</option>
			<option value="concubinage">Concubinage</option>
		</select></p>
		<p style="float:left; width:50%;"><label for="nb_personne"><strong>Nombre de personnes rattachées au foyer fiscal :</strong></label> <select name="nb_personne">
			<option value="">--</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
		</select></p>
		<div style="clear:both;"></div>
		
		<p><label for="origine"><strong>Origine de l\'inscription :</strong></label></p>
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_internet" value="internet" checked="checked" /> <label for="origine_internet" style="display:inline;">Internet</label></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_parrainage" value="parrainage" /> <label for="origine_parrainage" style="display:inline;">Parrainage</label></p>
		<p style="float:left; width:50%;"><label for="adherent_parain" style="display:inline;">Nom du parrain</label> <input type="text" name="adherent_parain" id="adherent_parain" class="details_origine"  style="display:inline-block; margin:0; float:right;" disabled="disabled" /></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_conseiller" value="conseiller" /> <label for="origine_conseiller" style="display:inline;">Conseiller</label></p>
		<p style="float:left; width:50%;"><label for="conseiller_nom" style="display:inline;">Nom du conseiller</label> <input type="text" name="conseiller_nom" id="conseiller_nom" class="details_origine" style="display:inline-block; margin:0; float:right;" disabled="disabled" /></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_autre" value="autre" /> <label for="origine_autre" style="display:inline;">Autre</label></p>
		<p style="float:left; width:50%;"><input type="text" name="autre_texte" id="autre_texte" class="details_origine" style="display:inline-block; margin:0; float:right;" disabled="disabled" /></p>
		<div style="clear:both;"></div>
		
		<p><a href="' . $_conf['URL_statuts'] . '" target="_blank">Télécharger les statuts</a></p>
		
		<p class="rememberme"><input type="checkbox" id="cgv" class="checkbox" name="cgv" style="float:left;"> <label for="cgv">Je reconnais avoir pris connaissance des <a href="' . $_conf['URL_statuts'] . '" target="_blank">statuts et du règlement intérieur</a> que je m’engage à respecter.</label></p>
		
		<p class="submit">
			<input type="submit" value="Je valide mon inscription" id="wp-submit" name="wp-submit">
		</p>
		
				
	</form>';


  return $resultat;
}


function FormInscription2018()
{
  if (is_admin()) {
    return;
  }
  global $_conf;

  $resultat .= '
	<script>
	
	jQuery(document).ready(function(e) {
		
		jQuery("#origine_parrainage").change(function(e) {
			if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
				jQuery("#adherent_parain").prop( "disabled", false );
			}
		});
		jQuery("#origine_conseiller").change(function(e) {
			if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
				jQuery("#conseiller_nom").prop( "disabled", false );
			}
		});
		jQuery("#origine_autre").change(function(e) {
			if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
				jQuery("#autre_texte").prop( "disabled", false );
			}
		});
		
		jQuery("#origine_internet").change(function(e) {
			if (jQuery(this).is(":checked"))
			{
				jQuery(".details_origine").prop( "disabled", true );
			}
		});
		
		jQuery("input[name=facturation_same]").change(function(e) {
			val = jQuery("input[name=facturation_same]:checked").val();
			if (val == 1)
				jQuery("#form_facturation").slideUp();
			else
				jQuery("#form_facturation").slideDown();			
        });
		
	});
	
	function CheckFormPreInscription()
	{
		if (jQuery("#nom").val()=="")
		{
			alert("Vous devez indiquer un nom");
			return false;
		}
		if (jQuery("#prenom").val()=="")
		{
			alert("Vous devez indiquer un prénom");
			return false;
		}
		if (jQuery("#adresse_1").val()=="")
		{
			alert("Vous devez indiquer une adresse");
			return false;
		}
		if (jQuery("#code_postal").val()=="")
		{
			alert("Vous devez indiquer un code postal");
			return false;
		}
		if (jQuery("#ville").val()=="")
		{
			alert("Vous devez indiquer une ville");
			return false;
		}
		if (jQuery("#tel_1").val()=="" && jQuery("#tel_2").val()=="")
		{
			alert("Vous devez indiquer un numéro de téléphone");
			return false;
		}
		if (jQuery("#email").val()=="")
		{
			alert("Vous devez indiquer une adresse email");
			return false;
		}
		if (jQuery("#email").val()!=jQuery("#cemail").val())
		{
			alert("Confirmation de l\'email invalide");
			return false;
		}
		
		if (jQuery("#situation_familiale").val()=="")
		{
			alert("Vous devez indiquer une situation familiale");
			return false;
		}
		if (jQuery("#nb_personne").val()=="")
		{
			alert("Vous devez indiquer un nombre de personnes rattachées au foyer fiscal ");
			return false;
		}
		
		if (jQuery("#origine_parrainage").is(":checked") && jQuery("#adherent_parain").val() == "")
		{
			alert("Vous devez indiquer le nom de votre parrain");
			return false;
		}
		
		if (jQuery("#origine_conseiller").is(":checked") && jQuery("#conseiller_nom").val() == "")
		{
			alert("Vous devez indiquer le nom de votre conseiller");
			return false;
		}
		
		if (jQuery("#origine_autre").is(":checked") && jQuery("#autre_texte").val() == "")
		{
			alert("Vous devez préciser l\'origine de votre adhésion");
			return false;
		}
		
		
		if (!jQuery("#cgv").is(\':checked\'))
		{
			alert("Vous devez valider les statuts et le règlement intérieur");
			return false;
		}
		
		if (!jQuery("#rgpd").is(\':checked\'))
		{
			alert("Vous devez accepter que les informations saisies soient exploitées");
			return false;
		}
		
		val = jQuery("input[name=facturation_same]:checked").val();
		if (val == 0)
		{			
			if (jQuery("#fact_prenom").val() == "")
			{
				alert("Vous devez indiquer votre prénom pour la facturation");
				return false;
			}	
			if (jQuery("#fact_nom").val() == "")
			{
				alert("Vous devez indiquer votre nom pour la facturation");
				return false;
			}	
			
			if (jQuery("#fact_adresse_1").val() == "")
			{
				alert("Vous devez indiquer votre adresse pour la facturation");
				return false;
			}
			
			if (jQuery("#fact_codepostal").val() == "")
			{
				alert("Vous devez indiquer votre code postal pour la facturation");
				return false;
			}
			
			if (jQuery("#fact_ville").val() == "")
			{
				alert("Vous devez indiquer votre ville pour la facturation");
				return false;
			}
			
			if (jQuery("#fact_email").val()=="")
			{
				alert("Vous devez indiquer votre adresse email pour la facturation");
				return false;
			}
			if (jQuery("#fact_email").val()!=jQuery("#fact_cemail").val())
			{
				alert("Confirmation de votre email invalide pour la facturation");
				return false;
			}
		}
		
		return true;
	}
	</script>
	<form action="' . $_conf['id_page_cb'] . '" method="post" onsubmit="return CheckFormPreInscription();">
		<input type="hidden" name="new_adh" value="1" />
		<input type="hidden" name="new_form" value="1" />
	
		<p>
			<label for="civilite">Civilité :</label></p>
		<p>
			<span style="display:inline-block; margin-right:20px;"><input type="radio" id="civilite_monsieur" class="text" value="Monsieur" name="civilite" style="display:inline-block;"> <label for="civilite_monsieur" style="display:inline-block;">Monsieur</label></span>
			<span style="display:inline-block;"><input type="radio" id="civilite_madame" class="text" value="Madame" name="civilite"  style="display:inline-block;"> <label for="civilite_madame" style="display:inline-block;">Madame</label></span>
		</p>
		<p style="float:left; width:50%;"><label for="prenom"><strong>Prénom :</strong></label> <input type="text" id="prenom" class="text" name="prenom" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="nom"><strong>Nom :</strong></label> <input type="text" id="nom" class="text" name="nom" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p><label for="adresse_1"><strong>Adresse 1 :</strong></label> <input type="text" id="adresse_1" class="text" name="adresse_1" style="width:95%"></p>
		<p><label for="adresse_2">Adresse 2 :</label> <input type="text" id="adresse_2" class="text" name="adresse_2" style="width:95%"></p>
		<p style="float:left; width:50%;"><label for="code_postal"><strong>Code postal :</strong></label> <input type="text" id="code_postal" class="text" name="code_postal" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="ville"><strong>Ville :</strong></label> <input type="text" id="ville" class="text" name="ville" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="tel_1"><strong>Téléphone 1 :</strong></label> <input type="text" id="tel_1" class="text" name="tel_1" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="tel_2">Téléphone 2 :</label> <input type="text" id="tel_2" class="text" name="tel_2" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="email"><strong>Email :</strong></label> <input type="text" id="email" class="text" name="email" style="width:90%; margin-bottom:0;"></p>
		<p style="float:left; width:50%;"><label for="cemail"><strong>Confirmation email :</strong></label> <input type="text" id="cemail" class="text" name="cemail" style="width:90%; margin-bottom:0;"></p>
		<div style="clear:both;"></div>
		<p style="float:left; width:50%;"><label for="situation_familiale"><strong>Situation familiale :</strong></label> <select name="situation_familiale">
			<option value="">--</option>
			<option value="marie">Marié</option>
			<option value="divorce">Divorcé</option>
			<option value="celibataire">Célibataire</option>
			<option value="veuf">Veuf</option>
			<option value="pacse">Pacsé</option>
			<option value="concubinage">Concubinage</option>
		</select></p>
		<p style="float:left; width:50%;"><label for="nb_personne"><strong>Nombre de personnes rattachées au foyer fiscal :</strong></label> <select name="nb_personne">
			<option value="">--</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
		</select></p>
		<div style="clear:both;"></div>
		
		<p><label for="origine"><strong>Origine de l\'inscription :</strong></label></p>
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_internet" value="internet" checked="checked" /> <label for="origine_internet" style="display:inline;">Internet</label></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_parrainage" value="parrainage" /> <label for="origine_parrainage" style="display:inline;">Parrainage</label></p>
		<p style="float:left; width:50%;"><label for="adherent_parain" style="display:inline;">Nom du parrain</label> <input type="text" name="adherent_parain" id="adherent_parain" class="details_origine"  style="display:inline-block; margin:0; float:right;" disabled="disabled" /></p>
		<div style="clear:both;"></div>
		
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_conseiller" value="conseiller" /> <label for="origine_conseiller" style="display:inline;">Conseiller</label></p>
		<p style="float:left; width:50%;"><label for="conseiller_nom" style="display:inline;">Nom du conseiller</label> <input type="text" name="conseiller_nom" id="conseiller_nom" class="details_origine" style="display:inline-block; margin:0; float:right;" disabled="disabled" /></p>
		<p style="float:left; width:50%;"></p><p style="float:left; width:50%;"><input type="checkbox" name="allow_partenaire_dossier"> <label for="allow_partenaire_dossier" style="display:inline; font-weight:normal;">J’autorise EDC à porter le suivi de mes dossiers concernant mon investissement immobilier à la connaissance du conseiller identifié ci-dessus.</label></p>
        <p style="float:left; width:50%;"></p><p style="float:left; width:50%;"><input type="checkbox" name="allow_partenaire_coord"> <label for="allow_partenaire_coord" style="display:inline; font-weight:normal;">J’autorise EDC à communiquer mes coordonnées au conseiller identifié ci-dessus.</label></p>
		
		<div style="clear:both; height:20px;"></div>
		
		<p style="float:left; width:50%;"><input type="radio" name="origine" id="origine_autre" value="autre" /> <label for="origine_autre" style="display:inline;">Autre</label></p>
		<p style="float:left; width:50%;"><input type="text" name="autre_texte" id="autre_texte" class="details_origine" style="display:inline-block; margin:0; float:left;" disabled="disabled" /></p>
		<div style="clear:both;"></div>
		
		<p><a href="' . $_conf['URL_statuts'] . '" target="_blank">Télécharger les statuts</a></p>
		
		<p class="rememberme"><input type="checkbox" id="cgv" class="checkbox" name="cgv" style="float:left;"> <label for="cgv">Je reconnais avoir pris connaissance des <a href="' . $_conf['URL_statuts'] . '" target="_blank">statuts et du règlement intérieur</a> que je m’engage à respecter.</label></p>
		<p class="rememberme"><input type="checkbox" id="rgpd" class="checkbox" name="cgv" style="float:left;"> <label for="rgpd">En soumettant ce formulaire, vous acceptez que les informations saisies soient exploitées pour permettre votre adhésion à l\'association.</p>
		
		
		
		
		<hr />
		
		<h3>Facturation</h3>
		
		<p style="float:left; width:50%;"><input type="radio" name="facturation_same" id="facturation_same_oui" value="1" /> <label for="facturation_same_oui" style="display:inline;">Utiliser les <strong>mêmes</strong> coordonnées pour la facturation</label></p>
		<p style="float:left; width:50%;"><input type="radio" name="facturation_same" id="facturation_same_non" value="0" /> <label for="facturation_same_non" style="display:inline;"><strong>Autres</strong> coordonnées pour la facturation</label></p>
		<div style="clear:both;"></div>
		
		<div id="form_facturation" style="display:none;">
			<p style="float:left; width:50%;"><label for="fact_societe"><strong>Société :</strong></label> <input type="text" id="fact_societe" class="text" name="fact_societe" style="width:90%; margin-bottom:0;"></p>
			<p style="float:left; width:50%;"><label for="fact_entite_commerciale"><strong>Entité commerciale :</strong></label> <input type="text" id="fact_entite_commerciale" class="text" name="fact_entite_commerciale" style="width:90%; margin-bottom:0;"></p>
			<div style="clear:both;"></div>
			<p>
				<label for="fact_civilite">Civilité :</label></p>
			<p>
				<span style="display:inline-block; margin-right:20px;"><input type="radio" id="fact_civilite_monsieur" class="text" value="Monsieur" name="fact_civilite" style="display:inline-block;"> <label for="fact_civilite_monsieur" style="display:inline-block;">Monsieur</label></span>
				<span style="display:inline-block;"><input type="radio" id="fact_civilite_madame" class="text" value="Madame" name="fact_civilite"  style="display:inline-block;"> <label for="fact_civilite_madame" style="display:inline-block;">Madame</label></span>
			</p>
			<p style="float:left; width:50%;"><label for="fact_prenom"><strong>Prénom :</strong></label> <input type="text" id="fact_prenom" class="text" name="fact_prenom" style="width:90%; margin-bottom:0;"></p>
			<p style="float:left; width:50%;"><label for="fact_nom"><strong>Nom :</strong></label> <input type="text" id="fact_nom" class="text" name="fact_nom" style="width:90%; margin-bottom:0;"></p>
			<div style="clear:both;"></div>
			<p><label for="fact_adresse_1"><strong>Adresse 1 :</strong></label> <input type="text" id="fact_adresse_1" class="text" name="fact_adresse_1" style="width:95%"></p>
			<p><label for="fact_adresse_2">Adresse 2 :</label> <input type="text" id="fact_adresse_2" class="text" name="fact_adresse_2" style="width:95%"></p>
			<p style="float:left; width:50%;"><label for="fact_code_postal"><strong>Code postal :</strong></label> <input type="text" id="fact_code_postal" class="text" name="fact_code_postal" style="width:90%; margin-bottom:0;"></p>
			<p style="float:left; width:50%;"><label for="fact_ville"><strong>Ville :</strong></label> <input type="text" id="fact_ville" class="text" name="fact_ville" style="width:90%; margin-bottom:0;"></p>
			<div style="clear:both;"></div>
			<p style="float:left; width:50%;"><label for="fact_email"><strong>Email :</strong></label> <input type="text" id="fact_email" class="text" name="fact_email" style="width:90%; margin-bottom:0;"></p>
			<p style="float:left; width:50%;"><label for="fact_cemail"><strong>Confirmation email :</strong></label> <input type="text" id="fact_cemail" class="text" name="fact_cemail" style="width:90%; margin-bottom:0;"></p>
			<div style="clear:both;"></div>
		</div>
		
		<hr />
		
		<p>
			L\'adhésion ne sera définitive qu\'après acceptation du conseil d\'administration conformément à l\'article 6 des statuts.
		</p>
		
		<p class="submit">
			<input type="submit" value="Je valide ma demande d\'adhésion" id="wp-submit" name="wp-submit">
		</p>
		
				
	</form>';

  return $resultat;
}

function GetFormCB()
{
  if (is_admin()) {
    return;
  }
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
  //	DB::Connexion("localhost", "bqjx5437_admin-assoedc", "hRdaGigvUxkZ", "bqjx5437_assoedc");

  if (isset($_POST['idp'])) {

    $id_prospect = $_POST['idp'];
    $checkSum = $_POST['c'];

    $resultat = '';

    $prospect = new Prospect($id_prospect);
    if ($prospect->GetCheckSum() != $_GET['c']) {
      return 'Vous n\'avez pas accès à cette page.';
    }

    $partenaire = new Partenaire($prospect->id_partenaire);

    // On créé la commande
    $commande = new Commande();
    $commande->id_prospect = $prospect->id_prospect;
    $commande->id_partenaire = $prospect->id_partenaire;
    $commande->date_reponse = date('Y-m-d H:i:s');;
    $commande->nom = $_POST['nom'];
    $commande->prenom = $_POST['prenom'];
    $commande->adresse_1 = $_POST['adresse_1'];
    $commande->adresse_2 = $_POST['adresse_2'];
    $commande->code_postal = $_POST['code_postal'];
    $commande->ville = $_POST['ville'];
    $commande->email = $_POST['email'];
    $commande->tel_1 = $_POST['tel_1'];
    $commande->tel_2 = $_POST['tel_2'];
    $commande->civilite = $_POST['civilite'];
    $commande->situation_familiale = $_POST['situation_familiale'];
    $commande->nb_personne = $_POST['nb_personne'];
    $commande->montant = $partenaire->prix_remised;
    $commande->date_paiement = date('Y-m-d H:i:s');
    $commande->allow_partenaire_dossier = (isset($_POST['allow_partenaire_dossier']) && $_POST['allow_partenaire_dossier'] == 'on') ? 1 : 0;
    $commande->allow_partenaire_coord = (isset($_POST['allow_partenaire_coord']) && $_POST['allow_partenaire_coord'] == 'on') ? 1 : 0;
    $commande->Save();

    $montant = $partenaire->prix_remised;
  } elseif (isset($_POST['new_adh_from_c'])) {
    $url = 'http://localhost:3000/cotisations/montant';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    $data_string = json_encode($data);
    curl_setopt(
      $ch,
      CURLOPT_HTTPHEADER,
      array(
        'Content-Type:application/json',
      )
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    /* $url = URL_WS_ADH . 'v1.0/json/getMontantCotissation'; */
    /* $response = \Httpful\Request::get($url)->send(); */
    if ($httpcode == 200) {
      $retval = json_decode($response);
    } else {

      return 'Erreur WS';
    }


    die(var_dump($retval));
    $montant = $retval->montant1;
    if ($retval->montant2 > 0) {
      $montant = $retval->montant2;
    }

    $dateFin = explode('-', $retval->date_fin);
    $dateFin = $dateFin[2] . '/' . $dateFin[1] . '/' . $dateFin[0];

    // On créé la commande
    $commande = new Commande();
    $commande->nom = $_POST['nom'];
    $commande->prenom = $_POST['prenom'];
    $commande->adresse_1 = $_POST['adresse_1'];
    $commande->adresse_2 = $_POST['adresse_2'];
    $commande->code_postal = $_POST['code_postal'];
    $commande->ville = $_POST['ville'];
    $commande->email = $_POST['email'];
    $commande->tel_1 = $_POST['tel_1'];
    $commande->tel_2 = $_POST['tel_2'];
    $commande->civilite = $_POST['civilite'];
    $commande->situation_familiale = $_POST['situation_familiale'];
    $commande->nb_personne = $_POST['nb_personne'];
    $commande->montant = $montant;
    $commande->date_paiement = date('Y-m-d H:i:s');
    $commande->origine = $_POST['origine'];
    $commande->origine_details = $_POST['conseiller_nom'];
    $commande->date_reponse = date('Y-m-d H:i:s');;

    $commande->conseiller_societe = $_POST['conseiller_societe'];
    $commande->conseiller_adresse_1 = $_POST['conseiller_adresse_1'];
    $commande->conseiller_adresse_2 = $_POST['conseiller_adresse_2'];
    $commande->conseiller_codepostal = $_POST['conseiller_codepostal'];
    $commande->conseiller_ville = $_POST['conseiller_ville'];
    $commande->conseiller_email = $_POST['conseiller_email'];

    $commande->Save();

    //$montant = Configuration::GetValue('PRIX_FIRST');

  } elseif (isset($_POST['new_adh'])) {
    if (isset($_POST['new_form'])) {
      $url = 'http://localhost:3000/cotisations/montant';

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
          'Content-Type:application/json',
        )
      );
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, $url);
      $response = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      /* $url = URL_WS_ADH . 'v1.0/json/getMontantCotissation'; */
      /* $response = \Httpful\Request::get($url)->send(); */
      if ($httpcode == 200) {
        $retval = json_decode($response);
      } else {

        return 'Erreur WS';
      }


      $montant = $retval->montant1;
      if ($retval->montant2 > 0) {
        $montant = $retval->montant2;
      }

      $dateFin = explode('-', $retval->date_fin);
      $dateFin = $dateFin[2] . '/' . $dateFin[1] . '/' . $dateFin[0];

      // On créé la commande
      $commande = new Commande();
      $commande->nom = $_POST['nom'];
      $commande->prenom = $_POST['prenom'];
      $commande->adresse_1 = $_POST['adresse_1'];
      $commande->adresse_2 = $_POST['adresse_2'];
      $commande->code_postal = $_POST['code_postal'];
      $commande->ville = $_POST['ville'];
      $commande->email = $_POST['email'];
      $commande->tel_1 = $_POST['tel_1'];
      $commande->tel_2 = $_POST['tel_2'];
      $commande->civilite = $_POST['civilite'];
      $commande->situation_familiale = $_POST['situation_familiale'];
      $commande->nb_personne = $_POST['nb_personne'];
      $commande->montant = $montant;
      $commande->date_paiement = date('Y-m-d H:i:s');

      /*
                                                                              $commande->origine = $_POST['origine'];

                                                                              $commande->origine_details = $_POST['conseiller_nom'];
                                                                              */
      $commande->origine = $_POST['origine'];
      if ($_POST['origine'] == 'parrainage') {
        $commande->origine_details = $_POST['adherent_parain'];
      }
      if ($_POST['origine'] == 'conseiller') {
        $commande->origine_details = $_POST['conseiller_nom'];
      }
      if ($_POST['origine'] == 'autre') {
        $commande->origine_details = $_POST['autre_texte'];
      }

      $commande->allow_partenaire_dossier = (isset($_POST['allow_partenaire_dossier']) && $_POST['allow_partenaire_dossier'] == 'on') ? 1 : 0;
      $commande->allow_partenaire_coord = (isset($_POST['allow_partenaire_coord']) && $_POST['allow_partenaire_coord'] == 'on') ? 1 : 0;

      if ($_POST['facturation_same'] == 0) {
        $commande->conseiller_societe = $_POST['fact_societe'];
        $commande->conseiller_entite_com = $_POST['fact_entite_commerciale'];
        $commande->conseiller_civilite = $_POST['fact_civilite'];
        $commande->conseiller_nom = $_POST['fact_nom'];
        $commande->conseiller_prenom = $_POST['fact_prenom'];
        $commande->conseiller_adresse_1 = $_POST['fact_adresse_1'];
        $commande->conseiller_adresse_2 = $_POST['fact_adresse_2'];
        $commande->conseiller_codepostal = $_POST['fact_code_postal'];
        $commande->conseiller_ville = $_POST['fact_ville'];
        $commande->conseiller_email = $_POST['fact_email'];
      }

      $commande->Save();
    } else {
      $url = 'http://localhost:3000/cotisations/montant';

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
          'Content-Type:application/json',
        )
      );
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, $url);
      $response = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      /* $url = URL_WS_ADH . 'v1.0/json/getMontantCotissation'; */
      /* $response = \Httpful\Request::get($url)->send(); */
      if ($httpcode == 200) {
        $retval = json_decode($response);
      } else {
        return 'Erreur WS';
      }


      $montant = $retval->montant1;
      if ($retval->montant2 > 0) {
        $montant = $retval->montant2;
      }

      $dateFin = explode('-', $retval->date_fin);
      $dateFin = $dateFin[2] . '/' . $dateFin[1] . '/' . $dateFin[0];

      // On créé la commande
      $commande = new Commande();
      $commande->nom = $_POST['nom'];
      $commande->prenom = $_POST['prenom'];
      $commande->adresse_1 = $_POST['adresse_1'];
      $commande->adresse_2 = $_POST['adresse_2'];
      $commande->code_postal = $_POST['code_postal'];
      $commande->ville = $_POST['ville'];
      $commande->email = $_POST['email'];
      $commande->tel_1 = $_POST['tel_1'];
      $commande->tel_2 = $_POST['tel_2'];
      $commande->civilite = $_POST['civilite'];
      $commande->situation_familiale = $_POST['situation_familiale'];
      $commande->nb_personne = $_POST['nb_personne'];
      $commande->montant = $montant;
      $commande->date_paiement = date('Y-m-d H:i:s');
      $commande->origine = $_POST['origine'];
      if ($_POST['origine'] == 'parrainage') {
        $commande->origine_details = $_POST['adherent_parain'];
      }
      if ($_POST['origine'] == 'conseiller') {
        $commande->origine_details = $_POST['conseiller_nom'];
      }
      if ($_POST['origine'] == 'autre') {
        $commande->origine_details = $_POST['autre_texte'];
      }
      $commande->Save();
    }

    //$montant = Configuration::GetValue('PRIX_FIRST');

  } elseif (isset($_GET['m']) && $_GET['m'] == 'ws') {
    //if ($_SESSION['adherent_infos']->NumAdhesion == '47749')
    //{
    $url = 'http://localhost:3000/cotisations';
    $data = array(
      "num_adherent" => trim($_SESSION['adherent_infos']->NumAdhesion),
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

    /* $url = URL_WS_ADH . 'v1.0/json/user/getPaiementCotisation'; */
    /* $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send(); */
    if ($httpcode == 200) {
      $retval = json_decode($response);

      if (isset($retval->Montant) && $retval->Montant > 0 &&  $retval->IdCotisationAnnee != "") {
        // On créé la commande
        $commande = new Commande();
        $commande->num_adhesion = $_SESSION['adherent_infos']->NumAdhesion;
        $commande->nom = $_SESSION['adherent_infos']->Nom;
        $commande->prenom = $_SESSION['adherent_infos']->Prenom;
        $commande->adresse_1 = $_SESSION['adherent_infos']->Adresse1;
        $commande->adresse_2 = $_SESSION['adherent_infos']->Adresse2;
        $commande->code_postal = $_SESSION['adherent_infos']->CP;
        $commande->ville = $_SESSION['adherent_infos']->Ville;
        $commande->email = $_SESSION['adherent_infos']->Email;
        $commande->tel_1 = $_SESSION['adherent_infos']->Domicile;
        $commande->tel_2 = $_SESSION['adherent_infos']->Portable;
        $commande->civilite = $_SESSION['adherent_infos']->Civilite;
        $commande->montant = $retval->Montant;
        $commande->date_paiement = date('Y-m-d H:i:s');
        $commande->Save();
      } else {
        return 'Erreur';
      }
    }
    //}
  } else {

    /*
                                                    $row = 0;

                                                    $montant = 0;

                                                    $trouve = false;
                                                    if (($handle = fopen(dirname(__FILE__).'/../../../../../CarteAuto.txt', "r")) !== FALSE)
                                                    {
                                                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                                            $row++;
                                                            if ($row>=1)
                                                            {
                                                                if (trim($data[0]) == trim($_SESSION['adherent_infos']->NumAdhesion))
                                                                {
                                                                    $montant = str_replace(',', '.', trim($data[1]));
                                                                    $trouve = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        fclose($handle);
                                                    }
                                                    else
                                                        echo 'Erreur fichier';

                                                    if ($trouve && $montant > 0)
                                                    {
                                                        // On créé la commande
                                                        $commande = new Commande();
                                                        $commande->num_adhesion = $_SESSION['adherent_infos']->NumAdhesion;
                                                        $commande->nom = $_SESSION['adherent_infos']->Nom;
                                                        $commande->prenom = $_SESSION['adherent_infos']->Prenom;
                                                        $commande->adresse_1 = $_SESSION['adherent_infos']->Adresse1;
                                                        $commande->adresse_2 = $_SESSION['adherent_infos']->Adresse2;
                                                        $commande->code_postal = $_SESSION['adherent_infos']->CP;
                                                        $commande->ville = $_SESSION['adherent_infos']->Ville;
                                                        $commande->email = $_SESSION['adherent_infos']->Email;
                                                        $commande->tel_1 = $_SESSION['adherent_infos']->Domicile;
                                                        $commande->tel_2 = $_SESSION['adherent_infos']->Portable;
                                                        $commande->civilite = $_SESSION['adherent_infos']->Civilite;
                                                        $commande->montant = $montant;
                                                        $commande->date_paiement = date('Y-m-d H:i:s');
                                                        $commande->Save();
                                                    }
                                                    else
                                                    {
                                                        die('Erreur');
                                                    }
                                                    */

    $url = 'http://localhost:3000/cotisations';
    $data = array(
      "num_adherent" => trim($_SESSION['adherent_infos']->NumAdhesion),
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

    /* $url = URL_WS_ADH . 'v1.0/json/user/getPaiementCotisation'; */
    /* $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send(); */
    if ($httpcode == 200) {
      $retval = json_decode($response);

      if (isset($retval->Montant) && $retval->Montant > 0 &&  $retval->IdCotisationAnnee != "") {
        // On créé la commande
        $commande = new Commande();
        $commande->num_adhesion = $_SESSION['adherent_infos']->NumAdhesion;
        $commande->nom = $_SESSION['adherent_infos']->Nom;
        $commande->prenom = $_SESSION['adherent_infos']->Prenom;
        $commande->adresse_1 = $_SESSION['adherent_infos']->Adresse1;
        $commande->adresse_2 = $_SESSION['adherent_infos']->Adresse2;
        $commande->code_postal = $_SESSION['adherent_infos']->CP;
        $commande->ville = $_SESSION['adherent_infos']->Ville;
        $commande->email = $_SESSION['adherent_infos']->Email;
        $commande->tel_1 = $_SESSION['adherent_infos']->Domicile;
        $commande->tel_2 = $_SESSION['adherent_infos']->Portable;
        $commande->civilite = $_SESSION['adherent_infos']->Civilite;
        $commande->date_paiement = date('Y-m-d H:i:s');
        $commande->Save();
      } else {
        return 'Erreur';
      }
    }
  }

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
    $resultat .= '<p>Cotisation valide jusqu\'au <strong>' . $dateFin . '</strong></p>';
  }
  if (isset($retval->MessagePaiement) && $retval->MessagePaiement == ' ') {
    $resultat .= '
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
  // PS le 16/12/2021 - Peut importe la valeur de MessageAccueil
  elseif (isset($retval->MessageAccueil) && $retval->MessageAccueil == ' ') {
    //	else {
    $resultat .= '
		<p>Montant : ' . $commande->montant . ' &euro;</p>
		';
    /* Hard coded message,
                                                       because for some reasons MessagePaiement and MessageAccueil appear NULL (in PHP) from the request vPaiementEnLigneExport.
                                                       Whenever MessagePaiement or MessageAccueil are not ' ', they appear as NULL.
                                                       Maybe the problem is due to apostrophes appearing in MessagePaiement and MessageAccueil (to be tested).

                                                       This solution takes care of MessagePaiement or MessageAccueil appearing NULL but don't overwrite if they are not.
                                                       However, this solution should be considered as main solution, because it allows to format text in a more fashion way.
                                                    */
    // PS le 16/12/2021 - Suppresion du message en dur pour le prélèvement 2021
    if (is_null($retval->MessagePaiement)) {
      $resultat .= '<p>
						  
						  </p>';
    } else {
      $resultat .= $retval->MessagePaiement;
    }

    $resultat .= '
		<br>
		<a href="https://www.assoedc.com/adherent-connexion-2/" style="background-color:#C00; color:#FFFFFF; padding:10px; display:inline-block;" title="Retour">Annuler</a>
		<br>
		<br>
		';
  }
  /**/ else {
    $resultat .= '
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
  /**/

  return $resultat;
}
