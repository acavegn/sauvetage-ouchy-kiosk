<?php

/** 
 * Kiosk
 * Page Web Cam : Affiche les images du jour
 */

require_once(__DIR__ . '/../../wp-load.php');
$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css');

define("NB_IMAGES", 6); // Nb of most recent images to load

?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<head>
  <link href="/kiosk/kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
  <style>
    .webcam_gallery {
      min-height: calc(100svh - var(--title-h));
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      grid-template-rows: repeat(2, auto);
      column-gap: 5px;
      row-gap: 5px;
      place-content: center;
    }

    .webcam_gallery img {
      width: 100%;
      height: auto;
      display: block;
    }
  </style>

  <script>
    async function get_webcam_images(gallery_tag) {
      const gallery = document.getElementById(gallery_tag);
      const res = await fetch("/wp-json/souchy/v1/webcam_history?filter=<?= date('Ymd'); ?>", {
        method: "GET",
        headers: {
          Accept: "application/html",
          "Content-Type": "application/json",
        }
      });
      const resData = await res.json();
      if (!resData.success || !resData.files || resData.files.length == 0) {
        gallery.innerHTML = "<p>Gallerie d'image indisponible</p>";
        return;
      }
      let html = "";
      let nb = 0;
      for (const [index, file] of resData.files.entries()) {
        const strDate = file.substr(15, 14);
        const year = strDate.substring(0, 4);
        const month = strDate.substring(4, 6);
        const day = strDate.substring(6, 8);
        const hours = strDate.substring(8, 10);
        const minutes = strDate.substring(10, 12);
        const seconds = strDate.substring(12, 14);
        const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        html += `<img src="/${file}">`;
        if (index >= <?= NB_IMAGES - 1; ?>) break; // Maximum 6 image les plus recente
      };
      gallery.innerHTML = html;

    }

    document.addEventListener("DOMContentLoaded", function() {
      get_webcam_images('webcam_content');
    });
  </script>
</head>

<body>
  <div class="backgroundpage">
    <!-- Header -->
    <header class="header">
      <img src="https://sisl.ch/images/logoSISL.svg" />
      <h1 class="title">Fenêtre sur le lac</h1>
      <img src="https://sauvetage-ouchy.ch/static-images/logo_sauvetage/logo_sauvetage_white.svg" />
    </header>
    <div class="webcam_gallery" id="webcam_content"> </div>
  </div>
</body>