<?php

/** 
 * Kiosk
 * Page Journée Motorisée : Uniquement pour la journée du 1er novembre 2025
 */

//require_once(__DIR__ . '/../../wp-load.php');
$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css') + filemtime($root . '/kiosk/pages/journeemotorisee_event_kiosk.css');
?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<head>
  <link href="/kiosk/kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
  <link href="/kiosk/pages/journeemotorisee_event_kiosk.css?ts=<?= $lastmodcss ?>" rel="stylesheet">

</head>

<body>




  <div class="infopage backgroundpage">
    <!-- Header -->
    <header class="header">
      <img src="https://sisl.ch/images/logoSISL.svg" />
      <h1 class="title">Bienvenue à la Journée Motorisée - 1er nov. 2025</h1>
      <img src="https://sauvetage-ouchy.ch/static-images/logo_sauvetage/logo_sauvetage_white.svg" />
    </header>

    <!-- Contenu -->
    <main class="content">
      <!-- Colonne gauche : Programme -->
      <section class="card">
        <h2>📋 Programme de la journée</h2>
        <ul class="agenda">
          <li>
            <div class="badge">08:00 – 08:30</div>
            <div>☕ Café d’accueil pour tous les participants</div>
          </li>
          <li>
            <div class="badge">08:30 – 09:00</div>
            <div>🧭 <b>Briefing commun </b>- rappels sécurité, explication du programme, répartition des groupes</div>
          </li>
          <li>
            <div class="badge">09:00 – 11:30</div>
            <div>🛥️ Exercices en groupe </div>
          </li>
          <li>
            <div class="badge">11:30 – 13:00</div>
            <div>🍽️ <b>Pause Repas</b></div>
          </li>
          <li>
            <div class="badge">13:00 – 15:45</div>
            <div>🛥️ Exercices en groupe </div>
          </li>
          <li>
            <div class="badge">15:45 – 16:15</div>
            <div>🧭 Débriefing + rangement</div>
          </li>
        </ul>
      </section>

      <!-- Colonne droite : Infos -->
      <aside class="card">
        <h2>ℹ️ Infos pratiques</h2>
        <p class="subtitle">Restauration & consommations (Cash, TWINT ou Carte)</p>
        <ul class="infos">
           <li class="info-item">
            <span class="dot"></span>
            <span>Café et croissant d'accueil 8h : Offert</span>
          </li>
            <li class="info-item">
            <span class="dot"></span>
            <span>Repas Midi : 25.- à la charge des participants</span>
          </li>
          <li class="info-item">
            <span class="dot"></span>
            <span>Café,Thé<br>3.00 </span>
       
            <span class="dot"></span>
            <span>Boisson Eau, Coca,...<br>3.50 </span>
       
            <span class="dot"></span>
            <span>Bière <br>4.00 </span>
  
            <span class="dot"></span>
            <span>Vin<br>20.00 </span>
          </li>
        

          </ul>
           <h2 style="margin-top:25px">ℹ️ Les exercices de la journée</h2>
            <p class="subtitle">Veuillez rester attentifs aux annonces tout au long de la journée.</p>
       
           <ul class="infos">
        <li class="info-item">
             <span class="dot"></span>
            <span>Appontage bateau CGN</span>
          </li>
          <li class="info-item">
             <span class="dot"></span>
            <span>Recherche en ligne</span>
          </li>

          <li class="info-item">
             <span class="dot"></span>
            <span>Sortie de l’eau d'une personne</span>
          </li>

          <li class="info-item">
             <span class="dot"></span>
            <span>Remorquage en ligne et à couple</span>
          </li>

         
        </ul>
      </aside>
    </main>


  </div>

 <!-- <div class="watermark">
    <div class="watermark__text">Brouillon<br>à corriger</div>
  </div>-->

</body>

</html>