<?php

/**
 * Page for admin dashboard for features settings
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link /admin/features
 *
 * @package OSIRIS
 * @since 1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

?>

<h1>
    <?= lang('Features', 'Funktionen') ?>
</h1>

<style>
    .table td.description {
        color: var(--muted-color);
        padding-top: 0;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    .with-description td {
        border-bottom: 0;
    }
</style>

<form action="<?= ROOTPATH ?>/crud/admin/features" method="post" id="role-form">

    <?php
    $features = $Settings->get('features');
    ?>


    <table class="table my-20">
        <tbody>
            <tr>
                <th colspan="2">
                    <?= lang('General features', 'Generelle Funktionen') ?>
                </th>
            </tr>
            <tr class="with-description">
                <td class="pl-20">
                    <?= lang('Coins', 'Coins') ?>
                </td>
                <?php
                $coins = $Settings->featureEnabled('coins');
                ?>
                <td class="">

                    <div class=" custom-radio d-inline-block ml-10">
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
                <td colspan="2" class="description">
                    <small> Coins werden nirgendwo gespeichert, sondern on-demand berechnet. Wenn ihr Coins global ausschaltet, werden sie also gar nicht erst berechnet und nirgendwo gezeigt.</small>
                </td>
            </tr>

            <tr class="">
                <td class="pl-20">
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


            <tr class="with-description">
                <td class="pl-20">
                    <?= lang('User profile metrics', 'Metriken im Nutzerprofil') ?>
                </td>
                <?php
                $user_metrics = $Settings->featureEnabled('user-metrics');
                ?>
                <td class="">
                    <div class=" custom-radio d-inline-block ml-10">
                    <input type="radio" id="user-metrics-true" value="1" name="values[user-metrics]" <?= $user_metrics ? 'checked' : '' ?>>
                    <label for="user-metrics-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="user-metrics-false" value="0" name="values[user-metrics]" <?= $user_metrics ? '' : 'checked' ?>>
                        <label for="user-metrics-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="description">
                    <small> Wenn diese Funktion ausgeschaltet wird, sind Nutzermetriken (Graphen) nur noch auf der eigenen Profilseite sichtbar.</small>
                </td>
            </tr>

            <tr class="with-description">
                <td class="pl-20">
                    <?= lang('Profile images', 'Profilbilder der Nutzenden') ?>
                </td>
                <?php
                $db_pictures = $Settings->featureEnabled('db_pictures');
                ?>
                <td class="">

                    <div class=" custom-radio d-inline-block ml-10">
                    <input type="radio" id="db_pictures-true" value="1" name="values[db_pictures]" <?= $db_pictures ? 'checked' : '' ?>>
                    <label for="db_pictures-true"><?= lang('Save in database', 'In Datenbank speichern') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="db_pictures-false" value="0" name="values[db_pictures]" <?= $db_pictures ? '' : 'checked' ?>>
                        <label for="db_pictures-false"><?= lang('Save in file system', 'Im Dateisystem speichern') ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="description">
                    <small>
                        <?= lang(
                            'Saving the profile pictures in the database is recommended if the pictures are maintained exclusively via OSIRIS. If the images are saved in the file system, they can be uploaded more easily (into the folder <code>/img/users</code>) and, for example, updated automatically. However, they must then have the user name as the name and be in JPEG format!',
                            'Die Profilbilder in der Datenbank zu speichern wird empfohlen, wenn die Bilder ausschließlich über OSIRIS gepflegt werden. Wenn die Bilder im Dateisystem gespeichert werden, kann man sie leichter anders hochladen (in den Ordner <code>/img/users</code>) und z.B. automatisch aktualisieren. Sie müssen dann aber den Username als Namen haben und im JPEG-Format sein!'
                        ) ?>
                    </small>
                </td>
            </tr>

<?php if (strtoupper(USER_MANAGEMENT) == 'AUTH') { ?>
    <tr>
                <td class="pl-20">
                    <?= lang('LDAP user synchronization', 'LDAP-Nutzersynchronisierung') ?>
                </td>
                <?php
                $sync = $Settings->featureEnabled('ldap-sync');
                ?>
                <td>
                    <div class="form-">
                        <label for="ldap-sync-blacklist" class=""><?= lang('Username Blacklist (separated by comma)', 'Username-Blacklist (Komma-getrennt)') ?></label>
                        <textarea class="form-control small" name="general[ldap-sync-blacklist]" id="ldap-sync-blacklist"><?= $Settings->get('ldap-sync-blacklist') ?></textarea>
                    </div>
                    <div class="form-">
                        <label for="ldap-sync-whitelist" class=""><?= lang('Username whitelist (separated by comma)', 'Username-Whitelist (Komma-getrennt)') ?></label>
                        <textarea class="form-control small" name="general[ldap-sync-whitelist]" id="ldap-sync-whitelist"><?= $Settings->get('ldap-sync-whitelist') ?></textarea>
                    </div>
                </td>
            </tr>
<?php } ?>


            <tr>
                <th colspan="2">
                    <?= lang('Guests', 'Gäste') ?>
                </th>
            </tr>
            <tr>
                <td class="pl-20">
                    <?= lang('Guests can be registered in OSIRIS', 'Gäste können in OSIRIS angemeldet werden') ?>
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
                <td class="pl-20">
                    <?= lang('External guest forms to complete registration', 'Externe Gästeformulare, um die Registration abzuschließen') ?>
                </td>
                <?php
                $guests = $Settings->featureEnabled('guest-forms');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="guest-forms-true" value="1" name="values[guest-forms]" <?= $guests ? 'checked' : '' ?>>
                        <label for="guest-forms-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="guest-forms-false" value="0" name="values[guest-forms]" <?= $guests ? '' : 'checked' ?>>
                        <label for="guest-forms-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                    <div class="row mt-10">
                        <label for="guest-forms-server" class="w-150 col flex-reset"><?= lang('Server address', 'Server-Adresse') ?></label>
                        <input type="text" class="form-control small col" name="general[guest-forms-server]" id="guest-forms-server" value="<?= $Settings->get('guest-forms-server') ?>">
                    </div>
                    <div class="row mt-10">
                        <label for="guest-forms-secret-key" class="w-150 col flex-reset"><?= lang('Secret key') ?></label>
                        <input type="text" class="form-control small col" name="general[guest-forms-secret-key]" id="guest-forms-secret-key" value="<?= $Settings->get('guest-forms-secret-key') ?>">
                    </div>
                </td>
            </tr>


            <tr>
                <th colspan="2">
                    <?= lang('Reporting', 'Berichterstattung') ?>
                </th>
            </tr>
            <tr>
                <td class="pl-20">
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
                <th colspan="2">
                    <?= lang('Projects', 'Projekte') ?>
                </th>
            </tr>
            <tr>
                <td class="pl-20">
                    <?= lang('Projects in OSIRIS', 'Projekte in OSIRIS') ?>
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

            <tr>
                <th colspan="2">
                    <?= lang('Concepts', 'Konzepte') ?>
                </th>
            </tr>
            <tr>
                <td class="pl-20">
                    <?= lang('Show concepts', 'Zeige Konzepte') ?>
                </td>
                <?php
                $concepts = $Settings->featureEnabled('concepts');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="concepts-true" value="1" name="values[concepts]" <?= $concepts ? 'checked' : '' ?>>
                        <label for="concepts-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="concepts-false" value="0" name="values[concepts]" <?= $concepts ? '' : 'checked' ?>>
                        <label for="concepts-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>
 <tr>
                <th colspan="2">
                    <?= lang('Word cloud') ?>
                </th>
            </tr>
            <tr>
                <td class="pl-20">
                    <?= lang('Show word clouds in user profiles', 'Zeige Word Clouds in Nutzerprofilen') ?>
                </td>
                <?php
                $wordcloud = $Settings->featureEnabled('wordcloud');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="wordcloud-true" value="1" name="values[wordcloud]" <?= $wordcloud ? 'checked' : '' ?>>
                        <label for="wordcloud-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="wordcloud-false" value="0" name="values[wordcloud]" <?= $wordcloud ? '' : 'checked' ?>>
                        <label for="wordcloud-false"><?= lang('disabled', 'deaktiviert') ?></label>
                    </div>
                </td>
            </tr>


            <tr>
                <th colspan="2">
                    <?= lang('OSIRIS Portfolio') ?>
                </th>
            </tr>
            <tr>
                <td class="pl-20">
                    <?= lang('Portal previews and API', 'Portal-Vorschau und API') ?>
                </td>
                <?php
                $portal = $Settings->featureEnabled('portal');
                ?>
                <td>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="portal-true" value="1" name="values[portal]" <?= $portal ? 'checked' : '' ?>>
                        <label for="portal-true"><?= lang('enabled', 'aktiviert') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" id="portal-false" value="0" name="values[portal]" <?= $portal ? '' : 'checked' ?>>
                        <label for="portal-false"><?= lang('disabled', 'deaktiviert') ?></label>
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


<?php if (strtoupper(USER_MANAGEMENT) == 'AUTH') { ?>
    <a href="<?=ROOTPATH?>/synchronize-users" class="btn">Synchronize LDAP Users</a>
<?php } ?>