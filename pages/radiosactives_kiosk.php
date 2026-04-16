<?php

/** 
 * Kiosk
 * Page Radios Actives : afficche la location des proches et radios actives
 */

require_once(__DIR__ . '/../../wp-load.php');
$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css');
?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<head>
    <link href="/kiosk/kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">
    <style>
        .content-grid>* {
            /* Permet aux items de rétrécir/occuper 100% de la hauteur */
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
            grid-template-rows: 150px 1fr;
            column-gap: 20px;

            /* important : on remplit, on n’espace pas */
            align-content: stretch;
            justify-content: stretch;
             align-items: stretch; 
        }

        .content-grid H2 {
            margin-bottom: 5px;
            margin-top: 5px;
          
      font-size: clamp(20px, 1.6vw + 10px, 32px);
      font-weight: 800;
      letter-spacing: 0.2px;

    }
        

        .A1 {
            grid-column: 1;
            grid-row: 1 /span 2;
        }


        .B1 {
            grid-column: 2;
            grid-row: 1;
            line-height: 1.4em;
        }

        .B2 {
            grid-column: 2;
            grid-row: 2;
             line-height: 1.3em;
        }


        .sisl_map {
            display: block;
            /* évite des surprises inline */
            width: 100%;
            height: 100%;
            border: none;
            filter: brightness(85%);
        }

        .sisl_base {
            font-size: clamp(22px, 0.9vw + 8px, 36px);
        }

        .sisl_unite {
            margin-left: 50px;
            font-size: clamp(22px, 0.9vw + 8px, 36px);
        }

        .sisl_indicatif {
            font-family: "courier";
            font-size: larger;
        }

        .sisl_active {
            color: greenyellow;

        }

        .sisl_inactive {
            color: rgb(183, 106, 106);

        }
    </style>

    <script>
        async function refresh_radioSISL() {
            elOuchy019 = document.getElementById('sisl_019');
            elOuchy219 = document.getElementById('sisl_219');
            elOuchy319 = document.getElementById('sisl_319');
            elOuchy019.classList.add('sisl_inactive');
            elOuchy019.classList.remove('sisl_active');
            elOuchy219.classList.add('sisl_inactive');
            elOuchy219.classList.remove('sisl_active');
            elOuchy319.classList.add('sisl_inactive');
            elOuchy319.classList.remove('sisl_active');
            elOuchy019.innerHTML = '<span class="sisl_indicatif">019</span> - Radio au local</div>';
            elOuchy219.innerHTML = '<span class="sisl_indicatif">219</span> - Vedette</div>';
            elOuchy319.innerHTML = '<span class="sisl_indicatif">319</span> - Dauphin</div>';
            elRadioList = document.getElementById('radio_list');
            elRadioList.classList.add('sisl_inactive');
            elRadioList.classList.remove('sisl_active');
            elRadioList.innerHTML = "<p>Aucune radio n'est active.</p>";

            // Request list des radios
            const deviceId = "1AC2F8D0F901"; // Le device "Raspery pi sauvetage local comite"
            const token = "abcfef0123456789abcfef0123456789abcfef0123456789abcfef0123456789"; // token idem 
            const timestamp = Math.floor(Date.now() / 1000).toString();
            const payload = JSON.stringify({
                "actionkey": "radiosisl"
            });
            const toSign = deviceId + payload;
            const sig = await hmacSHA256Hex(token, toSign);

            const res = await fetch("/souchy_direct/souchy_m5stack_api.php", {
                method: "POST",
                body: JSON.stringify({
                    "actionkey": "radiosisl"
                }),
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-Device-Id": deviceId,
                    "X-Timestamp": timestamp,
                    "X-Signature": sig

                }
            });
            const resData = await res.json();
            let radioListHtml = "";
            let itemHTML = "";
            for (const [index, obj] of resData.entries()) {
                const indicatif = (obj.name || '').slice(1);
                if (obj.isBase) {
                    itemHTML = `<div class="sisl_base"><span class="sisl_indicatif">${indicatif}</span> - ${obj.label}</div>`;
                } else {
                    itemHTML = `<div class="sisl_unite"><span class="sisl_indicatif">${indicatif}</span> - ${obj.distance_km.toFixed(2)} Km</div>`;
                }
                // cas particulier Ouchy, on liste indépendement du reste
                if (obj.name == 'L019') {
                    elOuchy019.classList.remove('sisl_inactive');
                    elOuchy019.classList.add('sisl_active');
                } else if (obj.name == 'L219') {
                    elOuchy219.classList.remove('sisl_inactive');
                    elOuchy219.classList.add('sisl_active');
                    elOuchy219.innerHTML = `<span class="sisl_indicatif">219</span> - Vedette à ${obj.distance_km.toFixed(2)} Km</div>`;
                } else if (obj.name == 'L319') {
                    elOuchy319.classList.remove('sisl_inactive');
                    elOuchy319.classList.add('sisl_active');
                    elOuchy319.innerHTML = `<span class="sisl_indicatif">319</span> - Dauphin à ${obj.distance_km.toFixed(2)} Km</div>`;
                } else {
                    radioListHtml += itemHTML;
                }
            };
            if (radioListHtml.length > 0) {
                elRadioList.innerHTML = radioListHtml;
                elRadioList.classList.remove('sisl_inactive');
                elRadioList.classList.add('sisl_active');
            }

        }

        // Helper: HMAC-SHA256 → hex (Web Crypto API)
        async function hmacSHA256Hex(keyString, message) {
            const enc = new TextEncoder();
            const keyData = enc.encode(keyString);
            const msgData = enc.encode(message);

            const cryptoKey = await crypto.subtle.importKey(
                "raw",
                keyData, {
                    name: "HMAC",
                    hash: "SHA-256"
                },
                false,
                ["sign"]
            );

            const sigBuf = await crypto.subtle.sign("HMAC", cryptoKey, msgData);
            const bytes = new Uint8Array(sigBuf);
            let hex = "";
            for (const b of bytes) hex += b.toString(16).padStart(2, "0");
            return hex;
        }

        document.addEventListener("DOMContentLoaded", function() {
            const doRefresh = () => refresh_radioSISL();
            doRefresh();
            const id = setInterval(doRefresh, 15000); // doRefresh toutes les 15 s
        });
    </script>

</head>

<body>

    <div class="backgroundpage">
        <!-- Header -->
        <header class="header">
            <img src="https://sisl.ch/images/logoSISL.svg" />
            <h1 class="title">Carte SISL & radios Lemano actives</h1>
            <img src="https://sauvetage-ouchy.ch/static-images/logo_sauvetage/logo_sauvetage_white.svg" />
        </header>

        <main class="content-grid">
            <!-- carte sisl -->
            <div class="A1">
                <iframe id="frame" class="sisl_map" referrerpolicy="no-referrer" allow=" geolocation; " src="https://map.sisl.ch"></iframe>
            </div>
            <!-- Colonne droite : Infos -->
            <!-- Status Ouchy -->
            <div class="B1">
                <h2><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="height:25px; margin-bottom: -2px; margin-right:10px;">
                        <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                        <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                    </svg> Ouchy</h2>
                <div id="sisl_019" class="sisl_base"><span class="sisl_indicatif">019</span> - Radio au local</div>
                <div id="sisl_219" class="sisl_base"><span class="sisl_indicatif">219</span> - Vedette</div>
                <div id="sisl_319" class="sisl_base"><span class="sisl_indicatif">319</span> - Dauphin</div>
            </div>
            <div class="B2">
                <h2><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="height:25px; margin-bottom: -4px; margin-right:10px;">
                        <path fill-rule="evenodd" d="M1.371 8.143c5.858-5.857 15.356-5.857 21.213 0a.75.75 0 0 1 0 1.061l-.53.53a.75.75 0 0 1-1.06 0c-4.98-4.979-13.053-4.979-18.032 0a.75.75 0 0 1-1.06 0l-.53-.53a.75.75 0 0 1 0-1.06Zm3.182 3.182c4.1-4.1 10.749-4.1 14.85 0a.75.75 0 0 1 0 1.061l-.53.53a.75.75 0 0 1-1.062 0 8.25 8.25 0 0 0-11.667 0 .75.75 0 0 1-1.06 0l-.53-.53a.75.75 0 0 1 0-1.06Zm3.204 3.182a6 6 0 0 1 8.486 0 .75.75 0 0 1 0 1.061l-.53.53a.75.75 0 0 1-1.061 0 3.75 3.75 0 0 0-5.304 0 .75.75 0 0 1-1.06 0l-.53-.53a.75.75 0 0 1 0-1.06Zm3.182 3.182a1.5 1.5 0 0 1 2.122 0 .75.75 0 0 1 0 1.061l-.53.53a.75.75 0 0 1-1.061 0l-.53-.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    Radios actives</h2>
                <div id="radio_list"></div>
            </div>
        </main>
    </div>
</body>