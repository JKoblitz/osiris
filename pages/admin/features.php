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
 * @since 1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

?>

<h1>
    <?= lang('Features', 'Funktionen') ?>
</h1>

<form action="<?=ROOTPATH?>/crud/admin/features" method="post" id="role-form">

    <?php
    $features = $Settings->get('features');
    ?>


    <table class="table my-20">
        <tbody>

            <tr>
                <td>
                    <?= lang('Coins', 'Coins') ?>
                </td>
                <?php
                $coins = $Settings->featureEnabled('coins');
                ?>
                <td>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="coins-true" value="1" name="values[coins]" <?= $coins ? 'checked' : '' ?>>
                        <label for="coins-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="coins-false" value="0" name="values[coins]" <?= $coins ? '' : 'checked' ?>>
                        <label for="coins-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>


            <tr>
                <td>
                    <?= lang('Achievements', 'Errungenschaften') ?>
                </td>
                <?php
                $achievements = $Settings->featureEnabled('achievements');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="achievements-true" value="1" name="values[achievements]" <?= $achievements ? 'checked' : '' ?>>
                        <label for="achievements-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="achievements-false" value="0" name="values[achievements]" <?= $achievements ? '' : 'checked' ?>>
                        <label for="achievements-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>


            <tr>
                <td>
                    <?= lang('User profile metrics', 'Metriken im Nutzerprofil') ?>
                </td>
                <?php
                $user_metrics = $Settings->featureEnabled('user-metrics');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="user-metrics-true" value="1" name="values[user-metrics]" <?= $user_metrics ? 'checked' : '' ?>>
                        <label for="user-metrics-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="user-metrics-false" value="0" name="values[user-metrics]" <?= $user_metrics ? '' : 'checked' ?>>
                        <label for="user-metrics-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                    <span class="font-size-12 d-block text-muted">
                        Wenn diese Funktion ausgeschaltet wird, sind Nutzermetriken (Graphen) nur noch auf der eigenen Profilseite sichtbar.
                    </span>
                </td>
            </tr>



            <tr>
                <td>
                    <?= lang('Guest forms', 'Gäste-Formulare') ?>
                </td>
                <?php
                $guests = $Settings->featureEnabled('guests');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="guests-true" value="1" name="values[guests]" <?= $guests ? 'checked' : '' ?>>
                        <label for="guests-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="guests-false" value="0" name="values[guests]" <?= $guests ? '' : 'checked' ?>>
                        <label for="guests-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>


            <tr>
                <td>
                    <?= lang('IDA Integration') ?>
                </td>
                <?php
                $ida = $Settings->featureEnabled('ida');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="ida-true" value="1" name="values[ida]" <?= $ida ? 'checked' : '' ?>>
                        <label for="ida-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="ida-false" value="0" name="values[ida]" <?= $ida ? '' : 'checked' ?>>
                        <label for="ida-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>

            
            <tr>
                <td>
                    <?= lang('Projects', 'Projekte') ?>
                </td>
                <?php
                $projects = $Settings->featureEnabled('projects');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="projects-true" value="1" name="values[projects]" <?= $projects ? 'checked' : '' ?>>
                        <label for="projects-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="projects-false" value="0" name="values[projects]" <?= $projects ? '' : 'checked' ?>>
                        <label for="projects-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>




        </tbody>
    </table>



    <button class="btn success">
        <i class="ph ph-floppy-disk"></i>
        Save
    </button>


</form>