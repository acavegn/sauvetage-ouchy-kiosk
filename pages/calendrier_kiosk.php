<?php

/** 
 * Kiosk
 * Page calendrier: affiche es events du calendrier 
 */

require_once(__DIR__ . '/../../wp-load.php');
$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js') + filemtime($root . '/wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css') + filemtime($root . '/wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.css');


require_once(ABSPATH . 'wp-content/plugins/sauvetage-ouchy/membre/souchy_membre.php');
require_once(ABSPATH . 'wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.php');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
unset($_SESSION['souchy_calendar']); // On reset la variable qui contine le calendrier avant, pour partir d'un status clean
$calevent = new souchy_calendar(0);
$_SESSION['souchy_calendar'] = $calevent; // on le garde pour le prochain call rest
// Evenements du jour
$dt = (new \DateTime())->format('Y-m-d');
$curentsevents = $calevent->get_events_json($dt . ' 00:00:00', $dt . ' 23:59:59');
// Anniversaires
$anniversaires = souchy_membre::get_anniversaire($dt);
?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<head>
  <link href="/kiosk/kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">

  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
  <script src="/wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.js?ts=<?= $lastmodcss ?>"></script>
  <link href="/wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.css?ts=<?= $lastmodcss ?>" rel="stylesheet">


  <script src="/kiosk/vendors/sbbUhr/sbbUhr-1.3.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var myClock; // define Clock object
      myClock = new sbbUhr("sbb_uhr_container", true, 60); // create new clock object sbbUhr([ID of container], [default = false, true = dark background, false = light background], Refresh rate in Frames per Second [default = as many as possible])
      myClock.start(); // start clock
      //myClock.stop(); // call to stop clock.
    });
  </script>

  <style>
    .SBBclock {
      top: 20px;
      right: 20px;
      position: fixed;
      width: 100px;
      height: 100px;
    }

    .content-grid>* {
      min-height: 0;
      min-width: 0;
    }

    .content-grid {
      padding-top: 10px;
      padding-left: 25px;
      /* donne une hauteur FIXE au conteneur = viewport - titre */
      height: calc(100svh - 100px);
      display: grid;
      grid-template-columns: 5fr 2fr;
      column-gap: 20px;
      grid-template-rows: auto 1fr;
      align-items: start;

      /* important : on remplit, on n’espace pas */
      align-content: stretch;
      justify-content: stretch;
    }

    .A1 {
      grid-column: 1;
      grid-row: 1 /span 2;
    }

    .B1 {
      grid-column: 2;
      grid-row: 1;
    }

    .B2 {
      grid-column: 2;
      grid-row: 2;
    }

    .calendrier .fc-daygrid-dot-event .fc-event-title,
    .calendrier .fc-event-time {
      color: var(--text);
    }

    .todayevents .title {
      font-size: larger;
    }

    .todayevents .periode {
      color: var(--muted);
    }

    .todayevents .inscrits {
      color: var(--muted);
      margin-left: 20px;
    }

    .todayevents h2 {
      font-size: clamp(20px, 1.6vw + 10px, 32px);
      font-weight: 800;
      letter-spacing: 0.2px;

    }
  </style>
</head>

<body>
  <div class="backgroundpage">
    <div id="sbb_uhr_container" class="SBBclock"> </div>

    <!-- Header -->
    <header class="header">
      <img src="https://sauvetage-ouchy.ch/static-images/logo_sauvetage/logo_sauvetage_white.svg" />
      <h1 class="title">Evènements à venir</h1>

    </header>

    <main class="content-grid">
      <!-- carte sisl -->
      <div class="A1">
        <div class="calendrier">
          <div id="loading" style="display:none;">Chargement en cours...</div>
          <div id='caldiv' data-kiosk=true></div>
          <div id='caldiv_hover'></div>
        </div>
      </div>
      <!-- Colonne droite : Infos -->
      <!-- anniversaires-->
      <?php if (sizeof($anniversaires) > 0) { ?>
        <div class="B1 todayevents">
          <h2>🎂 Joyeux Anniversaire</h2>
          <?php
          foreach ($anniversaires as $item) {
            echo '<div class="title">' . $item->bexio_name . ' 🎉 </div>';
          }
          ?>
        </div>
      <?php } ?>
      <!-- Status Ouchy -->
      <div class="B2 todayevents">
        <h2>🗓️ Aujourd'hui</h2>
        <?php
        if (sizeof($curentsevents) > 0) {
          foreach ($curentsevents as $item) {
            echo '<div class="title">' . $item["title"] . '</div>';
            echo '<div class="periode">' . $item["extendedProps"]["periode"] . '</div>';
            if (!empty($item['extendedProps']['inscriptions']) && is_iterable($item['extendedProps']['inscriptions'])) {
              echo '<div class="inscrits">';
              foreach ($item["extendedProps"]["inscriptions"] as $inscrit) {
                echo  $inscrit->bexio_name2 . " " . $inscrit->bexio_name1 . '<br>';
              }
              echo '</div>';
            }
          }
        } else  echo "<div class='periode'>Rien au calendrier… pour l’instant 😴<br>Un petit café en attendant une alarme ? ☕️ 🛟</div>";

        ?>
      </div>
    </main>
  </div>
</body>