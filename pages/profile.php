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
<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>
<!-- <script src="<?= ROOTPATH ?>/js/plotly-2.27.1.min.js"></script> -->


<script>
    const CURRENT_USER = '<?= $user ?>';
</script>


<script src="<?= ROOTPATH ?>/js/profile.js"></script>


<link rel="stylesheet" href="<?= ROOTPATH ?>/css/achievements.css?<?= filemtime(BASEPATH . '/css/achievements.css') ?>">

<style>
    .box.h-full {
        height: calc(100% - 2rem) !important;
    }

    .expertise {
        border-radius: var(--border-radius);
        background-color: white;
        border: 1px solid #afafaf;
        display: inline-block;
        padding: .2rem .8rem;
        box-shadow: var(--box-shadow);
        margin-right: .5rem;
    }

    .user-role {
        border-radius: var(--border-radius);
        background-color: white;
        border: 1px solid #afafaf;
        display: inline-block;
        padding: .2rem .8rem;
        box-shadow: var(--box-shadow);
        margin-right: .5rem;
        font-family: 'Consolas', 'Courier New', Courier, monospace;
        font-weight: 500;
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
$Achievement->checkAchievements();
$user_ac = $Achievement->userac;


include_once BASEPATH . "/php/Coins.php";
$Coins = new Coins();

$coins = $Coins->getCoins($user);

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

<?php if ($showcoins) { ?>
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

        <?php
        foreach ($scientist['depts'] as $i => $d) {
            $dept = $Groups->getGroup($d);
            if ($i > 0) echo ', ';
        ?>
            <a href="<?= ROOTPATH ?>/groups/view/<?= $dept['id'] ?>" style="color:<?= $dept['color'] ?? 'inherit' ?>">
                <?php if (in_array($user, $dept['head']?? [])) { ?>
                    <i class="ph ph-crown"></i>
                <?php } ?>
                <?= $dept['name'] ?>
            </a>
        <?php } ?>

        <br>

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
                    <?= lang('Maintainer: ' . $DB->getNameFromId($scientist['maintenance'])) ?>
                </span>
            <?php } ?>
        <?php } ?>

        <?php
        // show current guest state
        if (defined('GUEST_FORMS') && GUEST_FORMS) {
            $guestState = $osiris->guests->findOne(['username' => $user]);
            if (!empty($guestState)) { ?>
                <span class="user-role">
                    <?= lang('Guest:', 'Gast:') ?>
                    <?= fromToDate($guestState['start'], $guestState['end'] ?? null) ?>
                </span>
        <?php }
        }
        ?>


        <?php if ($showcoins) { ?>
            <p class="lead m-0">
                <i class="ph ph-lg ph-coin text-signal"></i>
                <b id="lom-points"><?= $coins ?></b>
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

    <div class="alert my-10 pb-20">
        <h5 class="title font-size-16">
            <?= lang('This is your personal profile page.', 'Dies ist deine persönliche Profilseite.') ?>
        </h5>

        <div class="btn-group btn-group-lg mr-5">
            <a class="btn" href="<?= ROOTPATH ?>/activities/new" data-toggle="tooltip" data-title="<?= lang('Add activity', 'Aktivität hinzufügen') ?>">
                <i class="ph ph-plus-circle text-osiris ph-fw"></i>
                <!-- <?= lang('Add activity', 'Aktivität hinzufügen') ?> -->
            </a>
            <a href="<?= ROOTPATH ?>/my-activities" class="btn" data-toggle="tooltip" data-title="<?= lang('My activities', 'Meine Aktivitäten ') ?>">
                <i class="ph ph-folder-user text-blue ph-fw"></i>
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
            <a class="btn" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Edit user profile', 'Bearbeite Profil') ?>">
                <i class="ph ph-edit text-muted ph-fw"></i>
                <!-- <?= lang('Edit user profile', 'Bearbeite Profil') ?> -->
            </a>
            <!-- <a class="btn" href="<?= ROOTPATH ?>/user/visibility/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Configure web profile', 'Webprofil bearbeiten') ?>">
                    <i class="ph ph-eye text-muted ph-fw"></i>
                </a> -->
            <a class="btn" href="<?= ROOTPATH ?>/preview/person/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Preview', 'Vorschau') ?>">
                <i class="ph ph-eye text-muted ph-fw"></i>
            </a>
        </div>


        <?php
        $issues = $DB->getUserIssues($user);
        if (!empty($issues)) {
            $issues = count(array_merge($issues));
        ?>
            <div class="alert danger mt-10">
                <a class="link text-danger text-decoration-none" href='<?= ROOTPATH ?>/issues'>
                    <?= lang(
                        "You have $issues unresolved " . ($issues == 1 ? 'message' : 'messages') . " with your activities.",
                        "Du hast $issues " . ($issues == 1 ? 'ungelöste Benachrichtigung' : 'ungelöste Benachrichtigungen') . " zu deinen Aktivitäten."
                    ) ?>
                </a>
            </div>
        <?php } ?>

        <?php
        $queue = $osiris->queue->count(['authors.user' => $user, 'duplicate' => ['$exists' => false]]);
        if ($queue !== 0) { ?>
            <div class="alert success mt-10">
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
                <div class="alert primary mt-10">
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
            <div class="alert muted mt-10">

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


        <?php
        $new = $Achievement->new;

        if (!empty($new)) {
            echo '<div class="mt-20">';
            echo '<h5 class="title font-size-16">' . lang('Congratulation, you achieved something new: ', 'Glückwunsch, du hast neue Errungenschaften erlangt:') . '</h5>';
            foreach ($new as $i => $n) {
                $Achievement->snack($n);
            }
            $Achievement->save();
            echo '</div>';
        }
        ?>

    </div>

<?php } else { ?>
    <div class="btn-group btn-group-lg mt-15 ml-5">
        <a class="btn" href="<?= ROOTPATH ?>/my-year/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('The year of ', 'Das Jahr von ') . $scientist['first'] ?> ">
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
    <br>
<?php } ?>




<!-- TAB AREA -->

<nav class="pills mt-10 mb-0">
    <a onclick="navigate('general')" id="btn-general" class="btn active"><?= lang('General', 'Allgemein') ?></a>

    <?php
    $publication_filter = [
        'authors.user' => "$user",
        'type' => 'publication'
    ];
    $count_publications = $osiris->activities->count($publication_filter);

    if ($count_publications > 0) { ?>
        <a onclick="navigate('publications')" id="btn-publications" class="btn"><?= lang('Publications', 'Publikationen') . " ($count_publications)" ?></a>
    <?php } ?>

    <?php
    $activities_filter = [
        'authors.user' => "$user",
        'type' => ['$ne' => 'publication']
    ];
    $count_activities = $osiris->activities->count($activities_filter);

    if ($count_activities > 0) { ?>
        <a onclick="navigate('activities')" id="btn-activities" class="btn"><?= lang('Other Activities', 'Andere Aktivitäten') . " ($count_activities)" ?></a>
    <?php } ?>

    <?php
    $membership_filter = [
        'authors.user' => "$user",
        'end' => null,
        '$or' => array(
            ['type' => 'misc', 'subtype' => 'misc-annual'],
            ['type' => 'review', 'subtype' =>  'editorial'],
        )
    ];
    $count_memberships = $osiris->activities->count($membership_filter);
    if ($count_memberships > 0) { ?>
        <a onclick="navigate('memberships')" id="btn-memberships" class="btn"><?= lang('Memberships', 'Mitgliedschaften') . " ($count_memberships)" ?></a>
    <?php } ?>

    <?php

    $project_filter = [
        '$or' => array(
            ['contact' => $user],
            ['persons.user' => $user]
        ),
        "status" => ['$ne' => "rejected"]
    ];

    $count_projects = $osiris->projects->count($project_filter);
    if ($count_projects > 0) { ?>
        <a onclick="navigate('projects')" id="btn-projects" class="btn"><?= lang('Projects', 'Projekte') . " ($count_projects)" ?></a>
    <?php } ?>

</nav>


<?php
$expertise = $scientist['expertise'] ?? array();

if ($currentuser) { ?>
    <div class="modal modal-lg" id="expertise" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content w-600 mw-full">
                <a href="#close-modal" class="btn float-right" role="button" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <h4 class="title mt-0">
                    <?= lang('Expertise') ?>
                </h4>

                <form action="<?= ROOTPATH ?>/update-expertise/<?= $user ?>" method="post" id="expertise-form">
                    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                    <?php foreach ($expertise as $n) { ?>
                        <div class="input-group d-inline-flex w-auto mr-5 mb-10">
                            <input type="text" name="values[expertise][]" value="<?= $n ?>" list="expertise-list" required class="form-control">
                            <div class="input-group-append">
                                <a class="btn" onclick="$(this).closest('.input-group').remove();">&times;</a>
                            </div>
                        </div>
                    <?php } ?>

                    <button class="btn mb-10" type="button" onclick="addName(event, this);">
                        <i class="ph ph-plus"></i>
                    </button>
                    <br>
                    <button class="btn primary small" type="submit"><?= lang('Save changes', 'Änderungen speichern') ?></button>
                </form>
            </div>
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
            var group = $('<div class="input-group d-inline-flex w-auto mr-5 mb-10"> ')
            group.append('<input type="text" name="values[expertise][]" value="" list="expertise-list" required class="form-control">')
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

<?php } ?>

<section id="general">

    <div class="row row-eq-spacing my-0">
        <div class="profile-widget col-md-6 col-lg-4">
            <div class="box h-full">
                <div class="content">
                    <h4 class="title">
                        <?= lang('Details') ?>
                        <?php if ($currentuser) { ?>
                            <a class="font-size-14 ml-10" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>">
                                <i class="ph ph-note-pencil ph-lg"></i>
                            </a>
                        <?php } ?>
                    </h4>
                </div>
                <table class="table simple small">
                    <tbody>
                        <tr>
                            <td>
                                <span class="key"><?= lang('Last name', 'Nachname') ?></span>
                                <?= $scientist['last'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="key"><?= lang('First name', 'Vorname') ?></span>
                                <?= $scientist['first'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="key"><?= lang('Academic title', 'Akademischer Titel') ?></span>
                                <?= $scientist['academic_title'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="key">Email</span>
                                <?= $scientist['mail'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="key"><?= lang('Telephone', 'Telefon') ?></span>
                                <?= $scientist['telephone'] ?? '' ?>
                            </td>
                        </tr>
                        <?php if (!empty($scientist['twitter'] ?? null)) { ?>
                            <tr>
                                <td>
                                    <span class="key">Twitter</span>

                                    <a href="https://twitter.com/<?= $scientist['twitter'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['twitter'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['orcid'] ?? null)) { ?>
                            <tr>
                                <td>
                                    <span class="key">ORCID</span>

                                    <a href="http://orcid.org/<?= $scientist['orcid'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['orcid'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['researchgate'] ?? null)) { ?>
                            <tr>
                                <td>
                                    <span class="key">ResearchGate</span>

                                    <a href="https://www.researchgate.net/profile/<?= $scientist['researchgate'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['researchgate'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['google_scholar'] ?? null)) { ?>
                            <tr>
                                <td>
                                    <span class="key">Google Scholar</span>

                                    <a href="https://scholar.google.com/citations?user=<?= $scientist['google_scholar'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['google_scholar'] ?></a>

                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($scientist['webpage'] ?? null)) {
                            $web = preg_replace('/^https?:\/\//', '', $scientist['webpage']);
                        ?>
                            <tr>
                                <td>
                                    <span class="key">Personal web page</span>

                                    <a href="https://<?= $web ?>" target="_blank" rel="noopener noreferrer"><?= $web ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($currentuser || !empty($scientist['expertise'] ?? array())) { ?>
                            <tr>
                                <td>
                                    <span class="key"><?= lang('Expertise') ?></span>
                                    <?php foreach ($scientist['expertise'] ?? array() as $key) { ?><a href="<?= ROOTPATH ?>/expertise?search=<?= $key ?>" class="badge blue mr-5 mb-5"><?= $key ?></a><?php } ?>
                                    <?php if ($currentuser) { ?> <a href="#expertise" class=""><i class="ph ph-edit"></i></a> <?php } ?>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="profile-widget col-md-6 col-lg-8">
            <div class="box h-full">
                <div class="content">


                    <h4 class="title">
                        <?= lang('Position', 'Position') ?>
                        <?php if ($currentuser) { ?>
                            <a class="font-size-14 ml-10" href="<?= ROOTPATH ?>/user/edit-bio/<?= $user ?>">
                                <i class="ph ph-note-pencil ph-lg"></i>
                            </a>
                        <?php } ?>
                    </h4>
                    <?php if (isset($scientist['position']) && !empty($scientist['position'])) { ?>
                        <p><?= $scientist['position'] ?></p>
                    <?php } else { ?>
                        <p><?= lang('No position given.', 'Keine Position angegeben.') ?></p>
                    <?php } ?>

                    <ul class="breadcrumb">
                        <?php foreach (($scientist['depts'] ?? []) as $D) { ?>
                            <li>
                                <a href="<?= ROOTPATH ?>/groups/view/<?= $D ?>">
                                    <?= $D ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                </div>
                <hr>
                <div class="content">

                    <h4 class="title">
                        <?= lang('Research interest', 'Forschungsinteressen') ?>
                        <?php if ($currentuser) { ?>
                            <a class="font-size-14 ml-10" href="<?= ROOTPATH ?>/user/edit-bio/<?= $user ?>">
                                <i class="ph ph-note-pencil ph-lg"></i>
                            </a>
                        <?php } ?>
                    </h4>

                    <?php if (isset($scientist['research']) && !empty($scientist['research'])) { ?>
                        <ul class="list">
                            <?php foreach ($scientist['research'] as $key) { ?>
                                <li><?= $key ?></li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p><?= lang('No research interests stated.', 'Keine Forschungsinteressen angegeben.') ?></p>
                    <?php } ?>
                </div>
                <hr>
                <div class="content">

                    <h4 class="title">
                        <?= lang('Curriculum Vitae') ?>
                        <?php if ($currentuser) { ?>
                            <a class="font-size-14 ml-10" href="<?= ROOTPATH ?>/user/edit-bio/<?= $user ?>">
                                <i class="ph ph-note-pencil ph-lg"></i>
                            </a>
                        <?php } ?>
                    </h4>

                    <?php if (isset($scientist['cv']) && !empty($scientist['cv'])) {
                        $cv = DB::doc2Arr($scientist['cv']);
                    ?>
                        <div class="biography">
                            <?php foreach ($cv as $entry) { ?>
                                <div class="cv">
                                    <span class="time"><?= $entry['time'] ?></span>
                                    <h5 class="title"><?= $entry['position'] ?></h5>
                                    <span class="affiliation"><?= $entry['affiliation'] ?></span>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p><?= lang('No CV given.', 'Kein CV angegeben.') ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-eq-spacing">
        <?php if ($currentuser && $Settings->hasPermission('complete-dashboard')) {

            $n_scientists = $osiris->persons->count(["roles" => 'scientist', "is_active" => true]);
            $n_approved = $osiris->persons->count(["roles" => 'scientist', "is_active" => true, "approved" => $lastquarter]);
        ?>
            <div class="col-6 col-md-3 profile-widget">
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

    </div>
<?php } ?>

</section>


<section id="publications" style="display:none">

    <h2><?= lang('Publications', 'Publikationen') ?></h2>

    <div class="mt-20 w-full">
        <table class="table dataTable responsive" id="publication-table">
            <thead>
                <tr>
                    <th><?= lang('Type', 'Typ') ?></th>
                    <th><?= lang('Activity', 'Aktivität') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>

        </table>
    </div>

    <div class="row row-eq-spacing my-0">

        <?php
        // IMPACT FACTOR WIDGET
        if (($currentuser || !$Settings->hasFeatureDisabled('user-metrics'))) { ?>
            <div class="profile-widget col-md-6 col-lg-8" id="chart-impact">
                <div class="box h-full">
                    <div class="chart content">
                        <h4 class="title mb-0">
                            <?= lang('Impact factor histogram', 'Impact Factor Histogramm') ?>
                        </h4>
                        <p class="text-muted mt-0"><?= lang('since', 'seit') . " " . $Settings->get('startyear') ?></p>
                        <canvas id="chart-impact-canvas" style="max-height: 30rem;"></canvas>
                    </div>
                </div>
            </div>
        <?php } ?>



        <?php
        // ROLE WIDGET
        if (($currentuser || !$Settings->hasFeatureDisabled('user-metrics'))) { ?>
            <div class="profile-widget col-md-6 col-lg-4" id="chart-authors">
                <div class="box h-full">
                    <div class="chart content">
                        <h4 class="title mb-0">
                            <?= lang('Role in publications', 'Rolle in Publikationen') ?>
                        </h4>
                        <p class="text-muted mt-0"><?= lang('since', 'seit') . " " . $Settings->get('startyear') ?></p>

                        <canvas id="chart-authors-canvas" style="max-height: 30rem;"></canvas>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>


<section id="activities" style="display:none">


    <h2><?= lang('Other activities', 'Andere Aktivitäten') ?></h2>

    <div class="mt-20 w-full">
        <table class="table dataTable responsive" id="activities-table">
            <thead>
                <tr>
                    <th><?= lang('Type', 'Typ') ?></th>
                    <th><?= lang('Activity', 'Aktivität') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>

        </table>
    </div>

    <?php if (($currentuser || !$Settings->hasFeatureDisabled('user-metrics'))) { ?>
        <div class="profile-widget" id="chart-activities">
            <div class="box">
                <div class="chart content">
                    <h4 class="title mb-0">
                        <?= lang('All activities', 'Alle Aktivitäten') ?>
                    </h4>
                    <p class="text-muted mt-0"><?= lang('in which ' . $scientist['first'] . ' was involved', 'an denen ' . $scientist['first'] . ' beteiligt war') ?></p>

                    <canvas id="chart-activities-canvas" style="max-height: 35rem;"></canvas>

                    <small class="text-muted">
                        <?= lang('For multi-year activities, only the start date is relevant.', 'Bei mehrjährigen Aktivitäten wird nur das Startdatum gezählt.') ?>
                    </small>
                </div>
            </div>
        </div>
    <?php } ?>

</section>


<section id="memberships" style="display:none">

    <?php

    if ($count_memberships > 0) {
        $memberships = $osiris->activities->find($membership_filter, ['sort' => ["type" => 1, "year" => -1, "month" => -1, "day" => -1]]);
    ?>

        <div class="profile-widget ">
            <div class="box h-full">
                <div class="content">
                    <h4 class="title"><?= lang('Ongoing memberships', 'Laufende Mitgliedschaften') ?></h4>
                </div>
                <table class="table simple">
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($memberships as $doc) {
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

    <?php } ?>


</section>



<section id="projects" style="display:none">
    <div class="row row-eq-spacing my-0">
        <?php
        if ($count_projects > 0) {
            $projects = $osiris->projects->find($project_filter, ['sort' => ["start" => -1, "end" => -1]]);

            $ongoing = [];
            $past = [];

            require_once BASEPATH . "/php/Project.php";
            $Project = new Project();
            foreach ($projects as $project) {
                $Project->setProject($project);
                if ($Project->inPast()) {
                    $past[] = $Project->widgetLarge($user);
                } else {
                    $ongoing[] = $Project->widgetLarge($user);
                }
            }
            $i = 0;
            $breakpoint = ceil($count_projects / 2);
        ?>
            <div class="profile-widget col-md-6">
                <?php if (!empty($ongoing)) { ?>

                    <h2><?= lang('Ongoing projects', 'Laufende Projekte') ?></h2>
                    <?php foreach ($ongoing as $html) { ?>
                        <?= $html ?>
                    <?php
                        $i++;
                        if ($i == $breakpoint) {
                            echo "</div><div class'col-md-6'>";
                        }
                    } ?>

                <?php } ?>


                <?php if (!empty($past)) { ?>
                    <h3><?= lang('Past projects', 'Vergangene Projekte') ?></h3>

                    <?php foreach ($past as $html) { ?>
                        <?= $html ?>
                    <?php
                        $i++;
                        if ($i == $breakpoint) {
                            echo "</div><div class'col-md-6'>";
                        }
                    } ?>

                <?php } ?>
            </div>



        <?php } ?>
    </div>
</section>

<?php
if (isset($_GET['verbose'])) {
    dump($scientist, true);
}
?>