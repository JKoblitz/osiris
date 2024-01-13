<?php

/**
 * Page to see latest changes
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /new-stuff
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<style>
    code.code {
        font-size: 1.4rem;
    }
</style>

<?php if (isset($USER) && !empty($USER)) {
    if ($USER['lastversion'] !== OSIRIS_VERSION) {
        $updateResult = $osiris->persons->updateOne(
            ['username' => $_SESSION['username']],
            ['$set' => ['lastversion' => OSIRIS_VERSION]]
        );
    }
} ?>


<div class='container'>
    <div class="content">
        <?php
        $text = file_get_contents(BASEPATH . "/news.md");
        $parsedown = new Parsedown;
        echo $parsedown->text($text);
        ?>
    </div>
</div>