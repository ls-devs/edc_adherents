<?php

session_start();

if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected']) {

  require_once(dirname(__FILE__) . '/conf/config.php');
  require_once(dirname(__FILE__) . '/lib/httpful.phar');
  require_once(dirname(__FILE__) . '/lib/fonctions_generales.php');

  if (isset($_GET['cp'])) {
    $url = 'http://localhost:3000/villes';
    $data = array(
      "code_postal" => $_GET['cp']
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

    /* $url = URL_WS_ADH . 'v1.0/json/getListeVilles'; */

    /* $response = \Httpful\Request::get($url . '/' . ((int)$_GET['cp']))->send(); */

    echo $response;

    die();
  } elseif (isset($_GET['cp_partenaire'])) {
    $url = URL_WS_ADH . 'v1.0/json/getListeVillesByName';

    $response = \Httpful\Request::get($url . '/' . ((int)$_GET['cp_partenaire']))->send();

    echo $response->body;

    die();
  } elseif (isset($_POST['message'])) {
    $url = 'http://localhost:3000/comments';
    $data = array(
      "id_fiche" => trim($_POST['IdFiche']),
      'message' => trim($_POST['message']),
      'id_contact' => $_SESSION['adherent_infos']->IdContact
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

    /* $url = URL_WS_ADH . 'v1.0/json/user/insert_action'; */

    /* $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('IdFiche=' . trim($_POST['IdFiche']) . '&Message=' . trim(urlencode($_POST['message'])))->send(); */
    if ($httpcode == 200) {
      $resultat = notifyEDC($_POST["IdFiche"], $_POST["message"]);
      echo "<div style='padding:5px;color:#060;background-color:#6F6;border-style:solid;border-width:1px;border-color:#060;'>La publication de votre Action a &eacute;t&eacute; effectu&eacute;e avec succ&egrave;s et le mail a bien été envoyé</div>";
    } else {
      print_r($response);
      $html = '<strong>Erreur WS</strong>';
    }
  } elseif ($_GET['_type'] == 'tel') {
    $url = 'http://localhost:3000/telephone/change';
    $data = array(
      "IdContactTelephone" => trim($_GET['IdContactTelephone']),
      "Numero" => trim($_GET['Numero']),
      "TelephoneType" => trim($_GET['IdTelephoneType'])
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

    /* $url = URL_WS_ADH . 'v1.0/json/user/change_tel'; */

    /* $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('IdTelephoneType=' . trim($_GET['IdTelephoneType']) . '&Numero=' . trim($_GET['Numero']) . '&IdContactTelephone=' . trim($_GET['IdContactTelephone']))->send(); */
    if ($httpcode == 200) {
      echo '1';
    } else {
      print_r($response);
      $html = '<strong>Erreur WS</strong>';
    }
  } elseif ($_GET['_type'] == 'dob') {
    $url = 'http://localhost:3000/changes/dob';
    $data = array(
      "IdContact" => trim($_GET['IdContact']),
      "DateNaissance" => trim($_GET['DateNaissance']),
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

    /* $url = URL_WS_ADH . 'v1.0/json/user/change_date_naissance'; */

    /* $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('IdContact=' . trim($_GET['IdContact']) . '&DateNaissance=' . trim($_GET['DateNaissance']))->send(); */
    if ($httpcode == 200) {
      $_SESSION['adherent_infos']->DateNaissance = $_GET['DateNaissance'];
      echo '1';
    } else {
      print_r($response);
      $html = '<strong>Erreur WS</strong>';
    }
  } elseif ($_GET['_type'] == 'loc') {
    $url = 'http://localhost:3000/changes/loc';
    $data = array(
      "id_investissement" => $_GET['IdInvestissement'],
      "date_premiere_loc" => $_GET['datePremiereLoc']
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

    /* $url = URL_WS_ADH . 'v1.0/json/user/change_premiere_loc'; */

    /* $response = \Httpful\Request::post($url)->authenticateWith(trim($_SESSION['adherent_login']), trim($_SESSION['adherent_pass']))->sendsType(\Httpful\Mime::FORM)->body('IdInvestissement=' . trim($_GET['IdInvestissement']) . '&datePremiereLoc=' . trim($_GET['datePremiereLoc']))->send(); */
    if ($httpcode == 200) {
      echo '1';
    } else {
      print_r($response);
      $html = '<strong>Erreur WS</strong>';
    }
  } elseif ($_GET['_type'] == 'actions') {
    $result = displayListeActions($_GET["IdFiche"]);
    return ($result);
  }
}
