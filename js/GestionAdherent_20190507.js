// JavaScript Document
// fonctions de mise à jour des données de l'adhérent et des investissements
function askForModifDob(maDiv, value, wpurl, IdContact, FormPHP)
{
	document.getElementById(maDiv).innerHTML = '<input size="8" maxlength="10" type="text" id="DateNaissance" name="DateNaissance" value="'+value+'" />&nbsp;<a href="javascript:modifierDob(\''+maDiv+'\', \''+FormPHP+'\', \''+IdContact+'\',\''+wpurl+'\')"><img src="'+wpurl+'/tick-white.png" align="absmiddle"></a><a href="javascript:retablirContenuDob(\''+maDiv+'\', \''+FormPHP+'\', \''+IdContact+'\', \''+wpurl+'\', \''+value+'\');"><img src="'+wpurl+'/slash.png" align="absmiddle"></a>';	
	document.getElementById(maDiv+"_error").innerHTML="<em>Modifiez votre date de naissance et validez</em>";
	document.getElementById("DateNaissance").value=value;
	jQuery('#DateNaissance').datepicker({dateFormat: 'dd/mm/yy'
											, showAnim: 'slideDown'
											, changeMonth: true
											, changeYear: true
											, yearRange: 'c-100:c'
											, autosize: 'true' });
	jQuery('#DateNaissance').datepicker("setDate",value);
												
}

function askForModifLoc(maDiv, value, wpurl, IdInvestissement, FormPHP)
{
	document.getElementById(maDiv).innerHTML = '<input size="8" maxlength="10" type="text" id="PremiereLoc" name="PremiereLoc" value="'+value+'" />&nbsp;<a href="javascript:modifierLoc(\''+maDiv+'\', \''+FormPHP+'\', \''+IdInvestissement+'\',\''+wpurl+'\')"><img src="'+wpurl+'/tick-white.png" align="absmiddle"></a><a href="javascript:retablirContenuLoc(\''+maDiv+'\', \''+FormPHP+'\', \''+IdInvestissement+'\', \''+wpurl+'\', \''+value+'\');"><img src="'+wpurl+'/slash.png" align="absmiddle"></a>';	
	document.getElementById(maDiv+"_error").innerHTML="<em>Modifiez la date de première location et validez</em>";
	document.getElementById("PremiereLoc").value=value;
	jQuery('#PremiereLoc').datepicker({dateFormat: 'dd/mm/yy'
											, showAnim: 'slideDown'
											, changeMonth: true
											, changeYear: true
											, minDate : document.getElementById('dateLivraison').value
											, maxDate : new Date()
											
											, autosize: 'true' });
	jQuery('#PremiereLoc').datepicker("setDate",value);
												
}

function askForModifTel(maDiv, value, wpurl, IdContactTelephone, selectType, FormPHP)
{
	document.getElementById(maDiv).innerHTML = selectType + '<input size="8" maxlength="10" type="text" id="Numero" name="Numero" value="'+value+'" />&nbsp;<a href="javascript:modifierTel(\''+maDiv+'\', \''+FormPHP+'\', \''+IdContactTelephone+'\',\''+wpurl+'\',\''+selectType.replace(/'/gi,"\\'")+'\')"><img src="'+wpurl+'/tick-white.png" align="absmiddle"></a><a href="javascript:retablirContenuTel(\''+maDiv+'\', \''+FormPHP+'\', \''+IdContactTelephone+'\', \''+wpurl+'\', \''+value+'\',\''+selectType.replace(/'/gi,"\\'")+'\');"><img src="'+wpurl+'/slash.png" align="absmiddle"></a>';	
	document.getElementById(maDiv+"_error").innerHTML="<em>Modifiez votre t&eacute;l&eacute;phone et validez</em>";
	document.getElementById("Numero").value=value;													
}



function reinit(maDiv)
{
	document.getElementById(maDiv).innerHTML="<i>Cliquez sur le crayon pour modifier la valeur</i>";
	document.getElementById(maDiv).style.display="inline";
	document.getElementById(maDiv).style.color="#888";
}

function cacher(maDiv)
{
	if (document.getElementById) 
	{
	  document.getElementById(maDiv).style.display="none";
	} 
	else if (document.all) 
	{
	  document.all[maDiv].style.display="none";
	} 
	else if (document.layers) 
	{
	  document.layers[maDiv].visibility="hidden";	
	}
}

function afficherMessage(maDiv, message, color)
{
	document.getElementById(maDiv).innerHTML=message;
	document.getElementById(maDiv).style.display="inline";
	document.getElementById(maDiv).style.color=color;
	setTimeout('reinit("'+maDiv+'")',4000);
}

function retablirContenuDob(maDiv, urlAjax, IdContact, wpurl, valeur)
{
	document.getElementById(maDiv).innerHTML = valeur +'<a href="javascript:askForModifDob(\'dob\',\''+document.getElementById('DateNaissance').value+'\', \''+wpurl+'\' ,\''+IdContact+'\',\''+urlAjax+'\')" > <img src="'+wpurl+'/pencil-small.png" alt="Modfier votre date de naissance" align="absmiddle" style="margin:0px;"/></a>';
}

function retablirContenuLoc(maDiv, urlAjax, IdInvestissement, wpurl, valeur)
{
	document.getElementById(maDiv).innerHTML = valeur +'<a href="javascript:askForModifLoc(\'loc\',\''+document.getElementById('PremiereLoc').value+'\', \''+wpurl+'\' ,\''+IdInvestissement+'\',\''+urlAjax+'\')" > <img src="'+wpurl+'/pencil-small.png" alt="Modfier la date de première location" align="absmiddle" style="margin:0px;"/></a>';
}


function retablirContenuTel(maDiv, urlAjax, IdContactTelephone, wpurl, valeur, selectType)
{
	document.getElementById(maDiv).innerHTML = valeur +'<a href="javascript:askForModifTel(\''+maDiv+'\',\''+document.getElementById('Numero').value+'\', \''+wpurl+'\' ,\''+IdContactTelephone+'\',\''+selectType.replace(/'/gi,"\\'")+'\',\''+urlAjax+'\')" > <img src="'+wpurl+'/pencil-small.png" alt="Modfier votre téléphone" align="absmiddle" style="margin:0px;"/></a>';
}

function modifierDob(maDiv, urlAjax, IdContact, wpurl)
{	
	var texte = new String();
	var maDate = new String(document.getElementById('DateNaissance').value);
	// on appelle la page
	texte = appelFormulairePHP(urlAjax+'&_type=dob&DateNaissance='+escape(document.getElementById('DateNaissance').value)+'&IdContact='+escape(IdContact));
	retablirContenuDob(maDiv, urlAjax, IdContact, wpurl, maDate);
	if (texte == 1)
	{
		// c'est ok on a reçu la date envoyée			
		afficherMessage(maDiv+"_error", "<i>Votre demande a &eacute;t&eacute; prise en compte</i>", "#093");
	}
	else 
	{
		if (texte == -1)
		{
			afficherMessage(maDiv+"_error", "Votre demande a &eacute;chou&eacute;", "#C33");
		}
		else
		{
			// on a une erreur de webservice
			afficherMessage(maDiv+"_error", "Votre demande a &eacute;chou&eacute;", "#C33");
			
		}
	}
}

function modifierLoc(maDiv, urlAjax, IdInvestissement, wpurl)
{	
	var texte = new String();
	var maDate = new String(document.getElementById('PremiereLoc').value);
	// on appelle la page
	texte = appelFormulairePHP(urlAjax+'&_type=loc&datePremiereLoc='+escape(document.getElementById('PremiereLoc').value)+'&IdInvestissement='+escape(IdInvestissement));
	retablirContenuLoc(maDiv, urlAjax, IdInvestissement, wpurl, maDate);
	if (texte == 1)
	{
		// c'est ok on a reçu la date envoyée			
		afficherMessage(maDiv+"_error", "<i>Votre demande a &eacute;t&eacute; prise en compte</i>", "#093");
	}
	else 
	{
		if (texte == -1)
		{
			afficherMessage(maDiv+"_error", "Votre demande a &eacute;chou&eacute;", "#C33");
		}
		else
		{
			// on a une erreur de webservice
			afficherMessage(maDiv+"_error", "Votre demande a &eacute;chou&eacute;", "#C33");
			
		}
	}
}

function modifierTel(maDiv, urlAjax, IdContactTelephone, wpurl, selectType)
{	
	var texte = new String();
	var Numero = new String(document.getElementById('Numero').value);
	var index = document.getElementById('IdTelephoneType').selectedIndex;
	var IdTelephoneType = new String(document.getElementById('IdTelephoneType').options[index].value );
	var isWhole_re       = /^\s*\d+\s*$/;
	var error=0;
	//alert(index+"\n"+IdTelephoneType+"\n"+Numero);
	if (index == 0)
	{
		error++;
		afficherMessage(maDiv+"_error", "Vous devez indiquer un type de t&eacute;l&eacute;phone", "#C33");		
	}
	
	if (Numero.search(isWhole_re) == -1)
	{
		error++;
		afficherMessage(maDiv+"_error", "Le t&eacute;l&eacute;phone doit être composé de chiffres exclusivement.", "#C33");	
	}
	else
	{
		if (Numero.length < 10)
		{
			error++;
			afficherMessage(maDiv+"_error", "Le t&eacute;l&eacute;phone doit comporter au moins 10 chiffres.", "#C33");	
		}
	}
	
	if (error == 0)
	{
		// on appelle la page
		texte = appelFormulairePHP(urlAjax+'&_type=tel&Numero='+escape(Numero)+'&IdTelephoneType='+escape(IdTelephoneType)+'&IdContactTelephone='+escape(IdContactTelephone));
		retablirContenuTel(maDiv, urlAjax, IdContactTelephone, wpurl, Numero, selectType);
		//retablirContenuTel(maDiv, FormPHP+'\', \''+IdContactTelephone+'\', \''+wpurl+'\', \''+value+'\',\''+selectType.replace(/'/gi,"\\'")+'\')
		if (texte == 1)
		{
			// c'est ok on a reçu la date envoyée			
			afficherMessage(maDiv+"_error", "<i>Votre demande a &eacute;t&eacute; prise en compte</i>", "#093");
		}
		else 
		{
			if (texte == -1)
			{
				afficherMessage(maDiv+"_error", "Votre demande a &eacute;chou&eacute;", "#C33");
			}
			else
			{
				// on a une erreur de webservice
				afficherMessage(maDiv+"_error", "Votre demande a &eacute;chou&eacute;", "#C33");
				
			}
		}
	}
}


function appelFormulairePHP(fichier)
{
     if(window.XMLHttpRequest) // FIREFOX
          xhr_object = new XMLHttpRequest();
     else if(window.ActiveXObject) // IE
          xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
     else
          return(false);
		  
     xhr_object.open("GET", fichier, false);
     xhr_object.send(null);
     if(xhr_object.readyState == 4) return (xhr_object.responseText);
     else return(false);
}

function PostFormulairePHP(fichier,data)
{
	var xhr_object = null; 
   if(window.XMLHttpRequest) // Firefox 
	  xhr_object = new XMLHttpRequest(); 
   else if(window.ActiveXObject) // Internet Explorer 
	  xhr_object = new ActiveXObject("Microsoft.XMLHTTP"); 
   else { // XMLHttpRequest non supporté par le navigateur 
	  alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
	  return; 
   } 
 
   xhr_object.open("POST", fichier, true);
	 
   xhr_object.onreadystatechange = function() { 
		  
	  if(xhr_object.readyState == 4) 
	  {			 
			document.getElementById("divMsg").innerHTML=xhr_object.responseText;
			setTimeout('retablirParticperAuDossier("reagir")',3000);
			return(true);
	  }
	  else
	  {
			document.getElementById("divMsg").innerHTML="Publication de votre action en cours...";  
	  }		  		 
   } 
 
   xhr_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   // --- ICI TU PASSE TES ARGUMENTS AU SCRIPT :	  
   xhr_object.send(data);	
}

function HTMLentities(texte) {

texte = texte.replace(/</g,'&lt;'); // 60 3C
texte = texte.replace(/>/g,'&gt;'); // 62 3E

return texte;
}

function valider_reaction_adh(formulaire,getEurekaValuePath)
{
	var leMessage = new String(HTMLentities(document.getElementById("message").value));
	var errors=0;
	
	if ( leMessage.replace(/ /g,"") == "") // je vire tous les espaces si la chaine est vide
	{
		errors++;
	}
	
	if (errors > 0)
	{
		alert("Veuillez indiquer un message avant de valider");	
		return(false);
	}
	else
	{
		// c'est bon donc on poste les données via Ajax	
		
		leMessage = leMessage.replace(/'/g, "''");
		//alert(leMessage.toString());
		var data = "message="+encodeURIComponent(leMessage.toString())+"&IdFiche="+encodeURIComponent(document.getElementById("IdFiche").value)+"&IdContact="+encodeURIComponent(document.getElementById("IdContact").value);
		var retour = PostFormulairePHP(formulaire,data);		
		document.getElementById("ListeActions").innerHTML=appelFormulairePHP(getEurekaValuePath+"&_type=actions&IdFiche="+document.getElementById("IdFiche").value);		
		return(false);			
	}
	
}

// fonction du formulaire de réaction
function reagir(maDiv, rep_icones, formulaire, IdFiche,IdContact, getEurekaValuePath)
{
	document.getElementById("contenuDiv").value = document.getElementById(maDiv).innerHTML;
	document.getElementById(maDiv).innerHTML ="<p><img style='margin:0px;' align='absmiddle' src='"+rep_icones+"/information.png'>&nbsp;Vous souhaitez nous faire part d'une avanc&eacute;e sur votre dossier ? vous avez un nouvel &eacute;l&eacute;ment ? Une remarque &agrave; nous transmettre ?<br />Remplissez le formulaire ci dessous puis cliquez sur le bouton \"Valider ma participation\" , votre action sera automatiquement prise en compte par nos services.</p> <form id='reaction' name='reaction' method='POST' onSubmit='return(valider_reaction_adh(\""+formulaire+"\",\""+getEurekaValuePath+"\"));'><div id='divMsg'><textarea style='width:90%;position:relative; top:0; left:0; right:0; bottom:0; padding:1em;' rows='5' name='message' id='message'></textarea><br><span align='right'><input type='submit' name='envoyer' style='font-size:14px; padding: 0 10px; color: #fff; background: #002C52; border: 0px; border-radius: 10px; height:27px; text-align:center; cursor: pointer;' value='Valider ma participation' ></span></div><input type='hidden' id='IdFiche' name='IdFiche' value='"+IdFiche+"'><input type='hidden' id='IdContact' name='IdContact' value='"+IdContact+"'></form>";
}

function retablirParticperAuDossier(maDiv)
{	
	document.getElementById(maDiv).innerHTML=document.getElementById("contenuDiv").value;	
}


//------------------------DEBUT------------------------------------
//------------ FUNCTION FORMULAIRE CHANGEMENT ADRESSE--------------
//-----------------------------------------------------------------

//Affiche infos différentes entre France ou Etranger selon le choix de l'adhérent
function ZoneGeoNouvelleAdresse(valeur)
{
	switch(valeur)
	{
		case "france":
			document.getElementById("ZoneGeoFrance").style.display = "inline";
			document.getElementById("ValidationForm").style.display = "inline"; // Affiche le bouton si le bouton radio France a été cochée pour la première fois
			document.getElementById("ZoneGeoEtranger").style.display = "none";
			document.getElementById("ValuePays").value="france";
			break;
		case "etranger":
			document.getElementById("ZoneGeoFrance").style.display = "none";
			document.getElementById("ValidationForm").style.display = "inline"; // Affiche le bouton si le bouton radio France a été cochée pour la première fois			
			document.getElementById("ZoneGeoEtranger").style.display = "inline";
			document.getElementById("ValuePays").value="etranger";
			break;
	}
}

function ChoixVilleManuelle()
{
	if (document.getElementById("CheckVilleManuelle").checked == true)
	{
		document.getElementById("tr_ville_inconnue").style.display = "table-row";
		document.getElementById("tr_ville").style.display= "none";
		document.getElementById("CheckVilleManuelle").value=1;
		//document.getElementById("CheckValueVille").value=1;
	}
	else
	{
		document.getElementById("tr_ville_inconnue").style.display="none";
		document.getElementById("tr_ville").style.display= "table-row";	
		document.getElementById("CheckVilleManuelle").value=0;
		//document.getElementById("CheckValueVille").value=0;
	}
}


function change_adresse()
{
	var erreur=0; // Variable pour compter le nombre d'erreur sur le formulaire de contact
	var message_erreur= "Veuillez renseigner :\n"; //Message d'erreur sur le formulaire de contact

	var zoneGeo = document.getElementById("zoneGeo").value;
	var Cmpl2=document.getElementById("Cmpl2").value;
	var CP=document.getElementById("CP").value;
	var IdVille=document.getElementById("ValueIdCommune").value;
	var IdPays=document.getElementById("ValueIdPays").value;
	var valuePays = document.getElementById("ValuePays").value;
	var ville=document.getElementById("VilleManuelle").value;
	var cp_lenght=CP.length;
	
	//SI CHOIX FRANCE
	if (valuePays == 'france')
	{
		if (Cmpl2 == "")
		{
			erreur++;
			message_erreur+="- Un libellé de voie\n";
		}
	
		if (CP == "")
		{
			erreur++;
			message_erreur+="- Un code postal\n";
		}
		else if(cp_lenght < 4 )
		{
			erreur++;
			message_erreur+="- Un code postal valide\n";
		}
		else
		{
			if (isNaN(CP))
			{
				erreur++;
				message_erreur+="- Un code postal valide\n";
			}
			else
			{
				//Je regarde l'état de la checkox pour voir quel est l'input de la ville que je dois surveiller (manuelle ou liste déroulante)
				if (document.getElementById("CheckVilleManuelle").checked == true)
				{
					//Si la case est cochée, on regarde qu'une ville a été saisie manuellement
					if (ville == "")
					{
						erreur++;
						message_erreur+="- Une ville\n";
					}
				}
				else
				{
					//Si la case est non  cochée, on regarde qu'une ville a été séléctionnée dans la liste déroulante
					if (IdVille == 0)
					{
						erreur++;
						message_erreur+="- Une ville\n";
					}
				}
			}
		}
	}
	else
	{
		document.getElementById("ValuePays").value="etranger";
		//SI CHOIX ETRANGER
		if (IdPays == 0)
		{
			erreur++;
			message_erreur+="- Un pays\n";
		}
		
		if (document.getElementById("AdresseEtranger").value == '')
		{
			erreur++;
			message_erreur+="- Une adresse\n";
		}
		
	}

	//Affichage des erreurs	du formulaire
	if (erreur > 0)
	{
		alert (message_erreur);
		return (false);
	}
	return (true);
}

//-------------------------FIN-------------------------------------
//------------ FUNCTION FORMULAIRE CHANGEMENT ADRESSE--------------
//-----------------------------------------------------------------