<?php

/**
 * Page to see scientists profile
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /profile/<username>
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<?php
// get OSIRIS version
$version = $osiris->system->findOne(['key' => 'version']);
if ($version['value'] != OSIRIS_VERSION) { ?>
    <div class="alert danger mb-20">
        <h3 class="title"><?= lang('Warning', 'Warnung') ?></h3>
        <?= lang('
        A new OSIRIS-Version has been found. Please click <a href="' . ROOTPATH . '/migrate">here</a> to migrate', '
        Eine neue OSIRIS-Version wurde gefunden. Bitte klicke <a href="' . ROOTPATH . '/migrate">hier</a>, um zu migrieren.') ?>
    </div>
<?php } ?>



<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>
<script src="<?= ROOTPATH ?>/js/chartjs-plugin-datalabels.min.js"></script>
<link rel="stylesheet" href="<?= ROOTPATH ?>/css/achievements.css?<?= filemtime(BASEPATH . '/css/achievements.css') ?>">

<style>
    .box.h-full {
        height: calc(100% - 2rem) !important;
    }

    .expertise {
        padding: .3rem 1rem;
        background-color: white;
        border-radius: 2rem;
        border: 1px solid var(--border-color);
        display: inline-block;
        -moz-box-shadow: inset 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        -webkit-box-shadow: inset 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        box-shadow: inset 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        margin-left: .5rem;
    }

    .user-role {
        padding: .3rem 1rem;
        background-color: white;
        /* border-radius: 2rem; */
        border: 1px solid var(--border-color);
        display: inline-block;
        -moz-box-shadow: inset 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        -webkit-box-shadow: inset 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        box-shadow: inset 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        margin-right: .5rem;
        font-weight: 500;
        font-size: 1.2rem;
    }
</style>

<?php


$Q = CURRENTQUARTER - 1;
$Y = CURRENTYEAR;
if ($Q < 1) {
    $Q = 4;
    $Y -= 1;
}
$lastquarter = $Y . "Q" . $Q;

$currentuser = $user == $_SESSION['username'];

// Check for new achievements
$Achievement = new Achievement($osiris);
$Achievement->initUser($user);
$achievements = $Achievement->achievements;
$Achievement->checkAchievements();
$user_ac = $Achievement->userac;

include_once BASEPATH . "/php/_lom.php";
$LOM = new LOM($user, $osiris);

$_lom = 0;

$stats = [];


$groups = [];
foreach ($Settings->get('activities') as $key => $value) {
    $groups[$value['id']] = 0;
}


$authors = ["first" => 0, "last" => 0, 'middle' => 0, 'editor' => 0];

$issues = 0;

$years = [];
$lom_years = [];
for ($i = $Settings->get('startyear'); $i <= CURRENTYEAR; $i++) {
    $years[] = strval($i);
    $lom_years[$i] = 0;
}

$impacts = [];
$journals = [];


$filter = ['$or' => [['authors.user' => "$user"], ['editors.user' => "$user"], ['user' => "$user"]], 'year' => ['$gte' => $Settings->get('startyear')]];
$options = ['sort' => ["year" => -1, "month" => -1, "day" => -1]];
$cursor = $osiris->activities->find($filter, $options);
$activities = $cursor->toArray();

foreach ($activities as $doc) {
    if (!isset($doc['type']) || !isset($doc['year'])) continue;
    // if ($doc['year'] < $Settings->get('startyear')) continue;

    $type = $doc['type'];

    $l = $LOM->lom($doc);
    $_lom += $l['lom'];
    $lom_years[$doc['year']] +=  $l['lom'];

    if ($type == 'publication' && isset($doc['journal'])) {
        // dump([get_impact($doc['journal'], $doc['year'] - 1), $doc['year'], $doc['journal']], true);
        if (!isset($doc['impact'])) {
            $if = $DB->get_impact($doc);
            if (!empty($if)) {
                $osiris->activities->updateOne(
                    ['_id' => $doc['_id']],
                    ['$set' => ['impact' => $if]]
                );
            }
        } else {
            $if = $doc['impact'];
        }
        if (!empty($if)) {
            $impacts[] = $if;
            // $impacts[] = [$year, $if];
            $journals[] = $doc['journal'];
        }
    }
    if ($type == 'publication') {
        foreach ($doc['authors'] ?? array() as $a) {
            if (($a['user'] ?? '') == $user) {
                if (isset($a['position']) && (in_array($a['position'], ['last', 'corresponding']))) {
                    $authors['last']++;
                } elseif (isset($a['position']) && $a['position'] == 'first') {
                    $authors['first']++;
                } else {
                    $authors['middle']++;
                }
                break;
            }
        }

        foreach ($doc['editors'] ?? array() as $a) {
            if (($a['user'] ?? '') == $user) {
                $authors['editor']++;
                break;
            }
        }
    }

    if (!array_key_exists($type, $groups)) continue;

    $year = strval($doc['year']);


    if (!isset($stats)) $stats = [];
    if (!isset($stats[$year])) $stats[$year] = array_merge(["x" => $year], $groups);

    $stats[$year][$type] += 1;

    if ($currentuser) {
        $Format->setDocument($doc);
        if ($Format->has_issues()) {
            $issues++;
        }
    }
}


// $showcoins = (!($scientist['hide_coins'] ?? true));
if ($Settings->hasFeatureDisabled('coins')) {
    $showcoins = false;
} else {
    $showcoins = ($scientist['show_coins'] ?? 'no');
    if ($showcoins == 'all') {
        $showcoins = true;
    } elseif ($showcoins == 'myself' && $currentuser) {
        $showcoins = true;
    } else {
        $showcoins = false;
    }
}
?>

<?php if ($showcoins != 'no') { ?>
    <div class="modal modal-lg" id="coins" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content w-600 mw-full">
                <a href="#close-modal" class="btn float-right" role="button" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <?php
                include BASEPATH . "/components/what-are-coins.php";
                ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php

$img_exist = file_exists(BASEPATH . "/img/users/$user.jpg");
if ($img_exist) {
    $img = ROOTPATH . "/img/users/$user.jpg";
} else {
    // standard picture
    $img = ROOTPATH . "/img/person.jpg";
}

if ($currentuser || $Settings->hasPermission('upload-user-picture')) { ?>
    <!-- Modal for updating the profile picture -->
    <div class="modal modal-lg" id="change-picture" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content w-600 mw-full">
                <a href="#close-modal" class="btn float-right" role="button" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

                <h2 class="title">
                    <?= lang('Change profile picture', 'Profilbild ändern') ?>
                </h2>

                <form action="<?= ROOTPATH ?>/update-profile/<?= $user ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                    <div class="custom-file mb-20" id="file-input-div">
                        <input type="file" id="profile-input" name="file" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                        <label for="profile-input"><?= lang('Upload new profile image', 'Lade ein neues Profilbild hoch') ?></label>
                        <br><small class="text-danger">Max. 2 MB.</small>
                    </div>

                    <p>
                        <?= lang('Please note that your profile picture will be visible to all users of OSIRIS.', 'Bitte beachte, dass dein Profilbild für alle OSIRIS-Nutzer:innen sichtbar sein wird.') ?>
                    </p>
                    <script>
                        var uploadField = document.getElementById("profile-input");

                        uploadField.onchange = function() {
                            if (this.files[0].size > 2097152) {
                                toastError(lang("File is too large! Max. 2MB is supported!", "Die Datei ist zu groß! Max. 2MB werden unterstützt."));
                                this.value = "";
                            };
                        };
                    </script>
                    <button class="btn primary">
                        <i class="ph ph-upload"></i>
                        Upload
                    </button>
                </form>

                <?php if ($img_exist) { ?>
                    <hr>
                    <form action="<?= ROOTPATH ?>/update-profile/<?= $user ?>" method="post">
                        <input type="hidden" name="delete" value="true">
                        <button class="btn danger">
                            <i class="ph ph-trash"></i>
                            <?= lang('Delete current picture', 'Aktuelles Bild löschen') ?>
                        </button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>


<div class=" my-0">



    <div class="row align-items-center my-0">
        <div class="col flex-grow-0">
            <div class="position-relative">
                <img src="<?= $img ?>" alt="" class="profile-img">
                <?php if ($currentuser || $Settings->hasPermission('upload-user-picture')) { ?>
                    <a href="#change-picture" class="position-absolute p-10 bottom-0 right-0 text-white"><i class="ph ph-edit"></i></a>
                <?php } ?>

            </div>

        </div>
        <div class="col ml-20">
            <h1 class="m-0"><?= $name ?></h1>

            <h3 class="m-0 text-<?= $scientist['dept'] ?>">
                <?php
                if (!empty($scientist['dept'])) {
                    echo $Settings->getDepartments($scientist['dept'])['name'] ?? '';
                }
                ?>
            </h3>

            <?php if (!($scientist['is_active'] ?? true)) { ?>
                <span class="text-danger user-role">
                    <?= lang('Former Employee', 'Ehemalige Beschäftigte') ?>
                </span>
            <?php } else { ?>
                <?php foreach ($scientist['roles'] as $role) { ?>
                    <span class="user-role">
                        <?= strtoupper($role) ?>
                    </span>
                <?php } ?>
                <!-- <span class="user-role">Last login: <?= $scientist['lastlogin'] ?></span> -->
                <?php if ($currentuser && !empty($scientist['maintenance'] ?? null)) { ?>
                    <span class="user-role">
                        <?=lang('Maintainer: '.$DB->getNameFromId($scientist['maintenance']))?>
                    </span>
                <?php } ?>
            <?php } ?>

            <?php if ($showcoins) { ?>
                <p class="lead mt-0">
                    <i class="ph ph-lg ph-coin text-signal"></i>
                    <b id="lom-points"><?= $_lom ?></b>
                    Coins
                    <a href='#coins' class="text-muted">
                        <i class="ph ph-question text-muted"></i>
                    </a>
                </p>
            <?php } ?>

        </div>

        <?php
        if (!$Settings->hasFeatureDisabled('achievements')) {
            if (!empty($user_ac) && !($scientist['hide_achievements'] ?? false) && !($USER['hide_achievements'] ?? false)) {
        ?>
                <div class="achievements text-right" style="max-width: 35rem;">
                    <h5 class="m-0"><?= lang('Achievements', 'Errungenschaften') ?>:</h5>

                    <?php
                    $Achievement->widget();
                    ?>

                </div>
        <?php
            }
        } ?>
    </div>



    <?php if ($currentuser) { ?>

        <div class="box p-5 row-<?= $scientist['dept'] ?>" style="border-left-width:5px">
            <div class="m-10">
                <h5 class="title font-size-16">
                    <?= lang('This is your personal profile page.', 'Dies ist deine persönliche Profilseite.') ?>
                </h5>

                <div class="btn-group btn-group-lg mr-5">
                    <a class="btn" href="<?= ROOTPATH ?>/activities/new" data-toggle="tooltip" data-title="<?= lang('Add activity', 'Aktivität hinzufügen') ?>">
                        <i class="ph ph-plus-circle text-osiris ph-fw"></i>
                        <!-- <?= lang('Add activity', 'Aktivität hinzufügen') ?> -->
                    </a>
                    <a href="<?= ROOTPATH ?>/my-activities" class="btn" data-toggle="tooltip" data-title="<?= lang('My activities', 'Meine Aktivitäten ') ?>">
                        <i class="ph ph-folder-user text-primary ph-fw"></i>
                        <!-- <?= lang('My activities', 'Meine Aktivitäten ') ?> -->
                    </a>
                    <a class="btn" href="<?= ROOTPATH ?>/my-year/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('My Year', 'Mein Jahr') ?>">
                        <i class="ph ph-calendar text-success ph-fw"></i>
                        <!-- <?= lang('My Year', 'Mein Jahr') ?> -->
                    </a>


                </div>
                <div class="btn-group btn-group-lg mr-5">
                    <a class="btn" href="<?= ROOTPATH ?>/achievements" data-toggle="tooltip" data-title="<?= lang('My Achievements', 'Meine Errungenschaften') ?>">
                        <i class="ph ph-trophy text-signal ph-fw"></i>
                    </a>

                    <a class="btn" href="<?= ROOTPATH ?>/visualize/coauthors?scientist=<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('My Coauthor network', 'Mein Koautoren-Netzwerk') ?>">
                        <i class="ph ph-graph text-osiris ph-fw"></i>
                    </a>
                </div>

                <div class="btn-group btn-group-lg">
                    <a class="btn btn" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Edit user profile', 'Bearbeite Profil') ?>">
                        <i class="ph ph-edit text-muted ph-fw"></i>
                        <!-- <?= lang('Edit user profile', 'Bearbeite Profil') ?> -->
                    </a>
                </div>

                <?php if ($issues !== 0) { ?>
                    <div class="alert danger mt-20">
                        <a class="link text-danger text-decoration-none" href='<?= ROOTPATH ?>/issues'>
                            <?= lang(
                                "You have $issues unresolved " . ($issues == 1 ? 'issue' : 'issues') . " with your activities.",
                                "Du hast $issues " . ($issues == 1 ? 'ungelöstes Problem' : 'ungelöste Probleme') . " mit deinen Aktivitäten."
                            ) ?>
                        </a>
                    </div>
                <?php } ?>

                <?php
                $queue = $osiris->queue->count(['authors.user' => $user, 'duplicate' => ['$exists' => false]]);
                if ($queue !== 0) { ?>
                    <div class="alert success mt-20">
                        <a class="link text-success" href='<?= ROOTPATH ?>/queue/user'>
                            <?= lang(
                                "We found $queue new " . ($queue == 1 ? 'activity' : 'activities') . " for you. Review them now.",
                                "Wir haben $queue " . ($queue == 1 ? 'Aktivität' : 'Aktivitäten') . " von dir gefunden. Überprüfe sie jetzt."
                            ) ?>
                        </a>
                    </div>
                <?php } ?>

                <?php
                if (lang('en', 'de') == 'de') {
                    // no news in english
                    if (empty($scientist['lastversion'] ?? '') || $scientist['lastversion'] !== OSIRIS_VERSION) { ?>
                        <div class="alert primary mt-20">
                            <a class="link text-decoration-none" href='<?= ROOTPATH ?>/new-stuff#version-<?= OSIRIS_VERSION ?>'>
                                <?= lang(
                                    "There has been an OSIRIS-Update since your last login. Have a look at the news.",
                                    "Es gab ein OSIRIS-Update, seitdem du das letzte Mal hier warst. Schau in die News, um zu wissen, was neu ist."
                                ) ?>
                            </a>
                        </div>
                <?php }
                } ?>

                <?php
                $approvedQ = array();
                if (isset($scientist['approved'])) {
                    $approvedQ = $scientist['approved']->bsonSerialize();
                }


                if ($Settings->hasPermission('scientist') && !in_array($lastquarter, $approvedQ)) { ?>
                    <div class="alert muted mt-20">

                        <div class="title">
                            <?= lang("The past quarter ($lastquarter) has not been approved yet.", "Das vergangene Quartal ($lastquarter) wurde von dir noch nicht freigegeben.") ?>
                        </div>

                        <p>
                            <?= lang('
                            For the quarterly controlling, you need to confirm that all activities from the previous quarter are stored in OSIRIS and saved correctly.
                            To do this, go to your year and check your activities. Afterwards you can release the quarter via the green button.
                            ', '
                            Für das Quartalscontrolling musst du bestätigen, dass alle Aktivitäten aus dem vergangenen Quartal in OSIRIS hinterlegt und korrekt gespeichert sind.
                            Gehe dazu in dein Jahr und überprüfe deine Aktivitäten. Danach kannst du über den grünen Button das Quartal freigeben.
                            ') ?>
                        </p>

                        <a class="btn success" href="<?= ROOTPATH ?>/my-year/<?= $user ?>?year=<?= $Y ?>&quarter=<?= $Q ?>">
                            <?= lang('Review & Approve', 'Überprüfen & Freigeben') ?>
                        </a>
                    </div>
                <?php } ?>

                <div class="mt-20">
                    <?php
                    $new = $Achievement->new;

                    if (!empty($new)) {
                        echo '<h5 class="title font-size-16">' . lang('Congratulation, you achieved something new: ', 'Glückwunsch, du hast neue Errungenschaften erlangt:') . '</h5>';
                        foreach ($new as $i => $n) {
                            $Achievement->snack($n);
                        }
                        $Achievement->save();
                    }
                    ?>
                </div>

            </div>

        </div>

    <?php } else { ?>
        <div class="btn-group btn-group-lg mt-15 ml-5">
            <a class="btn" href="<?= ROOTPATH ?>/my-year/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('The year of', 'Das Jahr ') . $scientist['first'] ?> ">
                <i class="ph ph-calendar text-success ph-fw"></i>
            </a>
            <a href="<?= ROOTPATH ?>/my-activities?user=<?= $user ?>" class="btn" data-toggle="tooltip" data-title="<?= lang('All activities of ', 'Alle Aktivitäten von ') . $scientist['first'] ?>">
                <i class="ph ph-folder-user text-primary ph-fw"></i>
            </a>
            <a href="<?= ROOTPATH ?>/visualize/coauthors?scientist=<?= $user ?>" class="btn" data-toggle="tooltip" data-title="<?= lang('Coauthor Network of ', 'Koautoren-Netzwerk von ') . $scientist['first'] ?>">
                <i class="ph ph-graph text-danger ph-fw"></i>
            </a>

            <a class="btn" href="<?= ROOTPATH ?>/achievements/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Achievements of ', 'Errungenschaften von ') . $scientist['first'] ?>">
                <i class="ph ph-trophy text-signal ph-fw"></i>
            </a>
        </div>
        <div class="btn-group btn-group-lg mt-15 ml-5">
            <?php if ($Settings->hasPermission('edit-user-profile')) { ?>
                <a class="btn" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Edit user profile', 'Bearbeite Profil') ?>">
                    <i class="ph ph-edit text-muted ph-fw"></i>
                </a>
            <?php } ?>
            <?php if (($scientist['is_active'] ?? true) && $Settings->hasPermission('set-user-inactive')) { ?>
                <a class="btn" href="<?= ROOTPATH ?>/user/delete/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Inactivate user', 'Nutzer:in inaktivieren') ?>">
                    <i class="ph ph-trash text-danger ph-fw"></i>
                </a>
            <?php } ?>

        </div>

        <?php if (($Settings->hasPermission('complete-dashboard')) && isset($scientist['approved'])) {
            $approvedQ = $scientist['approved']->bsonSerialize();
            sort($approvedQ);
            echo "<div class='mt-20'>";
            echo "<b>" . lang('Quarters approved', 'Bestätigte Quartale') . ":</b>";
            foreach ($approvedQ as $appr) {
                $Q = explode('Q', $appr);
                echo "<a href='" . ROOTPATH . "/my-year/$user?year=$Q[0]&quarter=$Q[1]' class='badge success ml-5'>$appr</a>";
            }
            echo "</div>";
        } ?>


    <?php } ?>


    <?php
    $expertise = $scientist['expertise'] ?? array();
    ?>
    <?php if ($currentuser) { ?>

        <div class="box mb-0" id="expertise">
            <div class="p-10 pb-0">

                <label for="expertise" class="font-weight-bold">
                    <i class="ph ph-barbell text-osiris"></i> <?= lang('Expertise:') ?>
                </label>
            </div>
            <div class="p-10 pt-0">

                <form action="<?= ROOTPATH ?>/update-expertise/<?= $user ?>" method="post" id="expertise-form">
                    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                    <?php foreach ($expertise as $n) { ?>
                        <div class="input-group sm d-inline-flex w-auto mr-5 mb-10">
                            <input type="text" name="values[expertise][]" value="<?= $n ?>" list="expertise-list" required>
                            <div class="input-group-append">
                                <a class="btn" onclick="$(this).closest('.input-group').remove();">&times;</a>
                            </div>
                        </div>
                    <?php } ?>

                    <button class="btn small" type="button" onclick="addName(event, this);">
                        <i class="ph ph-plus"></i>
                    </button>
                    <br>
                    <button class="btn primary small" type="submit"><?= lang('Save changes', 'Änderungen speichern') ?></button>
                </form>
            </div>

        </div>

        <datalist id="expertise-list">
            <?php
            foreach ($osiris->persons->distinct('expertise') as $d) { ?>
                <option><?= $d ?></option>
            <?php } ?>
        </datalist>

        <script>
            function addName(evt, el) {
                var group = $('<div class="input-group sm d-inline-flex w-auto mr-5 mb-10"> ')
                group.append('<input type="text" name="values[expertise][]" value="" list="expertise-list" required>')
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
    <?php } else if (!empty($scientist['expertise'] ?? array())) { ?>
        <div class="mt-20" id="expertise">
            <b><i class="ph ph-barbell text-osiris"></i> <?= lang('Expertise:') ?></b>

            <?php foreach ($scientist['expertise'] ?? array() as $key) { ?>
                <span class="expertise"><?= $key ?></span>
            <?php } ?>
        </div>

    <?php } ?>

    <div class="row row-eq-spacing my-0">
        <div class="profile-widget col-lg-6">
            <div class="box h-full">
                <div class="content">
                    <?php if ($currentuser) { ?>
                        <a class="float-right" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>">
                            <i class="ph ph-note-pencil ph-lg"></i>
                        </a>
                    <?php } ?>
                    <h4 class="title"><?= lang('Details') ?></h4>
                </div>
                <table class="table simple">
                    <tbody>
                        <tr>
                            <td><?= lang('Last name', 'Nachname') ?></td>
                            <td><?= $scientist['last'] ?? '' ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('First name', 'Vorname') ?></td>
                            <td><?= $scientist['first'] ?? '' ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Academic title', 'Akademischer Titel') ?></td>
                            <td><?= $scientist['academic_title'] ?? '' ?></td>
                        </tr>
                        <!-- <tr>
                        <td><?= lang('Gender', 'Geschlecht') ?></td>
                        <td><?php
                            $genders = [
                                'm' => lang('male', 'männlich'),
                                'f' => lang('female', 'weiblich'),
                                'd' => lang('non-binary', 'divers'),
                                'n' => lang('not specified', 'nicht angegeben'),
                            ];
                            echo $genders[$scientist['gender'] ?? 'n'];
                            ?>
                        </td>
                    </tr> -->
                        <tr>
                            <td>Email</td>
                            <td><?= $scientist['mail'] ?? '' ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Telephone', 'Telefon') ?></td>
                            <td><?= $scientist['telephone'] ?? '' ?></td>
                        </tr>
                        <?php if (!empty($scientist['twitter'] ?? null)) { ?>
                            <tr>
                                <td>Twitter</td>
                                <td>
                                    <a href="https://twitter.com/<?= $scientist['twitter'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['twitter'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['orcid'] ?? null)) { ?>
                            <tr>
                                <td>ORCID</td>
                                <td>
                                    <a href="http://orcid.org/<?= $scientist['orcid'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['orcid'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['researchgate'] ?? null)) { ?>
                            <tr>
                                <td>ResearchGate</td>
                                <td>
                                    <a href="https://www.researchgate.net/profile/<?= $scientist['researchgate'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['researchgate'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['google_scholar'] ?? null)) { ?>
                            <tr>
                                <td>Google Scholar</td>
                                <td>
                                    <a href="https://scholar.google.com/citations?user=<?= $scientist['google_scholar'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['google_scholar'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['webpage'] ?? null)) {
                            $web = preg_replace('/^https?:\/\//', '', $scientist['webpage']);
                        ?>
                            <tr>
                                <td>Personal web page</td>
                                <td>
                                    <a href="https://<?= $web ?>" target="_blank" rel="noopener noreferrer"><?= $web ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($currentuser && $Settings->hasPermission('complete-dashboard')) {

            $n_scientists = $osiris->accounts->count(["roles" => 'scientist', "is_active" => true]);
            $n_approved = $osiris->accounts->count(["roles" => 'scientist', "is_active" => true, "approved" => $lastquarter]);
        ?>
            <div class="col-6 col-md-3 profile-widget ">
                <div class="box h-full">
                    <div class="chart content">
                        <h5 class="title text-center"><?= $lastquarter ?></h5>

                        <canvas id="approved-<?= $lastquarter ?>"></canvas>
                        <div class="text-right mt-5">
                            <button class="btn small" onclick="loadModal('components/controlling-approved', {q: '<?= $Q ?>', y: '<?= $Y ?>'})">
                                <i class="ph ph-magnifying-glass-plus"></i> <?= lang('Details') ?>
                            </button>
                        </div>

                        <script>
                            var ctx = document.getElementById('approved-<?= $lastquarter ?>')
                            var myChart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['<?= lang("Approved", "Bestätigt") ?>', '<?= lang("Approval missing", "Bestätigung fehlt") ?>'],
                                    datasets: [{
                                        label: '# of Scientists',
                                        data: [<?= $n_approved ?>, <?= $n_scientists - $n_approved ?>],
                                        backgroundColor: [
                                            '#ECAF0095',
                                            '#B61F2995',
                                        ],
                                        borderColor: '#464646', //'',
                                        borderWidth: 1,
                                    }]
                                },
                                plugins: [ChartDataLabels],
                                options: {
                                    responsive: true,
                                    plugins: {
                                        datalabels: {
                                            color: 'black',
                                            // anchor: 'end',
                                            // align: 'end',
                                            // offset: 10,
                                            font: {
                                                size: 20
                                            }
                                        },
                                        legend: {
                                            position: 'bottom',
                                            display: false,
                                        },
                                        title: {
                                            display: false,
                                            text: 'Scientists approvation'
                                        }
                                    }
                                }
                            });
                        </script>

                    </div>
                </div>
            </div>
        <?php } ?>


        <?php if ($currentuser && $Settings->hasPermission('reports')) { ?>
            <div class="col-6 col-md-3 profile-widget ">
                <div class=" h-full">
                    <div class="py-10">
                        <div class="link-list">
                            <?php if ($Settings->hasPermission('complete-dashboard')) { ?>
                                <a class="border" href="<?= ROOTPATH ?>/dashboard"><?= lang('Dashboard', 'Dashboard') ?></a>
                            <?php } ?>

                            <?php if ($Settings->hasPermission('complete-queue')) { ?>
                                <a class="border" href="<?= ROOTPATH ?>/queue/editor"><?= lang('Queue', 'Warteschlange') ?></a>
                            <?php } ?>

                            <?php if ($Settings->hasPermission('reports')) { ?>
                                <a class="border" href="<?= ROOTPATH ?>/reports"><?= lang('Reports', 'Berichte') ?></a>
                            <?php } ?>

                            <?php if ($Settings->hasPermission('lock-activities')) { ?>
                                <a class="border" href="<?= ROOTPATH ?>/controlling"><?= lang('Lock activities', 'Aktivitäten sperren') ?></a>
                            <?php } ?>

                            <?php if ($Settings->hasPermission('admin-panel')) { ?>
                                <a class="border" href="<?= ROOTPATH ?>/admin/general"><?= lang('Admin-Panel') ?></a>
                            <?php } ?>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php
        // PUBLICATION WIDGET
        $pubs = [];
        $i = 0;
        foreach ($activities as $doc) {
            if ($doc['type'] != 'publication') continue;
            if ($i++ >= 5) break;
            $pubs[] = $doc;
        }
        if (!empty($pubs)) { ?>
            <div class="profile-widget col-lg-6">
                <div class="box h-full">
                    <div class="content">
                        <h4 class="title"><?= lang('Latest publications', 'Neueste Publikationen') ?></h4>
                    </div>
                    <table class="table simple">
                        <tbody>
                            <?php
                            foreach ($pubs as $doc) {
                                $id = $doc['_id'];
                            ?>
                                <tr id='tr-<?= $id ?>'>
                                    <td class="w-50"><?= $doc['rendered']['icon'] ?></td>
                                    <td>
                                        <?php
                                        if ($USER['display_activities'] == 'web') {
                                            echo $doc['rendered']['web'];
                                        } else {
                                            echo $doc['rendered']['print'];
                                        }
                                        ?>
                                    </td>
                                    <td class="unbreakable w-25">
                                        <a class="btn link square" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                            <i class="ph ph-arrow-fat-line-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="content mt-0">
                        <a href="<?= ROOTPATH ?>/my-activities?user=<?= $user ?>#type=publication" class="btn osiris">
                            <i class="ph ph-book-bookmark mr-5"></i> <?= lang('All publications of ', 'Alle Publikationen von ') . $name ?>
                        </a>
                    </div>

                </div>
            </div>
        <?php } ?>




        <?php
        // IMPACT FACTOR WIDGET
        if (($currentuser || !$Settings->hasFeatureDisabled('user-metrics')) && !empty($impacts)) { ?>
            <div class="profile-widget col-lg-6">
                <div class="box h-full">
                    <div class="chart content text-center">
                        <h5 class="title mb-0">
                            <i class="ph ph-file-text text-primary"></i>
                            <?= lang('Impact factor histogram', 'Impact Factor Histogramm') ?>
                        </h5>
                        <p class="text-muted mt-0"><?= lang('since', 'seit') . " " . $Settings->get('startyear') ?></p>
                        <canvas id="chart-impact" style="max-height: 30rem;"></canvas>
                        <?php
                        $x = [];
                        $y = [];
                        if (!empty($impacts)) {
                            $max_impact = ceil(max($impacts));


                            for ($i = 0; $i <= $max_impact; $i++) {
                                $x[] = $i;
                            }
                            $y = array_fill(0, $max_impact, 0);

                            foreach ($impacts as $val) {
                                $imp = floor($val);
                                $y[$imp]++;
                            }
                        }
                        ?>

                        <script>
                            var ctx = document.getElementById('chart-impact')
                            var labels = JSON.parse('<?= json_encode($x) ?>');
                            var colors = [
                                // '#83D0F595',
                                '#006EB795',
                                // '#13357A95',
                                // '#00162595'
                            ]
                            var i = 0

                            console.log(labels);
                            var data = {
                                type: 'bar',
                                options: {
                                    plugins: {
                                        legend: {
                                            display: false,
                                            position: 'bottom'
                                        },
                                        tooltip: {
                                            callbacks: {
                                                title: (items) => {
                                                    if (!items.length) {
                                                        return '';
                                                    }
                                                    const item = items[0];
                                                    const x = item.parsed.x;
                                                    const min = x;
                                                    const max = x + 1;
                                                    return `IF: ${min} - ${max}`;
                                                }
                                            }
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        x: {
                                            type: 'linear',
                                            ticks: {
                                                stepSize: 1
                                            },
                                            stacked: true,
                                            title: {
                                                display: true,
                                                text: lang('Impact factor', 'Impact factor')
                                            },
                                        },
                                        y: {
                                            title: {
                                                display: true,
                                                text: lang('Number of publications', 'Anzahl Publikationen')
                                            },
                                            ticks: {
                                                callback: function(value, index, ticks) {
                                                    // only show full numbers
                                                    if (Number.isInteger(value)) {
                                                        return value
                                                    }
                                                    return "";
                                                }
                                            }
                                        }
                                    },
                                },
                                data: {
                                    labels: JSON.parse('<?= json_encode($x) ?>'),
                                    datasets: [

                                        <?php
                                        // foreach ($quarters as $q => $data) {
                                        //     $imp = [];
                                        //     for ($i = 1; $i < $max_impact; $i++) {
                                        //         $imp[] = $data['impacts'][$i] ?? 0;
                                        //     }
                                        ?> {
                                            data: JSON.parse('<?= json_encode($y) ?>'),
                                            backgroundColor: colors[i++],
                                            borderWidth: 1,
                                            borderColor: '#464646',
                                            borderRadius: 4
                                        },
                                        <?php
                                        //  } 
                                        ?>

                                    ],
                                }
                            }


                            console.log(data);
                            var myChart = new Chart(ctx, data);
                        </script>
                    </div>
                </div>
            </div>
        <?php } ?>



        <?php
        // ROLE WIDGET
        if (($currentuser || !$Settings->hasFeatureDisabled('user-metrics')) && array_sum($authors) > 0) { ?>
            <div class="profile-widget col-md-6 col-lg-3">
                <div class="box h-full">
                    <div class="chart content text-center">
                        <h5 class="title mb-0">
                            <i class="ph ph-graduation-cap text-primary"></i>
                            <?= lang('Role of', 'Rolle von') ?> <?= $scientist['first'] ?> <?= lang('in publications', 'in Publikationen') ?>
                        </h5>
                        <p class="text-muted mt-0"><?= lang('since', 'seit') . " " . $Settings->get('startyear') ?></p>

                        <canvas id="chart-authors" style="max-height: 30rem;"></canvas>

                        <?php
                        $labels = array();
                        $data = array();
                        $colors = array();
                        if ($authors['first'] !== 0) {
                            $labels[] = lang("First author", "Erstautor");
                            $data[] = $authors['first'];
                            $colors[] = '#006EB795';
                        }
                        if ($authors['last'] !== 0) {
                            $labels[] = lang("Last author", "Letztautor");
                            $data[] = $authors['last'];
                            $colors[] = '#13357A95';
                        }
                        if ($authors['middle'] !== 0) {
                            $labels[] = lang("Middle author", "Mittelautor");
                            $data[] = $authors['middle'];
                            $colors[] = '#83D0F595';
                        }
                        if ($authors['editor'] !== 0) {
                            $labels[] = lang("Editorship", "Editorenschaft");
                            $data[] = $authors['editor'];
                            $colors[] = '#13357A';
                        }
                        ?>

                        <script>
                            var ctx = document.getElementById('chart-authors')
                            var myChart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: <?= json_encode($labels) ?>,
                                    datasets: [{
                                        label: '# of Scientists',
                                        data: <?= json_encode($data) ?>,
                                        backgroundColor: <?= json_encode($colors) ?>,
                                        borderColor: '#464646', //'',
                                        borderWidth: 1,
                                    }]
                                },
                                plugins: [ChartDataLabels],
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            display: true,
                                        },
                                        title: {
                                            display: false,
                                            text: 'Scientists approvation'
                                        },
                                        datalabels: {
                                            color: 'black',
                                            // anchor: 'end',
                                            // align: 'end',
                                            // offset: 10,
                                            font: {
                                                size: 20
                                            }
                                        }
                                    },
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (($currentuser || !$Settings->hasFeatureDisabled('user-metrics')) && $showcoins) { ?>
            <div class="profile-widget col-md-6 col-lg-3">
                <div class="box h-full">
                    <div class="chart content text-center">
                        <h5 class="title">
                            <i class="ph ph-lg ph-coin text-signal"></i>
                            <?= lang('Coins per Year', 'Coins pro Jahr') ?>
                        </h5>
                        <canvas id="chart-coins" style="max-height: 30rem;"></canvas>
                    </div>

                    <?php
                    $data = [];
                    $lastval = 0;
                    $labels = [];
                    foreach ($lom_years as $year => $val) {
                        $labels[] = $year;
                        $data[] = [$lastval, $val + $lastval];
                        $lastval = $val + $lastval;
                    }
                    ?>

                    <script>
                        var ctx = document.getElementById('chart-coins')
                        var raw_data = JSON.parse('<?= json_encode($data) ?>');
                        var labels = JSON.parse('<?= json_encode($labels) ?>');
                        console.log(raw_data);
                        var colors = new Array(labels.length - 1);
                        colors.fill('#ECAF0095')
                        // colors[colors.length] = '#ECAF00'
                        console.log(colors);
                        var data = {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'coins',
                                    data: raw_data,
                                    backgroundColor: colors,
                                    borderWidth: 1,
                                    borderColor: '#464646',
                                    borderSkipped: false,
                                    // barPercentage: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: (data) => {
                                                return data.parsed.y - data.parsed.x;
                                            }
                                        }
                                    },
                                    legend: {
                                        display: false
                                    },
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: lang('Years', 'Jahre')
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: lang('Coins (accumulated)', 'Coins (akkumuliert)')
                                        }
                                    }
                                }
                            }
                        }


                        console.log(data);
                        var myChart = new Chart(ctx, data);
                    </script>
                </div>
            </div>
        <?php } ?>

        <?php if (($currentuser || !$Settings->hasFeatureDisabled('user-metrics')) && !empty($stats)) { ?>
            <div class="profile-widget col-lg-6">
                <div class="box h-full">
                    <div class="chart content">
                        <h5 class="title text-center">
                            <?= lang('All activities in which ' . $scientist['first'] . ' was involved', 'Alle Aktivitäten, an denen ' . $scientist['first'] . ' beteiligt war') ?>
                        </h5>
                        <canvas id="chart-activities" style="max-height: 35rem;"></canvas>

                        <small class="text-muted">
                            <?= lang('For multi-year activities, only the start date is relevant.', 'Bei mehrjährigen Aktivitäten wird nur das Startdatum gezählt.') ?>
                        </small>
                    </div>
                </div>

                <script>
                    var ctx = document.getElementById('chart-activities')
                    var raw_data = Object.values(<?= json_encode($stats) ?>);
                    var years = JSON.parse('<?= json_encode($years) ?>')
                    console.log(raw_data);
                    var data = {
                        type: 'bar',
                        options: {
                            plugins: {
                                title: {
                                    display: false,
                                    text: 'All activities'
                                },
                                legend: {
                                    display: true,
                                }
                            },
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: true,
                                    title: {
                                        display: true,
                                        text: lang('Years', 'Jahre')
                                    }
                                },
                                y: {
                                    stacked: true,
                                    ticks: {
                                        callback: function(value, index, ticks) {
                                            // only show full numbers
                                            if (Number.isInteger(value)) {
                                                return value
                                            }
                                            return "";
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: lang('Number of activities', 'Anzahl der Aktivitäten')
                                    }
                                }
                            },
                            maintainAspectRatio: false,
                            onClick: (e) => {
                                const canvasPosition = Chart.helpers.getRelativePosition(e, activityChart);

                                // Substitute the appropriate scale IDs
                                const dataX = activityChart.scales.x.getValueForPixel(canvasPosition.x);
                                const dataY = activityChart.scales.y.getValueForPixel(canvasPosition.y);
                                console.log(years[dataX], dataY);
                                window.location = ROOTPATH + "/my-year/<?= $user ?>?year=" + years[dataX]
                            }
                        },
                        data: {
                            labels: years,
                            datasets: [
                                <?php $n = 3;
                                foreach ($groups as $group => $i) {
                                    $color = $Settings->getActivities($group)['color'];
                                    // $color = adjustBrightness($color, ($n--)*10);
                                ?> {
                                        label: '<?= $Settings->getActivities($group)['name'] ?>',
                                        data: raw_data,
                                        parsing: {
                                            yAxisKey: '<?= $group ?>'
                                        },
                                        backgroundColor: '<?= $color ?>95',
                                        borderColor: '#464646', //'<?= $color ?>',
                                        borderWidth: 1
                                    },
                                <?php } ?>
                            ]
                        }
                    }


                    console.log(data);
                    var activityChart = new Chart(ctx, data);
                </script>
            </div>
        <?php } ?>

        <?php if (!empty($activities)) { ?>
            <div class="profile-widget col-md-12 col-lg-6">
                <div class="box h-full">
                    <div class="content">
                        <h4 class="title"><?= lang('Latest activities', 'Neueste Aktivitäten') ?></h4>
                    </div>
                    <table class="table simple">
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($activities as $doc) {
                                if ($doc['type'] == 'publication') continue;
                                if ($i++ > 5) break;
                                $id = $doc['_id'];
                                $Format->setDocument($doc);

                            ?>
                                <tr id='tr-<?= $id ?>'>
                                    <td class="w-50"><?= $Format->activity_icon(); ?></td>
                                    <td>
                                        <?php
                                        if ($USER['display_activities'] == 'web') {
                                            echo $Format->formatShort();
                                        } else {
                                            echo $Format->format();
                                        }
                                        ?>

                                    </td>
                                    <td class="unbreakable w-25">
                                        <a class="btn link square" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                            <i class="ph ph-arrow-fat-line-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="content mt-0">
                        <a href="<?= ROOTPATH ?>/my-activities?user=<?= $user ?>" class="btn osiris">
                            <i class="ph ph-book-bookmark mr-5"></i>
                            <?= lang('All activities', 'Alle Aktivitäten ') ?>
                        </a>
                    </div>

                </div>
            </div>
        <?php } ?>


        <?php
        $filter = [
            'authors.user' => "$user",
            'end' => null,
            '$or' => array(
                ['type' => 'misc', 'subtype' => 'misc-annual'],
                ['type' => 'review', 'subtype' =>  'editorial'],
            )
        ];

        $count_memberships = $osiris->activities->count($filter);

        if ($count_memberships > 0) {
            $memberships = $osiris->activities->find($filter, ['sort' => ["type" => 1, "year" => -1, "month" => -1]]);
        ?>

            <div class="profile-widget col-lg-12 col-xl-6">
                <div class="box h-full">
                    <div class="content">
                        <h4 class="title"><?= lang('Ongoing memberships', 'Laufende Mitgliedschaften') ?></h4>
                    </div>
                    <table class="table simple">
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($memberships as $doc) {
                                if ($doc['type'] == 'publication') continue;
                                // if ($i++ > 5) break;
                                $id = $doc['_id'];

                                $Format->setDocument($doc);
                            ?>
                                <tr id='tr-<?= $id ?>'>
                                    <td class="w-50"><?= $Format->activity_icon(); ?></td>
                                    <td>
                                        <?php
                                        if ($USER['display_activities'] == 'web') {
                                            echo $Format->formatShort();
                                        } else {
                                            echo $Format->format();
                                        }
                                        ?>

                                    </td>
                                    <td class="unbreakable w-25">
                                        <a class="btn link square" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                            <i class="ph ph-arrow-fat-line-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php
        } ?>

    </div>
    <?php
    if (isset($_GET['verbose'])) {
        echo "<h4>Person</h4>";
        dump($DB->getPerson($user), true);
        echo "<h4>Account</h4>";
        dump($DB->getAccount($user), true);
        echo "<hr>";
        dump($scientist, true);
    }
    ?>

</div>