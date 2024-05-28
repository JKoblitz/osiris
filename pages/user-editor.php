<?php

/**
 * Page to edit user information
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /user/edit/<username>
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<h1 class="mt-0">
    <i class="ph ph-student"></i>
    <?= $data['name'] ?>
</h1>


<form action="<?= ROOTPATH ?>/crud/users/update/<?= $data['username'] ?>" method="post">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

    <p>
        <b>Username:</b> <?= $data['username'] ?? '' ?>
    </p>


    <fieldset>
        <legend><?= lang('Name and personal information', 'Name und persönliche Informationen') ?></legend>

        <div class="form-row row-eq-spacing">
            <div class="col-sm-2">
                <label for="academic_title">Title</label>
                <select name="values[academic_title]" id="academic_title" class="form-control">
                    <option value="" <?= $data['academic_title'] == '' ? 'selected' : '' ?>></option>
                    <option value="Dr." <?= $data['academic_title'] == 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                    <option value="Prof. Dr." <?= $data['academic_title'] == 'Prof. Dr.' ? 'selected' : '' ?>>Prof. Dr.</option>
                    <option value="PD Dr." <?= $data['academic_title'] == 'PD Dr.' ? 'selected' : '' ?>>PD Dr.</option>
                    <option value="Prof." <?= $data['academic_title'] == 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                    <option value="PD" <?= $data['academic_title'] == 'PD' ? 'selected' : '' ?>>PD</option>
                    <!-- <option value="Prof. Dr." <?= $data['academic_title'] == 'Prof. Dr.' ? 'selected' : '' ?>>Prof. Dr.</option> -->
                </select>
            </div>
            <div class="col-sm">
                <label for="first"><?= lang('First name', 'Vorname') ?></label>
                <input type="text" name="values[first]" id="first" class="form-control" value="<?= $data['first'] ?? '' ?>">
            </div>
            <div class="col-sm">
                <label for="last"><?= lang('Last name', 'Nachname') ?></label>
                <input type="text" name="values[last]" id="last" class="form-control" value="<?= $data['last'] ?? '' ?>">
            </div>
        </div>


        <?php
        if (!isset($data['names'])) {
            $names = [
                $data['formalname'],
                Document::abbreviateAuthor($data['last'], $data['first'], true, ' ')
            ];
        } else {
            $names = $data['names'];
        }
        ?>


        <div class="form-group">
            <label for="names" class=""><?= lang('Names for author matching', 'Namen für das Autoren-Matching') ?></label>

            <div class="">
                <?php foreach ($names as $n) { ?>
                    <div class="input-group sm d-inline-flex w-auto">
                        <input type="text" name="values[names][]" value="<?= $n ?>" required class="form-control">
                        <div class="input-group-append">
                            <a class="btn" onclick="$(this).closest('.input-group').remove();">×</a>
                        </div>
                    </div>
                <?php } ?>

                <button class="btn primary small ml-10" type="button" onclick="addName(event, this);">
                    <i class="ph ph-plus"></i> <?= lang('Add name', 'Füge Namen hinzu') ?>
                </button>
            </div>

            <script>
                function addName(evt, el) {
                    var group = $('<div class="input-group sm d-inline-flex w-auto"> ')
                    group.append('<input type="text" name="values[names][]" value="" required class="form-control">')
                    // var input = $()
                    var btn = $('<a class="btn">')
                    btn.on('click', function() {
                        $(this).closest('.input-group').remove();
                    })
                    btn.html('&times;')

                    group.append($('<div class="input-group-append">').append(btn))
                    // $(el).prepend(group);
                    $(group).insertBefore(el);
                }
            </script>
        </div>


        <div class="form-group">
            <span><?= lang('Gender', 'Geschlecht') ?>:</span>
            <?php
            $gender = $data['gender'] ?? 'n';
            ?>

            <div class="custom-radio d-inline-block ml-10">
                <input type="radio" name="values[gender]" id="gender-m" value="m" <?= $gender == 'm' ? 'checked' : '' ?>>
                <label for="gender-m"><?= lang('Male', 'Männlich') ?></label>
            </div>
            <div class="custom-radio d-inline-block ml-10">
                <input type="radio" name="values[gender]" id="gender-f" value="f" <?= $gender == 'f' ? 'checked' : '' ?>>
                <label for="gender-f"><?= lang('Female', 'Weiblich') ?></label>
            </div>
            <div class="custom-radio d-inline-block ml-10">
                <input type="radio" name="values[gender]" id="gender-d" value="d" <?= $gender == 'd' ? 'checked' : '' ?>>
                <label for="gender-d"><?= lang('Non-binary', 'Divers') ?></label>
            </div>
            <div class="custom-radio d-inline-block ml-10">
                <input type="radio" name="values[gender]" id="gender-n" value="n" <?= $gender == 'n' ? 'checked' : '' ?>>
                <label for="gender-n"><?= lang('Not specified', 'Nicht angegeben') ?></label>
            </div>

        </div>

    </fieldset>



    <fieldset>
        <legend><?= lang('Organisational unit', 'Organisationseinheiten') ?></legend>

        <?php
        $tree = $Groups->getHirarchyTree();
        $depts = DB::doc2Arr($data['depts'] ?? []);
        ?>
        <div class="form-group">
            <?= lang('Select multiple with <kbd>Ctrl</kbd>.', 'Wähle mehrere mit <kbd>Strg</kbd>.') ?>

            <select name="values[depts][]" id="dept" class="form-control" multiple="multiple" size="5">
                <option value="">Unknown</option>
                <?php
                foreach ($tree as $d => $dept) { ?>
                    <option value="<?= $d ?>" <?= (in_array($d, $depts)) == $d ? 'selected' : '' ?>><?= $dept ?></option>
                <?php } ?>
            </select>

        </div>

    </fieldset>


    <fieldset>
        <legend><?= lang('Contact', 'Kontakt') ?></legend>
        <div class="form-row row-eq-spacing">

            <div class="col-sm-4">
                <label for="telephone"><?= lang('Telephone', 'Telefon') ?></label>
                <input type="text" name="values[telephone]" id="telephone" class="form-control" value="<?= $data['telephone'] ?? '' ?>">
            </div>

            <div class="col-sm-4">
                <label for="mail">Mail</label>
                <input type="text" name="values[mail]" id="mail" class="form-control" value="<?= $data['mail'] ?? '' ?>">
            </div>

            <div class="col-sm-4">
                <label for="orcid">ORCID</label>
                <input type="text" name="values[orcid]" id="orcid" class="form-control" value="<?= $data['orcid'] ?? '' ?>">
            </div>
        </div>

        <div class="form-row row-eq-spacing mb-10">
            <div class="col-sm-4">
                <label for="twitter">Twitter</label>
                <input type="text" name="values[twitter]" id="twitter" class="form-control" value="<?= $data['twitter'] ?? '' ?>">
            </div>

            <div class="col-sm-4">
                <label for="researchgate">ResearchGate Handle</label>
                <input type="text" name="values[researchgate]" id="researchgate" class="form-control" value="<?= $data['researchgate'] ?? '' ?>">
            </div>

            <div class="col-sm-4">
                <label for="google_scholar">Google Scholar ID</label>
                <input type="text" name="values[google_scholar]" id="google_scholar" class="form-control" value="<?= $data['google_scholar'] ?? '' ?>">
                <small class="text-muted">
                    <?= lang('Not the URL! Only the bold part: https://scholar.google.com/citations?user=<b>2G1YzvwAAAAJ</b>&hl=de ', 'Nicht die URL! Nur der fettgedruckte Teil: https://scholar.google.com/citations?user=<b>2G1YzvwAAAAJ</b>&hl=de') ?>
                </small>
            </div>
        </div>

    </fieldset>

    <?php if (!($data['is_active'] ?? true)) { ?>
        <fieldset>
            <legend>
                <?= lang('Reactivate inactive user account', 'Inaktiven Account reaktivieren') ?>
            </legend>
            <div class="custom-checkbox mb-10">
                <input type="checkbox" id="is_active" value="1" name="values[is_active]">
                <label for="is_active"><?= lang('Reactivate', 'Reaktivieren') ?></label>
            </div>
        </fieldset>
    <?php } ?>



    <fieldset>
        <legend><?= lang('Roles', 'Rollen') ?></legend>
        <?php
        // dump($data['roles']);
        foreach ($Settings->get('roles') as $role) {
            // everyone is user: no setting needed
            if ($role == 'user') continue;

            // check if user has role
            $has_role = in_array($role, DB::doc2Arr($data['roles'] ?? array()));

            $disable = false;
            if (!$Settings->hasPermission('user.roles')) $disable = true;
            // only admin can make others admins
            if ($role == 'admin' && !$Settings->hasPermission('admin.give-right')) $disable = true;
        ?>
            <div class="form-group custom-checkbox d-inline-block ml-10 mb-10 <?= $disable ? 'text-muted' : '' ?>">
                <input type="checkbox" id="role-<?= $role ?>" value="<?= $role ?>" name="values[roles][]" <?= ($has_role) ? 'checked' : '' ?> <?= $disable ? 'onclick="return false;"' : '' ?>>
                <label for="role-<?= $role ?>"><?= strtoupper($role) ?></label>
            </div>
        <?php } ?>


    </fieldset>

    <?php if ($data['username'] == $_SESSION['username'] || $Settings->hasPermission('user.settings')) { ?>

        <fieldset>
            <legend><?= lang('Profile preferences', 'Profil-Einstellungen') ?></legend>


            <div class="">
                <span><?= lang('Activity display', 'Aktivitäten-Anzeige') ?>:</span>
                <?php
                $display_activities = $data['display_activities'] ?? 'web';
                ?>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[display_activities]" id="display_activities-web" value="web" <?= $display_activities == 'web' ? 'checked' : '' ?>>
                    <label for="display_activities-web"><?= lang('Web') ?></label>
                </div>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[display_activities]" id="display_activities-print" value="print" <?= $display_activities != 'web' ? 'checked' : '' ?>>
                    <label for="display_activities-print"><?= lang('Print', 'Druck') ?></label>
                </div>
            </div>


            <?php
            if ($Settings->featureEnabled('coins')) {
            ?>

                <div class="mt-10">
                    <span><?= lang('Coin visibility', 'Sichtbarkeit der Coins') ?>:</span>
                    <?php
                    $show_coins = $data['show_coins'] ?? 'none';
                    ?>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" name="values[show_coins]" id="show_coins-true" value="none" <?= $show_coins == 'none' ? 'checked' : '' ?>>
                        <label for="show_coins-true"><?= lang('For nobody', 'Für niemanden') ?></label>
                    </div>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" name="values[show_coins]" id="show_coins-myself" value="myself" <?= $show_coins == 'myself' ? 'checked' : '' ?>>
                        <label for="show_coins-myself"><?= lang('For myself', 'Für mich') ?></label>
                    </div>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" name="values[show_coins]" id="show_coins-all" value="all" <?= $show_coins == 'all' ? 'checked' : '' ?>>
                        <label for="show_coins-all"><?= lang('For all', 'Für jeden') ?></label>
                    </div>
                </div>
            <?php
            }
            ?>


            <?php
            if ($Settings->featureEnabled('achievements')) {
            ?>
                <div class="mt-10">
                    <span><?= lang('Show achievements', 'Zeige Errungenschaften') ?>:</span>
                    <?php
                    $hide_achievements = $data['hide_achievements'] ?? false;
                    ?>

                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" name="values[hide_achievements]" id="hide_achievements-false" value="false" <?= $hide_achievements ? '' : 'checked' ?>>
                        <label for="hide_achievements-false"><?= lang('Yes', 'Ja') ?></label>
                    </div>
                    <div class="custom-radio d-inline-block ml-10">
                        <input type="radio" name="values[hide_achievements]" id="hide_achievements-true" value="true" <?= $hide_achievements ? 'checked' : '' ?>>
                        <label for="hide_achievements-true"><?= lang('No', 'Nein') ?></label>
                    </div>
                </div>
            <?php
            }
            ?>
        </fieldset>

        <fieldset>
            <legend>
                <?= lang('Transfer the maintenance of your profile', 'Übertrage die Pflege deines Profils') ?>
            </legend>

            <div class="form-group form-inline mb-0">
                <label for="maintenance">Username:</label>

                <input type="text" list="user-list" name="values[maintenance]" id="maintenance" class="form-control" value="<?= $data['maintenance'] ?? '' ?>">
            </div>

            <p class="m-0 text-danger">
                <i class="ph ph-warning"></i>
                <?= lang(
                    'Warning: this person gets full access to your OSIRIS profile and can edit in your name.',
                    'Warnung: diese Person erhält vollen Zugriff auf dein OSIRIS-Profil und kann in deinem Namen editieren.'
                ) ?>
            </p>

            <datalist id="user-list">
                <?php
                $all_users = $osiris->persons->find();
                foreach ($all_users as $s) { ?>
                    <option value="<?= $s['username'] ?>"><?= "$s[last], $s[first] ($s[username])" ?></option>
                <?php } ?>
            </datalist>
        </fieldset>
    <?php } ?>




    <button type="submit" class="btn primary">
        Update
    </button>
</form>