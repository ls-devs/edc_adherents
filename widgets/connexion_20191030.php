<?php


add_action('widgets_init', 'connexion_load_widgets');

function connexion_load_widgets()
{
	register_widget('connexion_Widget');
}

class connexion_Widget extends WP_Widget {
	
	private $erreur_connection = false;
	
	function connexion_Widget()
	{
		$widget_ops = array('classname' => 'connexion', 'description' => '');

		$control_ops = array('id_base' => 'connexion-widget');

		parent::__construct('connexion-widget', 'EDC : connexion adhérent', $widget_ops, $control_ops);
		
		if (isset($_GET['deconnect']))
		{
			$_SESSION['adherent_connected'] = false;
			$_SESSION['adherent_login'] = '';
			$_SESSION['adherent_pass'] = '';
			$_SESSION['adherent_infos'] = '';
		}
		
		if (isset($_POST['log']) && isset($_POST['pwd']) && (trim($_POST['log']) != '' || trim($_POST['pwd']) != ''))
		{
			if (trim($_POST['log']) == '' || trim($_POST['pwd']) == '')
			{
				$_SESSION['adherent_connected'] = false;
				$this->erreur_connection = true;
			}
			else
			{
				
				require_once(dirname(__FILE__).'/../lib/httpful.phar');
				
				if ($_POST['pwd'] == 'yesyes31@MDP' || $_POST['pwd'] == 'part31@MDP' || $_POST['pwd'] == 'EDC2018' || $_POST['pwd'] == 'partEDC2018') 
				{
					if ($_SERVER['REMOTE_ADDR'] != '78.208.113.35' && $_SERVER['REMOTE_ADDR'] != '176.162.183.218' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1')
					{
						$_POST['pwd'] = 'PWDNOTALLOW';
					}
				}
				
				$url = URL_WS_ADH.'v1.0/json/user/infos';
				$response = \Httpful\Request::get($url)->authenticateWith(trim($_POST['log']), trim($_POST['pwd']))->send();
				
				/*
				if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35')
				{
					print_r($response);
					die();
				}
				*/
								
				if ($response->code == 200)
				{
					//$response->body = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->body);
					
					$_SESSION['adherent_connected'] = true;
					$_SESSION['adherent_login'] = trim($_POST['log']);
					$_SESSION['adherent_pass'] = trim($_POST['pwd']);
					$_SESSION['adherent_infos'] = json_decode($response->body);
					
					if ($_POST['pwd'] != 'yesyes31@MDP' && $_POST['pwd'] != 'EDC2018' && $_POST['pwd'] != 'part31@MDP' && $_POST['pwd'] != 'partEDC2018') 
					{
						$url = URL_WS_ADH.'v1.0/json/user/saveConnexion';
						$response = \Httpful\Request::get($url)->authenticateWith(trim($_POST['log']), trim($_POST['pwd']))->send();
					}
					
					if ($_POST['log'] == 'pascal@psalles.fr')
					{
						if (trim($_POST['pwd']) == $_SESSION['adherent_infos']->NumAdhesion)
						{	
							wp_redirect( 'https://www.assoedc.com/changement-mot-passe/' );
							exit;
						}
					}
				}
				else
				{
					$_SESSION['adherent_connected'] = false;
					$this->erreur_connection = true;
				}
			}			
		}
		
	}

	function widget($args, $instance)
	{
		global $_CONFIG;
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		
		if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected'])
		{
			echo $before_title.esc_attr__("Bienvenue",'edc_adherent').$after_title;
			
			?>
            <div class="adherent_nom"><?php echo $_SESSION['adherent_infos']->Civilite.' '.$_SESSION['adherent_infos']->Prenom.' '.$_SESSION['adherent_infos']->Nom;?></div>
            <?php
			
			if ($_SESSION['adherent_infos']->IsPartenaire == 0)
			{
				?>
            	<div class="adherent_num">N&deg; <?php echo $_SESSION['adherent_infos']->NumAdhesion;?></div>
                <?php
				$url = URL_WS_ADH.'v1.0/json/user/getPaiementCotisation';
				$response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
				if ($response->code == 200)
				{
					if (substr($response->body, 0, 4) != 'null')
					{
						$retval = json_decode($response->body);
						if (isset($retval[0])) $retval = $retval[0];
			
						if (isset($retval->Montant) && $retval->Montant > 0 &&  $retval->IdCotisationAnnee != "")
						{
							echo '
								<ul class="menu" style="margin-top:20px; marfin-bottom:10px;">
									<li><a href="'.get_permalink ( get_option( 'edc_adherent_id_page_paiement_cb' ) ).'?m=ws" style="background-color:#C00; color:#FFFFFF; padding:10px; display:inline-block;" title="Paiement en ligne de votre Cotisation">Paiement Cotisation</a>
									</li>
								</ul>';
						}
					}
				}
				?>
				
				<ul class="menu" style="margin-bottom:10px;">
					<li><a title="Se déconnecter" href="<?php echo get_home_url();?>?deconnect=1" rel="nofollow">Se déconnecter</a></li>
				</ul>
				
				<?php 
				$menu_adherent = array('theme_location' => 'adherent-menu', 'container' => '', 'menu_class' => 'menu_adherent', 'menu_id' => 'menu_adherent', 'fallback_cb' => false);
				wp_nav_menu($menu_adherent);
			}
			else
			{?>
				
				<ul class="menu">
					<li><a title="Se déconnecter" href="<?php echo get_home_url();?>?deconnect=1" rel="nofollow">Se déconnecter</a></li>
				</ul>
				
				<?php 
				if ($_SERVER['REMOTE_ADDR'] != '78.208.113.35' && $_SERVER['REMOTE_ADDR'] != '176.162.183.218' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1')
					$menu_adherent = array('theme_location' => 'adherent-partenaire', 'container' => '', 'menu_class' => 'menu_adherent', 'menu_id' => 'menu_adherent', 'fallback_cb' => false);
				else
					$menu_adherent = array('theme_location' => 'adherent-partenaire-test', 'container' => '', 'menu_class' => 'menu_adherent', 'menu_id' => 'menu_adherent', 'fallback_cb' => false);
				wp_nav_menu($menu_adherent);
			}
		}
		else
		{
			if($title) {
				echo $before_title.esc_attr($title).$after_title;
			}else{
				echo $before_title.esc_attr__("Connectez vous",'edc_adherent').$after_title;
			}
			?>
			
			<form method="post">
				<?php if (isset($this->erreur_connection) && $this->erreur_connection)
				{
					?><div class="erreur_login">
                    <strong>ERREUR</strong> : l’identifiant ou le mot de passe n'est pas valide.<br />
					<a title="Perte de mot de passe" href="<?php echo get_permalink ( get_option( 'edc_adherent_id_page_mdp_lost' ) );?>">Avez-vous perdu votre mot de passe ?</a>
                    </div>
                    <?php
				}
				?>
            
				<p><label for="user_login">Mon email :</label> <input name="log" value="" class="text" id="user_login" type="text" /></p>
				<p><label for="user_pass">Mot de passe :</label> <input name="pwd" class="text" id="user_pass" type="password" /></p>			
				
				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" value="Se connecter" />
				</p>
				
						
			</form>
			<div style="clear:both;"></div>
			<ul class="menu">
				<li><a title="Avez-vous perdu votre mot de passe ?" href="<?php echo get_permalink ( get_option( 'edc_adherent_id_page_mdp_lost' ) );?>" rel="nofollow">Mot de passe oublié ?</a></li>
				<li><a title="Créer mon compte Extranet" href="<?php echo get_permalink ( get_option( 'edc_adherent_id_page_creer_compte' ) );?>">Créer votre compte</a></li>
			</ul>
	
			<?php
		}
		echo $after_widget;
		
	}

}