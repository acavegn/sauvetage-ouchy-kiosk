<?php

/** 
 * Kiosk
 * Vitrine 019 : Infomation sur le local 
 */

require_once(__DIR__ . '/../../wp-load.php');
$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css') + filemtime($root . '/kiosk/pages/vitrine019_kiosk.css');


require_once(ABSPATH . 'wp-content/plugins/sauvetage-ouchy/membre/souchy_membre.php');
require_once(ABSPATH . 'wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.php');
$calevent = new souchy_calendar(0);

?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<head>
  <script src="/kiosk/vendors/sbbUhr/sbbUhr-1.3.js"></script>
  <link href="/kiosk/kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
  <link href="/kiosk/pages/vitrine019_kiosk.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var myClock; // define Clock object
      myClock = new sbbUhr("sbb_uhr_container", true, 60); // create new clock object sbbUhr([ID of container], [default = false, true = dark background, false = light background], Refresh rate in Frames per Second [default = as many as possible])
      myClock.start(); // start clock
      //myClock.stop(); // call to stop clock.
    });
  </script>
</head>

<body>
  <div class="backgroundpage">
    <div id="sbb_uhr_container" class="SBBclock"> </div>

    <!-- Header -->
    <header class="header">
      <img src="https://sauvetage-ouchy.ch/static-images/logo_sauvetage/logo_sauvetage_white.svg" />
      <h1 class="title">Bienvenue au Sauvetage d'Ouchy</h1>
    </header>

    <main class="content-grid">

      <!-- Info local -->
      <div class="A1 card">
        <h2>📘 Règles du local</h2>
        <p class="subtitle">et du savoir vivre ensemble</p>
        <li class="info-item">
          <span class="dot"></span>
          <span>Merci laisser les locaux plus propre en sortant que vous ne les avez trouvés en entrant</span>
        </li>
        <li class="info-item">
          <span class="dot"></span>
          <span>Le parking est réservé aux membres en activité (Vigie, Intervention, Séance,... )</span>
        </li>
        <li class="info-item">
          <span class="dot"></span>
          <span>Veuillez ranger les bouteilles vides, verre, papiers, etc dans la boite de recyclage ad-hoc SVP. La planète vous remercie.</span>
        </li>
        <li class="info-item">
          <span class="dot"></span>
          <span>Questions ou soucis au local ? Alors contactez Antonin. votre dévoué responsable matérel</span>
        </li>

      </div>

      <!-- Vigie et surveillance -->
      <div class="A2 card">
        <h2>👀 Vigie et Surveillance</h2>
        <p class="subtitle">De l'eau est à disposition des équipages pendant <b>les vigies et surveillances. (frigo a coté des penderies)</b><br>Veuillez svp faire le suivi des consommations comme indiqué sur la feuille ad-hoc.</p>
        <p class="subtitle"> Pensez à nettoyer la Vedette et/ou le Dauphin après utilisation.</p>
      </div>


      <!-- Info Boissons -->
      <div class="B1 card">
        <h2>☕ Boissons</h2>
        <p class="subtitle">Les consommations sont à régler de suite via TWINT.</p>
        <div class="coteacote-content">
          <ul class="infos">
            <li class="info-item">
              <span class="dot"></span>
              <span>Café,Thé : CHF 1.50 </span>
            </li>
            <li class="info-item">
              <span class="dot"></span>
              <span>Eau, Coca, Thé Froid : CHF 2.50 </span>
            </li>
             <li class="info-item">
              <span class="dot"></span>
              <span>Bière : CHF 2.50 </span>
            </li>
            <li class="info-item">
              <span class="dot"></span>
              <span>Vin - Dérisée (5dl) : CHF 10.- </span>
            </li>
          </ul>
          <div class="qr-container">
            <img src="/kiosk/pages/images/QR_Code_Twint.png" alt="QR Code TWINT">
          </div>
        </div>
      </div>

      <!-- Wifi Membres -->
      <div class="B2 card">
        <h2>🌐 Wifi</h2>
        <p class="subtitle">Accès pour les membres</p>
        <div class="coteacote-content">
          <div class="qr-container" style="width:220px;" >
            <img style="width:initial;height:initial;" src="/kiosk/pages/images/Wifi_membre_connection.png"><br>Scan le QR pour une<br>connexion automatique</img>
          </div>
          <ul class="infos">
            <li class="info-item">
              <span class="dot"></span>
              <span>SSID : <br> Sauvetage-Ouchy membres</span>
            </li>
            <li class="info-item">
              <span class="dot"></span>
              <span>Mot de passe : <br> Sauvetage.019</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Publicité -->
      <div class="C1 card">
        <h2>👍 A faire absolument à Ouchy</h2>
        <p class="subtitle">Balade, Jeux de piste, avoir du plaisir en découvrant Ouchy avant de prendre l'apéro en terrasse.</p>
        <p class="subtitle">C'est ici : <a style="color:white;" href="https://sauvetage-ouchy.ch/jeu_de_piste/"> https://sauvetage-ouchy.ch/jeu_de_piste/</a></p>
        <img class="pub" src="images/Image_flyer_theo.png"></img>
      </div>



    </main>

  </div>
</body>