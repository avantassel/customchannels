<?php
// Due to CORS policy with this endpoint need to do this from PHP
$recentlyPlayed = file_get_contents("https://ensemble.customchannels.net/api/channels/222/recent");
header('Content-Type: application/json');
echo $recentlyPlayed ?? "{}";
?>