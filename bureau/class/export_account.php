<?php

// EXPERIMENTAL : user data export.

include("config.php");

sem_release($alternc_sem);

$mem->su($id);

$dom->lock();
echo $dom->alternc_export();
echo $mail->alternc_export();
echo $aws->alternc_export();
$dom->unlock();

$mem->unsu();

?>