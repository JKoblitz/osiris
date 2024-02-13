<?php

/**
 * Page to edit user web visibility
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /user/visibility/<username>
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$scientist = $data;

$img_exist = file_exists(BASEPATH . "/img/users/$user.jpg");
if ($img_exist) {
    $img = ROOTPATH . "/img/users/$user.jpg";
} else {
    // standard picture
    $img = ROOTPATH . "/img/no-photo.png";
}
?>
<h1 class="mt-0">
    <i class="ph ph-eye"></i>
    <?= $data['name'] ?>
</h1>

<h2 class="subtitle">
    <?= lang('Configure web profile', 'Webprofil konfigurieren') ?>
</h2>



<table class="table">
    <div class="tbody">
        <tr>
            <td>Bild</td>
        </tr>
        <tr>
            <td>Telefon</td>
        </tr>
        <tr>
            <td>Mail</td>
        </tr>
        <tr>
            <td>Position</td>
        </tr>
        <tr>
            <td>Forschungsinteressen</td>
        </tr>
        <tr>
            <td>CV</td>
        </tr>
        <tr>
            <td>Gremien</td>
        </tr>
        <tr>
            <td>Publikationen</td>
        </tr>
        <tr>
            <td>Vorträge & Poster</td>
        </tr>
    </div>
</table>