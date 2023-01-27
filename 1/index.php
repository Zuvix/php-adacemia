<?php

function get_current_time()
{
    $now = new DateTime();
    return $now->format('Y-m-d H:i:s');
}

function log_arrival_to_file($file, $content, $meskanie)
{
    if ($meskanie) {
        $content = $content . " meskanie";
    }
    $handle = fopen($file, 'a');
    fwrite($handle, $content . "\n");
    fclose($handle);
}

function get_logs($file)
{
    if (file_exists($file)) {
        $handle = fopen($file, 'r');
        $content = fread($handle, filesize($file));
        echo nl2br($content);
        fclose($handle);
    }
}

//Fake time is for testing purposes
function log_student($file, $fake_time = false)
{
    $time_of_arival = $fake_time ? $fake_time : get_current_time();
    $date = strtotime($time_of_arival);
    $hour = date('H', $date);

    if ($hour >= 20 && $hour < 24) {
        die("Nemôžem zapísať príchod, lebo je z rozsahu 20-24h.");
    }

    $meskanie = $hour >= 8 ? true : false;
    log_arrival_to_file($file, $time_of_arival, $meskanie);
}

//Main part
$file = 'log.txt';

//Log fake student arival
log_student($file, '2023-01-27 7:51:32');

//Log real time student arival
log_student($file);

get_logs($file);
