<?php

/* ****************************************
 * API accessible sans authentification utilisateur
 * Réservée au kiosk
 * 
 * Un deviceID recu en cookie ou en header identifie le device concerné
 **************************************** */

require_once(__DIR__ . '/../wp-load.php');
require_once(__DIR__ . '/../wp-content/plugins/sauvetage-ouchy/souchy_core.php');


// Clés d'accès à l'api voulu
const API_GET_CONFIGURATION = 'config'; // Retourne la cofiguration des pagess du kiosk
// const M5_KEY_CALENDAR019 =  'calendar019';
// const M5_KEY_RADIOSISL =  'radiosisl';
// const M5_KEY_APPAIRING =  'pair';
// const M5_KEY_USERINFO = 'userinfo';


// Récupération de la configuration (Device, token, playlist,..)
$root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$configfile = file_get_contents($root .'/kiosk/kiosk_config.json');
if(!$configfile) {
    http_response_code(404); // Not found
    echo json_encode(["success"=>false,"error" => "configuration missing"]);
     exit;
}
$config=json_decode($configfile);

// Vérification que le device est connu 
$device_id = $_SERVER['HTTP_X_DEVICE_ID'] ?? $_COOKIE["kiosk_device_id"] ?? '';
if (!$device_id) {
    http_response_code(403); // Accès interdit
    echo json_encode(["success"=>false,'error' => 'Unknown device.']);
    souchy::log_activity('KIOSK','Tentative de Start du kiosk - Unknown device '.$device_id );
    exit;
}
$devices = $config->devices;
$device = current(array_filter($devices, fn($o) => $o->id == $device_id)) ?: null;
if (empty($device)) {
    http_response_code(403); // Accès interdit
    echo json_encode(["success"=>false,'error' => 'Unauthorised device.']);
    souchy::log_activity('KIOSK','Tentative de Start du kiosk - Unauthorised device ' );
    exit;
}
 souchy::log_activity('KIOSK','Start du kiosk '.$device_id  );

// Vérification des headers requis 
$timestamp = $_SERVER['HTTP_X_TIMESTAMP'] ?? '';
if ( !$timestamp ) {
    http_response_code(400);
    echo json_encode(["success"=>false,'error' => 'missing headers']);
    exit;
}
// Anti-replay: timestamp ±3s
$elapsed=abs(time() - (int)($timestamp/1000));
if ($elapsed  > 3) {
    http_response_code(400);
    echo json_encode(["success"=>false,'error' => 'stale timestamp '.$elapsed." ".time()]);
    exit;
}


// On s'attend a avoir  un JSON sur cet API 
$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(array("success"=>false,"error" => json_last_error_msg()));
    exit;
}
$actionkey = $data['actionkey'] ?? '';


// Execution de l'action 
switch ($actionkey) {
    case API_GET_CONFIGURATION:
        echo json_encode(array("success"=>true,"config" => array("playlist"=>$config->playlist)));
        break;
    default:
        http_response_code(403); // Accès interdit
        echo json_encode(["success"=>false,'error' => 'Unknown API']);
        break;
}
