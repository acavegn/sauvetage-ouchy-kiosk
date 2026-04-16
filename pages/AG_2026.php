<?php

/** 
 * Kiosk
 * Page AG 2026 : Uniquement pour la journée de l'assembleée Générale
 */

$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css') + filemtime($root . '/kiosk/pages/AG_2026.css');
?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<head>
  <link href="/kiosk/kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
  <link href="/kiosk/pages/AG_2026.css?ts=<?= $lastmodcss ?>" rel="stylesheet">

</head>

<body>




  <div class="infopage backgroundpage">
    <!-- Header -->
    <header class="header">
      <img src="https://sisl.ch/images/logoSISL.svg" />
      <h1 class="title">Assemblée Générale - 26 février 2026</h1>
      <img src="https://sauvetage-ouchy.ch/static-images/logo_sauvetage/logo_sauvetage_white.svg" />
    </header>

    <!-- Contenu -->
    <main class="content">
      <!-- Colonne gauche : Programme -->
      <section class="card" style="text-align: center;">
        <h2>📣 Oyez, oyez, braves gens</h2>
        <p>
         <br><br>
          Nous avons le plaisir de vous informer que la
        <p class="bigfont">140ème Assemblée Générale</p>
        <p>du
          Sauvetage d'Ouchy est convoquée le :</p>
        <p class="bigfont">
          Jeudi 26 février 2026 à 19h00</p>
        <p>Au Club des Navigateurs – Société Nautique d’Ouchy</p>
        <p>&nbsp;</p><p>et comme de bien entendu, nous vous invitons ensuite à</p>
        <p class="bigfont">un apéritif</p>
        <p>qui se déroulera dans les locaux du Sauvetage d'Ouchy.</p>


      </section>

      <!-- Colonne droite : Infos -->
      <aside class="card">
        <h2>🗓️ Ordre du jour</h2>
        <ul class="infos">
          <li class="info-item"><span class="dot"></span><span>1. Ouverture de l'Assemblée et liste de présence</span></li>
          <li class="info-item"><span class="dot"></span><span>2. Approbation du procès-verbal de l’Assemblée Générale du 27 février 2025</span></li>
          <li class="info-item"><span class="dot"></span><span>3. Rapport du Président</span></li>
          <li class="info-item"><span class="dot"></span><span>4. Rapport du Trésorier</span></li>
          <li class="info-item"><span class="dot"></span><span>5. Rapport des vérificateurs des comptes<br>- Vote du rapport et décharge au comité<br>- Élection des vérificateurs des comptes</span></li>
          <li class="info-item"><span class="dot"></span><span>6. Rapport du chef des gardes</span></li>
          <li class="info-item"><span class="dot"></span><span>7. Rapport du chef du matériel</span></li>
          <li class="info-item"><span class="dot"></span><span>8. Rapport du responsable des bateaux</span></li>
          <li class="info-item"><span class="dot"></span><span>9. Rapport du barreur</span></li>
          <li class="info-item"><span class="dot"></span><span>10. Rapport du responsable formation</span></li>
          <li class="info-item"><span class="dot"></span><span>11. Rapport du secrétaire et projets IT</span></li>
          <li class="info-item"><span class="dot"></span><span>12. Démissions, admissions, radiations</span></li>
          <li class="info-item"><span class="dot"></span><span>13. Élection du Président</span></li>
          <li class="info-item"><span class="dot"></span><span>14. Élection des membres du comité</span></li>
          <li class="info-item"><span class="dot"></span><span>15. Commission des membres d'honneur</span></li>
          <li class="info-item"><span class="dot"></span><span>16. Élection des commissions pour le projet d'achat d'un nouveau bateau</span></li>
          <li class="info-item"><span class="dot"></span><span>17. Activités 2026</span></li>
          <li class="info-item"><span class="dot"></span><span>18. Divers / Propositions individuelles</span></li>
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