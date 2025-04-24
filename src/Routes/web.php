<?php


use Denason\Wikipedia\Facades\Wikipedia;

use Illuminate\Support\Facades\Route;

Route::get('wikipedia', function () {

    $infobox = wiki()->infoBox('iran');
    foreach ($infobox as $key => $info)
    {
        echo $key. '=' . $info. '<br/>';
}

});
