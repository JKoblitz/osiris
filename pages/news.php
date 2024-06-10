<?php

/**
 * Page to see latest changes
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /new-stuff
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */
?>

<style>
    code.code {
        font-size: 1em;
    }
    h2 {
        /* font-family: 'Menlo', 'Courier New', Courier, monospace; */
        color: var(--secondary-color);
    }

</style>

<?php if (isset($USER) && !empty($USER)) {
    if (!isset($USER['lastversion']) || $USER['lastversion'] !== OSIRIS_VERSION) {
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