<?php

/**
 * Page for admin dashboard for features settings
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /admin/features
 *
 * @package OSIRIS
 * @since 1.2.3
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

?>


<?php
    include BASEPATH . "/components/admin-nav.php";
?>

<h1>
    <?=lang('Features', 'Funktionen')?>
</h1>

<form action="#" method="post" id="role-form">

    <?php
    $features = $Settings->get('features');
    ?>


    <div class="box">
        <div class="content">
            <h2 class="title">
                <?=lang('Guest forms', 'Gäste-Formulare')?>
            </h2>

            <div class="form-group">
                <span>
                <?= lang('Active', 'Aktivieren') ?>
                </span>
                <?php
                    $active = $Settings->featureActive('guests');
                ?>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="guests-true" value="true" name="features[guests][active]" <?= $active ? 'checked' : '' ?>>
                    <label for="guests-true">ja</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="guests-false" value="false" name="features[guests][active]" <?= $active ? '' : 'checked' ?>>
                    <label for="guests-false">nein</label>
                </div>
            </div>

        </div>
    </div>

    <div class="box">
        <div class="content">
            <h2 class="title">
                <?=lang('IDA Integration')?>
            </h2>

            <div class="form-group">
                <span>
                <?= lang('Active', 'Aktivieren') ?>
                </span>
                <?php
                    $active = $Settings->featureActive('ida');
                ?>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="ida-true" value="true" name="features[ida][active]" <?= $active ? 'checked' : '' ?>>
                    <label for="ida-true">ja</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="ida-false" value="false" name="features[ida][active]" <?= $active ? '' : 'checked' ?>>
                    <label for="ida-false">nein</label>
                </div>
            </div>

        </div>
    </div>

    <div class="box">
        <div class="content">
            <h2 class="title">
                <?=lang('Projects', 'Projekte')?>
            </h2>

            <div class="form-group">
                <span>
                <?= lang('Active', 'Aktivieren') ?>
                </span>
                <?php
                    $active = $Settings->featureActive('projects');
                ?>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="projects-true" value="true" name="features[projects][active]" <?= $active ? 'checked' : '' ?>>
                    <label for="projects-true">ja</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="projects-false" value="false" name="features[projects][active]" <?= $active ? '' : 'checked' ?>>
                    <label for="projects-false">nein</label>
                </div>
            </div>


            <div class="form-group">
                <label for="cordis"><a href="https://cordis.europa.eu/user/api/en" target="_blank" rel="noopener noreferrer">CORDIS</a> API-Key (EU)</label>
                <input type="text" class="form-control" name="features[projects][cordis-api]" id="cordis" value="<?=$features['projects']['cordis-api'] ?? ''?>">
            </div>
            <div class="form-group">
                <label for="gepris">GEPRIS API-Key (DFG)</label>
                <input type="text" class="form-control" name="features[projects][gepris-api]" id="gepris" value="<?=$features['projects']['gepris-api'] ?? ''?>">
            </div>
        </div>
    </div>

    <button class="btn success">
        <i class="ph ph-floppy-disk"></i>
        Save
    </button>


</form>