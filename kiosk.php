<?php

/* ***************************
 *  Kiosk : Main page to hadle the Kiosk 
 *  on a Raspberry PI with Display dsi 13.3'' 
 * 
 *  The pages are loaded and rotated, according to  a config json in the js file
 *  
 * ***************************/

$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css') . filemtime($root . '/kiosk/kiosk.css');

// Récupération du device ID
// Le device ID permettra de savoir quelle configuration et pages
// a donner au kiosk, en fonction de sa localisation 
// on l'enregistre ensuite en coockie illimité
$device_id = (empty($_GET["deviceID"]) ? null : $_GET["deviceID"]);
$options = [
    'expires' => 0,
    'path' => "",
    'domain' => "",
    'secure' => false,
    'httponly' => false,
    'samesite' => 'Strict'
];
setcookie('kiosk_device_id', $device_id, $options);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kiosk 019</title>
    <script src="kiosk.js?ts=<?= $lastmodjs ?>" defer></script>
    <link href="kiosk.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
    <link href="kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
   

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const kiosk = new souchy_kiosk();
            kiosk.init();
            window.kiosk = kiosk;
        });
    </script>
</head>

<body>

      <!-- Bare de menu en haut -->
       <div id="toolbar" class="toolbar">
        <button onclick="window.location.reload();">R</button> <button id="toggle" class="toggle on" title="Démarrer/Arrêter la rotation">⏸️ Arrêter la rotation</button>
        <div class="spacer"></div>
        <div id="pages" class="pages"></div>
        <div id="status" class="status"></div>
    </div>

      <!-- Le contenu / Pages du kiosk-->
       <iframe id="kioskFrame" class="kiosk-frame" src="" referrerpolicy="no-referrer" allow="fullscreen;"></iframe>

    <!-- Overlay Mode Nuit -->
    <div id="night-overlay" class="night-hidden" >
        <div class="night-content">
            <div class="night-moon"></div>
            <div class="night-text">mode nuit</div>
            <div class="night-sub">Je dors Zzzzz... Zzz.... Zzzzz...<br> Touchez l'écran pour me réveiller.</div>
        </div>
    </div>

</body>

</html>