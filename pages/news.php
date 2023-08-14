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

<div class='container'>
    <h1>OSIRIS</h1>
    <h4 class="text-osiris font-weight-normal mt-0">The Open, Simple and Integrated Research Information System</h4>

    <p>
        <?= lang(
            'OSIRIS is my attempt to create a light-weight and open-source research information system; made-to-measure the needs of smaller institutes such as the DSMZ. Thereby, usability is my key concern.',
            'OSIRIS ist mein Ansatz, ein leichtgewichtiges Open-Source Forschungsinformationssystem zu schaffen; maßgeschneidert auf die Bedürfnisse kleinerer Institute wie der DSMZ. Dabei ist die Nutzerfreundlichkeit der Seite mein größtes Anliegen.'
        ) ?>
    </p>

    <blockquote class="mb-20 alert font-size-16" style="border-left: 5px solid var(--osiris-color);">
        <p>
            A user interface is like a joke. If you have to explain it, it’s not that good”.
        </p>
        <em>— Martin Leblanc</em>
    </blockquote>

    <hr>
    <?php
    $text = file_get_contents(BASEPATH . "/news.md");
    $parsedown = new Parsedown;
    echo $parsedown->text($text);
    ?>
</div>