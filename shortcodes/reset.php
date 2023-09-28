<?php

add_shortcode("ADHERENT_RESET_PASS_FORM", "getResetForm");
add_shortcode("ADHERENT_RESET_SEND_TOKEN", "getSendtoken");

function getResetForm()
{

  $form =
    '
      <h1>Réinitialiser votre mot de passe</h1>
      <form method="post">
        <p>
          <label for="password">Nouveau mot de passe : </label> 
          <input name="password" value="" class="text" id="password" type="password" />
        </p>
        <p>
          <label for="confirm_pass">Confirmer le nouveau mot de passe :</label> 
          <input name="confirm_pass" class="text" id="confirm_pass" type="password" />
        </p>			
        <p class="submit">
          <input type="submit" name="wp-submit" id="wp-submit" value="Modifier mon mot de passe" />
        </p>
     </form>';

  return $form;
}

function getSendtoken()
{
  $token = $_GET['token'];
  $password = $_POST['password'];
  $confirm_pass = $_POST['confirm_pass'];

  if (isset($password) && isset($confirm_pass)) {

    if ($password !== $confirm_pass) {
      return "Les mots de passe ne correspondent pas";
    } else {

      $url = 'http://localhost:3000/reset/verify';
      $data = [
        "token" => $token,
        "password" => $password,
      ];

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

      $result = json_decode($response);

      if ($httpcode !== 200) {
        return "Erreur modification mot de passe : " . $result->Message;
      } else {
        echo '<script type="text/javascript">';
        echo 'alert("Votre mot de passe à été modifié");';
        echo 'window.location.href = "http://localhost/assoedc/adherent-connexion-2/";';
        echo '</script>';
        exit;
      }
    }
  }
}
