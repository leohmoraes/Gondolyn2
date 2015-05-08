<?php

    /*
    |--------------------------------------------------------------------------
    | Module Menu
    |--------------------------------------------------------------------------
    */

?>

@if (Session::get('logged_in'))
<li><a href="<?= URL::to('sample'); ?>"><span class="fa fa-gear"></span> Sample Module</a></li>
@endif