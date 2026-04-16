<?php

/** 
 * Kiosk
 * Page météo: affiche la météo de la station
 */

require_once(__DIR__ . '/../../wp-load.php');
$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$lastmodjs = filemtime($root . '/kiosk/kiosk.js') + filemtime($root . '/wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.js');
$lastmodcss = filemtime($root . '/kiosk/kiosk_common.css') + filemtime($root . '/wp-content/plugins/sauvetage-ouchy/calendar/souchy_calendar.css');
 
require_once(__DIR__ . '/../../wp-content/plugins/souchy-weather-station/souchy-weather-station.php');
?>

<!DOCTYPE html>
<html lang="fr">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<head>
    <link href="/kiosk/kiosk_common.css?ts=<?= $lastmodcss ?>" rel="stylesheet">

    <script src='/wp-content/plugins/souchy-weather-station/vendors/RGraph/libraries/RGraph.common.core.js'></script>
    <script src='/wp-content/plugins/souchy-weather-station/vendors/RGraph/libraries/RGraph.gauge.js'></script>
    <script src='/wp-content/plugins/souchy-weather-station/vendors/RGraph/libraries/RGraph.odo.js'></script>
    <script src='/wp-content/plugins/souchy-weather-station/vendors/RGraph/libraries/RGraph.common.dynamic.js'></script>
    <script src='/wp-content/plugins/souchy-weather-station/vendors/RGraph/libraries/RGraph.common.effects.js'></script>
    <script src='/wp-content/plugins/souchy-weather-station/vendors/RGraph/libraries/RGraph.meter.js' )></script>
    <script src='/wp-content/plugins/souchy-weather-station/vendors/RGraph/libraries/RGraph.drawing.image.js'></script>
    <script src='/wp-content/plugins/souchy-weather-station/souchy-weather-station.js'></script>
    <link href='/wp-content/plugins/souchy-weather-station/souchy-weather-station.css' rel="stylesheet">




    <style>
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
    </style>
</head>

<body>
    <div class="backgroundpage">

        <!-- Header -->
        <header class="header">
            <img src="https://sauvetage-ouchy.ch/static-images/logo_sauvetage/logo_sauvetage_white.svg" />
            <h1 class="title">Météo du Sauvetage d'Ouchy </h1>
        </header>

        <main class="content-grid">
            <!-- Station Ouchy -->
            <div class="A1">
                <p>Relevé de la station le <span  id="ts-releve"></span></p><br>
                <div class="gauges_container">
                    <div class="gauge_item">
                        <canvas id="vent_vitesse" width="300" height="300" style="cursor: default;"></canvas>
                        <div id="vent_html" class="gauge_item_desc"></div>
                    </div>
                    <div class="gauge_item" style="margin-top:20px;">
                        <div class="meter_ventdirection_border">
                            <canvas id="vent_direction" width="240" height="240" style="cursor: default;padding-left:1px;"></canvas>
                        </div>
                        <div id="vent_direction_html" class="gauge_item_desc" style="  margin-top: 21px;"></div>
                    </div>
                    <div class="gauge_item">
                        <canvas id="temperature" width="300" height="300" style="cursor: default;"></canvas>
                        <div id="temperature_html" class="gauge_item_desc"></div>
                    </div>
                    <div class="gauge_item">
                        <canvas id="pression" width="300" height="300" style="cursor: default;"></canvas>
                        <div id="pression_html" class="gauge_item_desc"></div>
                    </div>
                    <div class="gauge_item">
                        <canvas id="humidite" width="300" height="300" style="cursor: default;"></canvas>
                        <div id="humidite_html" class="gauge_item_desc"></div>
                    </div>
                    <div class="gauge_item">
                        <canvas id="soleil" width="300" height="300" style="cursor: default;"></canvas>
                        <div id="soleil_html" class="gauge_item_desc"></div>
                    </div>
                </div>'

            </div>
            <!-- Météo suisse-->
            <div class="B1">
                <?php
                date_default_timezone_set('Europe/Paris');
                  $data = meteosuisse_data(null, '1007');
                        $jour_numero = date("w", $data["prevision_npa"]->currentWeather->time / 1000);
                        $jours_abreges = array("di", "lu", "ma", "me", "je", "ve", "sa");
                        $jour_abrege = $jours_abreges[$jour_numero];
                       
                echo "<p>Prévision Météosuisse à Ouchy à " . date("H:i", $data["prevision_npa"]->currentWeather->time / 1000) . "</p><br>";
                ?>
                <div class="wforcast_border">
                    <div class="wforcast_container">
                        <?php

                       $html = '';
                        foreach ($data["prevision_npa"]->forecast as  $value) {
                            $html .= ' 
                                <div class="wforcast_item">
                                    <div class="wforcast_item_day">' . $jours_abreges[$jour_numero] . '</div>
                                    <div class="wforcast_item_icon"><img src="/static-images/iconmeteo/' . $value->iconDayV2 . '.svg"></div>
                                    <div class="wforcast_item_temp">' . $value->temperatureMin . ' | ' . $value->temperatureMax . ' °C</div>
                                    <div class="wforcast_item_pluie">' . $value->precipitation . ' mm</div>
                                </div>';
                            $jour_numero += 1;
                            if ($jour_numero > 6) $jour_numero = 0;
                        }
                        echo $html;
                        ?>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
                document.addEventListener("DOMContentLoaded", (event) => {
                    var gMeteo = new sws();
                    gMeteo.initDashboard();
                });
            </script>
            
</body>