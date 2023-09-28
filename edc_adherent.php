<?php
/*
 * Plugin Name:       EDC Adhérents
 * Description:       Espace adhérents
 * Version:           0.1
 * Author:            SMorillon
 * Author URI:        http://www.smorillon.com
 */

ob_start();

require_once(dirname(__FILE__) . '/conf/config.php');


require_once(dirname(__FILE__) . '/custom-post-types/includes.php');
// require_once(dirname(__FILE__).'/php_to_pdf/vendor/autoload.php');

$createPageInstall = false;

define('EDC_PLUGIN_PATH', $dir = plugin_dir_path(__FILE__));

function get_version()
{
  return "0.1";
}


function the_content_adherent_privee($content)
{
  if (!isset($_SESSION['adherent_connected']) || !$_SESSION['adherent_connected']) {
    $content_temp = explode('[PRIVEE_ADH]', $content);

    if (count($content_temp) > 1) {
      $content = $content_temp[0];

      $content .= '
			 <div class="reserved_adherent">
				<h2>La suite de cet article est réservée aux adhérents.</h2>
				<p>Connectez-vous pour voir l\'article en intégralité</p>
			 </div>
			 ';
    }
  } else {
    $content = str_replace('[PRIVEE_ADH]', '', str_replace('<p>[PRIVEE_ADH]</p>', '', $content));
  }


  return $content;
}

add_filter('the_content', 'the_content_adherent_privee');

add_action('add_meta_boxes', 'acces_restreint_checkboxes');
function acces_restreint_checkboxes()
{
  add_meta_box(
    'acces_restreint_id',          // this is HTML id of the box on edit screen
    'Accès restreint aux adhérents',    // title of the box
    'acces_restreint_content',   // function to be called to display the checkboxes, see the function below
    'post',        // on which edit screen the box should appear
    'side',      // part of page where the box should appear
    'high'      // priority of the box
  );
}


add_filter('single_template', 'template_custom_post_type');

function template_custom_post_type($single)
{
  global $post;

  /* Checks for single template by post type */
  if ($post->post_type == 'partenairepost') {
    if (file_exists(EDC_PLUGIN_PATH . 'templates/post-partenaire-secure.php')) {
      return EDC_PLUGIN_PATH . 'templates/post-partenaire-secure.php';
    }
  } elseif ($post->post_type == 'adherentpost') {
    if (file_exists(EDC_PLUGIN_PATH . 'templates/post-adherent-secure.php')) {
      return EDC_PLUGIN_PATH . 'templates/post-adherent-secure.php';
    }
  }

  return $single;
}


add_filter('archive_template', 'template_archive_custom_post_type');
function template_archive_custom_post_type($archive_template)
{
  global $post;

  if (is_post_type_archive('partenairepost')) {
    if (file_exists(EDC_PLUGIN_PATH . 'templates/archive-partenaire-secure.php')) {
      return EDC_PLUGIN_PATH . 'templates/archive-partenaire-secure.php';
    }
  } elseif (is_post_type_archive('adherentpost')) {
    if (file_exists(EDC_PLUGIN_PATH . 'templates/archive-adherent-secure.php')) {
      return EDC_PLUGIN_PATH . 'templates/archive-adherent-secure.php';
    }
  }
  return $archive_template;
}



// display the metabox
function acces_restreint_content($post)
{
  // nonce field for security check, you can have the same
  // nonce field for all your meta boxes of same plugin
  wp_nonce_field(plugin_basename(__FILE__), 'acces_restreint_nonce');

  echo '<input type="checkbox" name="acces_restreint_cb" value="1" ' . ((get_post_meta($post->ID, 'acces_restreint_cb', true) == 1) ? 'checked="checked"' : '') . ' /> Accès réservé aux adhérent';
}

// save data from checkboxes
add_action('save_post', 'acces_restreint_field_data');
function acces_restreint_field_data($post_id)
{
  global $createPageInstall;

  if ($createPageInstall) {
    return;
  }

  // check if this isn't an auto save
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // security check
  if (!wp_verify_nonce($_POST['acces_restreint_nonce'], plugin_basename(__FILE__))) {
    return;
  }

  // further checks if you like,
  // for example particular user, role or maybe post type in case of custom post types

  // now store data in custom fields based on checkboxes selected
  if (isset($_POST['acces_restreint_cb'])) {
    update_post_meta($post_id, 'acces_restreint_cb', 1);
  } else {
    update_post_meta($post_id, 'acces_restreint_cb', 0);
  }
}

class EDC_Adherents_Plugin
{
  public function __construct()
  {
    global $wpdb, $wp_version, $wpmu_version, $_conf;

    define('GESTION_ADH_REP_ICONES', plugins_url('icones', __FILE__));

    require_once(dirname(__FILE__) . '/widgets/connexion.php');
    require_once(dirname(__FILE__) . '/shortcodes/init.php');
    require_once(dirname(__FILE__) . '/templates/init.php');

    $this->templates = array('templates/template-adherent-secure.php' => 'Adhérent secure', 'templates/template-partenaire-secure.php' => 'Partenaire secure');


    // Add a filter to the attributes metabox to inject template into the cache.
    if (version_compare(floatval(get_bloginfo('version')), '4.7', '<')) {
      // 4.6 and older
      add_filter(
        'page_attributes_dropdown_pages_args',
        array($this, 'register_project_templates')
      );
    } else {
      // Add a filter to the wp 4.7 version attributes metabox
      add_filter(
        'theme_page_templates',
        array($this, 'add_new_template')
      );
    }

    // Add a filter to the save post to inject out template into the page cache
    add_filter(
      'wp_insert_post_data',
      array($this, 'register_project_templates')
    );

    // Add a filter to the template include to determine if the page has our
    // template assigned and return it's path
    add_filter(
      'template_include',
      array($this, 'view_project_template')
    );


    add_action('wp_enqueue_scripts', array($this, 'enqueue'));

    add_action('init', array($this, 'register_my_menu'));

    add_filter('the_content', array($this, 'check_acces'));

    if (function_exists('register_sidebar')) {
      register_sidebar(array(
        'name' => 'Sidebar adherent',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
      ));
    }
  }

  public function register_project_templates($atts)
  {
    // Create the key used for the themes cache
    $cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

    // Retrieve the cache list.
    // If it doesn't exist, or it's empty prepare an array
    $templates = wp_get_theme()->get_page_templates();
    if (empty($templates)) {
      $templates = array();
    }

    // New cache, therefore remove the old one
    wp_cache_delete($cache_key, 'themes');

    // Now add our template to the list of templates by merging our templates
    // with the existing templates array from the cache.
    $templates = array_merge($templates, $this->templates);

    // Add the modified cache to allow WordPress to pick it up for listing
    // available templates
    wp_cache_add($cache_key, $templates, 'themes', 1800);

    return $atts;
  }

  public function add_new_template($posts_templates)
  {
    $posts_templates = array_merge($posts_templates, $this->templates);
    return $posts_templates;
  }

  public function view_project_template($template)
  {
    // Get global post
    global $post;

    // Return template if post is empty
    if (!$post) {
      return $template;
    }

    // Return default template if we don't have a custom one defined
    if (!isset($this->templates[get_post_meta(
      $post->ID,
      '_wp_page_template',
      true
    )])) {
      return $template;
    }

    $file = plugin_dir_path(__FILE__) . get_post_meta(
      $post->ID,
      '_wp_page_template',
      true
    );

    // Just to be safe, we check if the file exist first
    if (file_exists($file)) {
      return $file;
    } else {
      echo $file;
    }

    // Return template
    return $template;
  }

  public function check_acces($content)
  {
    if (is_admin() && is_object($GLOBALS['post']) && $GLOBALS['post']->post_type == 'post') {
      if (get_post_meta($GLOBALS['post']->ID, 'acces_restreint_cb', true) == 1 && (!isset($_SESSION['adherent_connected']) || !$_SESSION['adherent_connected'])) {
        return '<p>' . get_the_post_thumbnail($GLOBALS['post']->ID, 'full') . '</p><p>Pour continuer la lecture, vous devez être identifié. Cliquez sur « Se connecter », à gauche, après avoir renseigné votre email et votre mot de passe.</p>';
      } else {
        return $content;
      }
    } else {
      return $content;
    }
  }

  public function enqueue()
  {
    global $_conf;

    wp_enqueue_script('edca-js', plugins_url('js/GestionAdherent.js', __FILE__), false, null, false);
    wp_enqueue_script('edca-js-date', plugins_url('js/jquery-ui.js', __FILE__), false, get_version());
    wp_enqueue_style('edc_adherent_date', plugins_url('js/jquery-css.css', __FILE__), '', get_version());
    wp_enqueue_style('edc_adherent', plugins_url('css/edc_adherent.css', __FILE__), '', get_version());


    $_conf = array();
    // PS le 04/11/2021
    //$_conf['URL_statuts'] = '/download/reglement-et-statuts/?wpdmdl=3856';
    $_conf['URL_statuts'] = 'https://www.assoedc.com/wp-content/uploads/2022/07/Statuts-et-reglement-2022.pdf';
    $_conf['id_page_cb'] = get_permalink(get_option('edc_adherent_id_page_paiement_cb'));
    $_conf['id_page_cb_ok'] = get_permalink(get_option('edc_adherent_id_page_paiement_cb_ok'));  //'https://adherents.edc.asso.fr/?page_id=12383';
    $_conf['id_page_cb_ko'] = get_permalink(get_option('edc_adherent_id_page_paiement_cb_ko')); //'https://adherents.edc.asso.fr/?page_id=12384';
    $_conf['id_page_cb_annule'] = get_permalink(get_option('edc_adherent_id_page_paiement_cb_annule')); //'https://adherents.edc.asso.fr/?page_id=12385';
    $_conf['id_page_cb_auto'] = get_home_url() . '/reponse_auto_cb.php'; //'https://adherents.edc.asso.fr/reponse_auto_cb.php';
  }

  public function register_my_menu()
  {
    register_nav_menu('adherent-menu', __('Menu adhérent'));
    register_nav_menu('adherent-partenaire', __('Menu partenaire_Test'));
    register_nav_menu('adherent-partenaire-test', __('Menu partenaire '));

    /*
        EDC_Partenaires_Remove();
        EDC_Partenaires_Install();
        die('toto');
        */

    //EDC_Partenaires_AddNew(); die('maintenance, recharger la page');
  }
}

new EDC_Adherents_Plugin();

// OK

register_activation_hook(__FILE__, 'EDC_Install');
register_deactivation_hook(__FILE__, 'EDC_Remove');

function EDC_Install()
{
  EDC_Adherents_Install();
  EDC_Partenaires_Install();
}

function EDC_Remove()
{
  EDC_Adherents_Remove();
  EDC_Partenaires_Remove();
}

function EDC_Adherents_Install()
{
  global $wpdb, $createPageInstall;
  $createPageInstall = true;

  // Adhérent - Connexion
  $the_page_title = 'Adhérent - Connexion';
  $the_page_name = 'adherent-connexion';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = "[ADHERENT_CONNEXION]";
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    //$the_page->post_content = "[ADHERENT_MDP_LOST]";
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-not-secure.php'); // Sidebar

  delete_option('edc_adherent_id_page_connexion');
  add_option('edc_adherent_id_page_connexion', $the_page_id);

  // Adhérent - Mot de passe oublié
  $the_page_title = 'Adhérent - Mot de passe oublié';
  $the_page_name = 'adherent-mot-de-passe_oublie';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = "[ADHERENT_MDP_LOST]";
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    //$the_page->post_content = "[ADHERENT_MDP_LOST]";
    $the_page_id = wp_update_post($the_page);
  }

  delete_option('edc_adherent_id_page_mdp_lost');
  add_option('edc_adherent_id_page_mdp_lost', $the_page_id);

  // Adhérent - Créer mon espace personnalisé et sécurisé
  $the_page_title = 'Adhérent - Créer mon espace personnalisé et sécurisé';
  $the_page_name = 'adherent-creer-compte';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<h1>L\'accès à l\'Espace Adhérent n\'est autorisé qu\'à nos membres adhérents. Si vous n\'êtes pas adhérent ou si vous avez été adhérent mais que vous ne faites plus partie de notre Association, aucun compte ne sera crée ni réactivé.</h1>
<h2>Avant de remplir le formulaire suivant, merci de lire les informations suivantes :</h2>
<ul>
<li><u>Vous recevez nos mails</u> (lettres d\'informations, notification sur l\'évolution de votre dossier, communication diverse, ...), il est tout à fait possible que votre compte existe déjà.
<br/>Aussi, nous vous invitons à vérifier que votre compte ne soit pas déjà créé en utilisant la réinitialisation de votre mot de passe en cliquant sur <a href="' . get_permalink(get_option('edc_adherent_id_page_mdp_lost')) . '" alt="Mot de passe">le lien suivant</a>.<br/>
Un mail explicatif vous sera envoyé à l\'adresse que vous aurez saisi afin de modifier votre mot de passe. 
</li>
<li><u>Vous recevez aucun de nos mails de communication ou bien vous ne recevez pas d\'email en utilisant la première étape</u>, remplissez les champs suivants afin de valider la création de votre compte.
</li>
</ul>
<p>A l\'issu de la validation de votre demande, vous recevrez un mail de confirmation à l\'adresse indiqué.
<br/>Votre compte sera activé sous les 48h.</p>
[ADHERENT_CREER_COMPTE]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  delete_option('edc_adherent_id_page_creer_compte');
  add_option('edc_adherent_id_page_creer_compte', $the_page_id);

  // Adhérent - Aide à la déclaration fiscale 2016
  $the_page_title = 'Adhérent - Aide à la déclaration fiscale 2016';
  $the_page_name = 'adherent-aide-declaration-2016';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<h1 style="text-align: justify;">Aide à la déclaration fiscale 2016 !</h1>
<p style="text-align: justify;"><strong>Vous avez certainement reçu notre <span class="domtooltips">aide à la déclaration<span class="domtooltips_tooltip" style="display: none">Il s\'agit d\'un document fournit chaque année par votre <span class="domtooltips">gestionnaire de biens <span class="domtooltips_tooltip" style="display: none">Voir Administrateur de biens</span></span>afin de vous aider à remplir votre déclaration de revenus fonciers 2044 ou 2044S. Il détaille les recettes à déclarer, ainsi que les charges et honoraires déductibles.</span></span> fiscale 2016 sur votre messagerie électronique (si vous en possédez une)</strong>.</p>
<p style="text-align: justify;">Si vous ne nous avez pas communiqué d&rsquo;adresse électronique, vous recevrez votre aide à la déclaration afin de vous aider à remplir votre déclaration de revenus fonciers 2044 ou 2044S. Il détaille les recettes à déclarer, ainsi que les charges et honoraires déductibles.</span></span> par voie postale aux alentours du 22 avril.</p>
<p style="text-align: justify;">Ce courrier est aujourd&rsquo;hui mis à votre disposition en téléchargement ci-après.</p>
<p style="text-align: justify;"><span style="font-size: large; color: #d60036;"><strong>Si, dans la liste ci-dessous, il vous manque une ou plusieurs aides, nous vous invitons à nous contacter pour vous fournir toutes informations utiles : </strong></span></p>
<ul>
<li><span style="font-size: large; color: #d60036;"><strong>Par téléphone : 0 805 404 555 ;</strong></span></li>
<li><span style="font-size: large; color: #d60036;"><strong>Par e-mail : <a href="mailto:impots@edc.asso.fr.">impots@edc.asso.fr.</a></strong></span></li>
</ul>
<p style="text-align: justify;">Vous trouverez également, dans la partie &laquo;&nbsp;<a href="https://www.assoedc.com/telechargements/" target="_blank">Téléchargement</a>&nbsp;&raquo;, nos didacticiels pour compléter seul votre ou vos déclaration(s) d&rsquo;impôts.</p>
<p style="text-align: justify;">Enfin,<strong> n&rsquo;oubliez pas, du 28 avril au 7 juin 2016, notre permanence téléphonique Assisit&rsquo;-Impôts </strong>sera disponible pour vous aider à compléter et vérifier toutes vos déclarations d&rsquo;impôts !</p>
[ADHERENT_AIDE_IMPOT_2016]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_aide_declaration_2016');
  add_option('edc_adherent_id_page_aide_declaration_2016', $the_page_id);

  // Adhérent - Mon compte extranet
  $the_page_title = 'Adhérent - Mon compte extranet';
  $the_page_name = 'mon-compte-extranet';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<h1 style="text-align: justify;">Modifier mon mot de passe ou mon adresse mail</h1>
[ADHERENT_MON_COMPTE]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mon_compte_extranet');
  add_option('edc_adherent_id_page_mon_compte_extranet', $the_page_id);

  // Adhérent - Mes données
  $the_page_title = 'Adhérent - Mes données';
  $the_page_name = 'mes-donnees';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_MES_DONNEES]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mes_donnees');
  add_option('edc_adherent_id_page_mes_donnees', $the_page_id);

  // Adhérent - Mes documents
  $the_page_title = 'Adhérent - Mes documents';
  $the_page_name = 'mes-documents';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_MES_DOCUMENTS]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mes_documents');
  add_option('edc_adherent_id_page_mes_documents', $the_page_id);

  // Adhérent - Changement adresse
  $the_page_title = 'Adhérent - Changer mon adresse';
  $the_page_name = 'changer-mon-adresse';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_CHANGE_ADRESSE]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_change_adresse');
  add_option('edc_adherent_id_page_change_adresse', $the_page_id);

  // Adhérent - Mon investissement
  $the_page_title = 'Adhérent - Mon investissement';
  $the_page_name = 'mon-investissement';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_MON_INVESTISSEMENT]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mon_investissement');
  add_option('edc_adherent_id_page_mon_investissement', $the_page_id);

  // Adhérent - Mes investissements
  $the_page_title = 'Adhérent - Mes investissements';
  $the_page_name = 'mes-investissements';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_MES_INVESTISSEMENTS]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mes_investissements');
  add_option('edc_adherent_id_page_mes_investissements', $the_page_id);

  // Adhérent - Mes dossiers
  $the_page_title = 'Adhérent - Mes dossiers';
  $the_page_name = 'mes-dossiers';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_MES_DOSSIERS]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mes_dossiers');
  add_option('edc_adherent_id_page_mes_dossiers', $the_page_id);

  // Adhérent - Mon dossier
  $the_page_title = 'Adhérent - Mon dossier';
  $the_page_name = 'mon-dossier';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_MON_DOSSIER]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mon_dossier');
  add_option('edc_adherent_id_page_mon_dossier', $the_page_id);

  // Adhérent - Mes cotisations
  $the_page_title = 'Adhérent - Mes cotisations';
  $the_page_name = 'mes-cotisations';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<h2 style="text-align: justify;" align="center">Dans la liste ci-dessous, vous trouverez l\'ensemble des cotisations que vous nous avez réglé.</h2>
<p style="text-align: justify;" align="center">Pour les lignes depuis 2008 le fichier PDF de votre reçu à présenter à l\'administration fiscale est accessible en cliquant sur l\'icône <img class="alignnone size-full wp-image-7151" title="document-pdf" alt="" src="/wp-content/uploads/2012/01/document-pdf.png" width="16" height="16" /> en bout de ligne.</p>
<p>Pour ouvrir vos cotisations vous devez disposer d\'Acrobat Reader<br /> <a href="http://www.adobe.com/go/getreader_fr" target="_blank"><img class="alignnone size-full wp-image-7168 aligncenter" title="get_adobe_reader" alt="" src="/wp-content/uploads/2012/01/get_adobe_reader.png" width="158" height="39" /></a></p>
<h2>Vous réglez votre cotisation par chèque, avez-vous pensé au prélèvement automatique ?</h2>
<p>Afin de sécuriser et d’automatiser votre règlement, nous vous proposons d’opter pour le prélèvement automatique, qui présente plusieurs avantages :</p>
<ul>
<li>Simple : une attestation et un RIB suffisent</li>
<li>Souple : vous pouvez l’interrompre à tout moment</li>
<li>Fiable : bénéficiez de nos services sans interruption</li>
<li>Un secours adhérent renforcé, l\'association maîtrise son budget</li>
</ul>
<h2>Vous avez changé de compte bancaire ?</h2>
<p>Téléchargez notre autorisation de prélèvement et suivez nos indications : <a title="Autorisation de prélèvement" href="/?p=4816">autorisation de prélèvement</a></p>
<p>[ADHERENT_MES_COTISATIONS]</p>
<blockquote>
<p style="text-align: left;" align="center"><strong>N’oubliez pas ! Le montant de votre cotisation peut, sous certaines conditions, être déduit de vos revenus locatifs ou de vos bénéfices industriels commerciaux !</strong></p>
</blockquote>';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_mes_cotisations');
  add_option('edc_adherent_id_page_mes_cotisations', $the_page_id);

  // Adhérent - Attestation cotisation
  $the_page_title = 'Adhérent - Mes cotisations - Attestation';
  $the_page_name = 'mes-cotisations-attestation';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_ATTESTATION_COTISATION]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_recu_fiscal');
  add_option('edc_adherent_id_page_recu_fiscal', $the_page_id);

  // Adhérent - Nous contacter
  $the_page_title = 'Adhérent - Nous contacter';
  $the_page_name = 'adherent-nous-contacter';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_NOUS_CONTACTER]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_nous_contacter');
  add_option('edc_adherent_id_page_nous_contacter', $the_page_id);

  // Adhérent - Paiement CB
  $the_page_title = 'Paiement de la cotisation';
  $the_page_name = 'adherent-paiement-cb';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[ADHERENT_FormCB]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  //update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_paiement_cb');
  add_option('edc_adherent_id_page_paiement_cb', $the_page_id);

  // Adhérent - Paiement CB OK
  $the_page_title = 'Paiement accepté';
  $the_page_name = 'adherent-paiement-cb-ok';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<p>Nous vous remercions pour votre adhésion.</p>
<p>Celle-ci sera prise en compte dès la validation de votre paiement par la banque.</p>';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  //update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_paiement_cb_ok');
  add_option('edc_adherent_id_page_paiement_cb_ok', $the_page_id);

  // Adhérent - Paiement CB KO
  $the_page_title = 'Paiement refusé';
  $the_page_name = 'adherent-paiement-cb-ko';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<p>Nous sommes désolé mais votre paiement a été refusé.</p>';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  //update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_paiement_cb_ko');
  add_option('edc_adherent_id_page_paiement_cb_ko', $the_page_id);

  // Adhérent - Paiement CB Annule
  $the_page_title = 'Paiement annulé';
  $the_page_name = 'adherent-paiement-cb-annule';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<p>Vous avez annulé votre paiement.</p>';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  //update_post_meta($the_page_id, '_wp_page_template', 'template-adherent-secure.php');

  delete_option('edc_adherent_id_page_paiement_cb_annule');
  add_option('edc_adherent_id_page_paiement_cb_annule', $the_page_id);

  $createPageInstall = false;
}


function EDC_Partenaires_Install()
{
  global $wpdb, $createPageInstall;
  $createPageInstall = true;

  // Partenaire - Mot de passe oublié
  $the_page_title = 'Partenaire - Mot de passe oublié';
  $the_page_name = 'partenaire-mot-de-passe_oublie';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = "[PARTENAIRE_MDP_LOST]";
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    //$the_page->post_content = "[ADHERENT_MDP_LOST]";
    $the_page_id = wp_update_post($the_page);
  }

  delete_option('edc_partenaire_id_page_mdp_lost');
  add_option('edc_partenaire_id_page_mdp_lost', $the_page_id);

  // Partenaire - Mon compte extranet
  $the_page_title = 'Partenaire - Mon compte extranet';
  $the_page_name = 'partenaire-mon-compte-extranet';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '<h1 style="text-align: justify;">Modifier mon mot de passe</h1>
[PARTENAIRE_MON_COMPTE]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_mon_compte_extranet');
  add_option('edc_partenaire_id_page_mon_compte_extranet', $the_page_id);

  // Partenaire - Mes données
  $the_page_title = 'Partenaire - Mes données';
  $the_page_name = 'partenaire-mes-donnees';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_MES_DONNEES]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_mes_donnees');
  add_option('edc_partenaire_id_page_mes_donnees', $the_page_id);

  // Partenaire - Changement adresse
  $the_page_title = 'Partenaire - Changer mon adresse';
  $the_page_name = 'partenaire-changer-mon-adresse';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_CHANGE_ADRESSE]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_change_adresse');
  add_option('edc_partenaire_id_page_change_adresse', $the_page_id);

  // Partenaire - Nous contacter
  $the_page_title = 'Partenaire - Nous contacter';
  $the_page_name = 'partenaire-nous-contacter';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_NOUS_CONTACTER]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_nous_contacter');
  add_option('edc_partenaire_id_page_nous_contacter', $the_page_id);


  // Partenaire - Mes clients
  $the_page_title = 'Partenaire - Mes clients';
  $the_page_name = 'partenaire-mes-clients';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_MES_CLIENTS]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'templates/template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_mes_clients');
  add_option('edc_partenaire_id_page_mes_clients', $the_page_id);

  // Partenaire - Clients - Coordonnées
  $the_page_title = 'Coordonnees-Clients';
  $the_page_name = 'Coordonnees-Clients';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_COORDONNEES_CLIENTS]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'templates/template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_coordonnées_clients');
  add_option('edc_partenaire_id_page_coordonnees_clients', $the_page_id);

  // Partenaire - Adhésions
  $the_page_title = 'Partenaire - Adhésions';
  $the_page_name = 'partenaire-faire-adherer';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_FAIRE_ADHERER]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'templates/template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_adherer');
  add_option('edc_partenaire_id_page_adherer', $the_page_id);

  // Partenaire - Adhésions
  $the_page_title = 'Signer bulletin adhésion';
  $the_page_name = 'signer-bulletin_adhesion';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_FAIRE_SIGNER]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  delete_option('edc_partenaire_id_page_faire_signer');
  add_option('edc_partenaire_id_page_faire_signer', $the_page_id);

  $createPageInstall = false;
}

function EDC_Partenaires_AddNew()
{
  // Partenaire - Adhésions
  $the_page_title = 'Partenaire - Adhésions';
  $the_page_name = 'partenaire-faire-adherer';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_FAIRE_ADHERER]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  update_post_meta($the_page_id, '_wp_page_template', 'templates/template-partenaire-secure.php');

  delete_option('edc_partenaire_id_page_adherer');
  add_option('edc_partenaire_id_page_adherer', $the_page_id);

  // Partenaire - Adhésions
  $the_page_title = 'Signer bulletin adhésion';
  $the_page_name = 'signer-bulletin_adhesion';

  $the_page = get_page_by_title($the_page_title);

  if (!$the_page) {
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_name'] = $the_page_name;
    $_p['post_content'] = '[PARTENAIRE_FAIRE_SIGNER]';
    $_p['post_status'] = 'publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'
    $the_page_id = wp_insert_post($_p);
  } else {
    $the_page_id = $the_page->ID;
    $the_page->post_status = 'publish';
    $the_page_id = wp_update_post($the_page);
  }

  delete_option('edc_partenaire_id_page_faire_signer');
  add_option('edc_partenaire_id_page_faire_signer', $the_page_id);
}

function EDC_Adherents_Remove()
{
  global $wpdb;

  $the_page_id = get_option('edc_adherent_id_page_connexion');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_connexion");

  $the_page_id = get_option('edc_adherent_id_page_mdp_lost');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mdp_lost");

  $the_page_id = get_option('edc_adherent_id_page_creer_compte');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_creer_compte");

  $the_page_id = get_option('edc_adherent_id_page_aide_declaration_2016');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_aide_declaration_2016");

  $the_page_id = get_option('edc_adherent_id_page_mon_compte_extranet');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mon_compte_extranet");

  $the_page_id = get_option('edc_adherent_id_page_mes_donnees');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mes_donnees");

  $the_page_id = get_option('edc_adherent_id_page_mes_documents');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mes_documents");

  $the_page_id = get_option('edc_adherent_id_page_change_adresse');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_change_adresse");

  $the_page_id = get_option('edc_adherent_id_page_mon_investissement');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mon_investissement");

  $the_page_id = get_option('edc_adherent_id_page_mes_investissements');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mes_investissements");

  $the_page_id = get_option('edc_adherent_id_page_mes_dossiers');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mes_dossiers");

  $the_page_id = get_option('edc_adherent_id_page_mon_dossier');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mon_dossier");

  $the_page_id = get_option('edc_adherent_id_page_paiement_cb');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_paiement_cb");

  $the_page_id = get_option('edc_adherent_id_page_paiement_cb_ok');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_paiement_cb_ok");

  $the_page_id = get_option('edc_adherent_id_page_paiement_cb_ko');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_paiement_cb_ko");

  $the_page_id = get_option('edc_adherent_id_page_paiement_cb_annule');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_paiement_cb_annule");

  $the_page_id = get_option('edc_adherent_id_page_mes_cotisations');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_mes_cotisations");

  $the_page_id = get_option('edc_adherent_id_page_recu_fiscal');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_recu_fiscal");

  $the_page_id = get_option('edc_adherent_id_page_nous_contacter');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_adherent_id_page_nous_contacter");
}

function EDC_Partenaires_Remove()
{
  global $wpdb;

  $the_page_id = get_option('edc_partenaire_id_page_mdp_lost');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_partenaire_id_page_mdp_lost");

  $the_page_id = get_option('edc_partenaire_id_page_mon_compte_extranet');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_partenaire_id_page_mon_compte_extranet");

  $the_page_id = get_option('edc_partenaire_id_page_mes_donnees');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_partenaire_id_page_mes_donnees");

  $the_page_id = get_option('edc_partenaire_id_page_change_adresse');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_partenaire_id_page_change_adresse");

  $the_page_id = get_option('edc_partenaire_id_page_nous_contacter');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_partenaire_id_page_nous_contacter");

  $the_page_id = get_option('edc_partenaire_id_page_mes_clients');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_partenaire_id_page_mes_clients");

  $the_page_id = get_option('edc_partenaire_id_page_adherer');
  if ($the_page_id) {
    wp_delete_post($the_page_id);
  }
  delete_option("edc_partenaire_id_page_adherer");
}
