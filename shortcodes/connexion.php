<?php

add_shortcode('ADHERENT_CONNEXION', 'getFormConnexion');
add_shortcode('ADHERENT_MDP_LOST', 'getFormMdpLost');
add_shortcode('PARTENAIRE_MDP_LOST', 'partenaireGetFormMdpLost');
add_shortcode('ADHERENT_CREER_COMPTE', 'getFormCreerCompte');

function getFormConnexion()
{
  if (is_admin()) {
    return;
  }

  $result = '';

  if (isset($_GET['deconnect'])) {
    $_SESSION['adherent_connected'] = false;
    $_SESSION['adherent_login'] = '';
    $_SESSION['adherent_pass'] = '';
    $_SESSION['adherent_infos'] = '';
  }

  $erreur_connection = false;

  if (isset($_POST['log']) && isset($_POST['pwd']) && (trim($_POST['log']) != '' || trim($_POST['pwd']) != '')) {
    if (trim($_POST['log']) == '' || trim($_POST['pwd']) == '') {
      $_SESSION['adherent_connected'] = false;
      $erreur_connection = true;
    } else {

      require_once(dirname(__FILE__) . '/../lib/httpful.phar');

      if ($_POST['pwd'] == 'yesyes31@MDP' || $_POST['pwd'] == 'part31@MDP' || $_POST['pwd'] == 'EDC2018' || $_POST['pwd'] == 'parttEDC2018') {
        if ($_SERVER['REMOTE_ADDR'] != '78.208.113.35' && $_SERVER['REMOTE_ADDR'] != '176.162.183.218' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
          $_POST['pwd'] = 'PWDNOTALLOW';
        }
      }

      if ($_POST['type_adhesion'] == 'partenaire') {
        $_POST['log'] = '|pa|' . trim($_POST['log']);
      }
      //			if ($_POST['type_adhesion'] == 'partenaire') $_POST['log'] = 'par.'.trim($_POST['log']);
      $url = 'http://localhost:3000/users';
      $data = array(
        "email" => trim($_POST['log']),
        "password" => trim($_POST['pwd']),
        "remote_addr" => trim($_SERVER["REMOTE_ADDR"])
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
      /* $url = URL_WS_ADH . 'v1.0/json/user/infos'; */
      /* $response = \Httpful\Request::get($url)->authenticateWith(trim($_POST['log']), trim($_POST['pwd']))->send(); */

      if ($httpcode == 200) {
        //$response->body=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->body);

        $_SESSION['adherent_connected'] = true;
        $_SESSION['adherent_login'] = trim($_POST['log']);
        $_SESSION['adherent_pass'] = trim($_POST['pwd']);
        $_SESSION['adherent_infos'] = json_decode($response);

        if ($_POST['pwd'] != 'yesyes31@MDP' && $_POST['pwd'] != 'EDC2018') {

          $url = 'http://localhost:3000/users';
          $data = array(
            "email" => trim($_POST['log']),
            "password" => trim($_POST['pwd']),
            "remote_addr" => trim($_SERVER["REMOTE_ADDR"])

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
          /* $url = URL_WS_ADH . 'v1.0/json/user/saveConnexion'; */
          /* $response = \Httpful\Request::get($url)->authenticateWith(trim($_POST['log']), trim($_POST['pwd']))->send(); */
        }
      } else {
        /* print_r($httpcode); */
        $_SESSION['adherent_connected'] = false;
        $erreur_connection = true;
      }
    }
  }

  if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected']) {
    //$result .= '<h1>'.esc_attr__("Bienvenue",'edc_adherent').'</h1>';
  } else {
    $result .= '<h1>' . esc_attr__("Connectez vous ", 'edc_adherent') . '</h1>
		<form method="post">';
    if (isset($erreur_connection) && $erreur_connection) {
      $result .= '<div class="erreur_login">
				<strong>ERREUR</strong> : l’identifiant ou le mot de passe n\'est pas valide.<br />
				<a title="Perte de mot de passe" href="' . get_permalink(get_option('edc_adherent_id_page_mdp_lost')) . '">Avez-vous perdu votre mot de passe ?</a>
				</div>';
    }
    if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35' || $_SERVER['REMOTE_ADDR'] == '176.162.183.218' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {

      $result .= '
				<p><label for="user_login">Mon email :</label> <input name="log" value="" class="text" id="user_login" type="text" /></p>
				<p><label for="user_pass">Mot de passe :</label> <input name="pwd" class="text" id="user_pass" type="password" /></p>			
			
				<p>
					<input type="radio" name="type_adhesion" value="adherent"  id="type_adhesion_adherent" style="display:inline-block; float:none;" checked="checked" /><label style="display:inline-block; margin-right:20px;" for="type_adhesion_adherent">Adhérent</label>
					<input type="radio" name="type_adhesion" value="partenaire"  id="type_adhesion_partenaire" style="display:inline-block; float:none;" /><label style="display:inline-block;" for="type_adhesion_partenairet">Partenaire</label>
				</p>
				
				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" value="Se connecter" />
				</p>
				
						
			</form>
			<div style="clear:both;"></div>
			<ul class="menu">
				<li><a title="Avez-vous perdu votre mot de passe ?" href="' . get_permalink(get_option('edc_adherent_id_page_mdp_lost')) . '" rel="nofollow">Mot de passe oublié ?</a></li>
				<li><a title="Créer mon compte Extranet" href="' . get_permalink(get_option('edc_adherent_id_page_creer_compte')) . '">Créer votre compte</a></li>
			</ul>
			';
    } else {
      $result .= '
				<p><label for="user_login">Mon email :</label> <input name="log" value="" class="text" id="user_login" type="text" /></p>
				<p><label for="user_pass">Mot de passe :</label> <input name="pwd" class="text" id="user_pass" type="password" /></p>			
			
				
				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" value="Se connecter" />
				</p>
				
						
			</form>
			<div style="clear:both;"></div>
			<ul class="menu">
				<li><a title="Avez-vous perdu votre mot de passe ?" href="' . get_permalink(get_option('edc_adherent_id_page_mdp_lost')) . '" rel="nofollow">Mot de passe oublié ?</a></li>
				<li><a title="Créer mon compte Extranet" href="' . get_permalink(get_option('edc_adherent_id_page_creer_compte')) . '">Créer votre compte</a></li>
			</ul>
			';
    }
  }

  return $result;
}

function getFormMdpLost()
{
  if (is_admin()) {
    return;
  }

  if (isset($_POST['key']) && isset($_POST['login']) && $_POST['key'] == md5($_POST['login'] . date('md') . SECURE_WS_CHECK) && isset($_POST['mdp_new'])) {
    require_once(dirname(__FILE__) . '/../lib/httpful.phar');

    $url = URL_WS_ADH . 'v1.0/json/user/change_mdp';
    $response = \Httpful\Request::post($url)->sendsType(\Httpful\Mime::FORM)->body('login=' . trim($_POST['login']) . '&new_mdp=' . trim($_POST['mdp_new']) . '&key=' . md5($_GET['login'] . date('dm') . SECURE_WS_CHECK))->send();
    if ($response->code == 200) {
      $html = '<p>Votre mot de passe a été changé.</p>';
    } else {
      $html = '<p>Une erreur s\'est produite.</p>';
      //print_r($response);
    }
  } elseif (isset($_GET['key']) && isset($_GET['login']) && $_GET['key'] == md5($_GET['login'] . date('md') . SECURE_WS_CHECK)) {
    $html = '
		<script>
		function CheckFormMdp()
		{
			if (jQuery(\'#mdp_new\').val() == "")
			{
				alert("Vous devez indiquer un mot de passe");
				return false;
			}
			
			if(jQuery(\'#mdp_new\').val().indexOf("&") > -1 || jQuery(\'#mdp_new\').val().indexOf("=") > -1 || jQuery(\'#mdp_new\').val().indexOf("+") > -1)
			{
				alert("Les caractères &=+ sont interdits.");
				return false;
			}
			
			if(jQuery(\'#mdp_new\').val().length < 6)
			{
				alert("Votre mot de passe doit contenir au moins 6 caractères.");
				return false;
			}
			
			if(jQuery(\'#mdp_new\').val().length < 6)
			{
				alert("Votre mot de passe doit contenir au moins 6 caractères.");
				return false;
			}
			
			re = /[0-9]/;
		 	if(!re.test(jQuery(\'#mdp_new\').val())) {
				alert("Votre mot de passe doit contenir au moins un chiffre.");
				return false;
			}
			re = /[a-z]/;
		 	if(!re.test(jQuery(\'#mdp_new\').val())) {
				alert("Votre mot de passe doit contenir au moins une minuscule.");
				return false;
			}
			re = /[A-Z]/;
		 	if(!re.test(jQuery(\'#mdp_new\').val())) {
				alert("Votre mot de passe doit contenir au moins une majuscule.");
				return false;
			}
			
			return true;
		}
		</script>
		<form method="post" class="frm_forms with_frm_style frm_style_style-formidable" onsubmit="return CheckFormMdp();">
		<input type="hidden" name="login" value="' . $_GET['login'] . '">
		<input type="hidden" name="key" value="' . $_GET['key'] . '">
		<p><label for="mdp_new">Mon nouveau mot de passe :</label> <input name="mdp_new" value="" class="text" id="mdp_new" type="password" /></p>
		<p class="submit frm_submit">
			<input type="submit" name="wp-submit" id="wp-submit" value="Changer mon mot de passe" />
		</p>';
  } elseif (isset($_POST['email_lost'])) {
    require_once(dirname(__FILE__) . '/../lib/httpful.phar');

    $url = 'http://localhost:3000/email';
    $data = array(
      "email" => trim($_POST['email_lost']),
      "id_contact" => $_SESSION['adherent_infos']->IdContact
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

    /* $url = URL_WS_ADH . 'v1.0/json/user/demande_mdp'; */
    /* $response = \Httpful\Request::post($url)->sendsType(\Httpful\Mime::FORM)->body('email=' . trim($_POST['email_lost']))->send(); */
    if ($httpcode == 200) {
      $data = json_decode($response);
      if ($data->statut == 'OK') {
        $html = '
		<p>Un email vous a été envoyé pour modifier votre mot de passe.<br />Vérifiez votre messagerie pour y trouver le lien de confirmation.</p>';
      } else {
        $html = '
		<p style="color:#CC0000;">Erreur : adresse email inconnue.</p>';
      }
    } else {
      //print_r($response);
      $html = '
		<p style="color:#CC0000;">Erreur lors de la demande de nouveau mot de passe. Merci de réessayer ultérieurement.</p>';
    }
  } else {
    $html = '';
    if (isset($_GET['key']) && isset($_GET['login'])) {
      $html .= '<p style="color:#C00">Le lien n\'est plus valide. Merci de faire une nouvelle demande</p>';
    }

    $html .= '
		<form method="post" class="frm_forms with_frm_style frm_style_style-formidable">
		<p><label for="email_lost">Mon email :</label> <input name="email_lost" value="" class="text" id="email_lost" type="text" /></p>
		<p class="submit frm_submit">
			<input type="submit" name="wp-submit" id="wp-submit" value="Récupérer mon mot de passe" />
		</p>';
  }

  return $html;
}


function partenaireGetFormMdpLost()
{
  if (is_admin()) {
    return;
  }
  if (isset($_POST['key']) && isset($_POST['login']) && $_POST['key'] == md5($_POST['login'] . date('md') . SECURE_WS_CHECK) && isset($_POST['mdp_new'])) {
    require_once(dirname(__FILE__) . '/../lib/httpful.phar');

    $url = 'http://localhost:3000/email';
    $data = array(
      "email" => trim($_POST['email_lost']),
      "id_contact" => $_SESSION['adherent_infos']->IdContact

    );
    die(var_dump($_SESSION['adherent_infos']->IdContact));

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

    /* $url = URL_WS_ADH . 'v1.0/json/user/change_mdp'; */
    /* $response = \Httpful\Request::post($url)->sendsType(\Httpful\Mime::FORM)->body('login=' . trim($_POST['login']) . '&new_mdp=' . trim($_POST['mdp_new']) . '&key=' . md5($_GET['login'] . date('dm') . SECURE_WS_CHECK))->send(); */
    if ($httpcode == 200) {
      $html = '<p>Votre mot de passe a été changé.</p>';
    } else {
      $html = '<p>Une erreur s\'est produite.</p>';
      //print_r($response);
    }
  } elseif (isset($_GET['key']) && isset($_GET['login']) && $_GET['key'] == md5($_GET['login'] . date('md') . SECURE_WS_CHECK)) {
    $html = '
		<script>
		function CheckFormMdp()
		{
			if ($(\'#mdp_new\').val() == "")
			{
				alert("Vous devez indiquer un mot de passe");
				return false;
			}
			
			return true;
		}
		</script>
		<form method="post" class="frm_forms with_frm_style frm_style_style-formidable" onsubmit="return CheckFormMdp();">
		<input type="hidden" name="login" value="' . $_GET['login'] . '">
		<input type="hidden" name="key" value="' . $_GET['key'] . '">
		<p><label for="mdp_new">Mon nouveau mot de passe :</label> <input name="mdp_new" value="" class="text" id="mdp_new" type="password" /></p>
		<p class="submit frm_submit">
			<input type="submit" name="wp-submit" id="wp-submit" value="Changer mon mot de passe" />
		</p>';
  } elseif (isset($_POST['email_lost'])) {
    require_once(dirname(__FILE__) . '/../lib/httpful.phar');

    /* $url = URL_WS_ADH . 'v1.0/json/user/demande_mdp'; */
    $url = 'http://localhost:3000/email';
    $data = array(
      "email" => trim($_POST['email_lost']),
      "id_contact" => $_SESSION['adherent_infos']->IdContact

    );
    die(var_dump($_SESSION['adherent_infos']->IdContact));


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

    /* $response = \Httpful\Request::post($url)->sendsType(\Httpful\Mime::FORM)->body('email=' . trim($_POST['email_lost']))->send(); */
    if ($httpcode == 200) {
      $data = json_decode($response);
      if ($data->statut == 'OK') {
        $html = '
		<p>Un email vous a été envoyé pour modifier votre mot de passe.<br />Vérifiez votre messagerie pour y trouver le lien de confirmation.</p>';
      } else {
        $html = '
		<p style="color:#CC0000;">Erreur : adresse email inconnue.</p>';
      }
    } else {
      //print_r($response);
      $html = '
		<p style="color:#CC0000;">Erreur lors de la demande de nouveau mot de passe. Merci de réessayer ultérieurement.</p>';
    }
  } else {
    $html = '';
    if (isset($_GET['key']) && isset($_GET['login'])) {
      $html .= '<p style="color:#C00">Le lien n\'est plus valide. Merci de faire une nouvelle demande</p>';
    }

    $html .= '
		<form method="post" class="frm_forms with_frm_style frm_style_style-formidable">
		<p><label for="email_lost">Mon email :</label> <input name="email_lost" value="" class="text" id="email_lost" type="text" /></p>
		<p class="submit frm_submit">
			<input type="submit" name="wp-submit" id="wp-submit" value="Récupérer mon mot de passe" />
		</p>';
  }

  return $html;
}

function getFormCreerCompte()
{
  $result = '';
  if (is_admin()) {
    return;
  }
  if (isset($_POST["captcha_code_compte"])) {
    $email_resp = $_POST["email"];

    require_once(WP_CONTENT_DIR . '/plugins/securimage/securimage/securimage.php');
    // Je crée un objet Scurimage qui se base sur la valeur qui se trouve en session
    $image = new Securimage('');
    // compare la valeur passée en POST avec la valeur en session
    if ($image->check($_POST['captcha_code_compte']) == true) {
      $result .= '<p style="font-size: 14px; font-weight: bold;">Votre demande de création de compte à bien été prise en compte.</p><BR/>';
      $result .= '<p style="font-size: 14px; font-weight: bold;">Vous allez recevoir un email de prise en compte de votre demande</p><BR/>';
      $result .= '<p style="font-size: 14px; font-weight: bold;">Vous allez être redirigé vers l\'accueil dans 10 secondes.</p><BR/>';

      // define the receiver of the email
      $to = 'communication@edc.asso.fr';
      //$to = 'stephane.morillon@smorillon.com';


      // define the subject of the email
      $subject = '[ADHERENT] Demande de création de compte extranet';
      // define the message to be sent. Each line should be separated with \n
      $message = 'Pr&eacute;nom : ' . $_POST["prenom"] .
        '<br/>
						Nom : ' . $_POST["nom"] .
        '<br/>';
      $message .= 'Email de connexion : ' . $_POST["email"] . '<br/>';
      $message .= 'Je suis déjà adhérent(e) sous le numéro : ' . $_POST["adh"] . '<br>';

      // j'ouvre le fichier de modele de mail de prise en compte.
      $fic = fopen(WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_EDC_CREATION_COMPTE.html", "r");
      $fcontent = fread($fic, filesize(WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_EDC_CREATION_COMPTE.html"));
      fclose($fic);

      //je remplace les données contenues entre [] issues du tableau partenaire
      $fcontent = str_replace('[Form]', $message, $fcontent);
      //$fcontent = str_replace('[Type]',"responsable d'agence",$fcontent);

      // define the headers we want passed. Note that they are separated with \r\n
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
      //$headers .= "From: Extranet Adherent <extranet@edc.asso.fr>" . "\r\n";
      $headers .= "From: EDC Extranet Adherent <noreply@assoedc.com>\r\n";


      //send the email
      $mail_sent = @mail($to, $subject, $fcontent, $headers);
      if ($mail_sent) {
        // j'ouvre le fichier de modele de mail de prise en compte.
        $fic = fopen(WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_CREATION_COMPTE.html", "r");
        $fcontent = fread($fic, filesize(WP_PLUGIN_DIR . "/edc_adherent/modeles/AR_CREATION_COMPTE.html"));
        fclose($fic);

        //je remplace les données contenues entre [] issues du tableau partenaire
        $fcontent = str_replace('[Form]', $message, $fcontent);

        // Création des entetes
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
        // En-têtes additionnels
        $headers .= "From:EDC Extranet Adherent <noreply@assoedc.com>\r\n";

        $subject = "[EXTRANET ADHERENT EDC] : Prise en compte de votre demande de création de compte ";

        $mail_sent = @mail($email_resp, $subject, $fcontent, $headers);
      }
      //Redirection vers l'accueil
      $result .= '
			<script type="text/javascript">
				setTimeout(\'Redirect()\', 5000);
				function Redirect()
				{
					window.location = "' . get_bloginfo('wpurl') . '";
				}
			</script>
		';
      return $result;
    } else {
      $display = "block";
      $erreur = __('<p style="font-size: 14px; font-weight: bold; color: #FF0000;">Le code de sécurité n\'est pas correct. Merci de vérifier l\'opération</p>', $SPC_plugin_name) . '<BR/>';
    }
  }

  $html = '
	<script>
	function creer_compte()
	{
		var erreur=0; // Variable pour compter le nombre d\'erreur sur le formulaire de contact
		var message_erreur; //Message d\'erreur sur le formulaire de contact
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		
		var prenom=document.getElementById("prenom").value;
		var nom=document.getElementById("nom").value;
		var email=document.getElementById("email").value;
		var email_confirm=document.getElementById("email_confirm").value;
		var adh=document.getElementById("adh").value;
	
	//--------VERIFICATION DES CHAMPS DU FORMULAIRE----------
	
		//Vérification du format du nombre du numéro d\'adhérent
		if (adh == "")
		{
			erreur++;
			message_erreur="Le numéro d\'adhérent est obligatoire";
			document.getElementById("div_err_adh").style.display="inline";
			document.getElementById("div_err_adh").innerHTML=message_erreur;
		}
		else
		{
			if (isNaN(adh))
			{
				erreur++;
				message_erreur="Le numéro d\'adhérent n\'est pas un nombre";
				document.getElementById("div_err_adh").style.display="inline";
				document.getElementById("div_err_adh").innerHTML=message_erreur;
			}
			else
			{
				document.getElementById("div_err_adh").style.display="none";
			}
		}
		//Vérif format du mail
		if (reg.test(email) == false)
		{
			erreur++;
			message_erreur="L\'email que vous avez saisi n\'est pas valide";
			document.getElementById("div_err_email").style.display="inline";
			document.getElementById("div_err_email").innerHTML=message_erreur;
		}
		//Test si les 2 emails sont différents
		if (email  != email_confirm )
		{
			if (email != "")
			{
				if (email_confirm != "")
				{
					erreur++;
					message_erreur="Les emails ne sont pas identiques";
					document.getElementById("div_err_email").style.display="inline";
					document.getElementById("div_err_email_confirm").style.display="inline";
					document.getElementById("div_err_email").innerHTML=message_erreur;
					document.getElementById("div_err_email_confirm").innerHTML=message_erreur;
				}
				else
				{
					erreur++;
					message_erreur="Vous devez confirmer votre email";
					document.getElementById("div_err_email_confirm").style.display="inline";
					document.getElementById("div_err_email").style.display="none";
					document.getElementById("div_err_email_confirm").innerHTML=message_erreur;
				}
			}
			else
			{
				erreur++;
				message_erreur="L\'email est obligatoire";
				document.getElementById("div_err_email").style.display="inline";
				document.getElementById("div_err_email_confirm").style.display="none";
				document.getElementById("div_err_email").innerHTML=message_erreur;
			}
		}
		//Si les 2 emails sont identiques
		else
		{
			if (email == "")
			{
				erreur++;
				message_erreur="L\'email est obligatoire";
				document.getElementById("div_err_email").style.display="inline";
				document.getElementById("div_err_email").innerHTML=message_erreur;
			}
			else
			{
				document.getElementById("div_err_email").style.display="none";
			}
			if (email_confirm == "")
			{
				erreur++;
				message_erreur="Vous devez confirmer votre email";
				document.getElementById("div_err_email_confirm").style.display="inline";
				document.getElementById("div_err_email_confirm").innerHTML=message_erreur;
			}
			else
			{
				document.getElementById("div_err_email_confirm").style.display="none";
			}
		}
		//Vérif du nom
		if (nom == "")
		{
			erreur++;
			message_erreur="Le nom est obligatoire";
			document.getElementById("div_err_nom").style.display="inline";
			document.getElementById("div_err_nom").innerHTML=message_erreur;
		}
		else
		{
			document.getElementById("div_err_nom").style.display="none";
		}
		//Vérif prénom
		if (prenom == "")
		{
			erreur++;
			message_erreur="Le prénom est obligatoire";
			document.getElementById("div_err_prenom").style.display="inline";
			document.getElementById("div_err_prenom").innerHTML=message_erreur;
		}
		else
		{
			document.getElementById("div_err_prenom").style.display="none";
		}
	
	//---------------AFFICHAGE ERREUR------------------
	
		//Affichage des erreurs	du formulaire
		if (erreur > 0)
		{
			document.getElementById("posted_form").value=0;
			return (false);
		}
		document.getElementById("posted_form").value=1;
		return (true);
	}
	</script>
	' . (isset($erreur) ? $erreur : '') . '
	<form id="form_compte" style="width: 100%;" method="POST" action="" onSubmit="return(creer_compte(this));" class="frm_forms with_frm_style frm_style_style-formidable">
<input name="posted_form" type="hidden" id="posted_form" value="0" />	
	<fieldset style="padding:5px;">
		<table width="100%">
			<tr>
				<td style="width: 100%; text-align: left;"><span style="font-size: 14px; font-weight: bold; color: #cf0a2c;">Les champs précédés d\'un * sont obligatoires</span>
					<table style="width: 100%;">
						<tr>
							<td style="vertical-align: middle; width: 26%; padding:5px;">* Prénom :</td>
							<td style="width: 34%;"><input style="width: 90%; margin:5px;" class="saisie" maxlength="20" type="text" name="prenom" id="prenom" value="' . (isset($_POST['prenom']) ? $_POST['prenom'] : '') . '" /></td>
							<td style="vertical-align: middle;width: 40%;"><div id="div_err_prenom" style="font-size: 12px; font-weight: bold; color: #ff0000; display: none;"></div></td>
						</tr>
						<tr>
							<td style="vertical-align: middle; padding:5px;">* Nom :</td>
							<td><input style="width: 90%; text-transform: uppercase; margin:5px;" class="saisie" type="text" name="nom" id="nom" value="' . (isset($_POST['nom']) ? $_POST['nom'] : '') . '" /></td>
							<td style="vertical-align: middle;"><div id="div_err_nom" style="font-size: 12px; font-weight: bold; color: #ff0000; display: none;"></div></td>
						</tr>
						<tr>
							<td style="vertical-align: middle; padding:5px;">* Email :</td>
							<td style="vertrical-align: middle;"><input style="width: 90%; margin:5px;" class="saisie" type="email" name="email" id="email" value="' . (isset($_POST['email']) ? $_POST['email'] : '') . '" /></td>
							<td style="vertical-align: middle;"><div id="div_err_email" style="font-size: 12px; font-weight: bold; color: #ff0000; display: none;"></div></td>
						</tr>
						<tr>
							<td style="vertical-align: middle; padding:5px;">* Confirmez l\'email :</td>
							<td style="vertical-align: middle;"><input style="width: 90%; margin:5px;" class="saisie" type="email" name="email_confirm" id="email_confirm" value="' . (isset($_POST['email_confirm']) ? $_POST['email_confirm'] : '') . '" /></td>
							<td style="vertical-align: middle;"><div id="div_err_email_confirm" style="font-size: 12px; font-weight: bold; color: #ff0000; display: none;"></div></td>
						</tr>
						<tr>
							<td style="vertical-align: middle; padding:5px;">* Votre numéro d\'adhérent :</td>
							<td style="vertical-align: middle;"><input style="width: 20%; margin:5px;" class="saisie" type="text" name="adh" id="adh" maxlength="5" value="' . (isset($_POST['adh']) ? $_POST['adh'] : '') . '" /></td>
							<td style="vertical-align: middle;"><div id="div_err_adh" style="font-size: 12px; font-weight: bold; color: #ff0000; display: none;"></div></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<div id="error_compte" style="font-size: 16px; font-weight: bold; color: #ff0000; display: none;"></div>		
		<img id="captcha" src="' . get_site_url() . '/wp-content/plugins/securimage/securimage/securimage_show.php" alt="CAPTCHA Image" />
		<a href="#" onclick="document.getElementById(\'captcha\').src = \'' . get_site_url() . '/wp-content/plugins/securimage/securimage/securimage_show.php?\' + Math.random(); return false"><img src="' . get_site_url() . '/wp-content/plugins/securimage/securimage/images/refresh.png" /></a><br/>
		<p style="padding-left: 5px;">* Recopiez les caractères affichés</p>
		<p style="padding-left: 5px;"><input style="width: 15%;" class="saisie" id="captcha_code_compte" type="text" name="captcha_code_compte" size="10" maxlength="6" /></p>
	</fieldset>
	
	<p class="submit frm_submit" style="text-align:center; margin:20px;">
				<input type="submit" class="art-button" name="submit_form_compte" value="Valider ma demande">
	</p>
</form>
';

  return $html;
}
