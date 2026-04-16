<?php 
// Page affiche en cas d'erreur
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="kiosk_common.css" rel="stylesheet">
</head>

<body style="color:white;margin-top:100px; font-size:40px;">
     <p> Erreur inattendue ! </p>
    <?php if(isset($_GET["error"])) echo $_GET["error"]; ?>

          <p> Sinon merci de m'envoyer un message (André C.) </p> 
</body>