<?php


add_action('widgets_init', 'connexion_load_widgets');

function connexion_load_widgets()
{
  register_widget('connexion_Widget');
}

class connexion_Widget extends WP_Widget
{
  private $erreur_connection = false;
  private $firstConn = false;

  public function __construct()
  {
    parent::__construct(
      'connexion-widget', // Base ID
      esc_html__('EDC : connexion adhérent'), // Name
      array('description' => esc_html__('A Foo Widget', 'text_domain'), "classname" => "connexion") // Args
    );
  }


  public function connexion_Widget()
  {
    if (isset($_GET['deconnect'])) {
      $_SESSION['adherent_connected'] = false;
      $_SESSION['adherent_login'] = '';
      $_SESSION['adherent_pass'] = '';
      $_SESSION['adherent_infos'] = '';
      $_SESSION["is_connected"] = false;
      session_start();
      session_destroy();
      wp_redirect(get_home_url());
    }

    if (isset($_POST['log']) && isset($_POST['pwd']) && (trim($_POST['log']) != '' || trim($_POST['pwd']) != '')) {
      if (trim($_POST['log']) == '' || trim($_POST['pwd']) == '') {
        $_SESSION['adherent_connected'] = false;
        $_SESSION["is_connected"] = false;
        $this->erreur_connection = true;
      } else {
        require_once(dirname(__FILE__) . '/../lib/httpful.phar');

        // if ($_POST['pwd'] == 'yesyes31@MDP' || $_POST['pwd'] == 'part31@MDP' || $_POST['pwd'] == 'EDC2018' || $_POST['pwd'] == 'partEDC2018') {
        //   if ($_SERVER['REMOTE_ADDR'] != '78.208.113.35' && $_SERVER['REMOTE_ADDR'] != '176.162.183.218' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
        //     $_POST['pwd'] = 'PWDNOTALLOW';
        //   }
        // }

        if ($_POST['type_adhesion'] == 'partenaire') {
          $_POST['log'] = '|pa|' . trim($_POST['log']);
        }

        $url = 'http://localhost:3000/users';
        $data = array(
          "email" => trim($_POST['log']),
          "password" => trim($_POST['pwd']),
          "remote_addr" => $_SERVER['REMOTE_ADDR']

        );
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt(
          $ch,
          CURLOPT_HTTPHEADER,
          array(
            'Content-Type:application/json',
            'Content-Length: ' . strlen($data_string)
          )
        );
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = curl_exec($ch);
        curl_close($ch);
        /* $url = URL_WS_ADH . 'v1.0/json/user/infos'; */
        /* $response = \Httpful\Request::get($url)->authenticateWith(trim($_POST['log']), trim($_POST['pwd']))->send(); */


        if ($httpcode == 200) {
          $resp = json_decode($response);

          if ($resp->firstConnAfterRework === 1) {
            $this->firstConn = true;
            /* echo '<script text="text/javascript">'; */
            /* echo 'alert("Un email urgent vous à été envoyé pour pouvoir vous connecter");'; */
            /* echo 'window.location.href = "http://localhost/assoedc/adherent-connexion-2/";'; */
            /* echo '</script>'; */
            /* exit; */
          } else {

            $_SESSION['adherent_connected'] = true;
            $_SESSION['adherent_login'] = trim($_POST['log']);
            $_SESSION['adherent_pass'] = trim($_POST['pwd']);
            $_SESSION['adherent_infos'] = json_decode($result, true);

            // if ($_POST['pwd'] != 'yesyes31@MDP' && $_POST['pwd'] != 'EDC2018' && $_POST['pwd'] != 'part31@MDP' && $_POST['pwd'] != 'partEDC2018') {
            //   $url = URL_WS_ADH . 'v1.0/json/user/saveConnexion';
            //   $response = \Httpful\Request::get($url)->authenticateWith(trim($_POST['log']), trim($_POST['pwd']))->send();
            // }

            // if ($_POST['log'] == 'pascal@psalles.fr') {
            //   if (trim($_POST['pwd']) == $_SESSION['adherent_infos']->NumAdhesion) {
            //     wp_redirect('https://www.assoedc.com/changement-mot-passe/');
            //     exit;
            //   }
            // }

            // if ($_SESSION['adherent_infos']->IsPartenaire != 0) {
            //   wp_redirect('https://www.assoedc.com/partenaire-mes-clients/');
            //   exit;
            // }
          }
        } else {
          $_SESSION['adherent_connected'] = false;
          $_SESSION["is_connected"] = false;
          $this->erreur_connection = true;
        }
      }
    }
  }

  public function widget($args, $instance)
  {
    if (isset($_GET['deconnect'])) {
      $_SESSION['adherent_connected'] = false;
      $_SESSION['adherent_login'] = '';
      $_SESSION['adherent_pass'] = '';
      $_SESSION['adherent_infos'] = '';
      $_SESSION["is_connected"] = false;
      session_start();
      session_destroy();
      wp_redirect(get_home_url());
    }

    if (isset($_POST['log']) && isset($_POST['pwd']) && (trim($_POST['log']) != '' || trim($_POST['pwd']) != '')) {
      if (trim($_POST['log']) == '' || trim($_POST['pwd']) == '') {
        $_SESSION['adherent_connected'] = false;
        $_SESSION["is_connected"] = false;
        $this->erreur_connection = true;
      } else {
        require_once(dirname(__FILE__) . '/../lib/httpful.phar');

        // if ($_POST['pwd'] == 'yesyes31@MDP' || $_POST['pwd'] == 'part31@MDP' || $_POST['pwd'] == 'EDC2018' || $_POST['pwd'] == 'partEDC2018') {
        //   if ($_SERVER['REMOTE_ADDR'] != '78.208.113.35' && $_SERVER['REMOTE_ADDR'] != '176.162.183.218' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
        //     $_POST['pwd'] = 'PWDNOTALLOW';
        //   }
        // }

        if ($_POST['type_adhesion'] == 'partenaire') {
          $_POST['log'] = '|pa|' . trim($_POST['log']);
        }

        $url = 'http://localhost:3000/users';
        $data = array(
          "email" => trim($_POST['log']),
          "password" => trim($_POST['pwd']),
          "remote_addr" => $_SERVER['REMOTE_ADDR']
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

          $resp = json_decode($response);


          if ($resp->firstConnAfterRework === 1) {
            $this->firstConn = true;
            /* echo '<script text="text/javascript">'; */
            /* echo 'alert("Un email urgent vous à été envoyé pour pouvoir vous connecter");'; */
            /* echo 'window.location.href = "http://localhost/assoedc/adherent-connexion-2/";'; */
            /* echo '</script>'; */
            /* exit; */
          } else {

            $_SESSION['adherent_login'] = trim($_POST['log']);
            $_SESSION['adherent_connected'] = true;
            $_SESSION['adherent_pass'] = trim($_POST['pwd']);
            $_SESSION['adherent_infos'] = json_decode($response);


            // if ($_POST['pwd'] != 'yesyes31@MDP' && $_POST['pwd'] != 'EDC2018' && $_POST['pwd'] != 'part31@MDP' && $_POST['pwd'] != 'partEDC2018') {
            //   $url = URL_WS_ADH . 'v1.0/json/user/saveConnexion';
            //   $response = \Httpful\Request::get($url)->authenticateWith(trim($_POST['log']), trim($_POST['pwd']))->send();
            // }

            // if ($_POST['log'] == 'pascal@psalles.fr') {
            //     wp_redirect('https://www.assoedc.com/changement-mot-passe/');
            //     exit;
            //   }
            // }

            // if ($_SESSION['adherent_infos']->IsPartenaire != 0) {
            //   wp_redirect('https://www.assoedc.com/partenaire-mes-clients/');
            //   exit;
            // }
          }
        } else {
          $_SESSION['adherent_connected'] = false;
          $_SESSION["is_connected"] = false;
          $this->erreur_connection = true;
        }
      }
    }

    /* if (!isset($_SESSION["is_connected"])) { */
    /*   session_start(); */
    /*   $this->connexion_Widget(); */
    /* } */
    global $_CONFIG;
    extract($args);

    $title = apply_filters('widget_title', $instance['title']);

    //   if (trim($_POST['pwd']) == $_SESSION['adherent_infos']->NumAdhesion) {
    echo $before_widget;

    if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected']) {
      echo $before_title . esc_attr__("Bienvenue", 'edc_adherent') . $after_title;


?>
      <div class="adherent_nom"><?php echo $_SESSION['adherent_infos']->Civilite . ' ' . $_SESSION['adherent_infos']->Prenom . ' ' . $_SESSION['adherent_infos']->Nom; ?></div>
      <?php

      if ($_SESSION['adherent_infos']->IsPartenaire == 0) {
      ?>
        <div class="adherent_num">N&deg; <?php echo $_SESSION['adherent_infos']->NumAdhesion; ?></div>
        <?php
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
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //        $url = URL_WS_ADH . 'v1.0/json/user/getPaiementCotisation';
        //       $response = \Httpful\Request::get($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->send();
        if ($httpcode == 200) {
          $retval = json_decode($result);

          if (isset($retval->Montant) && $retval->Montant > 0 &&  $retval->IdCotisationAnnee != "") {
            echo '
								<ul class="menu" style="margin-top:20px; marfin-bottom:10px;">
									<li><a href="' . get_permalink(get_option('edc_adherent_id_page_paiement_cb')) . '?m=ws" style="background-color:#C00; color:#FFFFFF; padding:10px; display:inline-block;" title="Paiement en ligne de votre Cotisation">Paiement Cotisation</a>
									</li>
								</ul>';
          }
        }
        ?>

        <ul class="menu" style="margin-bottom:10px;">
          <li><a title="Se déconnecter" href="?deconnect=1" rel="nofollow">Se déconnecter</a></li>
        </ul>

      <?php
        $menu_adherent = array('theme_location' => 'adherent-menu', 'container' => '', 'menu_class' => 'menu_adherent', 'menu_id' => 'menu_adherent', 'fallback_cb' => false);
        wp_nav_menu($menu_adherent);
      } else { ?>

        <ul class="menu">
          <li><a title="Se déconnecter" href="?deconnect=1" rel="nofollow">Se déconnecter</a></li>
        </ul>

      <?php
        if ($_SERVER['REMOTE_ADDR'] != '78.208.113.35' && $_SERVER['REMOTE_ADDR'] != '176.162.183.218' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
          $menu_adherent = array('theme_location' => 'adherent-partenaire', 'container' => '', 'menu_class' => 'menu_adherent', 'menu_id' => 'menu_adherent', 'fallback_cb' => false);
        } else {
          $menu_adherent = array('theme_location' => 'adherent-partenaire-test', 'container' => '', 'menu_class' => 'menu_adherent', 'menu_id' => 'menu_adherent', 'fallback_cb' => false);
        }
        wp_nav_menu($menu_adherent);
      }
    } else {
      if ($title) {
        echo $before_title . esc_attr($title) . $after_title;
      } else {
        echo $before_title . esc_attr__("Connectez vous", 'edc_adherent') . $after_title;
      }
      ?>

      <form method="post">
        <?php if (isset($this->erreur_connection) && $this->erreur_connection) {
        ?><div class="erreur_login">
            <!-- <strong>ERREUR</strong> : l’identifiant ou le mot de passe n'est pas valide.<br /> -->
            <a title="Perte de mot de passe" href="<?php echo get_permalink(get_option('edc_adherent_id_page_mdp_lost')); ?>">Avez-vous perdu votre mot de passe ?</a>
          </div>
        <?php
        } ?>
        <?php if (isset($this->firstConn) && $this->firstConn) {
        ?><div class="erreur_login">
            <!-- <strong>ERREUR</strong> : l’identifiant ou le mot de passe n'est pas valide.<br /> -->
            <p title="Perte de mot de passe" href="">Cher Utilisateur, dans le cadre de notre politique de sécurité, vous êtes invités à renouveler votre mot de passe afin de pouvoir de nouveau accéder à votre compte. Un email vient de vous être envoyé.</p>
          </div>
        <?php
        }

        if ($_SERVER['REMOTE_ADDR'] == '78.208.113.35' || $_SERVER['REMOTE_ADDR'] == '176.162.183.218' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
        ?>


          <p><label for="user_login">Mon email :</label> <input name="log" value="" class="text" id="user_login" type="text" /></p>
          <p><label for="user_pass">Mot de passe :</label> <input name="pwd" class="text" id="user_pass" type="password" /></p>

          <p>
            <input type="radio" name="type_adhesion" value="adherent" id="type_adhesion_adherent" style="display:inline-block; float:none;" checked="checked" /><label style="display:inline-block; margin-right:20px;" for="type_adhesion_adherent">Adhérent</label>
            <input type="radio" name="type_adhesion" value="partenaire" id="type_adhesion_partenaire" style="display:inline-block; float:none;" /><label style="display:inline-block;" for="type_adhesion_partenairet">Partenaire</label>
          </p>

          <p class="submit">
            <input type="submit" name="wp-submit" id="wp-submit" value="Se connecter" />
          </p>


      </form>
      <div style="clear:both;"></div>
      <ul class="menu">
        <li><a title="Avez-vous perdu votre mot de passe ?" href="<?php echo get_permalink(get_option('edc_adherent_id_page_mdp_lost')); ?>" rel="nofollow">Mot de passe oublié ?</a></li>
        <li><a title="Créer mon compte Extranet" href="<?php echo get_permalink(get_option('edc_adherent_id_page_creer_compte')); ?>">Créer votre compte</a></li>
      </ul>

    <?php
        } else {
    ?>
      <p><label for="user_login">Mon email :</label> <input name="log" value="" class="text" id="user_login" type="text" /></p>
      <p><label for="user_pass">Mot de passe :</label> <input name="pwd" class="text" id="user_pass" type="password" /></p>


      <p class="submit">
        <input type="submit" name="wp-submit" id="wp-submit" value="Se connecter" />
      </p>


      </form>
      <div style="clear:both;"></div>
      <ul class="menu">
        <li><a title="Avez-vous perdu votre mot de passe ?" href="<?php echo get_permalink(get_option('edc_adherent_id_page_mdp_lost')); ?>" rel="nofollow">Mot de passe oublié ?</a></li>
        <li><a title="Créer mon compte Extranet" href="<?php echo get_permalink(get_option('edc_adherent_id_page_creer_compte')); ?>">Créer votre compte</a></li>
      </ul>
<?php
        }
      }
      echo $after_widget;
    }
  }
