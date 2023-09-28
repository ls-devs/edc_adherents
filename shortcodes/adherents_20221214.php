<?php
require_once(dirname(__FILE__).'/../lib/fonctions_generales.php');
require_once(dirname(__FILE__).'/../lib/httpful.phar');

add_shortcode('ADHERENT_AIDE_IMPOT_2016', 'getAideImpot2016');
add_shortcode('ADHERENT_MON_COMPTE', 'getMonCompte');
add_shortcode('ADHERENT_SECURE_MDP', 'getSecureMdp');
add_shortcode('ADHERENT_MES_DONNEES', 'getMesDonnees');
add_shortcode('ADHERENT_CHANGE_ADRESSE', 'getChangeAdresse');
add_shortcode('ADHERENT_MES_INVESTISSEMENTS', 'getMesInvestissements');
add_shortcode('ADHERENT_MON_INVESTISSEMENT', 'getMonInvestissement');
add_shortcode('ADHERENT_MES_DOSSIERS', 'getMesDossiers');
add_shortcode('ADHERENT_MON_DOSSIER', 'getMonDossier');
add_shortcode('ADHERENT_MES_COTISATIONS', 'getMesCotisations');
add_shortcode('ADHERENT_MON_APPEL_COTISATION', 'getAppelCotissation');
add_shortcode('ADHERENT_MON_APPEL_COTISATION_2018', 'getAppelCotissation2018');
add_shortcode('ADHERENT_MON_APPEL_COTISATION_2018', 'getAppelCotissation2018');
add_shortcode('ADHERENT_MON_APPEL_COTISATION_2019', 'getAppelCotissation2019');
add_shortcode('ADHERENT_MON_APPEL_COTISATION_2020', 'getAppelCotissation2020');
add_shortcode('ADHERENT_MON_APPEL_COTISATION_2021', 'getAppelCotissation2021');
add_shortcode('ADHERENT_MON_APPEL_COTISATION_2022', 'getAppelCotissation2022');
add_shortcode('ADHERENT_MON_APPEL_COTISATION_2023', 'getAppelCotissation2023');
add_shortcode('ADHERENT_ATTESTATION_COTISATION', 'attestationCotisation2PDF');
add_shortcode('ADHERENT_NOUS_CONTACTER', 'nous_contacter');
add_shortcode('ADHERENT_MES_DOCUMENTS', 'getMesDocuments');
add_shortcode('ADHERENT_MESSAGE_ACCUEIL', 'getMessageAccueil');

function nous_contacter()
{	
	if (is_admin()) return;
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
	
	$url= get_permalink ( get_option( 'edc_adherent_id_page_nous_contacter' ) );
	
	
	//Formulaire envoy�
	if (isset($_POST["Message"]) && !empty($_POST["Message"]))
	{	
		$email=$_POST["email"];		
		//session_start('EDC');
		
		$email_to = "";
				
		$email_to = $sujet[stripslashes($_POST['destinataire'])];;
		$subject = "[EXTRANET ADHERENT EDC] : " . stripslashes($_POST['destinataire']);	

		if ( $email_to != "")
		{
			// GF 17-12-2012 Utilisation de la fonction générale envoiMail
			//Envoi d'un mail contenant le message de l'adhérent au service concerné
			$fichierModel=WP_PLUGIN_DIR."/edc_adherent/modeles/AR_EDC_message.html";
			
			$t_contenu=array(	0=>array("search"=>"[NumAdhesion]", "replace"=>$_SESSION['adherent_infos']->NumAdhesion)
								,1=>array("search"=>"[Date]", "replace"=>date("d/m/Y H:i:s"))
								,2=>array("search"=>"[Civilite]", "replace"=>$_SESSION['adherent_infos']->Civilite)
								,3=>array("search"=>"[Prenom]", "replace"=>$_SESSION['adherent_infos']->Prenom)
								,4=>array("search"=>"[Nom]", "replace"=>$_SESSION['adherent_infos']->Nom)
								,5=>array("search"=>"[Email]", "replace"=>$_SESSION['adherent_infos']->Email)
								,6=>array("search"=>"[Message]", "replace"=>nl2br(stripslashes($_POST['Message'])))						
								);
								
			$t_infosMail = array(	"From"=> 'extranet@edc.asso.fr'
									,"To"=>$email_to
									,"Subject"=>$subject
									,"Reply to"=>$_SESSION['adherent_infos']->Email
								);
			
			//Envoi de l'accusé de réception à l'adhérent
			if(envoiMail($fichierModel, $t_contenu, $t_infosMail) == true)
			{
				if ($_POST['destinataire'] == 'La vente de mon bien')
				{
					$fichierModel=WP_PLUGIN_DIR."/edc_adherent/modeles/AR_ADH_message_vente.html";
				}
				else
				{
					$fichierModel=WP_PLUGIN_DIR."/edc_adherent/modeles/AR_ADH_message.html";
				}
				
				$t_contenu=array(	0=>array("search"=>"[NumAdhesion]", "replace"=>$_SESSION['adherent_infos']->NumAdhesion)
									,1=>array("search"=>"[Date]", "replace"=>date("d/m/Y H:i:s"))
									,2=>array("search"=>"[Civilite]", "replace"=>$_SESSION['adherent_infos']->Civilite)
									,3=>array("search"=>"[Prenom]", "replace"=>$_SESSION['adherent_infos']->Prenom)
									,4=>array("search"=>"[Nom]", "replace"=>$_SESSION['adherent_infos']->Nom)
									,5=>array("search"=>"[Email]", "replace"=>$_SESSION['adherent_infos']->Email)
									,6=>array("search"=>"[Domaine]", "replace"=> stripslashes($_POST['destinataire']))
									,7=>array("search"=>"[Message]", "replace"=>nl2br(stripslashes($_POST['Message'])))						
								);
									
				$t_infosMail = array(	"From"=>'noreply@assoedc.com'
										,"To"=>$_SESSION['adherent_infos']->Email
										,"Subject"=>"[EXTRANET ADHERENT EDC] : Prise en compte de votre demande"
									);
	
				envoiMail($fichierModel, $t_contenu, $t_infosMail);
				
				$ret = '<script type="text/javascript">';
   				$ret .= '   location.href= \''.$url . '?errorse=0\';';
   				$ret .= '</script>';
				$ret .= '<div style="padding:5px;background-color:white;">Envoi de votre message en cours...</div>';
				return $ret;
	
			}
			else
			{
				$ret = $headers;
				$ret .= $subject;
				$ret .= $message;
				$ret .= '<script type="text/javascript">';
   				$ret .= '   location.href= \''.$url . '?errorse=1\';';
   				$ret .= '</script>';
				return $ret;
			}
		}
		else
		{
			$ret = $headers;
			$ret .= $subject;
			$ret .= $message;
			$ret .= '<script type="text/javascript">';
			$ret .= '   location.href= \''.$url . '?errorse=1\';';
			$ret .= '</script>';
			return $ret;
		}
	}
	else
	{
		//Formulaire d'envoi		
		// BM 27/06/2011
		// Ajout d'un test pour sortir la cr�ation du formulaire lorsque l'on affiche un retour d'envoi par email.
		if (isset($_GET['errorse']) && $_GET['errorse']=="1")
		{
			// il y a eu une erreur d'envoi
			$result .= '<h6 style="color:red">Votre message n&#146;a pu &ecirc;tre envoy&eacute; suite &agrave; une anomalie technique</h6>';
		}
		elseif (isset($_GET['errorse']) && $_GET['errorse']=="0")
		{
			// il n'y a pas eu d'erreur
			$result .= '<h6 style="color:green; font-size:14px;">Votre message nous a &eacute;t&eacute; adress&eacute;.</h6>';
			$result .= '<h6 style="color:green; font-size:14px;">Un mail r&eacute;capitulant votre demande vous a été envoyé.</h6>';
		}
		else
		{
			// on affiche le formulaire
			$result .= '<form name="UpdateDonnees" id="UpdateDonnees" method="post" action="' . $url . '">';
			
					$result .= '<h6 style="margin-top:5px;margin-bottom:5px;">Votre question concerne : <select name="destinataire" style="margin-bottom:0px"></h6>';
					
					foreach($sujet as $s => $dest)
					{		
						$result .= '<option value="' . $s . '">' . $s . '</option>';
					}
					$result .= '</select><br/>';
			
				$result .= '<h6 style="margin-top:5px;margin-bottom:5px;">Votre message :</h6><br/><textarea name="Message" rows="10" cols="72"></textarea></p><br/>';
				$result .= '<p style="text-align:right"><input type="submit" id="submit" name="submit" value="Envoyer"/></p>';
				$result .= '</form>';
				$result .= '<div id="about"></div>';
		}		
	}
	// on revoie l'affichage dans la page
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}

function getMonDossier()
{
	if (is_admin()) return;
	
	$url_mes_dossiers= get_permalink ( get_option( 'edc_adherent_id_page_mes_dossiers' ) ); //searchPageUrl('[mesdossiers]');
	$result .='';
	//Si l'url contient l'id de la fiche
	//session_start('EDC');
	//print_r($_SESSION);
	if(isset($_GET['ficheid']))
	{
		$url = URL_WS_ADH.'v1.0/json/user/getFiche/'.$_GET["ficheid"];	
		$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
		if ($response->code == 200)
			$retval = json_decode($response->body);
		else
			return 'Erreur WS';

		//var_dump($retval);
				
		if (isset($retval->fiche->Gestionnaire) && is_object($retval->fiche->Gestionnaire)) $retval->fiche->Gestionnaire = '';
		
		$fiche = array('FicheId'=>isset($retval->fiche->FicheId)?(string)$retval->fiche->FicheId:''
					, 'FicheRef'=>isset($retval->fiche->FicheRef)?(string)$retval->fiche->FicheRef:''
					, 'DatePriseEnCompte'=>isset($retval->fiche->DatePriseEnCompte)?date_to_string((string)$retval->fiche->DatePriseEnCompte):''
					, 'DateCloture'=>isset($retval->fiche->DateCloture)?date_to_string((string)$retval->fiche->DateCloture):''
					, 'Domaine'=>isset($retval->fiche->Domaine)?(string)$retval->fiche->Domaine:''
					, 'SousDomaine'=>isset($retval->fiche->SousDomaine)?(string)$retval->fiche->SousDomaine:''
					, 'Gestionnaire'=>isset($retval->fiche->Gestionnaire)?(string)$retval->fiche->Gestionnaire:''
					, 'Statut'=>isset($retval->fiche->Statut)?(string)$retval->fiche->Statut:''
					, 'Programme'=>isset($retval->fiche->Programme)?(string)$retval->fiche->Programme:''
					, 'Lot'=>isset($retval->fiche->Lot)?(string)$retval->fiche->Lot:''
					, 'DateDerniereAction'=>isset($retval->fiche->DateDerniereAction)?date_to_string((string)$retval->fiche->DateDerniereAction):''
					, 'lib_statut'=>isset($retval->fiche->lib_statut)?(string)$retval->fiche->lib_statut:''
					, 'lib_histo'=>isset($retval->fiche->lib_histo)?(string)$retval->fiche->lib_histo:'');
		//Affichage des informations g�n�rales du dossier(fiche)
		//print_r($fiche);
		$result .='<div id="dossier">';			
		if($fiche['lib_statut']== 1)
		{
			$result .= '<div align="center" style="background-color: #eeeeee">Ce dossier a &eacute;t&eacute; cl&ocirc;tur&eacute; le '.$fiche['DateCloture'].'</div>';
		}
		else
		{
			$result .= '<div style="background-color:#C4DEF1">Dossier en cours de traitement</div>';
		}	
				
		if ($fiche['SousDomaine'] =='')				
			$result .='<h1>'.$fiche['Domaine'].'  (ref : '.$fiche['FicheRef'].')</h1>';
		else
			$result .='<h1>'.$fiche['SousDomaine'].' (ref : '.$fiche['FicheRef'].')</h1>';

		if ($fiche['DatePriseEnCompte']!= '' || $fiche['DateDerniereAction']!= '' ||$fiche['Statut']!= '' ||$fiche['Gestionnaire']!= '')
		{
			$result .= '<div style="font-size:small;margin-left:25px;align:right">
							<ul>';
			
			if ($fiche['Programme'] != '' &&  $fiche['Lot'] != '')
			{
				$result .='<li><em>concerne mon investissement <strong>'.$fiche['Programme'] . ' Lot : ' . $fiche['Lot'].'</strong></em></li>';
			}
			else
			{
				$result .='<li><em>Ce dossier qui concerne <strong>mon adh&eacute;sion </strong></em></li>';
			}
			if($fiche['DatePriseEnCompte']!= '')
			{
				$result .= '<li><em>a &eacute;t&eacute; pris en compte le <strong>' . $fiche['DatePriseEnCompte'] . '</strong></em></li>';
			}
			if($fiche['DateDerniereAction']!= '')
			{
				$result .= '<li><em>la derni&egrave;re action r&eacute;alis&eacute;e date du <strong>' . $fiche['DateDerniereAction'] . '</strong></em></li>';
			}
			
			if ($fiche['Gestionnaire']!= '')
			{
				$result .= '<li><em>est suivi par <strong>' . $fiche['Gestionnaire'] . '</strong></em></li>';
			}
			$result .= '</ul></div>';
		}
		$result .= '</div>';

		//fin de la table des informations sur la fiche
		
		//R�cup�ration de l'url donn�e dans la page d'administration WP
		
		$formulairePostReaction = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';; //searchPageUrl('[postReactionAdherent]');
		
		$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
		//Recherche des actions correspondantes � la fiche
		
		//S'il y a des actions, affichage des actionsv
		//$result .= '<center><hr style="margin-top:20px" width="75%" size="2" noshade/></center>';			
		$toujours_ouverte=true;
		
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
		
		if ($toujours_ouverte ==true)
		{
			//if ($user_email == "bmasdoua@yahoo.fr") 
			$result .= '<div id="reagir">
								<p><img style="margin:0px;" align="absmiddle" src="'.GESTION_ADH_REP_ICONES.'/information.png">&nbsp;Vous souhaitez nous faire part d\'une avanc&eacutee; sur votre dossier ? vous avez un nouvel &eacute;l&eacute;ment ? une remarque &agrave; nous transmettre ? Cliquez sur le bouton &laquo; Ajouter un commentaire &raquo;</p>
								<div align="right">
								<input type="button" id="reagir_dossier" class="art-button" name="reagir_dossier" value="Ajouter un commentaire" onClick="reagir(\'reagir\',\''.GESTION_ADH_REP_ICONES.'\', \''.$formulairePostReaction.'\', \''.$_GET["ficheid"].'\', \''.$_SESSION['adherent_infos']->IdContact.'\',\''.$url_ajax.'\');"></div>
							</div>								
							<input type="hidden" name="contenuDiv" id="contenuDiv" value="">';
		}
		
		if ($_SESSION['adherent_infos']->NumAdhesion == '47749')
			$result .= getMesDocuments($_GET["ficheid"], '', false);
		
		
		$result .= '<div id="ListeActions">'.displayListeActions($_GET["ficheid"]).'</div>';	
		
	}
	else
	{
		//Si l'url de la page ne contient pas l'id du dossier (fiche) 	=> l'utilisateur n'est pas pass� au pr�alable par Mes dossiers
		//Redirection sur Mes dossiers
		$ret = '<script type="text/javascript">';
		$ret .= '   location.href= \''. $url_mes_dossiers. '\';';
		$ret .= '</script>';
		return $ret;
	
	}				

	return '<div style="padding:5px;background-color:white;">' . $result .'</div>';

}

function getMesDossiers()
{
	if (is_admin()) return;
	
	try
	{
		$result ='';
		
		$result = displayListeDossiers2($result);		
		
		return '<div style=" padding:5px;background-color:white;overflow: auto;">' . $result . '</div>';
	// Fin modif BM
	}
	catch(Exception $e)
	{
		die('error');
		return 'Une erreur est survenue, veuilez nous excuser pour ce d�sagr�ement';
	}
	
}


function getMesDocuments($id_fiche = '', $id_lot = '', $formAdd = true)
{
	if (is_admin()) return;
	if ($_SESSION['adherent_infos']->NumAdhesion != '47749') return;
	
	$url = URL_WS_ADH.'v1.0/json/user/getDocuments';	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	if ($response->code == 200)
		$retval = json_decode($response->body);
	else
		return 'Erreur WS';
		
	foreach($retval as &$fichier)
	{
		$fichier->DateArchivage = date('Y-m-d H:i', strtotime($fichier->DateArchivage));
		$fichier->DateArchivageAff = date('d/m/Y H:i', strtotime($fichier->DateArchivage));
	}
	
	for ($i=0; $i<count($retval)-1; $i++)
	{
		$trouve = false;
		for ($j=0; $j<count($retval)-$i-1; $j++)
		{
			if ($retval[$j]->DateArchivage < $retval[$j+1]->DateArchivage)
			{
				$t = $retval[$j];
				$retval[$j] = $retval[$j+1];
				$retval[$j+1] = $t;
				$trouve = true;
			}
		}
		
		if (!$trouve) break;
	}
	
	$result = '';
	foreach($retval as $fichier)
	{
		if ($id_fiche != '' && strtolower($id_fiche) != strtolower($fichier->IdFiche)) continue;
		if ($id_lot != '' && strtolower($id_lot) != strtolower($fichier->IdLot)) continue;
		
		$result .='
		<tr>
			<td>'.$fichier->Categorie.((trim($fichier->SousCategorie)!='')?' &gt; '.$fichier->SousCategorie:'').'</td>				
			<td>'.(($fichier->TitreAffichage!='')?$fichier->TitreAffichage:'Sans nom').'.'.$fichier->Extension.'</td>
			<td>'.$fichier->DateArchivageAff.'</td>
			<td><a href="https://adherents.edc.asso.fr/download_documents.php?f='.$fichier->IdFichier.'&num='.$_SESSION['adherent_infos']->IdAdhesion.'&c='.md5($fichier->IdFichier.'toto'.$_SESSION['adherent_infos']->IdAdhesion).'" target="_blank">Télécharger</a></td>
		</tr>';
	}
	
	if ($result != '')
	{
		$result ='<table class="rouge" style="width:100%;border-style:none;border-width:0px;">
			<tr>				
				<th>Catégorie</th>
				<th>Fichier</th>
				<th>Date</th>
				<th></th>
			</tr>'.$result;
		
		$result .='</table>';
	}
	else
	{
		$result = 'Aucun document disponible';
	}
	
	//print_r($retval);	
	$formAddContent = '';
	if ($formAdd)
	{
		$formAddContent = '';
		
		if (isset($_POST['todo']) && $_POST['todo'] == 'uploadDocument')
		{
			if ($_FILES["document"]["error"] == UPLOAD_ERR_OK)
			{
				$uploads_dir = dirname(__FILE__).'/../../../../tmp_documents';
				
				$tmp_name = $_FILES["document"]["tmp_name"];
				$name = $_FILES["document"]["name"];
				
				if (file_exists($uploads_dir.'/'.$name))
				{
					$n = explode('.', $name);
					$ext = array_pop($n);
					$nameSansExt = implode('.', $n);
					$i = 1;
					while (file_exists($uploads_dir.'/'.$nameSansExt.'_'.$i.'.'.$ext))
						$i++;
						
					$name = $nameSansExt.'_'.$i.'.'.$ext;
				}
				
				if (move_uploaded_file($tmp_name, $uploads_dir.'/'.$name))
				{
					$data = array(
							'filename'          => $name,
							'commentaire'     => $_POST['commentaire'],
							'ip_add' => $_SERVER['REMOTE_ADDR']
						);
					
					$url = URL_WS_ADH.'v1.0/json/user/addDocument';	
					$response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->body($data)->sendsType('application/x-www-form-urlencoded')->send();
					if ($response->code == 200)
						$r = json_decode($response->body);
					else
					{
						return 'Erreur WS';
					}
				
					$formAddContent = '<p>Votre document nous est bien parvenu, nous le traiterons dans les plus brefs délais.</p>';
					
				}
				else
					$formAddContent = '<p>Erreur lors de l\'envoi du document : déplacement du fichier impossible.';
			}
			else
			{
				$formAddContent = '<p>Erreur lors de l\'envoi du document : ';
				switch($_FILES["document"]["error"])
				{
					case UPLOAD_ERR_INI_SIZE : $formAddContent .= 'taille du document trop importante.'; break;
					case UPLOAD_ERR_FORM_SIZE : $formAddContent .= 'taille du document trop importante.'; break;
					case UPLOAD_ERR_PARTIAL : $formAddContent .= 'le document n\'a été que partiellement téléchargé.'; break;
					case UPLOAD_ERR_NO_FILE : $formAddContent .= 'aucun fichier n\'a été téléchargé'; break;
				}
				$formAddContent .= '</p>';
			}
		}
		
		$formAddContent .= '<a href="#" id="openFormAdd" style="background-color:#d91733; color:#FFFFFF; padding:10px;">Ajouter un document</a><br /><br />';
		
		$formAddContent .= '<script>
		jQuery(document).ready(function(e) {
			
			jQuery("#openFormAdd").click(function(e) {
                e.preventDefault();
				jQuery("#openFormAdd").hide();
				jQuery("#formUpload").show();
            });
			
           	jQuery("#formUpload").submit(function(e) {
                
				if (jQuery("#document").val() == "")
				{
					e.preventDefault();
					alert("Vous devez sélectionner un fichier");
				}
				else if (jQuery("#commentaire").val() == "")
				{
					e.preventDefault();
					alert("Vous devez indiquer un commentaire");
				}
				else
				{
					jQuery("#bEnvoyerFormAdd").hide();
					jQuery("#messageUpload").show();
				}
            });
        });
		</script>';
		
		
		$formAddContent .= '<form id="formUpload" style="display:none;" method="post" enctype="multipart/form-data">
	        <input type="hidden" name="todo" value="uploadDocument" />
            <label for="document">Document à envoyer : </label><input type="file" id="document" name="document" />
            <label for="commentaire">Commentaire</label>
            <textarea name="commentaire" style="width:300px;" id="commentaire"></textarea>
            <input type="submit" value="Envoyer" id="bEnvoyerFormAdd" style="background-color:#d91733; color:#FFFFFF; border:0; padding:5px 10px; cursor:pointer;" />
			<p style="display:none;" id="messageUpload">Envoi du document en cours. Merci de patienter, cette opération peux prendre plusieurs minutes.</p>
        </form>';
	}
	

	return '<div style=" padding:5px;background-color:white;overflow: auto;">' . $formAddContent . $result . '</div>';
}


function displayListeDossiers2($result)
{	
	if (is_admin()) return;
	$url_details = get_permalink ( get_option( 'edc_adherent_id_page_mon_dossier' ) ); // searchPageUrl('[detail_dossier]');
		
	$url = URL_WS_ADH.'v1.0/json/user/getFiches';	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
		$retval = json_decode($response->body);
	else
		return 'Erreur WS';
	
	// BM : nous aurons 3 zones, 
	// les fiches en cours
	// les fiches cloturees
	// les fiches historique
	$t_fiches =array();
	//Elaboration du HTML en sortie
	$legende ='<p>
				<img src="'.GESTION_ADH_REP_ICONES.'/status-offline.png" title="Ce dossier a &eacute;t&eacute; clotur&eacute;" alt="Ferm&eacute;" align="absmiddle"> <i>Dossier clotur&eacute;</i> | <img src="'.GESTION_ADH_REP_ICONES.'/status.png" title="Ce dossier est en cours de traitement" alt="Ouvert" align="absmiddle"><i>Dossier en cours de traitement</i> | <img src="'.GESTION_ADH_REP_ICONES.'/new-text.png" title="Une action a &eacute;t&eacute; ajout&eacute;e &agrave; votre dossier" alt="Nouveau !" align="absmiddle"><i>Une action a &eacute;t&eacute; r&eacute;cemment ajout&eacute;e</i></p>';
	$entete_encours ='<table class="rouge" style="width:100%;border-style:none;border-width:0px;">
			<tr>					
				<th>&nbsp;</th>
				<th>Ce dossier concerne</th>
				<th>Ce dossier porte sur</th>
				<th>Ouvert le</th>
				<th>Derni&egrave;re<br>action</th>
				<th>&nbsp;</th>
				<th>Détail</th>
			</tr>';
	$entete_traitees ='<table class="rouge" style="width:100%;border-style:none;border-width:0px;">
			<tr>	
				<th>&nbsp;</th>				
				<th>Ce dossier concerne</th>
				<th>Ce dossier porte sur</th>
				<th>Ouvert le</th>
				<th>Cl&ocirc;tur&eacute;<br/>le</th>
				<th>&nbsp;</th>
				<th>D&eacute;tail</th>
			</tr>';				
			
	// corps de		
	$t_fiches["encours"]='';
	$t_fiches["traitees"]='';
	$t_fiches["histo"]='';
	
	if (isset($retval->Fiches->FicheId)) $retval->Fiches = array($retval->Fiches);
	
	$i=0;
	foreach($retval->Fiches as $fiche)
	{	
		//Pour chaque fiche cr�ation d'un affichage tabulaire
		$bgcolor = '';
		if ($i%2==0)
		{
			//$bgcolor='#C4DEF1';	
		}
		else
		{
			$bgcolor = '#FFFFFF';	
		}
		
		$txt_fiche='<tr bgcolor="'.$bgcolor.'">';
		
		$txt_fiche.='<td style="vertical-align:middle; ">';
		if($fiche->lib_statut == 1)
		{
			$txt_fiche .='<img src="'.GESTION_ADH_REP_ICONES.'/status-offline.png" title="Ce dossier a &eacute;t&eacute; clotur&eacute;" alt="Ferm&eacute;">';	
		}
		else
		{
			$txt_fiche .='<img src="'.GESTION_ADH_REP_ICONES.'/status.png" title="Ce dossier est en cours" alt="Ce dossier est en cours">';		
		}
		$txt_fiche.='</td>';
//			$txt_fiche.='<td style="border-style:none;border-width:0px">'.((string)$fiche->FicheRef).'</td>';
		$txt_fiche.='<td style="vertical-align:middle; ">';
		
		if (isset($fiche->Programme) && $fiche->Programme != '' && isset($fiche->Lot) && $fiche->Lot != '')
		{
			$txt_fiche.=(string)$fiche->Programme.' Lot : '.(string)$fiche->Lot;
		}
		else
		{
			$txt_fiche .='Mon adh&eacute;sion';
		}
		$txt_fiche .='</td>
		<td style="vertical-align:middle; ">';			
		if (isset($fiche->SousDomaine) && $fiche->SousDomaine != '')
		{
			$txt_fiche .=formatDomaine((string)$fiche->SousDomaine);	
		}
		else
		{
			$txt_fiche .=formatDomaine((string)$fiche->Domaine);	
		}
		/*$txt_fiche .='</td>
					<td>'.date_to_string((string)$fiche->DatePriseEnCompte).'</td>
					<td>'.date_to_string((string)$fiche->DateDerniereAction).'</td>
					<td><a href="'.$url_details.'&ficheid='.((string)$fiche->FicheId).'"><img src="'.WP_PLUGIN_URL.'/GestionAdherent/icones/eye.png" align="absmiddle" style="float:left;"></a></td>
				</tr>';*/
		// on teste la date de dernière action
		list($jour,$mois,$annee)=explode("/",date_to_jjmmaaaa((string)$fiche->DateDerniereAction));
		//$ts_action = time(0,0,0,$mois,$jour,$annee);		
		$ts_action = strtotime($annee.'-'.$mois.'-'.$jour);
		$ts_now = time();
		/*
		if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35')
		{
			
			echo $jour.'-'.$mois.'-'.$annee.'<br />';
			echo $ts_action.' '.date('Y-m-d H:i:s', $ts_action).'<br />';
			echo $ts_now.' '.date('Y-m-d H:i:s', $ts_now).'<br />';
			echo ($ts_now - $ts_action).' < '.(3*3600*24).'<br />';
		}
		*/
		//echo $ts_now .'-'.$ts_action .'='.($ts_now - $ts_action).' / '.(3*3600*24).'<br> ';
		if (($ts_now - $ts_action) < (3*3600*24))
		{
			$icone_new ='<img src="'.GESTION_ADH_REP_ICONES.'/new-text.png" title="Une action a &eacute;t&eacute r&eacute;cemment ajout&eacute;e &aacute; votre dossier" alt="Nouveau !">';	
			//$bgcolor_new ='bgcolor="#E4680C"';
		}
		else
		{
			$icone_new ='';	
			//$bgcolor_new='';
		}
		$bgcolor_new = '';
		
		$txt_fiche .='</td>
					<td style="text-align:center; vertical-align:middle;">'.date_to_jjmmaaaa((string)$fiche->DatePriseEnCompte).'</td>';
		if($fiche->lib_statut == 1)
		{			
			$txt_fiche .='<td style="text-align:center;vertical-align:middle;">'.date_to_jjmmaaaa((string)$fiche->DateCloture).'</td>';
		}
		else
		{
			$txt_fiche .='<td style="text-align:center;vertical-align:middle;">'.date_to_jjmmaaaa((string)$fiche->DateDerniereAction).'</td>';
		}
					
		$txt_fiche .='<td style="vertical-align:middle;" '.$bgcolor_new.'>'.$icone_new.'</td>
					<td style="text-align:center;vertical-align:middle;"><a href="'.$url_details.'?ficheid='.((string)$fiche->FicheId).'"><img src="'.GESTION_ADH_REP_ICONES.'/eye.png" title="Consultez le d&eacute;tail de votre dossier"></a></td>
				</tr>';		
		if($fiche->lib_statut == 1)
		{
			if($fiche->lib_histo == 1)
			{
				$t_fiches["histo"].=$txt_fiche;	
			}
			else
			{
				$t_fiches["traitees"].=$txt_fiche;		
			}
		}
		else
		{
			$t_fiches["encours"].=$txt_fiche;	
		}
		
		if (is_object($fiche->Gestionnaire)) $fiche->Gestionnaire = '';
		
		//Enregistrement en session de la fiche avec pour index son id
			$_SESSION[((string)$fiche->FicheId)]=array('FicheRef'=>((string)$fiche->FicheRef),
															'Lot'=> ((string)$fiche->Lot),
															'Programme'=>((string)$fiche->Programme),
															'Domaine'=>formatDomaine((string)$fiche->Domaine),
															'SousDomaine'=>formatDomaine((string)$fiche->SousDomaine),
															'DatePriseEnCompte'=>date_to_string((string)$fiche->DatePriseEnCompte),
															'DateDerniereAction'=>date_to_string((string)$fiche->DateDerniereAction),
															'DateCloture'=>date_to_string((string)$fiche->DateCloture),
															'Statut'=>formatStatut((string)$fiche->Statut),
															'Gestionnaire'=>((string)$fiche->Gestionnaire));

			//Stockage du guid de la fiche dans un tableau 
			$guid_fiches[] = ((string)$fiche->FicheId);
					
		$i++;				
	}
	$pied_table = '</table>';		
	
	$result = $legende; 
	if (strlen($t_fiches["encours"]) >0)
	{
		$result .='<H2>Mes dossiers en cours</H2>';	
		$result .=$entete_encours;
		$result .= $t_fiches["encours"];
		$result.=$pied_table;
	}
	else
	{					
		$result .='<H2>Mes dossiers en cours</H2>';	
		$result .='<p>Vous n&apos;avez aucun dossier actuellement en cours de traitement par nos services.</p>';			
	}
	
	/*if ($t_fiches["traitees"] != '')
	{
		$result .='<H2>Mes dossiers cloturés dans les 30 derniers jours</H2>';	
		$result .=$entete_traitees;
		$result .= $t_fiches["traitees"];
		$result.=$pied_table;
	}*/
	
	if ($t_fiches["histo"] != '' || $t_fiches["traitees"] != '')
	{
		$result .='<H2>Historique de mes dossiers cl&ocirc;tur&eacute;s</H2>';	
		$result .=$entete_traitees;
		$result .= $t_fiches["traitees"];
		$result .= $t_fiches["histo"];
		$result.=$pied_table;
	}
	//print_r($_SESSION);
	return $result;

}

function getMonInvestissement()
{
	if (is_admin()) return;
	//session_start('EDC');
	$result ='<style>
					ul#tabnav {
								font: bold 11px verdana, arial, sans-serif;
								list-style-type: none;
								padding-bottom: 24px;
								border-bottom: 1px solid #82ABC4;
								margin: 0 0 0 0;
								background-image: none;
							}
					ul#tabnav li{
								float: left;
								height: 21px;
								background-color: #82ABC4;
								margin: 2px 2px 2px 0;
								border: 1px solid #82ABC4;
								background-image: none;
							}
					ul#tabnav li.active {
								border-bottom: 1px solid #fff;
								background-color: #fff;								
							}
					#tabnav a {
							float: left;
							display: block;
							text-decoration: none;
							padding: 4px;
						}
					#tabnav a:hover {						
							background: #fff;
						}
				</style>';
	// URL de la page permettant de visualiser le contenu du dossier 
	$url_details_dossier= get_permalink ( get_option( 'edc_adherent_id_page_mon_dossier' ) );; //searchPageUrl('[detail_dossier]');
	$url_details_invest= get_permalink ( get_option( 'edc_adherent_id_page_mon_investissement' ) );; //searchPageUrl('[detail_invest]');
	
	
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	// si pas d'id on renvoie vers la page d'accueil
	if(isset($_GET['id']))
	{		
		$t_infos_invest=array();
		$nb_invest=0;
		// tableau qui contiendra les fiches de chaque investissement
		$t_fiches_invest=array();

		//R�cup�ration des informations de l'utilisateur
		
		$tabonglet=array();
		// Pour les gestions multi lot 
		// j'interroge le webservice sur le programme concerné 
		
		
		$url = URL_WS_ADH.'v1.0/json/user/getInvestissements';
		$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
		if ($response->code == 200)
			$t_programmes = json_decode($response->body);
		else
			return 'Erreur WS';
			
		if (isset($t_programmes->Invest->Nom)) $t_programmes->Invest = array($t_programmes->Invest);
		
		// je boucle sur les programmes retournés jusqu'à trouver les lots de celui demandé 
		foreach($t_programmes->Invest as $t_programme)
     	{
			// si c'est le programme demandé en paramètre
			if ($t_programme->IdProgramme == $_GET['id'])
			{				
//resultat = $t_programme->Nom;
//echo $_GET['callback']."(".json_encode($resultat).");";
//exit;		
//resultat = 'test';
//echo $_GET['callback']."(".json_encode($resultat).");";
				if ($nb_invest==0) 
				{
					// C'est le premier invest du programme, je met de coté les infos 
					// du programme pour ne pas les perdre
					$t_detail_programme["nom"] = $t_programme->Nom;
					$t_detail_programme["adresse"] = $t_programme->Adresse;
					$t_detail_programme["ville"] = $t_programme->Ville;
					$t_detail_programme["codepostal"] = $t_programme->CodePostal;
					$t_detail_programme["pays"] = $t_programme->Pays;
					$t_detail_programme["promoteur"] = $t_programme->Promoteur;					
					$t_detail_programme["syndic"] = $t_programme->Syndic;						
					$t_detail_programme["lien_photo"] = $t_programme->lien_photo;
				}
				// je met les IdInvest dans un tableau pour appel à la focntion de détail invest.
				$t_infos_invest[$nb_invest] = array();
				$t_infos_invest[$nb_invest]["id"] = $t_programme->IdInvestissement;	
				// si j'ai un adb sur le lot, je le préfère à l'adb du programme
				if($t_programme->ADB != '')
				{
					$t_infos_invest[$nb_invest]["adb"] = $t_programme->ADB;
				}
				else
				{
					$t_infos_invest[$nb_invest]["adb"] = $t_programme->ADBPROG;
				}
				
				// pour le conseiller, si j'ai un suiveur, je l'affiche, sinon j'affiche le vendeur.	
				if ($t_programme->ConseillerActuel != '')				
				{ 
					$t_infos_invest[$nb_invest]["conseiller"]=$t_programme->ConseillerActuel;
				}
				else
				{
					if ($t_programme->ConseillerVendeur != '')
						$t_infos_invest[$nb_invest]["conseiller"]=$t_programme->ConseillerVendeur;	
					else
						$t_infos_invest[$nb_invest]["conseiller"] = "";
				}
				$t_infos_invest[$nb_invest]["banque"]=$t_programme->Banque;				
				$t_infos_invest[$nb_invest]["notaire"]=$t_programme->Notaire;				
				
				$t_infos_invest[$nb_invest]["NbPb"]=$t_programme->NbPb;
				$nb_invest++;
			}			
		}
		

		// BM : 10/09/2012 recherche des messages passés dans la fiche violette
		$url = URL_WS_ADH.'v1.0/json/user/getActionsWebProgramme/'.$_GET['id'];
		$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
		if ($response->code == 200)
			$actionsPgm = json_decode($response->body);
		else
			return 'Erreur WS';
			
		$annoncesPgm = "";
		
		if ($actionsPgm && isset($actionsPgm->WebPg))
		{		
			//$annoncesPgm = "";
			if (isset($actionsPgm->WebPgm->type_action)) $actionsPgm->WebPgm = array($actionsPgm->WebPgm);
			
			foreach($actionsPgm->WebPgm as $action)
			{
				list($libelle_type_action, $type_action) = explode(" \| ",(string)$action->type_action);
				//echo $libelle_type_action ." -".$type_action."<br>";
				if ($type_action=='alert') 
				{
					$img = "exclamation-button.png";
					$div_bg_color ="rgb(252, 248, 227)";
					$div_text_color = "rgb(192, 152, 83)";
				}
				else 
				{
					$img="information-button.png";
					$div_bg_color ="#d9edf7";
					$div_text_color = "#3a87ad";
				}
				$annoncesPgm .= '<div id="annoncesPgm" style="padding:3px;margin-top:2px;background-color:'.$div_bg_color.';color:'.$div_text_color.';border-style:solid;border-width:1px;border-color:'.$div_text_color.'">
										<img style="float:left;margin:0px" src="'.GESTION_ADH_REP_ICONES.'/'.$img.'" align="absmiddle">&nbsp;' .(string)$action->Observations.'
									<p align="right"><em>le '.date_to_jjmmaaaa((string)$action->DateCreation) .'</em></p>
									</div>';
			}							
			$actionsPgm=null;							
		}
		
		// les différentes url de cartes on propose une carte générale en petit : $url_carte_programme_situation 
		// et une carte plus précise $url_carte_programme_precise
		
		if (!is_string($t_detail_programme["adresse"])) $t_detail_programme["adresse"] = '';
		
		$url_carte_programme_situation = "https://maps.googleapis.com/maps/api/staticmap?zoom=4&size=100x100&sensor=false&key=AIzaSyDq1b9PPli58_JR2JMwWjOweyaDJG4Ai1U&markers=color:red%7Clabel:A%7C".urlencode((string)$t_detail_programme["adresse"]." ".(string)$t_detail_programme["codepostal"]." ".(string)$t_detail_programme["ville"]." ".(string)$t_detail_programme["pays"]);
		$url_carte_programme_precise = "https://maps.googleapis.com/maps/api/staticmap?zoom=10&size=300x300&sensor=false&key=AIzaSyDq1b9PPli58_JR2JMwWjOweyaDJG4Ai1U&markers=color:red%7Clabel:A%7C".urlencode((string)$t_detail_programme["adresse"]." ".(string)$t_detail_programme["codepostal"]." ".(string)$t_detail_programme["ville"]." ".(string)$t_detail_programme["pays"]);		
		
		// la boucle d'affichage de tout ceci en premier la fiche d'identité du programme avec les informations 
		// globales puis la boucle sur les investissements du dit programme et les données propres à chacun.
		$result .='<table width="100%">
					<tbody>
					<tr style="border-width:0px 0px 0px 0px;">
						<td style="border-width:0px 0px 0px 0px; vertical-align:top; text-align:left;">
							<H1>'.$t_detail_programme["nom"].'</H1>
							<p>'.(is_string($t_detail_programme["adresse"])?$t_detail_programme["adresse"]:'').'</p>
							<p>'.$t_detail_programme["codepostal"].' '.$t_detail_programme["ville"].' ('.$t_detail_programme["pays"].')</p>
							<p><strong>Promoteur : </strong>'.(is_string($t_detail_programme["syndic"])?$t_detail_programme["promoteur"]:'').'</p>';
		if (is_string($t_detail_programme["syndic"]) && !empty($t_detail_programme["syndic"]))
			$result .='<p><strong>Syndic :</strong> '.$t_detail_programme["syndic"].'</p>';							 
		$result .='';
		
		/*
		if (file_exists(WP_CONTENT_DIR."/../phototheque/".$t_detail_programme["nom"].".pdf"))
		{
			$result .= '<img src="'.GESTION_ADH_REP_ICONES.'/camera.png" align="absmiddle"><a href="'.get_option( 'siteurl' ).'/phototheque/'.str_replace("'","&apos;",$t_detail_programme["nom"]).'.pdf" target="_blank">Galerie Photos</a></li>'; 
		}
		*/
		
		 $lien_photo=""; 
		 if (is_string($t_detail_programme["lien_photo"]) && $t_detail_programme["lien_photo"] != '')
		 {
			 $result .= '<br />Nous avons visité la résidence où est situé votre investissement locatif. Cliquez sur le lien pour télécharger notre reportage : <a href="https://adherents.edc.asso.fr/download_ext.php?file='.$t_detail_programme["lien_photo"].'&num='.$_SESSION['adherent_infos']->NumAdhesion.'&c='.md5(str_replace("&apos;", "'", $t_detail_programme["lien_photo"]).'toto'.$_SESSION['adherent_infos']->NumAdhesion).'" target="_blank">Rapport d’EDC </a>';
		 }
		
		$result .='<p><strong>'.$nb_invest.'</strong> logement(s) dans ce programme</p>

				'.$annoncesPgm.'
							</td>
							<td style="border-width:0px 0px 0px 0px; vertical-align:top; text-align:left;">
							<div >
							<div style="position:absolute;margin-top:0px;margin-left:0px;float:left;" >
									<img style="border-style:solid;border-width:1px;border-color:#000000;" src="'.$url_carte_programme_situation.'"/>
								</div>
								<img src="'.$url_carte_programme_precise.'"/>
								
							</div>
						</td>
						<tr style="border-width:0px 0px 0px 0px;">
							<td style="border-width:0px 0px 0px 0px;" colspan="2">
							<ul id="tabnav" style="list-style: none;">';
		// liste des investissements
		// on présente chaque investissement dans un onglet.
		// pour ce faire un tableau $tabonglets à deux dimensions, la première est accédée par le guid investissement		
		// la seconde dimension contient une cellule avec le HTML de la partie de gauche et une cellule avec le 
		// html de la partie de droite.
		
		for ($i=0; $i<count($t_infos_invest);$i++)
		{
			$url = URL_WS_ADH.'v1.0/json/user/getDetailsInvestissement/'.$t_infos_invest[$i]["id"];
			$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
			if ($response->code == 200)
				$data_invest = json_decode($response->body);
			else
				return 'Erreur WS';

			
			$Invest = array('Id'=>(string)$t_infos_invest[$i]["id"],
						'RefNum'=>utf2latin((string)$data_invest->Invest->RefNum),
						'DateSignature'=> ((string)$data_invest->Invest->DateSignature),
						'DateAcceptationOffrePret'=>((string)$data_invest->Invest->DateAcceptationOffrePret),
						'DateProcuration'=>((string)$data_invest->Invest->DateProcuration),
						'DateActe'=>((string)$data_invest->Invest->DateActe),
						'DateLivraisonReelle'=>((string)$data_invest->Invest->DateLivraisonReelle),
						'DateLivraisonPrevisionnelle'=>((string)$data_invest->Invest->DateLivraisonPrevisionnelle),
						'DateLivraisonPrevisionnelleInitiale'=>((string)$data_invest->Invest->DateLivraisonPrevisionnelleInitiale),
						'DateLocationInitiale'=>((string)$data_invest->Invest->DateLocationInitiale),
						'InvestissementProduit'=>utf2latin((string)$data_invest->Invest->InvestissementProduit),
						'Groupe'=>utf2latin((string)$data_invest->Invest->Groupe),
						'LotType'=>utf2latin((string)$data_invest->Invest->LotType),
						'LotNature'=>utf2latin((string)$data_invest->Invest->LotNature),
						'SurfaceHabitable'=>utf2latin((string)$data_invest->Invest->SurfaceHabitable),
						'SurfaceAnnexe'=>utf2latin((string)$data_invest->Invest->SurfaceAnnexe),
						'MontantTTCLogement'=>utf2latin((string)$data_invest->Invest->MontantTTCLogement),
						'MontantTTCParking'=>utf2latin((string)$data_invest->Invest->MontantTTCParking),
						'LoyerMensuelLogement'=>utf2latin((string)$data_invest->Invest->LoyerMensuelLogement),
						'LoyerMensuelParking'=>utf2latin((string)$data_invest->Invest->LoyerMensuelParking),
						'DateAchevementTravaux'=>utf2latin((string)$data_invest->Invest->DateAchevementTravaux),
						'H2Envoye_bool'=>utf2latin((string)$data_invest->Invest->H2Envoye_bool));	
						
			if ((string)$Invest["Id"] == (string)$_GET["id_invest_demande"])
			{
				$active = ' class="active" ';
				$id_invest_demande = 	$Invest["Id"];	
			}
			else
			{
				if ($i==0 && !isset($_GET["id_invest_demande"])) 
				{
					$active = ' class="active" ';	
					$id_invest_demande = 	$Invest["Id"];	
				}
				else
				{
					$active = '';
				}
			}
			$result .='<li '.$active.' >
							<a href="'.$url_details_invest.'?id_invest_demande='.$Invest["Id"].'&id='.$_GET["id"].'">Lot n&deg;'.$Invest['RefNum'].'</a></li>';
			
			
			$dateLivraison = '';
			if ($Invest['DateLivraisonReelle'] != '')
			{
				$dateLivraison = $Invest['DateLivraisonReelle'];
			}
			else
			{
				if ($Invest['DateLivraisonPrevisionnelle']!= '')
				{
					$dateLivraison = $Invest['DateLivraisonPrevisionnelle'];
				}
				else
				{
					$dateLivraison = $Invest['DateLivraisonPrevisionnelleInitiale'];
				}
			}
			
			if (is_object($t_infos_invest[$i]["adb"])) $t_infos_invest[$i]["adb"] = '';
			
			$tabonglet[$Invest["Id"]]=array();
			$tabonglet[$Invest["Id"]]["contenuGauche"] ='			
			<h3>Lot N° '.$Invest['RefNum'].'</h3>
			<ul style="list-style:none;">
				<li><strong>Dispositif fiscal :</strong> '.$Invest['InvestissementProduit'].'</li>
				<li><strong>Géré par :</strong> '.$t_infos_invest[$i]["adb"].'</li>
				<li><strong>Conseiller :</strong> '.$t_infos_invest[$i]["conseiller"].'</li>';
			if (!empty(	$t_infos_invest[$i]["notaire"]))
				$tabonglet[$Invest["Id"]]["contenuGauche"] .='<li><strong>Notaire :</strong> '.$t_infos_invest[$i]["notaire"].'</li>';
			if (!empty(	$t_infos_invest[$i]["banque"]))
				$tabonglet[$Invest["Id"]]["contenuGauche"] .='<li><strong>Banque :</strong> '.$t_infos_invest[$i]["banque"].'</li>';
			$tabonglet[$Invest["Id"]]["contenuGauche"] .='</ul>';
			// Les dates importantes
			$tabonglet[$Invest["Id"]]["contenuGauche"] .=' <h4>Les dates importantes :</h4>
					<ul style="list-style:none;">';
			if ($Invest['DateSignature'] != "")
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"]  .= '<li><strong>R&eacute;serv&eacute; le </strong>' . $Invest['DateSignature'] . '</li>'; 
			}
			if ($Invest['DateAcceptationOffrePret'] != "")
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"].= '<li><strong>Acceptation du pr&ecirc;t le </strong>' . test_empty($Invest['DateAcceptationOffrePret']) . '</li>';
			}		
			if ($Invest['DateProcuration'] != "")
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Procuration le </strong>' . test_empty($Invest['DateProcuration']) . '</li>';
			}
			if ($Invest['DateActe'] != "")
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Act&eacute; le </strong>' . test_empty($Invest['DateActe']) . '</li>';
			}
/*			if ($Invest['DateAchevementTravaux'] != '')
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Date d&#146;ach&egrave;vement des travaux : </strong>' . test_empty($Invest['DateAchevementTravaux']) . '</li>';
			}*/
			if ($dateLivraison != '')
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Livr&eacute; le </strong>' . test_empty($dateLivraison) . '<input type="hidden" id="dateLivraison" name="dateLivraison" value="'.test_empty($dateLivraison).'"></li>';
				if ($Invest["DateLocationInitiale"] =='')
				{
					$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li>
						<img src="'.GESTION_ADH_REP_ICONES.'/megaphone.png" alt="Attention" align="absmiddle" style="margin:0px;"/><strong><span style="color:#F00">Nous ne disposons pas de votre date de premi&egrave;re location de ce bien.</span></strong>&nbsp;
						<span id="loc">
							'.$Invest["DateLocationInitiale"].' 
							<a href="javascript:askForModifLoc(\'loc\',\''.$Invest["DateLocationInitiale"].'\',\''.GESTION_ADH_REP_ICONES.'\',\''.$Invest["Id"].'\',\''.$url_ajax.'\')" >
								<img src="'.GESTION_ADH_REP_ICONES.'/pencil-small.png" alt="Modfier la date de première location" align="absmiddle" style="margin:0px;"/>
							</a>
						</span>
						<span id="loc_error" style="color:#888"><em>cliquez sur le crayon pour modifier</em></span>
					</li>';								
				}
				else
				{
					$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li>
						<strong>Date 1ere location :</strong>&nbsp;
						<span id="loc">
							'.$Invest["DateLocationInitiale"].' 
							<a href="javascript:askForModifLoc(\'loc\',\''.$Invest["DateLocationInitiale"].'\',\''.GESTION_ADH_REP_ICONES.'\',\''.$Invest["Id"].'\',\''.$url_ajax.'\')" >
								<img src="'.GESTION_ADH_REP_ICONES.'/pencil-small.png" alt="Modfier la date de première location" align="absmiddle" style="margin:0px;"/>
							</a>
						</span>
						<span id="loc_error" style="color:#888"><em>cliquez sur le crayon pour modifier</em></span>
					</li>';				
				}
			}													
			// 5 mars 2012 => Modif BM : Ajout de la modif de la date de première location

							
			$tabonglet[$Invest["Id"]]["contenuGauche"].='</ul>
			<h4>Caractéristiques du logement :</h4>
					<ul style="list-style:none;">';
			if ($Invest['LotType'] != '')
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Type : </strong>' . test_empty($Invest['LotType']) . '</li>';
			}
			if ($Invest['LotNature'] != '')
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Nature : </strong>' . test_empty($Invest['LotNature']) . '</li>';
			}
			if ($Invest['SurfaceHabitable'] != '' && $Invest['SurfaceHabitable'] != '0' )
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Surface habitable : </strong>' . formatSurface($Invest['SurfaceHabitable']) . '</li>';
			}
			if ($Invest['SurfaceAnnexe'] != '' && $Invest['SurfaceAnnexe'] != '0' )
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Surface annexe : </strong>' . formatSurface($Invest['SurfaceAnnexe']) . '</li>';
			}
			if ($Invest['MontantTTCLogement'] != '' && $Invest['MontantTTCLogement'] != 'NULL' && $Invest['MontantTTCLogement'] != '0.00' )
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Montant TTC du logement : </strong>' . number_format($Invest['MontantTTCLogement'],0,","," ") . ' &euro;</li>';
			}
			if ($Invest['MontantTTCParking'] != '' && $Invest['MontantTTCParking'] != 'NULL' && $Invest['MontantTTCParking'] != '0.00' )
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Montant TTC du parking : </strong>' . number_format($Invest['MontantTTCParking'],0,","," ") . ' &euro;</li>';
			}
			if ($Invest['LoyerMensuelLogement'] != '' && $Invest['LoyerMensuelLogement'] != 'NULL' && $Invest['LoyerMensuelLogement'] != '0.00' )
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Loyer mensuel du logement : </strong>' . number_format($Invest['LoyerMensuelLogement'],0,","," ") . ' &euro;</li>';
			}
			if ($Invest['LoyerMensuelParking'] != '' && $Invest['LoyerMensuelParking'] != 'NULL' && $Invest['LoyerMensuelParking'] != '0.00' )
			{
				$tabonglet[$Invest["Id"]]["contenuGauche"] .= '<li><strong>Loyer mensuel du parking : </strong>' . number_format($Invest['LoyerMensuelParking'],0,","," ") . ' &euro;</li>';
			}
			$tabonglet[$Invest["Id"]]["contenuGauche"] .='</ul>';
			
			// contenu de l'onglet droit
			
			// s'il y a des ADF, on affiche les ADF
			
			$url = URL_WS_ADH.'v1.0/json/user/getADFInvestissement/'.$t_infos_invest[$i]["id"];
			$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
			if ($response->code == 200)
				$retvaladf = json_decode($response->body);
			else
				return 'Erreur WS';
				
			if ($retvaladf && isset($retvaladf->adf) )
			{
				$tabonglet[$Invest["Id"]]["contenuDroite"].='<h4>Appels de fond</h4>';
				$retour = '<table class="rouge" width="100%" cellpadding="2" cellspacing="0" border="0" align="center">
							<tr>
							<thead>
								<th>Date d&apos;envoi</th>
								<th>Etape</th>
								<th>Taux<br />avancement</th>
								<th>Montant</th>								
							</thead>
							</tr>
							<tbody>';
				if (isset($retvaladf->adf->DateEnvoiPromoteurBanque))
				{
					$retour .= '<tr>
							<td align="center">'.(string)date_to_jjmmaaaa($retvaladf->adf->DateEnvoiPromoteurBanque).'</td>
							<td>'.(string)$retvaladf->adf->AppelDeFondEtape.'</td>
							<td align="center">'.(string)$retvaladf->adf->Taux.' %</td>																
							<td align="right" style="white-space:nowrap;">'.(string)formatPrice($retvaladf->adf->Montant).' &euro;</td>
						</tr>';
				}
				else foreach($retvaladf->adf as $adf)
				{
					$retour .= '<tr>
									<td align="center">'.(string)date_to_jjmmaaaa($adf->DateEnvoiPromoteurBanque).'</td>
									<td>'.(string)$adf->AppelDeFondEtape.'</td>
									<td align="center">'.(string)$adf->Taux.' %</td>
									<td align="right" style="white-space:nowrap;">'.(string)formatPrice($adf->Montant).' &euro;</td>
								</tr>';
				}						
				$retour .= '</tbody>
						</table>';		
				$retvaladf=null;				
				$tabonglet[$Invest["Id"]]["contenuDroite"].=$retour;
			}
			
			// s'il y a des IntÃ©rÃªts intercalaires
			$url = URL_WS_ADH.'v1.0/json/user/getIInterInvestissement/'.$t_infos_invest[$i]["id"];
			$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
			if ($response->code == 200)
				$retvalinter = json_decode($response->body);
			else
				return 'Erreur WS';
				
			if ($retvalinter && isset($retvalinter->iinter))
			{
				$tabonglet[$Invest["Id"]]["contenuDroite"].='<h4>Int&eacute;r&egrave;ts intercalaires</h4>';
				$retour = '<table class="rouge" width="100%" cellpadding="2" cellspacing="0" border="0" align="center">
							<thead>	
								<tr>							
									<th>Date d&apos;envoi</th>
									<th>Montant</th>								
								</tr>
							</thead>
							<tbody>';
				if (isset($retvalinter->iinter->Montant)) $retvalinter->iinter = array($retvalinter->iinter);
				
				foreach($retvalinter->iinter as $iinter)
				{
					$retour .= '<tr>
									<td align="center">'.(string)date_to_jjmmaaaa($iinter->DateEnvoiPromoteurBanque).'</td>
									<td align="right">'.(string)formatPrice($iinter->Montant).' &euro;</td>
								</tr>';
				}						
				$retour .= '</tbody>
						</table>';		
				$retvalinter=null;				
				$tabonglet[$Invest["Id"]]["contenuDroite"].=$retour;
			}
			
			if ($_SESSION['adherent_infos']->NumAdhesion == '47749')
			{
				$tabonglet[$Invest["Id"]]["contenuDroite"].= '<h4>Documents</h4>';
				$tabonglet[$Invest["Id"]]["contenuDroite"].= getMesDocuments('', $t_infos_invest[$i]["id"], false);
			}
			
		}						
		$result .='</ul>
							<table width=100% cellpadding="2" cellspacing="0" border="0">
							<tr>
								<td style="border-width:0px 0px 0px 0px; text-align:left;" width="50%">'.$tabonglet[$id_invest_demande]["contenuGauche"].'</td>
								<td style="border-width:0px 0px 0px 0px; text-align:left;">'.$tabonglet[$id_invest_demande]["contenuDroite"].'</td>
							</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>';
		
		
	}
	else // pas d'id passé en paramètres
	{
	
		//Recherche de l'url de la page de liste des investissements

		$ret = '<script type="text/javascript">';
   		$ret .= '   location.href= \''. get_permalink ( get_option( 'edc_adherent_id_page_mes_investissements' ) ) . '\';';
   		$ret .= '</script>';
		return $ret;
	}
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';

	
}

function getMesInvestissements()
{	
	if (is_admin()) return;
	$result ='';

	$url = URL_WS_ADH.'v1.0/json/user/getInvestissements';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	$retval = '';
	if ($response->code == 200)
	{
		$retval = json_decode($response->body);
	}
	else
		return 'Erreur WS';
		
	//session_start('EDC');
	//$impair=true;
	$impair=false;

	//Recherche de l'url de la page de d�tail des investissements
	$url_details_invest= get_permalink ( get_option( 'edc_adherent_id_page_mon_investissement' ) ); //searchPageUrl('[detail_invest]');
	
	
   // pas activé
   /*
	if ($_SESSION['MailEnvoyeInvest'] == 1)
	{
		$result .='<tr><td colspan="4" style="border-width:0px 0px 0px 0px;color:green;font-weight:bold">Votre demande de modification a &eacute;t&eacute; envoy&eacute;e</td></tr>';
		$_SESSION['MailEnvoyeInvest']= -1;
	}
	else
	{
		if ($_SESSION['MailEnvoyeInvest'] == 2)
		{
			$result .='<tr><td colspan="4" style="border-width:0px 0px 0px 0px;color:red;font-weight:bold">Une erreur est survenue. Votre demande de modification n&#146;a pas &eacute;t&eacute; envoy&eacute;e</td></tr>';
			$_SESSION['MailEnvoyeInvest']= -1;
		}
	}
	*/
	$idProg = '';
	// BM 15/09/2011 ajout de variables permettant de gérer l'affichage par résidence des différents biens.
	$old_programme = "";
	$nb_lots_du_programme=0;
	$t_investissements=array();
	$cpt=-1;
	// cette url permet d'utiliser l'API de google pour localiser les biens de l'adhérent
	$url_carte_france = "https://maps.googleapis.com/maps/api/staticmap?size=300x400&sensor=false&key=AIzaSyDq1b9PPli58_JR2JMwWjOweyaDJG4Ai1U";
	
	if (isset($retval->Invest->Nom)) $retval->Invest = array($retval->Invest);
	
     foreach($retval->Invest as $invest)
     {
		//PS le 20/04/2022 - On ignore les programmes ALLORA et S. PLAZA
		 if ($invest->Ville!='PARTENAIRE')
{
		// BM : 09/09/2011 on ne prend pas le champ Date de livraison réelle 
		// qui est celui du programme mais le champ Date de livraison du lot.
		  if (((string)$invest->DateLivraison)!= '')
          {
               $dateLivraison = ((string)$invest->DateLivraison);
          }
          else
          {
               if (((string)$invest->DateLivraisonPrevisionnelle)!= '')
               {
                    $dateLivraison = ((string)$invest->DateLivraisonPrevisionnelle);
               }
               else
               {
                    $dateLivraison = ((string)$invest->DateLivraisonPrevisionnelleInitiale);
               }
          }
		 
		 // BM le 11/05/2011 :  ajout d'un lien vers la Phototh�que si un pdf pour la r�sidence existe
		 // a faire évoluer vers une petit icone d'appareil photo et travailler sur une vue en vignettes
		 /*
		 if (file_exists(WP_CONTENT_DIR ."/../phototheque/".utf2latin((string)$invest->Nom).".pdf"))
		 {
			$lien_photo = '<li><a href="'.get_option( 'siteurl' ).'/phototheque/'.str_replace("'","&apos;",utf2latin((string)$invest->Nom)) .'.pdf" target="_blank">Galerie Photos</a></li>'; 
		 }
		 else
		 {
			$lien_photo=""; 
		 }
		 */
		 
		 $lien_photo=""; 
		 if (is_string($invest->lien_photo) && $invest->lien_photo != '')
		 {
			 $lien_photo='<br />Nous avons visité la résidence où est situé votre investissement locatif. Cliquez sur le lien pour télécharger notre reportage : <a href="https://adherents.edc.asso.fr/download_ext.php?file='.$invest->lien_photo.'&num='.$_SESSION['adherent_infos']->NumAdhesion.'&c='.md5(str_replace("&apos;", "'", $invest->lien_photo).'toto'.$_SESSION['adherent_infos']->NumAdhesion).'" target="_blank">Rapport d’EDC</a></li>';
		 }
				
		// Pour les multi lots on ne va afficher qu'une seule fois le programme et lier vers la page du programme.
		// donc je teste si je change de programme.
		if ($old_programme != $invest->Nom)
		{
			// Programme suivant comme j'ai commencé à -1, le premier est indexé 0
			$cpt++;
			// j'initialise le tableau	
			$t_investissements[$cpt]=array(); 
			// Id du programme						
			$t_investissements[$cpt]["IdProgramme"] =((string)$invest->IdProgramme);
			// Nom du programme
			$t_investissements[$cpt]["nomProgramme"] ='<STRONG>'. utf2latin((string)$invest->Nom) . '</STRONG> <i>situ&eacute; &aacute; '.utf2latin((string)$invest->Ville);
			// si le pays est  France, J'affiche le departement sinon j'affiche le pays (réunion gouadeloupe etc...) 
			if ($invest->Pays =='FRANCE') 
				$t_investissements[$cpt]["nomProgramme"].=' ('.substr((string)$invest->CodePostal,0,2).')</i>';
			else 
				$t_investissements[$cpt]["nomProgramme"].=' ('.((string)$invest->Pays).')</i>';
			
			// je créé le marqueur sur la carte google pour cette adresse avec la lettre suivante chr(65+cpt) en ascii 65=A 
			$url_carte_france .="&markers=color:red%7Clabel:".chr(65+$cpt)."%7C".urlencode($invest->adresse." ".$invest->CodePostal." ".$invest->Ville." ".$invest->Pays);
			
			// l'adresse du marqueur pour afficher l'image dans le tableau comme une légende
			$t_investissements[$cpt]["marqueur"]="https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=".chr(65+$cpt)."|FF0000|000000";			
			// j'intialise les compteurs de lots et de dossiers pour le programmme
			$t_investissements[$cpt]["nb_lots"]=0;
			$t_investissements[$cpt]["nb_dossiers"]=0;
			$t_investissements[$cpt]["lien_photo"]=$lien_photo;
			// je marque que je traite ce programme
			$old_programme =  utf2latin((string)$invest->Nom);
			$nb_lots_du_programme = 0;		
			//echo $old_programme;
		}		
		// je cumule chaque lot et les problèmes associés
		$t_investissements[$cpt]["nb_lots"]++;
		$t_investissements[$cpt]["nb_dossiers"]+=$invest->NbPb;
}
     }
	 
	 // fin de la boulce de mémorisaiton des programmes, je passe à la boucle d'affichage	 
	$result ='<table width="100%" align="center" border="0" style="border-width:0px 0px 0px 0px;">
				';
						// on affiche les programmes dans la table
						for ($i=0;$i<count($t_investissements);$i++)
						{
							$result.='<tr style="border-width:0px 0px 0px 0px;">		
										<td style="border-width:0px 0px 0px 0px; text-align:left;">
											<a href="' . $url_details_invest . '?id=' .$t_investissements[$i]["IdProgramme"]. '">'.$t_investissements[$i]["nomProgramme"].'</a> - '.$t_investissements[$i]["nb_lots"].' logement(s) - '.$t_investissements[$i]["nb_dossiers"].' dossier(s) en cours			
											'.$t_investissements[$i]["lien_photo"].'
								</td>
							</tr>';
						}	
	
	$result .= '</table>';
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}

function getAideImpot2016()
{
	if (is_admin()) return;
	$html = '';
	
	$url = URL_WS_ADH.'v1.0/json/user/getImpot2016';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$listFichier = json_decode($response->body);
		
		$html='<ul style="list-style:none;">';
		$i=1;
		
		if (is_string($listFichier)) $listFichier = array($listFichier);
		
		foreach($listFichier as $fichier)
		{
			$html.='<li><a href="https://file.edc.asso.fr/download_ext.php?a=2015&file='.$fichier.'&num='.$_SESSION['adherent_infos']->NumAdhesion.'&c='.md5($fichier.'toto'.$_SESSION['adherent_infos']->NumAdhesion).'" target="_blank" style=" font-weight:bold; color:#d60036;">Aide à la déclaration '.substr($fichier, 11 ,-4).'</a></li>';
			// $html.='<li><a href="https://adherents.edc.asso.fr/download_ext.php?a=2015&file='.$fichier.'&num='.$_SESSION['adherent_infos']->NumAdhesion.'&c='.md5($fichier.'toto'.$_SESSION['adherent_infos']->NumAdhesion).'" target="_blank" style=" font-weight:bold; color:#d60036;">Aide à la déclaration '.substr($fichier, 11 ,-4).'</a></li>';
			$i++;
		}
		$html .='</ul>';
	}
	else
	{
		$html = '<strong>Erreur WS</strong>';
	}
	
	return $html;			
}

function getMonCompte()
{
	if (is_admin()) return;
	$html = '';
	
	if (isset($_POST['posted_mail']) && $_POST['posted_mail'] == 1)
	{		
		$url = URL_WS_ADH.'v1.0/json/user/change_email';
	
		$response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('new_email='.trim($_POST['txtEmail']))->send();
		if ($response->code == 200)
		{
			$new_email = trim($_POST['txtEmail']);
			
			$_SESSION['adherent_login'] = trim($_POST['txtEmail']);
			$_SESSION['adherent_infos']->Email = trim($_POST['txtEmail']);
			
			$fic = fopen(WP_PLUGIN_DIR."/edc_adherent/modeles/modele_AR_EDC.html","r");
			$fcontent = fread($fic,filesize(WP_PLUGIN_DIR."/edc_adherent/modeles/modele_AR_EDC.html"));
			fclose($fic);
			
			$adherent = get_object_vars($_SESSION['adherent_infos']);
			
			reset($adherent);
			$fcontent = str_replace('[New_Email]',$new_email,$fcontent);
			$fcontent = str_replace('[Date]',date("d/m/Y H:i:s"),$fcontent);			
			
			for($i=0;$i<count($adherent);$i++)
			{
				$key = key($adherent);
				$fcontent = str_replace('['.$key.']',$adherent[$key],$fcontent);
				next($adherent);
			}
		
			// Création des entetes
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
			// En-têtes additionnels					
			$headers .= "From:EDC Extranet Adherent <noreply@assoedc.com>\r\n";
			
			//$to="favier.g@edc.asso.fr";
			$to = $new_email;
			$subject = "[EXTRANET ADHERENT EDC] : Modification de votre adresse email";	
			
			$mail_sent = @mail( $to, $subject, $fcontent, $headers);
			
			$html .= '<p style="font-size: 14px; font-weight: bold;">Votre changement a bien été effectué.</p>
			<p style="font-size: 14px; font-weight: bold;">Une confirmation vous a été envoyée à votre nouvelle adresse.</p>';	
		}
		else
		{
			print_r($response);
			$html = '<strong>Erreur WS</strong>';
		}
	}
	elseif (isset($_POST['posted_password']) && $_POST['posted_password'] == 1)
	{				
		$url = URL_WS_ADH.'v1.0/json/user/change_mdp_man';
	
		$response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('new_pwd='.trim($_POST['txtPassword']))->send();
		
		if ($response->code == 200)
		{
			header('location:'.get_home_url().'?deconnect=1');
			die();
		}
		else
		{
			$html = '<strong>Erreur WS</strong>';
		}
	}
	else
	{
		$html = '';
		
		$html .= '<p style="font-size: 16px;">Pour rappel, votre numéro d’adhérent est le n°<strong style="color: #cf0a2c;">'.$_SESSION['adherent_infos']->NumAdhesion.'</strong></p>
		<p style="font-size: 16px;">L’e-mail enregistré par nos services et utilisé pour nos communications est : <strong style="color: #cf0a2c;">'.$_SESSION['adherent_infos']->Email.'</strong></p>';
		
		$html .= '<div style="clear:both; height:20px;"></div>
		<div style="width:50%; text-align:center; float:left" class="more-link"><a href="javascript:AffDiv(\'form_email\');" class="button button_theme">Modifier mon email</a></div>
		<div style="width:50%; text-align:center; float:left" class="more-link"><a href="javascript:AffDiv(\'form_mdp\');" class="button button_theme">Modifier mon mot de passe</a></div>
		';
		
		$html .= '
		<script>
		function AffDiv(nom)
		{
			jQuery(\'#\'+nom).slideToggle();
		}
		
		function checkMail()
		{
			var error=0;
			var message_erreur;
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var adresse = document.getElementById("txtEmail").value;
			
			if (document.getElementById("txtEmail").value == "" || document.getElementById("txtEmailConfirm").value == "")
			{
				error++;
				message_erreur="ERREUR : Tous les champs doivent être remplis";
			}
		
			if (document.getElementById("txtEmail").value  != document.getElementById("txtEmailConfirm").value )
			{
				error++;
				message_erreur="ERREUR : Tous les champs doivent être identiques";
			}
			
			if (reg.test(adresse) == false)
			{
				error++;
				message_erreur="ERREUR : L\'adresse que vous avez saisi n\'est pas valide";		
			}
			
			if (error > 0)
			{
				document.getElementById("posted_mail").value=0;
				document.getElementById("error_email").style.display="inline";
				document.getElementById("error_email").innerHTML=message_erreur;
				return (false);
			}
			document.getElementById("posted_mail").value=1;
			return(true);
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
			
			
			if(document.getElementById("txtPassword").value.indexOf("&") > -1 || document.getElementById("txtPassword").value.indexOf("=") > -1 || document.getElementById("txtPassword").value.indexOf("+") > -1)
			{
				error++;
				message_erreur="ERREUR : Les caractères &=+ sont interdits.";
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
		</script>
		<form onsubmit="return(checkMail(this));" action="" method="POST" name="modif_email" id="form_email" class="frm_forms with_frm_style frm_style_style-formidable" style="display:none; margin:20px 0;">
		<input type="hidden" value="0" id="posted_mail" name="posted_mail">
		<div id="error_email" style="font-size: 16px; font-weight: bold; color: #ff0000; display: none;"></div>
			<p>Pour modifier votre email, vous devez préciser le nouveau (et le confirmer).</p>
			<p><label for="txtEmail" style="float:left; margin-right:10px; line-height:30px;">Email :</label><input type="email" name="txtEmail" id="txtEmail" style="font-size: 12px; color: #694F40; width:300px;"></p>
			<p><label for="txtEmailConfirm" style="float:left; margin-right:10px; line-height:30px;">Confirmez votre email :</label><input type="email" name="txtEmailConfirm" id="txtEmailConfirm" style="font-size: 12px; color: #694F40; width:300px;"></p>
			<p class="submit frm_submit"><input type="submit" value="Enregistrer ma nouvelle adresse mail" name="submit_email" class="art-button"></p>
		</form>';
		
		$html .= '
		
		<form onsubmit="return(checkPassword(this));" action="" method="POST" name="modif_password" id="form_mdp" class="frm_forms with_frm_style frm_style_style-formidable" style="display:none; margin:20px 0;">
		<input type="hidden" value="0" id="posted_password" name="posted_password">	
		<div id="error_password" style="font-size: 16px; font-weight: bold; color: #ff0000; display: none;"></div>
		<p>Pour modifier votre mot de passe, vous devez préciser le nouveau (et le confirmer).<br>
		Pour renforcer la sécurité de votre compte, votre mot de passe doit contenir au moins 6 caractères, au moins une majuscule et une minuscule et un chiffre.</p>
		<p>A défaut, votre nouveau mot de passe ne sera pas enregistré. </p>

		
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

function getSecureMdp()
{
	if (is_admin()) return;
	$html = '';
	
	if (isset($_POST['posted_password']) && $_POST['posted_password'] == 1)
	{				
		$url = URL_WS_ADH.'v1.0/json/user/change_mdp_man';
	
		$response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('new_pwd='.trim($_POST['txtPassword']))->send();
		
		if ($response->code == 200)
		{
			header('location:'.get_home_url().'?deconnect=1');
			die();
		}
		else
		{
			$html = '<strong>Erreur WS</strong>';
		}
	}
	else
	{
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


function getMesDonnees()
{
	if (is_admin()) return;
	
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getTelephones';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	if ($response->code == 200)
	{
		$listTel = json_decode($response->body);
	}
	else
	{
		//mail ('production@edc.asso.fr', 'EDC - Erreur - getTelephone', $_SESSION['adherent_login'].' '.print_r($response, true).print_r($_POST, true));
		//$result = '<strong>Erreur WS</strong>';
	}
	
	if (isset($listTel->IdTelephoneType)) $listTel = array($listTel);
	
	if ($_SESSION['MailEnvoye'] == 1)
	{
		$prise_en_compte.='<div style="padding:5px;color:#060;background-color:#EEE;border-style:solid;border-width:1px;border-color:#060;">
								<em>Nous vous remercions d&apos;avoir compl&eacute;t&eacute; et mis  &aacute; jour vos coordonn&eacute;es.</em><br />
								  <em>Lorsque vous aurez enregistr&eacute; les donn&eacute;es,  les modifications ne seront pas prises en compte imm&eacute;diatement.</em><br />								  <em>Comptez un d&eacute;lai moyen de 3 semaines avant de visualiser vos modifications.</em>
								</div>';
		$_SESSION['MailEnvoye']=-1;
	}
	else
	{
		if ($_SESSION['MailEnvoye'] == 2)
		{
			$prise_en_compte .='<div style="border-width:0px 0px 0px 0px;color:red;font-weight:bold">
					<em>Une erreur est survenue. Votre demande de modification n&#146;a pas &eacute;t&eacute; envoy&eacute;e</em>
					</div>';
			$_SESSION['MailEnvoye']=-1;
		}
	}	
	
	
	$result .= $prise_en_compte;
	
	/*
	$url_update_mes_donnees=searchPageUrl('[update_mesdonnees]');
	$url_ajax=searchPageUrl('[getEurekaValue]');
	$url_chgt_mdp=searchPageUrl('[SPC]');
	*/
	
	if ($_SESSION['adherent_infos']->est_npai == 1)
	{
		$style_adresse =' style=" background-color:#CC6666; border-style:solid; border-color:#FF0000; border-width:2px; color:#FFFFFF; padding:3px; "';	
		$texte_npai ='<h2 style="color:#FFFFFF; margin:2px;padding:3px"><img style="margin:0px;" src="'.GESTION_ADH_REP_ICONES.'/exclamation-diamond-frame.png">&nbsp;Votre adresse semble erron&eacute;e, merci de la vérifier.</H2>';
	}
	else
	{
		$style_adresse =' style=" padding:3px; "';	
	}
	
	/*
	$result .= '<div id="postit" style="height:234px; width:250px; background-attachment:top; float:right; background-repeat:no-repeat; background:url('.GESTION_ADH_REP_ICONES.'/postit.jpg);">
					<div align="center" style ="margin-top:50px;margin-left:50px;margin-right:20px;margin-bottom:25px">
					<img src="'.GESTION_ADH_REP_ICONES.'/truck--arrow.png" align="absmiddle" style="float:left;">&nbsp;Vous avez changé d&quot;adresse ?<br/><a href="' . get_permalink ( get_option( 'edc_adherent_id_page_change_adresse' ) ) . '">&gt;&gt;&gt; cliquez ici &lt;&lt;&lt;</a>
					';
	*/
	$result .= '<div id="postit" style="float:right;">
					
					<a href="' . get_permalink ( get_option( 'edc_adherent_id_page_change_adresse' ) ) . '" class="button button_theme">J’ai changé d’adresse</a>
					';
					
/*<hr/>
					<img src="'.GESTION_ADH_REP_ICONES.'/key-solid.png" align="absmiddle" style="float:left;">&nbsp;Vous souhaitez changer votre mot de passe ?<br/><a href="' . $url_chgt_mdp . '">&gt;&gt;&gt; cliquez ici &lt;&lt;&lt;</a>*/					

if(!isset($listTel))
{
	$result .='<hr/><img src="'.GESTION_ADH_REP_ICONES.'/traffic-cone.png" align="absmiddle" style="float:left;"><div style="color:#FF0000;">Nous n&quot;avons aucun numéro de téléphone pour vous contacter !</div>';		
}

/*elseif(date_to_string($adherent["DateNaissance"]) == '')
{
	$result .='<hr/><img src="'.GESTION_ADH_REP_ICONES.'/traffic-cone.png" align="absmiddle" style="float:left;">Pensez à nous préciser votre date de naissance !';			
}*/
$result .='</div>';
				
	$result .= '<div id="donnesPerso">
		<p style="margin-bottom:5px; font-weight:bold">Mon numéro d’adhérent : ' . $_SESSION['adherent_infos']->NumAdhesion . '</p><br/>';
	// l'adresse principale.
	$result .= '<div id="adresse" '.$style_adresse.'>'.$texte_npai.'<strong> Adresse principale : </strong><br/>';
	if ($_SESSION['adherent_infos']->Adresse1 != '' || $_SESSION['adherent_infos']->Adresse2 != '' || $_SESSION['adherent_infos']->Adresse3 != '' || $_SESSION['adherent_infos']->CP != '')
	{
		if ($_SESSION['adherent_infos']->Adresse1 != '' )
		{
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse1 . '<br/>';
		}
		if ($_SESSION['adherent_infos']->Adresse2 != '' )
		{
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse2 . '<br/>';
		}
		if ($_SESSION['adherent_infos']->Adresse3 != '' )
		{
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse3 . '<br/>';
		}
		if ($_SESSION['adherent_infos']->CP != '' )
		{
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->CP . ' ' . $_SESSION['adherent_infos']->Ville . '<br/>';
		}
		if ($_SESSION['adherent_infos']->Pays != '' )
		{
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Pays . '<br/>';
		}	
	}
	else
	{
		$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Information non renseign&eacute;e<br/>';
	}
				
	$result .='</div>
			</div>
				<div id="infosComplementaires">
				';
	//$result .='<table style="border-style:none;border-width: 0px">';
	if($_SESSION['adherent_infos']->Email != '')
	{
		$url_chgt_mdp = get_permalink ( get_option( 'edc_adherent_id_page_mon_compte_extranet' ) );
		
		/*$result .= '<tr><td width="30" style="border-style:none;border-width: 0px"><img src="'.GESTION_ADH_REP_ICONES.'/mail.png" align="absmiddle"/></td><td  style="border-style:none;border-width: 0px">' . $adherent["Email"] . '</td></tr>';*/
		$result .= '<p>
						<strong>Mon adresse email</strong> : '.$_SESSION['adherent_infos']->Email.'
						<a href="'.$url_chgt_mdp.'" >
							<img src="'.GESTION_ADH_REP_ICONES.'/pencil-small.png" alt="Modfier votre email" align="absmiddle" style="margin:0px;"/>
						</a>
						<span style="color:#888"><em>cliquez sur le crayon pour modifier</em></span>
			</p>';
	}
	
	
	$iTel = 0;
	foreach($listTel as $telephone)
	{
		$iTel++;
		$telephone = get_object_vars($telephone);
		foreach ($telephone as $key => $val)
		{
			$telephone[$key] = get_object_vars($val);
			$telephone[$key] = $telephone[$key][0];
		}
		
		$select_type = genererSelectTypeTelephone("IdTelephoneType",$telephone["IdTelephoneType"]);
		$result .= '<p>';
		if ($iTel == 1) $result .= '<strong>Mon numéro de téléphone principal</strong> : ';
		else $result .= '<strong>Un autre numéro de téléphone</strong> : ';
		$result .= '	<span id="tel'.$telephone["IdContactTelephone"].'" class="inputinline">
						&nbsp;' . $telephone["Numero"] . '
						<a href="javascript:askForModifTel(\'tel'.$telephone["IdContactTelephone"].'\',\''.$telephone["Numero"].'\',\''.GESTION_ADH_REP_ICONES.'\',\''.$telephone["IdContactTelephone"].'\',\''.$select_type.'\',\''.$url_ajax.'\')" >
							<img src="'.GESTION_ADH_REP_ICONES.'/pencil-small.png" alt="Modfier votre téléphone" align="absmiddle" style="margin:0px;"/>
						</a>
						</span>
						<span id="tel'.$telephone["IdContactTelephone"].'_error" style="color:#888"><em>cliquez sur le crayon pour modifier</em></span>
					</p>';	
	}
	
	if(date_to_string($_SESSION['adherent_infos']->DateNaissance) != '')
	{
		$result .= '<p>
					<strong>Ma date de naissance</strong> : 
					<span id="dob" class="inputinline">
						'.$_SESSION['adherent_infos']->DateNaissance.' 
						<a href="javascript:askForModifDob(\'dob\',\''.$_SESSION['adherent_infos']->DateNaissance.'\',\''.GESTION_ADH_REP_ICONES.'\',\''.$_SESSION['adherent_infos']->IdContact.'\',\''.$url_ajax.'\')" >
							<img src="'.GESTION_ADH_REP_ICONES.'/pencil-small.png" alt="Modfier votre date de naissance" align="absmiddle" style="margin:0px;"/>
						</a>
					</span>
					<span id="dob_error" style="color:#888"><em>cliquez sur le crayon pour modifier</em></span>
		</p>';
	}
	else
	{
		$result .= '<li><img src="'.GESTION_ADH_REP_ICONES.'/cake.png" alt="Date de naissance" align="absmiddle" style="margin:0px;"/>&nbsp;
		<span id="dob" class="inputinline">
			Date de naissance inconnue
			<a href="javascript:askForModifDob(\'dob\',\''.$_SESSION['adherent_infos']->DateNaissance.'\',\''.GESTION_ADH_REP_ICONES.'\',\''.$_SESSION['adherent_infos']->IdContact.'\',\''.$url_ajax.'\')" >
				<img src="'.GESTION_ADH_REP_ICONES.'/pencil-small.png" alt="Modfier votre date de naissance" align="absmiddle" style="margin:0px;"/>
			</a>
		</span>
		<span id="dob_error" style="color:#888"><em>cliquez sur le crayon pour modifier</em></span>
		</li>';	
	}
	
	$result .= '</ul></div>';
	
	
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}

function attestationCotisation2PDF()
{
	if (is_admin()) return;
	include(WP_PLUGIN_DIR."/edc_adherent/php_to_pdf/html2pdf.class.php");	
	//============== Insertion des images d'entête et de pied de page dans le courrier
	
	$pathimage=WP_PLUGIN_DIR."/edc_adherent/images";
	$pathmodele=WP_PLUGIN_DIR."/edc_adherent/modeles/";
	
	$url = URL_WS_ADH.'v1.0/json/user/getDetailCotisation/'.$_GET['IdCotisation'];
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$cotisation = json_decode($response->body);
		
		if (is_object($cotisation->prenom)) $cotisation->prenom = '';
		
	}
	else
	{
		return '<strong>Erreur WS</strong>';
	}

	//============== Modification du début de la lettre en fonction de la civilité
	$t_intitule = array('EURL'=> 'Cher(e) adhérent(e)',
						'SARL' => 'Cher(e) adhérent(e)',
						'Société' => 'Cher(e) adhérent(e)',
						'SCI' => 'Cher(e) adhérent(e)',
						'Maitre' => 'Cher(e) adhérent(e)',
						'Mrs' => 'Chers adhérents',
						'M. & Mme' => 'Chers adhérents',
						'Melle' => 'Chère adhérente',
						'Mme' => 'Chère adhérente',
						'Melles' =>'Chères adhérentes',
						'Mmes' => 'Chères adhérentes',
						'M.' => 'Cher adhérent');

	if ($cotisation->Annee > 2022)
		$path_modele=$pathmodele."/Attestation_cotisation_2022.html";
	else
		$path_modele=$pathmodele."/Attestation_cotisation_2018.html";
	$modele = @fopen($path_modele,"r");
	$content = fread($modele,filesize($path_modele));
	fclose($modele);

	//Mise en forme du bloc adresse
	$Adresse= $cotisation->Adresse1;
	if (is_string($cotisation->Adresse2) && $cotisation->Adresse2 !='')
	{
		$Adresse .='<br/>'.$cotisation->Adresse2;
	}
	if (is_string($cotisation->Adresse3) && $cotisation->Adresse3 !='')
	{
		$Adresse .='<br/>'.$cotisation->Adresse3;
	}

	//============== Fin de la modification
	$content = str_replace('[urlimage]',$pathimage,$content);

	$content = str_replace('[Date]',date("d/m/Y"),$content);		
	$content = str_replace('[Annee]',$cotisation->Annee,$content);
	$content = str_replace('[Numero_adherent]',$cotisation->Numero,$content);
	$content = str_replace('[CiviliteCourte]',$cotisation->CiviliteCourte,$content);
	$content = str_replace('[Prenom]',$cotisation->prenom,$content);
	$content = str_replace('[Nom]',$cotisation->nom,$content);
	$content = str_replace('[Adresse]',$Adresse,$content);
	$content = str_replace('[Code_postal]',$cotisation->CodePostal,$content);
	$content = str_replace('[Ville]',$cotisation->Ville,$content);
	$content = str_replace('[Pays]',$cotisation->pays,$content);
	$content = str_replace('[Intitule]',$t_intitule[(string)$cotisation->CiviliteCourte],$content);
	$content = str_replace('[IntitulePolitesse]',str_replace('C', 'c', $t_intitule[(string)$cotisation->CiviliteCourte]),$content);
	
	$content = str_replace('[MontantCotisationPrecedente]',number_format((float)$cotisation->MontantCotisation,2,","," ").' &euro;',$content);
		if ($cotisation->Annee >=2014) 
			$taux_tva =20.0;
		else
			$taux_tva =19.6;
	$content = str_replace('[TAUX_TVA]',number_format((float)$taux_tva,2,","," ").' %',$content);
	$content = str_replace('[Montant_TVA]',number_format((float)($cotisation->MontantCotisation - $cotisation->MontantCotisation/((100+$taux_tva)/100.0)),2,","," ").' &euro;',$content);
	
	$content = str_replace('[DatePaiementCotisationPrecedente]',$cotisation->DateEncaissementCotisation, $content);

	// Je crée le PDF
	$temp = ob_get_clean();
	ob_clean(); 
	ob_end_clean();
	$html2pdf = new HTML2PDF();
	$html2pdf->setDefaultFont('Helvetica');
	$html2pdf->writeHTML($content);
	$html2pdf->Output($cotisation->numero.'_ATTESTATION_DE_COTISATION_'.$cotisation->Annee.'.pdf', 'I');
	die();
}

function getAppelCotissation()
{
	if (is_admin()) return;
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getAppelCotissation';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$appels = json_decode($response->body);
		
		if (isset($appels[0]))
		{
			$result = '<p>Pour ouvrir votre appel à cotisation, cliquez sur l\'image suivante :</p>
			<p><a href="/appel_cotisation.php" target="_blank"><img class="size-full wp-image-6168 aligncenter" src="https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon.png" alt="adobe_pdf_icon" srcset="https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon.png 151w, https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon-150x143.png 150w" sizes="(max-width: 151px) 100vw, 151px" style="opacity: 1;" width="50"></a></p>';
		}
	}
	else
	{
		$result = '<strong>Erreur WS</strong>';
	}
	
	//if (trim($_SESSION['adherent_infos']->NumAdhesion) == '47749')
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
	
	//return '';
}


function getAppelCotissation2018()
{
	if (is_admin()) return;
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getAppelCotissation2018';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$appels = json_decode($response->body);
		
		if (isset($appels[0]))
		{
			$result = '<p>Pour ouvrir votre appel à cotisation, cliquez sur l\'image suivante :</p>
			<p><a href="/appel_cotisation_2018.php" target="_blank"><img class="size-full wp-image-6168 aligncenter" src="https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon.png" alt="adobe_pdf_icon" srcset="https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon.png 151w, https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon-150x143.png 150w" sizes="(max-width: 151px) 100vw, 151px" style="opacity: 1;" width="50"></a></p>';
		}
	}
	else
	{
		$result = '<strong>Erreur WS</strong>';
	}
	
	//if (trim($_SESSION['adherent_infos']->NumAdhesion) == '47749')
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
	
	//return '';
}

function getAppelCotissation2019()
{
	if (is_admin()) return;
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getAppelCotissation2019';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$appels = json_decode($response->body);
		
		if (isset($appels[0]))
		{
			$result = '<p>Pour ouvrir votre appel à cotisation, cliquez sur l\'image suivante :</p>
			<p><a href="/appel_cotisation_2019.php" target="_blank"><img class="size-full wp-image-6168 aligncenter" src="https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon.png" alt="adobe_pdf_icon" srcset="https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon.png 151w, https://www.assoedc.com/wp-content/uploads/2016/09/adobe_pdf_icon-150x143.png 150w" sizes="(max-width: 151px) 100vw, 151px" style="opacity: 1;" width="50"></a></p>';
		}
	}
	else
	{
		$result = '<strong>Erreur WS</strong>';
	}
	
	//if (trim($_SESSION['adherent_infos']->NumAdhesion) == '47749')
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
	
	//return '';
}

function getAppelCotissation2020()
{
	if (is_admin()) return;
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getAppelCotissation2020';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$appels = json_decode($response->body);
		
		if (isset($appels[0]))
		{
			$result = '<p>
			<a class="vc_general vc_btn3 vc_btn3-size-md vc_btn3-shape-square vc_btn3-style-flat vc_btn3-icon-left vc_btn3-color-danger" href="/appel_cotisation_2020.php" title="" target="_blank"><i class="vc_btn3-icon fa fa-file-pdf-o"></i> Téléchargez votre appel à cotisation</a></p>';
		}
	}
	else
	{
		$result = '<strong>Erreur WS</strong>';
	}
	
	//if (trim($_SESSION['adherent_infos']->NumAdhesion) == '47749')
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
	
	//return '';
}
function getAppelCotissation2021()
{
	if (is_admin()) return;
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getAppelCotissation2021';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$appels = json_decode($response->body);
		
		if (isset($appels[0]))
		{
			$result = '<p>
			<a class="vc_general vc_btn3 vc_btn3-size-md vc_btn3-shape-square vc_btn3-style-flat vc_btn3-icon-left vc_btn3-color-danger" href="/appel_cotisation_2021.php" title="" target="_blank"><i class="vc_btn3-icon fa fa-file-pdf-o"></i> Téléchargez votre appel à cotisation</a></p>';
		}
	}
	else
	{
		$result = '<strong>Erreur WS - response status : '. $response->code .'</strong>';
	}
	
	//if (trim($_SESSION['adherent_infos']->NumAdhesion) == '47749')
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
	
	//return '';
}

function getAppelCotissation2022()
{
	if (is_admin()) return;
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getAppelCotissation2022';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$appels = json_decode($response->body);
		
		if (isset($appels[0]))
		{
			$result = '<p>
			<a class="vc_general vc_btn3 vc_btn3-size-md vc_btn3-shape-square vc_btn3-style-flat vc_btn3-icon-left vc_btn3-color-danger" href="/appel_cotisation_2022.php" title="" target="_blank"><i class="vc_btn3-icon fa fa-file-pdf-o"></i> Téléchargez votre appel à cotisation</a></p>';
		}
	}
	else
	{
		$result = '<strong>Erreur WS - response status : '. $response->code .'</strong>';
	}
	
	//if (trim($_SESSION['adherent_infos']->NumAdhesion) == '47749')
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
	
	//return '';
}

function getAppelCotissation2023()
{
	if (is_admin()) return;
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getAppelCotissation2023';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
	{
		$appels = json_decode($response->body);
		
		if (isset($appels[0]))
		{
			$result = '<p>
			<a class="vc_general vc_btn3 vc_btn3-size-md vc_btn3-shape-square vc_btn3-style-flat vc_btn3-icon-left vc_btn3-color-danger" href="/appel_cotisation_2023.php" title="" target="_blank"><i class="vc_btn3-icon fa fa-file-pdf-o"></i> Téléchargez votre appel à cotisation</a></p>';
		}
	}
	else
	{
		$result = '<strong>Erreur WS - response status : '. $response->code .'</strong>';
	}
	
	//if (trim($_SESSION['adherent_infos']->NumAdhesion) == '47749')
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
	
	//return '';
}

function getMesCotisations()
{
	if (is_admin()) return;
	
	$result ='';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getCotisationsNew';
	//$url = URL_WS_ADH.'v1.0/json/user/getCotisations';
	
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	if ($response->code == 200)
	{
		//if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35') print_r($response);
		$cotisationss = json_decode($response->body);
	}
	else
	{
		//if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35') print_r($response);
		$result = '<strong>Erreur WS</strong>';
	}
	
	/*
	if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35')
		echo 'TOTO'.print_r($cotisationss, true).'TAT';
	*/
	
	$result ='';
	
	$fichier_pdf_recufiscal= get_permalink ( get_option( 'edc_adherent_id_page_recu_fiscal' ) );
	
	
	$msg_Paiement = '';
	/*
	if ($adherent["TypePaiement"] == "Chèque")
	{
		$msg_Paiement ="<p>Vous payez actuellement votre cotisation par <strong>ch&egrave;que</strong>. Pour passer au <strong>prélèvment automatique </strong>cliquez ici</p>";	
	}
	*/
	
	$result .= '<div id="cotisations">				
				'.$msg_Paiement.'
				<table class="rouge" width=100% cellpadding="3" cellspacing="0" border="0">
				<tr bgcolor="#002c52" style="color:#ffffff">
					<th>Ann&eacute;e</th>
					<th>Montant</th>
					<th>Date de r&egrave;glement</th>
					<th>Mode de r&egrave;glement</th>					
					<th>Etat</th>
					<th>&nbsp;</th>
				</tr>';	
	
	if (isset($cotisationss->Cotisations->annee)) $cotisationss->Cotisations = array($cotisationss->Cotisations);
	
	foreach($cotisationss as $cotisations)
	{
		if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35')
		{
			?><!--
            <?php print_r($cotisations);?>
            --><?php
		}
		
		if (!empty($cotisations->DateEncaissementCotisation))
		{
			/*if (($cotisations->annee == 2020 && $cotisations->mode_reglement != "Carte Bancaire" && $cotisations->mode_reglement != "Prélèvement") || $cotisations->annee <= date('Y')-2 || 
				$cotisations->EDI_bool != 0)*/
			if ($cotisations->annee <= date('Y')-2 || $cotisations->EDI_bool != 0)
			{
				$lien_supp = '';
				$etat = '<img src="'.GESTION_ADH_REP_ICONES.'/status-busy.png" alt="Non disponible" align="absmiddle"/>';	
			}
			else if ($cotisations->mode_reglement == "Prélèvement" && date('Y-m-d') < $cotisations->annee.'-03-05')			//	le reçu n'est disponible qu'à partir du 05/03 pour les prélèvements (2 mois après l'appel à cotisation)
			{
				$lien_supp = '';
				$etat = '<img src="'.GESTION_ADH_REP_ICONES.'/status-busy.png" alt="Non disponible" align="absmiddle"/>';	
			}
/*			else if ($cotisations->annee == 2019 && $cotisations->mode_reglement == "Prélèvement" && date('Y-m-d') < '2019-03-13')
			{
				$lien_supp = '';
				$etat = '<img src="'.GESTION_ADH_REP_ICONES.'/status-busy.png" alt="Non disponible" align="absmiddle"/>';	
			}
*/
			else
			{
				$etat ='<img src="'.GESTION_ADH_REP_ICONES.'/status.png" alt="R&eacute;gl&eacute;" align="absmiddle"/>';
				$lien_supp = '<a target="_blank" href="'.$fichier_pdf_recufiscal.'?IdCotisation='.$cotisations->IdCotisationAnnee.'"><img src="'.GESTION_ADH_REP_ICONES.'/blue-document-pdf-text.png" align="absmiddle" ></a>';
			}
		}
		else
		{
			$lien_supp =$cotisations->CotisationMotifImpaye.' ('.$cotisations->dateImpaye.')';
			$etat = '<img src="'.GESTION_ADH_REP_ICONES.'/status-busy.png" alt="Non disponible" align="absmiddle"/>';	
		}
		$result.= '<tr>
					<td width="20" style=" vertical-align:middle;text-align:center;">'.$cotisations->annee.'</td>
					<td width="40" style="vertical-align:middle;text-align:right; white-space:nowrap;">'.number_format((float)$cotisations->MontantCotisation,2,","," ").' €</td>
					<td style="vertical-align:middle;text-align:center">'.$cotisations->DateEncaissementCotisation.'</td>
					<td style="vertical-align:middle;text-align:center">'.$cotisations->mode_reglement.'</td>
					<td style="vertical-align:middle;text-align:center">'.$etat.'</td>
					<td style="vertical-align:middle;text-align:center">'.$lien_supp.'</td>
				</tr>';	
	}
	
	/*
		foreach($cotisationss->Cotisations as $cotisations)
		{
					
			if (!empty($cotisations->DateEncaissementCotisation))
			{
				if ($cotisations->annee <= date('Y')-2 || 
					$cotisations->annee == 2017 || 
					$cotisations->mode_reglement == "Gratuit" || 
					substr($cotisations->mode_reglement, 0, 3) == 'Off' || 
					substr($cotisations->mode_reglement, 0, 6) == 'Parain')
				{
					$lien_supp = '';
					$etat = '<img src="'.GESTION_ADH_REP_ICONES.'/status-busy.png" alt="Non disponible" align="absmiddle"/>';	
				}
				else
				{
					$etat ='<img src="'.GESTION_ADH_REP_ICONES.'/status.png" alt="R&eacute;gl&eacute;" align="absmiddle"/>';
					$lien_supp = '<a target="_blank" href="'.$fichier_pdf_recufiscal.'?IdCotisation='.$cotisations->IdCotisationAnnee.'"><img src="'.GESTION_ADH_REP_ICONES.'/blue-document-pdf-text.png" align="absmiddle" ></a>';
				}
			}
			else
			{
				$lien_supp =$cotisations->CotisationMotifImpaye.' ('.$cotisations->dateImpaye.')';
				$etat = '<img src="'.GESTION_ADH_REP_ICONES.'/status-busy.png" alt="Non disponible" align="absmiddle"/>';	
			}
			$result.= '<tr>
						<td width="20" style=" vertical-align:middle;text-align:center;">'.$cotisations->annee.'</td>
						<td width="40" style="vertical-align:middle;text-align:right; white-space:nowrap;">'.number_format((float)$cotisations->MontantCotisation,2,","," ").' €</td>
						<td style="vertical-align:middle;text-align:center">'.$cotisations->DateEncaissementCotisation.'</td>
						<td style="vertical-align:middle;text-align:center">'.$cotisations->mode_reglement.'</td>
						<td style="vertical-align:middle;text-align:center">'.$etat.'</td>
						<td style="vertical-align:middle;text-align:center">'.$lien_supp.'</td>
					</tr>';	
		}
	*/
	
	$result .='</table>
				</div>';	
	
	return '<div style="padding:5px;background-color:white;">' . $result . '</div>';
}

$typesTel = null;

function genererSelectTypeTelephone($name, $default)
{
	if (is_admin()) return;
	global $typesTel;
	
	if ($typesTel == null)
	{	
		$url = URL_WS_ADH.'v1.0/json/user/getTelephonesTypes';
		
		$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
		if ($response->code == 200)
		{
			$typesTel = json_decode($response->body);
		}
		else
		{
			$result = '<strong>Erreur WS</strong>';
		}
	}
	$liste = "<select name=\\'".$name."\\' Id=\\'".$name."\\'>
						<option value=\\'null\\'>--- S&eacute;lectionnez un type</option>";
	foreach($typesTel->Types as $type)
	{
		$liste .= "<option value=\\'". (string)$type->IdTelephoneType. "\\' ";
		//Sélection de l'agence déjŕ prédéfinie
		if ($default ==(string)$type->IdTelephoneType)
		{
			$liste .= ' SELECTED';
		}
		$liste .='>' . (string)$type->Type. '</option>';
	}
	$liste .= '</select>';
	return($liste);
}


$listPays = null;

function getListePays()
{
	if (is_admin()) return;
	if ($listPays == null)
	{
	
		$url = URL_WS_ADH.'v1.0/json/getListePays';
		
		$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
		if ($response->code == 200)
		{
			$listPays = json_decode($response->body);
		}
		else
		{
			$result = '<strong>Erreur WS</strong>';
		}
	}
	
	$liste ="	<select name='IdPays' Id='IdPays'>
					<option value='0'>Choisir votre pays</option>";
	foreach($listPays->Pays as $pays)
	{
		$liste .= "<option value='". (string)$pays->IdPays. "' ";
		$liste .='>' . (string)$pays->Pays. '</option>';
	}
	$liste .= '</select>';
	return('
	<script type="text/javascript">
	jQuery("document").ready(
		function(){
			jQuery("#IdPays").click(
				function ()
				{
					var pays=jQuery("#IdPays").val();
					jQuery("#ValueIdPays").val(pays);
				}
			);
		});
	</script>' . $liste);
}

function getChangeAdresse()
{
	if (is_admin()) return;
	if ($_SESSION['adherent_infos']->Adresse1 == '' && $_SESSION['adherent_infos']->CP != '')
		$_SESSION['adherent_infos']->Adresse1 = '-';
	
	/*
	if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35')
	{
		$_SESSION['adherent_infos']->Adresse1 = 'route';
		print_r($_SESSION['adherent_infos']);
	}
	*/
	
	$result ='';
	
	if (isset($_POST['Adresse1']))
	{
		$search   = array("\\","'");
		$replace = array("","''");
		
		$IdContact=$_POST["IdContact"];
		$IdAdhesion=$_POST["IdAdhesion"];
		$Adresse=str_replace($search,$replace,$_POST["Adresse"]);
		$Adresse1=str_replace($search,$replace,$_POST["Adresse1"]);
		$Adresse2=str_replace($search,$replace,$_POST["Adresse2"]);
		$Adresse3=str_replace($search,$replace,$_POST["Adresse3"]);
		$IdCommune=$_POST["IdCommune"];
		$CP=$_POST["CP"];
		$ValueIdPays=$_POST["ValueIdPays"];
		$ValuePays=$_POST["ValuePays"];
		$PaysById=$_POST["PaysById"];
		$VilleManuelle=str_replace($search,$replace,$_POST["VilleManuelle"]);
		$AdresseEtranger=str_replace($search,$replace,$_POST["AdresseEtranger"]);
		$CheckVilleManuelle=$_POST["CheckVilleManuelle"];
		$AncienneAdresse=str_replace($search,$replace,$_POST["AncienneAdresse"]);
		$NouvelleAdresse=str_replace($search,$replace,$_POST["NouvelleAdresse"]);
		$ChoixDiffusionAdresse=$_POST["ChoixDiffusionAdresse"];
	
		$InfoMAJEureka=0; // ME PERMET DE SAVOIR JE PEUX EFFECTUER UNE MISE A JOUR DE LA BDD.
		$InfoCreationFicheEureka=0; // ME PERMET DE SAVOIR SI JE DOIS EFFECTUER UNE CREATION DE FICHE POUR LE SERVICE ADMINISTRATIF.

		// SI L'ADHERENT A COCHE LA CASE "JE NE TROUVE PAS LA VILLE DANS CETTE LISTE"
		if ($CheckVilleManuelle != 0 && $CheckVilleManuelle != "")
		{
			$InfoMAJEureka++;		
			$InfoCreationFicheEureka++;	
		}
		
		// SI L'ADHERENT A CHOISI QUE SA NOUVELLE ADRESSE DEVAIT ETRE DIFFUSEE AUX INTERVENANTS DE SON INVESTISSEMENT
		if ($ChoixDiffusionAdresse != "edc")
			$InfoCreationFicheEureka++;
		
		//SI ADRESSE ETRANGER, JE NE DOIS PAS METTRE A JOUR L'ADRESSE EN BDD
		if ($ValuePays == "etranger")
		{
			$InfoMAJEureka++;
			$InfoCreationFicheEureka++;
		}

		//TEST DE VARIABLE. ME PERMET DE CONNAITRE SI JE PEUX MODIFIER LA BDD.
		//SI = 0, JE PEUX FAIRE UN UPDATE OU UN INSERT
		//SI > 0, JE NE PEUX RIEN FAIRE
		if ($InfoMAJEureka == 0)
		{
			//SI ADRESSE NON VIDE
			if ($_SESSION['adherent_infos']->Adresse1 != '')
			{
				//UPDATE ADRESSE
				
				//if ($_SESSION['adherent_infos']->NumAdhesion == '47749')
					$url = URL_WS_ADH.'v1.0/json/user/insert_adresse';
				//else			
				//	$url = URL_WS_ADH.'v1.0/json/user/update_adresse';
			
				$response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('AdresseComplement1='.trim($Adresse1).'&AdresseComplement2='.trim($Adresse2).'&AdresseComplement3='.trim($Adresse3).'&IdCommune='.trim($IdCommune))->send();
				if ($response->code == 200)
				{
				}
				else
				{
					//print_r($response);
					mail ('production@edc.asso.fr', 'EDC - Erreur - update_adresse', print_r($response, true).print_r($_POST, true));
					return '<strong>Erreur WS</strong>';
				}
			}
			else
			{
				//SINON ADRESSE VIDE
				//INSERTION DE L'ADRESSE
				$url = URL_WS_ADH.'v1.0/json/user/insert_adresse';
			
				$response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('AdresseComplement1='.trim($Adresse1).'&AdresseComplement2='.trim($Adresse2).'&AdresseComplement3='.trim($Adresse3).'&IdCommune='.trim($IdCommune))->send();
				if ($response->code == 200)
				{
					//echo '1';
				}
				else
				{
					mail ('production@edc.asso.fr', 'EDC - Erreur - insert_adresse', print_r($response, true).print_r($_POST, true));
					//print_r($response);
					return '<strong>Erreur WS</strong>';
				}	
			}
		}

		//--------------------------------------------------------------------------------------
		//-------------INSERTION D'UNE ACTION WEB DANS LA FICHE SUIVI DE L'ADHESION-------------
		//--------------------------------------------------------------------------------------
		
		//RECUPERATION DE L'ID DE LA FICHE SUIVI DE L'ADHESION
		$url = URL_WS_ADH.'v1.0/json/user/GetIdFicheSuiviAdhesion';
		$response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('InfoCreationFicheEureka='.trim($InfoCreationFicheEureka).'&AncienneAdresse='.trim($AncienneAdresse).'&NouvelleAdresse='.trim($NouvelleAdresse).'&IdCommune='.trim($IdCommune).'&ValuePays='.trim($ValuePays).'&CheckVilleManuelle='.trim($CheckVilleManuelle).'&ChoixDiffusionAdresse='.trim($ChoixDiffusionAdresse))->send();
		
		
		
		if ($response->code == 200)
		{
			$IdFiche = json_decode($response->body);

		}
		else
		{
			//print_r($response);
			return '<strong>Erreur WS</strong>';
		}	


		//-------------------------------------------------------
		//-------------DEBUT PARTIE ENVOI DES MAILS -------------
		//-------------------------------------------------------

		//FORMATAGE DES VARIABLES POUR AFFICHAGE CORRECT DANS MAILS
		$AncienneAdresse=str_replace("''","'",$AncienneAdresse);
		$NouvelleAdresse=str_replace("''","'",$NouvelleAdresse);
		
		$UpdateAdresseKO=0; // Me permet de savoir quand insérer un commentaire pour dire que l'adresse n'a pas été mise à jour
						
		if (isset($ValuePays) && $ValuePays == "etranger")
		{
			$Observation.="L''adresse que l''adhérent a renseigné ne se trouve pas en France Métropolitaine ou DOM-TOM.\n";
			$UpdateAdresseKO++;
		}
		
		if ($CheckVilleManuelle != 0 && $CheckVilleManuelle != "")
		{
			$Observation.="L''adhérent a inséré une ville manuellement. Cela signifie que cette ville n''existe pas sous Eureka. Il sera nécessaire de valider cette commune avant de l''intégrer dans la base de données via le site :http://www.laposte.fr/sna/rubrique.php3?id_rubrique=59.\n";
			$UpdateAdresseKO++;
		}

		if ($UpdateAdresseKO > 0)
		{
			// VARIABLE SERVANT POUR ACTION DE LA FICHE CHANGEMENT ADRESSE
			//$Observation .="La nouvelle adresse de l''adhérent n''a pas été renseignée dans Eureka.\n";

			//VARIABLE SERVANT POUR LE MAIL POUR LE SERVICE ADMIN (TEMPORAIRE)
			$ActionEDC = '<p style="color:#FF0000;">L\'adresse n\'a pas été automatiquement mise à jour sur Eureka.</p>';
			$ActionEDC .= '<p>'.nl2br(str_replace("''", "'", $Observation)).'</p>';
			
			
			$ActionADH='<p>Votre adresse n\'a pas été automatiquement mise à jour dans notre base de données.</p>
						<p>Une intervention de notre service administratif est nécessaire afin de valider votre demande.</p>';

			//VARIABLE POUR LE MESSAGE DE FIN AVANT LA REDIRECTION
			$messageFin='<p style="font-size:12pt; color:#FF0000;">Nous n\'avons pas pu mettre à jour votre adresse dans notre base de données car une intervention manuelle est nécessaire.</p>';
			$messageFin .='<p style="font-size:12pt; color:#FF0000;">Néanmoins, soyez assurés que votre demande a bien été prise en compte et qu\'un dossier a été ouvert et transféré dans le service concerné afin de mettre à jour votre adresse dans les meilleurs délais.';
			$TimeOut=10;
		}
		else
		{
			$Observation .="La nouvelle adresse de l''adhérent a déjà été renseignée dans Eureka.\n";
			$ActionEDC='<p style="color:#009900;">L\'adresse a été automatiquement mise à jour sur Eureka.</p>';
			$ActionADH='<p style="color:#009900;">Votre adresse a été automatiquement mise à jour dans notre base de données.</p>';
			$TimeOut=5;
		}
		
		if ($ChoixDiffusionAdresse != "edc")
		{
			$ActionEDC .='<p style="color:#FF0000;">L\'adhérent souhaite qu\'EDC communique sa nouvelle adresse à ses différents interlocuteurs.</p>';
			$ActionADH .='<p>Nous engageons une proc&eacute;dure d&acute;information aupr&egrave;s des intervenants de votre (ou vos) investissement(s). Celle ci concerne :</p>';
			$ActionADH .='<p>Pour ce qui est du changement aupr&egrave;s des organismes d&acute;&eacute;tat nous vous invitons &agrave; effectuer la proc&eacute;dure sur le site du gouvernement : <a style="color: #e4680c;" href="https://mdel.mon.service-public.fr/je-change-de-coordonnees.html">https://mdel.mon.service-public.fr/je-change-de-coordonnees.html</a></p>
							<p>Notre d&eacute;lai de traitement de votre demande est actuellement de 15 jours pour les changements d&acute;adresse postale.</p>';
			$Observation .="L''adhérent souhaite que l''association communique sa nouvelle adresse aux différents intervenants de son (ses) investissement(s).\n";
		}
		else
		{
			$Observation .="Le changement d''adresse ne concerne que l''association.\n";
			$ActionEDC .='<p style="color:#009900;">Il n\'est pas nécesaire de diffuser la nouvelle adresse de l\'adhérent.</p>';
		}
		
		//MODELE DE COURRIER POUR L'ASSOCIATION
		$fichierModel = WP_PLUGIN_DIR."/edc_adherent/modeles/AR_EDC_modification_adresse_postale.html";
		
		//JE REMPLACE CERTAINES VARIABLES DANS LE MODELE
		$t_contenu=array(	0=>array("search"=>"[NumAdhesion]", "replace"=>$_SESSION['adherent_infos']->NumAdhesion)
							,1=>array("search"=>"[DateAction]", "replace"=>date("d/m/Y H:i:s"))
							,2=>array("search"=>"[Civilite]", "replace"=>$_SESSION['adherent_infos']->Civilite)
							,3=>array("search"=>"[Prenom]", "replace"=>$_SESSION['adherent_infos']->Prenom)
							,4=>array("search"=>"[Nom]", "replace"=>$_SESSION['adherent_infos']->Nom)
							,5=>array("search"=>"[AncienneAdresse]", "replace"=>$AncienneAdresse)
							,6=>array("search"=>"[NouvelleAdresse]", "replace"=>$NouvelleAdresse)
							,7=>array("search"=>"[Action]", "replace"=>$ActionEDC)
							);
									

		//J'ENVOI LE MAIL A L'ASSOCIATION
		$t_infosMail = array(	"From"=>'noreply@assoedc.com'
								,"To"=>'sea@edc.asso.fr'
								,"Subject"=>"[EXTRANET ADHERENT EDC] : Changement d'adresse postale"
								,"Reply to"=>$_SESSION['adherent_infos']->Email
							);
		
		// ENVOI DU MAIL A L'ADHERENT SI ENVOI A ASSOCIATION OK
		if(envoiMail($fichierModel, $t_contenu, $t_infosMail) == true)
		{
			$fichierModel = WP_PLUGIN_DIR."/edc_adherent/modeles/AR_ADH_modification_adresse_postale.html";
					
			$t_contenu=array(	0=>array("search"=>"[NumAdhesion]", "replace"=>$_SESSION['adherent_infos']->NumAdhesion)
								,1=>array("search"=>"[DateAction]", "replace"=>date("d/m/Y H:i:s"))
								,2=>array("search"=>"[Civilite]", "replace"=>$_SESSION['adherent_infos']->Civilite)
								,3=>array("search"=>"[Prenom]", "replace"=>$_SESSION['adherent_infos']->Prenom)
								,4=>array("search"=>"[Nom]", "replace"=>$_SESSION['adherent_infos']->Nom)
								,5=>array("search"=>"[AncienneAdresse]", "replace"=>$AncienneAdresse)
								,6=>array("search"=>"[NouvelleAdresse]", "replace"=>$NouvelleAdresse)
								,7=>array("search"=>"[Action]", "replace"=>$ActionADH)
								);
					
			$t_infosMail = array(	"From"=>'noreply@assoedc.com'
									,"To"=>$_SESSION['adherent_infos']->Email
									,"Subject"=>"[EXTRANET ADHERENT EDC] : Prise en compte de votre changement d'adresse postale"
								);
		
			envoiMail($fichierModel, $t_contenu, $t_infosMail);
		}

		$result .= '<p style="font-size:12pt; color:#006600;">Votre demande de changement d\'adresse a bien été prise en compte.</p>';
		$result .= '<p style="font-size:12pt; color:#006600;">Un email de confirmation vous a été envoyé. Celui-ci contient les informations que vous nous avez communiquées.</p>';
		$result .= $messageFin;
		
		$url = URL_WS_ADH.'v1.0/json/user/infos';
		$response = \Httpful\Request::get($url)->authenticateWith($_SESSION['adherent_login'], $_SESSION['adherent_pass'])->send();
		if ($response->code == 200)
		{
			$_SESSION['adherent_infos'] = json_decode($response->body);
		}
	}
	elseif (isset($_POST['Cmpl4']))
	{
		$search   = array("\\");
		$replace = array("");
		
		$AncienneAdresse = '';
				
		if ($_SESSION['adherent_infos']->Adresse1 != '')
		{
			if ($_SESSION['adherent_infos']->Adresse1 != '' )
			{
				$AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse1 . '<br/>';
			}
			if ($_SESSION['adherent_infos']->Adresse2 != '' )
			{
				$AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse2 . '<br/>';
			}
			if ($_SESSION['adherent_infos']->Adresse3 != '' )
			{
				$AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse3 . '<br/>';
			}
			if ($_SESSION['adherent_infos']->CP != '' )
			{
				$AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->CP . ' ' . $_SESSION['adherent_infos']->Ville . '<br/>';
			}
			if ($_SESSION['adherent_infos']->Pays != '' )
			{
				$AncienneAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Pays .'';
			}
		}
		
		$Adresse= $_SESSION['adherent_infos']->Adresse1; //Me permet de récupérer l'ancienne Adresse1 pour savoir si update ou insert si modif
		$Cmpl4=str_replace($search,$replace,mb_strtoupper($_POST["Cmpl4"])); //Appt, Etage, Escalier, Chez...
		$Cmpl3=str_replace($search,$replace,mb_strtoupper($_POST["Cmpl3"])); //Immeuble, batiment, résidence
		$Cmpl2=str_replace($search,$replace,mb_strtoupper($_POST["Cmpl2"])); //Numéro et nom de la rue
		$Cmpl1=str_replace($search,$replace,mb_strtoupper($_POST["Cmpl1"])); //Mention spéciale, lieu dit, boite postale
		$CP=$_POST["CP"];
		$IdCommune=$_POST["ValueIdCommune"];
		$ValuePays=$_POST["ValuePays"]; // Permet de savoir si nouvelle adresse en france ou à l'étranger
		$ValueIdPays=$_POST["ValueIdPays"]; // Permet de récupérer l'Id du pays sélectionné
		$AdresseEtranger=str_replace($search,$replace,mb_strtoupper($_POST["AdresseEtranger"])); //Permet de récupérer le textarea qui contient l'adresse dans un pays étranger
		$CheckVilleManuelle=$_POST["CheckVilleManuelle"]; // Permet de savoir si l'adhérent à coché la case "Ma ville n'est pas dans cette liste"
		$VilleManuelle=str_replace($search,$replace,mb_strtoupper($_POST["VilleManuelle"])); // Récupère la valeur de la ville saisie

		//Par défaut, j'ai une ligne d'adresse (numéro et nom de la voie)
		$ligneAdresse=1;
		$Adresse3='';
		$Adresse2='';
		$Adresse1='';

		$order   = array("ESCALIER", " APT ", "APPARTEMENT", " RES ", " RÉS ", "RÉSIDENCE ", "BATIMENT ", "BÂTIMENT ", " BAT ");
		$replace = array("ESC", " APPT ", "APPT", " RÉSIDENCE ", " RÉSIDENCE ", "RÉSIDENCE ", "BÂT ", "BÂT ", " BÂT ");
		$Cmpl4 = str_replace($order, $replace, $Cmpl4);
		$Cmpl3 = str_replace($order, $replace, $Cmpl3);


		//Me permet de savoir le nombre de ligne d'une adresse
		if ($Cmpl4 != '')
			$ligneAdresse++;
		if ($Cmpl3 != '')
			$ligneAdresse++;
		if ($Cmpl1 != '')
			$ligneAdresse++;
			
		//Affectation des complément d'adresse
		switch ($ligneAdresse)
		{
			//Une seule ligne qui correspond au numéro et nom de voie
			case 1:
				$Adresse1=$Cmpl2;
			break;
			
			//Si 2 lignes d'adresse
			case 2:
				//Si présence d'un lieu dit
				if ($Cmpl1 != '')
				{
					$Adresse1=$Cmpl2;
					$Adresse2=$Cmpl1;
				}
				//Si présence d'une résidence
				if ($Cmpl3 != '')
				{
					$Adresse1=$Cmpl3;
					$Adresse2=$Cmpl2;
				}
				if ($Cmpl4 != '')
				{
					$Adresse1=$Cmpl4;
					$Adresse2=$Cmpl2;
				}
			break;
			
			//Si 3 lignes d'adresse
			case 3:
				//Si présence d'un lieu dit
				if ($Cmpl1 != '')
				{
					$Adresse3=$Cmpl1;
					$Adresse2=$Cmpl2;
					if ($Cmpl3 != '')
						$Adresse1=$Cmpl3;
					else
						$Adresse1=$Cmpl4;
				}
				else
				{
					//Si non présence d'un lieu dit, je peux en déduire toutes les lignes
					$Adresse3=$Cmpl2;
					$Adresse2=$Cmpl3;
					$Adresse1=$Cmpl4;
				}
			break;
			
			//Si 4 lignes (CAS qui ne doit pas exister normalement)
			case 4:
				$Adresse3=$Cmpl1;
				$Adresse2=$Cmpl2;
				$Adresse1=$Cmpl3.', '.$Cmpl4;
			break;
		}

		$ValueIdPays = 0;

		//Récupération de la ville par l'IdCommune
		$url = URL_WS_ADH.'v1.0/json/getVille/'.$IdCommune;
		$response = \Httpful\Request::get($url)->send();
		if ($response->code == 200)
		{
			$villeWS = json_decode($response->body);
			$val = 0;
			if (isset($villeWS->ville->$val))
				$VilleById=$villeWS->ville->$val;
			else
				$VilleById=$villeWS->ville;
		
			if ($ValuePays == 'france')
			{
				$val = 0;
				if (isset($villeWS->pays->$val))
					$ValueIdPays=$villeWS->pays->$val;
				else
					$ValueIdPays=$villeWS->pays;
			}
		}
		
		$url = URL_WS_ADH.'v1.0/json/getPays/'.$ValueIdPays;
		$response = \Httpful\Request::get($url)->send();
		if ($response->code == 200)
		{
			$p = json_decode($response->body);
			$val = 0;
			if (isset($p->$val))
				$PaysById = $p->$val;
			else
				$PaysById = $p;
		}
		else
		{
			$PaysById = '';
		}
		//NOUVELLE ADRESSE
		//FORMATAGE SPECIFIQUE SI ZONE = FRANCE OU ZONE = ETRANGER
		if ($ValuePays == 'france')
		{
			$NouvelleAdresse='';
			if (isset($Adresse1) && $Adresse1 != '' )
				$NouvelleAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $Adresse1;
			if (isset($Adresse2) && $Adresse2 != '' )
				$NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $Adresse2;
			if (isset($Adresse3) && $Adresse3 != '' )
				$NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $Adresse3;
		
			
			//Si selection d'une ville dans la liste déroulante
			if (($CheckVilleManuelle == 0) || ($CheckVilleManuelle == ""))
				$NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $CP . ' ' . $VilleById;
			else
				$NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $CP . ' ' . $VilleManuelle;
			$NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $PaysById;
		}
		else
		{
			$order   = array("\r\n", "\n", "\r");
			$replace = '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$AdresseEtranger = str_replace($order, $replace, $AdresseEtranger);
			$NouvelleAdresse .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$AdresseEtranger;
			$NouvelleAdresse .= '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$PaysById;
	
		}
		$result .= '	<h2>Veuillez trouver ci-après le récapitulatif des données saisies.</h2>';
		$result .='	<p style="font-size:12pt; font-weight:bold;">'.$NouvelleAdresse.'</p>';
		$result .='	<form name="ValideDonnees" id="ValideDonnees" method="post" style="width:100%;">
						<input name="Adresse" type="hidden" id="Adresse" value="'.$Adresse.'"/>
						<input name="Adresse1" type="hidden" id="Adresse1" value="'.$Adresse1.'"/>
						<input name="Adresse2" type="hidden" id="Adresse2" value="'.$Adresse2.'"/>
						<input name="Adresse3" type="hidden" id="Adresse3" value="'.$Adresse3.'"/>
						<input name="IdCommune" type="hidden" id="IdCommune" value="'.$IdCommune.'"/>
						<input name="CP" type="hidden" id="CP" value="'.$CP.'"/>
						<input name="ValueIdPays" type="hidden" id="ValueIdPays" value="'.$ValueIdPays.'"/>
						<input name="ValuePays" type="hidden" id="ValuePays" value="'.$ValuePays.'"/>
						<input name="PaysById" type="hidden" id="PaysById" value="'.$PaysById.'"/>
						<input name="VilleManuelle" type="hidden" id="VilleManuelle" value="'.$VilleManuelle.'"/>
						<input name="AdresseEtranger" type="hidden" id="AdresseEtranger" value="'.$AdresseEtranger.'"/>
						<input name="CheckVilleManuelle" type="hidden" id="CheckVilleManuelle" value="'.$CheckVilleManuelle.'"/>
						<input name="NouvelleAdresse" type="hidden" id="NouvelleAdresse" value="'.$NouvelleAdresse.'"/>
						<input name="AncienneAdresse" type="hidden" id="AncienneAdresse" value="'.$AncienneAdresse.'"/>
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
		$result .='	<p style="font-size:12pt;">Si les informations enregistrées sont exactes, nous vous remercions de confirmer votre changement d\'adresse en cliquant sur le bouton ci-dessous. Sinon cliquez <a href="'.$url_mes_donnees.'" style="font-size:12pt;">sur ce lien</a> pour retourner sur la page de vos informations personnelles.</p>';
		return $result;
	}
	else
	{
	
		//Affichage de l'adresse actuelle
		if ($_SESSION['adherent_infos']->Adresse1 != '')
		{
			$result .= '<p style="font-size:16pt;">L’adresse enregistrée par EDC est :</p>';
			if ($_SESSION['adherent_infos']->Adresse1 != '' )
			{
				$result .= '<span style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse1 . '<br/>';
			}
			if ($_SESSION['adherent_infos']->Adresse2 != '' )
			{
				$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse2 . '<br/>';
			}
			if ($_SESSION['adherent_infos']->Adresse3 != '' )
			{
				$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Adresse3 . '<br/>';
			}
			if ($_SESSION['adherent_infos']->CP != '' )
			{
				$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->CP . ' ' . $_SESSION['adherent_infos']->Ville . '<br/>';
			}
			if ($_SESSION['adherent_infos']->Pays != '' )
			{
				$result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $_SESSION['adherent_infos']->Pays .'</span>';
			}
		}
	
		//Choix de la zone géographique pour la nouvelle adresse
		$result .='	<p style="font-size:16pt; margin-top:40px;">Ma nouvelle adresse se situe</p>
					<table style="font-size:12pt;">
					<tr>
						<td style="vertical-align:middle;">
							<input class="saisie" type="radio" name="zoneGeo" id="zoneGeo" value="france" onClick="ZoneGeoNouvelleAdresse(this.value)"/>
						</td>
						<td style="vertical-align:middle; text-align:left;">En france (Métropolitaine ou DOM-TOM)</td>
					</tr>
					<tr>
						<td style="vertical-align:middle;">
							<input class="saisie" type="radio" name="zoneGeo" id="zoneGeo" value="etranger" onClick="ZoneGeoNouvelleAdresse(this.value)"/>
						</td>
						<td style="vertical-align:middle; text-align:left;">A l\'étranger</td>
					</tr>
					</table>
					<br/>';
		
		//Affichage du formulaire 
		$result .='	<form name="UpdateDonnees" id="UpdateDonnees" method="post" action="" onSubmit="return(change_adresse(this));" style="width:100%;">
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
								<td style="vertical-align:middle;">'.getListePays().'</td>
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
		
		$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
		
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
										
										
										
										jQuery("#ListeVille").load("'.$url_ajax.'&cp="+CP);
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

function getMessageAccueil()
{
	if (is_admin()) return;
	$result = '';
		
	$url_ajax = plugins_url( 'ajax.php', dirname(__FILE__) ).'?ajax=true';
	
	$url = URL_WS_ADH.'v1.0/json/user/getPaiementCotisation';
	$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
	
	if ($response->code == 200)
			{
				if (substr($response->body, 0, 4) != 'null')
				{
					$retval = json_decode($response->body);
					if (isset($retval[0])) $retval = $retval[0];
					$result = '<p>retval parsed</p>';

					// PS le 16/12/2021 - On n'a rien à faire de la valeur de MessagePaiement
//					if (isset($retval->MessagePaiement) && $retval->MessagePaiement == ' ' || $retval->MessagePaiement == '')
//					{				
						/* Hard coded message, 
						   because for some reasons MessagePaiement and MessageAccueil appear NULL (in PHP) from the request vPaiementEnLigneExport.
						   Whenever MessagePaiement or MessageAccueil are not ' ', they appear as NULL.
						   Maybe the problem is due to apostrophes appearing in MessagePaiement and MessageAccueil (to be tested).
						   
						   This solution takes care of MessagePaiement or MessageAccueil appearing NULL but don't overwrite if they are not.
						   However, this solution should be considered as main solution, because it allows to format text in a more fashion way.
						*/ 
						// PS le 16/12/2021 - Suppression du message spécifique pour 2021
						if (is_null($retval->MessageAccueil)) {
							$result = '<p>
									   </p>';
							//$result .= gettype($retval->MessageAccueil);
						} else {
							$result = '<p>'.$retval->MessageAccueil.'</p>';
						}
//					}
				}
			}
	else
	{
		$result = '<p>erreur WS</p>';
	}
	
	return $result;	
}
?>
