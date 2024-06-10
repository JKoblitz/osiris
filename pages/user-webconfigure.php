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
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

$scientist = $data;

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