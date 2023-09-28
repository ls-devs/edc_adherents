<?php
 set_time_limit(0);
$nom_page="Appel &agrave; cotisation 2018";
include("dbconnect_eureka.php");
include("../../liboperationnel.php");

require_once("../../../php_to_pdf/html2pdf.class.php");

$query='SELECT * FROM vCotisation2018 WHERE Numero = "'.$_GET['id_adherent'].'"';
$resultat = mssql_query($query);
if( $resultat === false) {
	?>Aucun document<?php
	die();
}
else
{
	if ($appel = mssql_fetch_array($resultat))
	{
				
		// Choix du module 
		$modele = '';
		switch($appel['Modele'])
		{
			case 1:
				if ($appel['Prelevement'] == 0)
					$modele = 'AAC 2018 Classique_Pop CHQ-CB-VRMT- Courrier';
				else
					$modele = 'AAC 2018 Classique_Pop PVMT';
				break;
			case 2:
				if ($appel['Prelevement'] == 0)
					$modele = 'AAC 2018 Trop percu_Pop CHQ-CB-VRMT- Courrier';
				else
					$modele = 'AAC 2018 Trop percu_Pop PVMT';
				break;
			case 3:
				if ($appel['Prelevement'] == 0)
					$modele = 'AAC 2018 Partenaire_Pop CHQ-CB-VRMT-Courrier';
				else
					$modele = 'AAC 2018 Partenaire_Pop PVMT-Courrier';
				break;
			case 4:
				if ($appel['Prelevement'] == 0)
					$modele = 'AAC 2018 Parrain-Cller-ere_Pop CHQ-CB-VRMT-Courrier';
				else
					$modele = 'AAC 2018 Parrain-Cller-ere_Pop PVMT';
				break;
			case 5:
				if ($appel['PriseEnCharge'] == '' || $appel['PriseEnCharge'] == 0)
					$modele = 'AAC 2018 Valoriciel-Courrier2';
				else
					$modele = 'AAC 2018 Valoriciel-Courrier';
				break;
			case 6:
				if ($appel['PriseEnCharge'] == '' || $appel['PriseEnCharge'] == 0)
					$modele = 'AAC 2018 Valoriciel-POP-Courrier2';
				else
					$modele = 'AAC 2018 Valoriciel-POP-Courrier';
				break;
		}
		
		// Pour test
		//$modele = 'AAC 2018 Parrain-Cller-ere_Pop PVMT';
		
		if ($modele == '')
			echo 'Erreur WS';
		else
		{
				
			define('_EURO_', chr(128));
			ob_start();
			
			include_once(dirname(__FILE__).'/Modele_cotisation_2018/'.$modele.'.php');
			
			$content = ob_get_clean();

			/////////////////////////////
			// REPLACE DANS LE CONTENT //
			/////////////////////////////
			if ($appel['Parrainage'] == '') $appel['Parrainage'] = 0;
			if ($appel['TropPercu'] == '') $appel['TropPercu'] = 0;
			if ($appel['Total'] == '') $appel['Total'] = 0;
			if ($appel['APayer'] == '') $appel['APayer'] = 0;
			if ($appel['PriseEnCharge'] == '') $appel['PriseEnCharge'] = 0;
			if ($appel['ParrainageConseiller'] == '') $appel['ParrainageConseiller'] = 0;
			
			
			$content = str_replace('[Modele]', ($appel['Modele']), $content);
			$content = str_replace('[Numero]', ($appel['Numero']), $content);
			$content = str_replace('[Groupe]', ($appel['Groupe']), $content);
			$content = str_replace('[groupe]', ($appel['Groupe']), $content);
			$content = str_replace('[Parrainage]', ($appel['Parrainage']), $content);
			$content = str_replace('[ParrainageConseiller]', ($appel['ParrainageConseiller']), $content);
			$content = str_replace('[Prelevement]', ($appel['Prelevement']), $content);
			$content = str_replace('[TropPercu]', ($appel['TropPercu']), $content);
			$content = str_replace('[PriseEnCharge]', ($appel['PriseEnCharge']), $content);
			$content = str_replace('[Total]', ($appel['Total']), $content);
			$content = str_replace('[APayer]', ($appel['APayer']), $content);
			$content = str_replace('[Pourcentage]', ($appel['Pourcentage']), $content); 
			$content = str_replace('[pourcentage]', ($appel['Pourcentage']), $content); 
			$content = str_replace('[NumeroCompte]', ($appel['NumeroCompte']), $content);
			$content = str_replace('[Nom]', ($appel['Nom']), $content);
			$content = str_replace('[Prenom]', ($appel['Prenom']), $content);
			$content = str_replace('[Civilite]', ($appel['Civilite']), $content);
			$content = str_replace('[AdresseComplement1]', ($appel['AdresseComplement1']), $content);
			$content = str_replace('[AdresseComplement2]', ($appel['AdresseComplement2']), $content);
			$content = str_replace('[AdresseComplement3]', ($appel['AdresseComplement3']), $content);
			$content = str_replace('[CodePostal]', ($appel['CodePostal']), $content);
			$content = str_replace('[Ville]', ($appel['Ville']), $content);
			$content = str_replace('[Pays]', ($appel['Pays']), $content);
			$content = str_replace('[EmailPrincipal]', ($appel['EmailPrincipal']), $content);
			$content = str_replace('[ConseillerNom]', ($appel['ConseillerNom']), $content);
			$content = str_replace('[ConseillerPrenom]', ($appel['ConseillerPrenom']), $content);
			$content = str_replace('[ilelle]', ($appel['ilelle']), $content);
			$content = str_replace('[Conseiller]', ($appel['Conseiller']), $content);
			$content = str_replace('[ConseillerCivilite]', ($appel['ConseillerCivilite']), $content); 
			$content = str_replace('[Portable]', ($appel['Portable']), $content);
			
			
			//echo $content; die();
			

			// convert in PDF
			try
			{
				$html2pdf = new HTML2PDF('P','A4','fr', false, 'ISO-8859-15', array(0, 0, 0, 0));
				$html2pdf->writeHTML(str_replace('&oelig;', 'oe', $content));
				$html2pdf->Output($appel['Nom'].'-AppelCotisation2018.pdf');
			}
			catch(HTML2PDF_exception $e) {
				echo $e;
				exit;
			}
		}
	}
}
?>

