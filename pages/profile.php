<?php

/**
 * Page to see scientists profile
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /profile/<username>
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */
?>

<?php

if (defined('OSIRIS_DB_VERSION') && OSIRIS_DB_VERSION != OSIRIS_VERSION) { ?>
    <div class="alert danger mb-20">
        <h3 class="title"><?= lang('Warning', 'Warnung') ?></h3>
        <?= lang('
        A new OSIRIS-Version has been found. Please click <a href="' . ROOTPATH . '/migrate">here</a> to migrate', '
        Eine neue OSIRIS-Version wurde gefunden. Bitte klicke <a href="' . ROOTPATH . '/migrate">hier</a>, um zu migrieren.') ?>
        <small>Installed: <?= OSIRIS_DB_VERSION ?></small>
    </div>
<?php } ?>


<!-- all necessary javascript -->
<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>
<script src="<?= ROOTPATH ?>/js/chartjs-plugin-datalabels.min.js"></script>
<script src="<?= ROOTPATH ?>/js/d3.v4.min.js"></script>
<script src="<?= ROOTPATH ?>/js/popover.js"></script>
<script src="<?= ROOTPATH ?>/js/d3-chords.js?v=2"></script>
<script src="<?= ROOTPATH ?>/js/d3.layout.cloud.js"></script>

<!-- all variables for this page -->
<script>
    const CURRENT_USER = '<?= $user ?>';
</script>
<script src="<?= ROOTPATH ?>/js/profile.js?v=4"></script>


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

if ($Settings->featureEnabled('achievements')) {
    $Achievement = new Achievement($osiris);
    $Achievement->initUser($user);
    $Achievement->checkAchievements();
    $user_ac = $Achievement->userac;
    $show_achievements =  !empty($user_ac) && !($scientist['hide_achievements'] ?? false);
} else {
    $show_achievements = false;
}


// $showcoins = (!($scientist['hide_coins'] ?? true));
if (!$Settings->featureEnabled('coins')) {
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

if ($showcoins) {
    if (!isset($_SESSION['coins']) || empty($_SESSION['coins'])) {
        include_once BASEPATH . "/php/Coins.php";
        $Coins = new Coins();
        $coins = $Coins->getCoins($user);
        $_SESSION['coins'] = $coins;
    } else {
        $coins = $_SESSION['coins'];
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


if ($currentuser || $Settings->hasPermission('user.image')) { ?>
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

                <form action="<?= ROOTPATH ?>/crud/users/profile-picture/<?= $user ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                    <div class="custom-file mb-20" id="file-input-div">
                        <input type="file" id="profile-input" name="file" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                        <label for="profile-input"><?= lang('Upload new profile image', 'Lade ein neues Profilbild hoch') ?></label>
                        <br><small class="text-danger">Max. 2 MB.</small>
                    </div>

                    <p>
                        <?= lang('Please note that your profile picture will be visible to all users of OSIRIS.', 'Bitte beachte, dass dein Profilbild für alle OSIRIS-Personen sichtbar sein wird.') ?>
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
                    <button class="btn secondary">
                        <i class="ph ph-upload"></i>
                        Upload
                    </button>
                </form>

                <?php if (true) { ?>
                    <hr>
                    <form action="<?= ROOTPATH ?>/crud/users/update-profile/<?= $user ?>" method="post">
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
            <?= $Settings->printProfilePicture($user, 'profile-img') ?>
            <?php if ($currentuser || $Settings->hasPermission('user.image')) { ?>
                <a href="#change-picture" class="position-absolute p-10 bottom-0 right-0 text-white"><i class="ph ph-edit"></i></a>
            <?php } ?>
        </div>
    </div>
    <div class="col ml-20">
        <h1 class="mt-0"><?= $name ?></h1>
        <h5 class="subtitle">
            <?= lang($scientist['position'] ?? '', $scientist['position_de'] ?? null) ?>
            <?php if ($scientist['hide'] ?? false) { ?>
                <small class="badge danger" data-toggle="tooltip" data-title="<?= lang('This person does not wish to be found in Portfolio', 'Diese Person möchte nicht in OSIRIS Portfolio gefunden werden.') ?>">
                    <i class="ph ph-globe-x m-0"></i>
                </small>
            <?php } ?>

        </h5>

        <style>
            .dept-list {
                list-style: none;
                padding: 0;
                margin: .5rem 0;
            }

            .dept-list li {
                margin: 0;
            }
        </style>
        <ul class="dept-list">
            <?php
            $depts = DB::doc2Arr($scientist['depts'] ?? []);
            if (in_array(null, $depts)) {
                // filter and change in database
                $depts = array_filter($depts);
                $osiris->scientists->updateOne(
                    ['username' => $user],
                    ['$set' => ['depts' => $depts]]
                );
            }

            if (!empty($scientist['depts'])) foreach ($depts as $i => $d) {
                $dept = $Groups->getGroup($d);
            ?>
                <li>
                    <a href="<?= ROOTPATH ?>/groups/view/<?= $dept['id'] ?>" style="color:<?= $dept['color'] ?? 'inherit' ?>">
                        <?php if (in_array($user, $dept['head'] ?? [])) { ?>
                            <i class="ph ph-crown"></i>
                        <?php } ?>
                        <?= $dept['name'] ?>
                        (<?= $dept['unit'] ?>)
                    </a>
                </li>
            <?php } ?>

        </ul>

        <?php if (!($scientist['is_active'] ?? true)) { ?>
            <span class="text-danger badge">
                <?= lang('Former Employee', 'Ehemalige Beschäftigte') ?>
            </span>
        <?php } ?>

        <!-- <span class="badge">Last login: <?= $scientist['lastlogin'] ?></span> -->
        <?php
        // show current guest state
        if ($Settings->featureEnabled('guests')) {
            $guestState = $osiris->guests->findOne(['username' => $user]);
            if (!empty($guestState)) { ?>
                <span class="badge">
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

    <div class="achievements text-right" style="max-width: 35rem;">
        <?php
        if ($show_achievements) {
        ?>
            <h5 class="m-0"><?= lang('Achievements', 'Errungenschaften') ?>:</h5>

            <?php
            $Achievement->widget();
            ?>
        <?php
        } ?>

        <?php foreach ($scientist['roles'] as $role) { ?>
            <span class="badge">
                <?= strtoupper($role) ?>
            </span>
        <?php } ?>
    </div>
</div>



<?php if ($currentuser) { ?>

    <div class="card my-10 pb-20">
        <h5 class="title font-size-16">
            <?= lang('This is your personal profile page.', 'Dies ist deine persönliche Profilseite.') ?>
        </h5>
        <div class="btn-toolbar">

            <div class="btn-group btn-group-lg">
                <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/add-activity" data-toggle="tooltip" data-title="<?= lang('Add activity', 'Aktivität hinzufügen') ?>">
                    <i class="ph ph-plus-circle ph-fw"></i>
                    <!-- <?= lang('Add activity', 'Aktivität hinzufügen') ?> -->
                </a>
                <a href="<?= ROOTPATH ?>/my-activities" class="btn text-primary border-primary" data-toggle="tooltip" data-title="<?= lang('My activities', 'Meine Aktivitäten ') ?>">
                    <i class="ph ph-folder-user ph-fw"></i>
                    <!-- <?= lang('My activities', 'Meine Aktivitäten ') ?> -->
                </a>
                <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/my-year/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('My Year', 'Mein Jahr') ?>">
                    <i class="ph ph-calendar ph-fw"></i>
                    <!-- <?= lang('My Year', 'Mein Jahr') ?> -->
                </a>

                <?php if ($Settings->featureEnabled('portal')) { ?>
                    <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/preview/person/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Preview', 'Vorschau') ?>">
                        <i class="ph ph-eye ph-fw"></i>
                    </a>
                <?php } ?>

            </div>
            <div class="btn-group btn-group-lg">
                <?php if ($show_achievements) { ?>
                    <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/achievements" data-toggle="tooltip" data-title="<?= lang('My Achievements', 'Meine Errungenschaften') ?>">
                        <i class="ph ph-trophy ph-fw"></i>
                    </a>
                <?php } ?>

            </div>

            <div class="btn-group btn-group-lg">
                <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Edit user profile', 'Bearbeite Profil') ?>">
                    <i class="ph ph-edit ph-fw"></i>
                    <!-- <?= lang('Edit user profile', 'Bearbeite Profil') ?> -->
                </a>
                <!-- <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/user/visibility/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Configure web profile', 'Webprofil bearbeiten') ?>">
                    <i class="ph ph-eye ph-fw"></i>
                </a> -->

            </div>
            <form action="<?= ROOTPATH ?>/download" method="post">

                <input type="hidden" name="filter[user]" value="<?= $user ?>">
                <input type="hidden" name="highlight" value="user">
                <input type="hidden" name="format" value="word">
                <input type="hidden" name="type" value="cv">

                <button class="btn text-primary border-primary large" data-toggle="tooltip" data-title="<?= lang('Export CV', 'CV exportieren') ?>">
                    <i class="ph ph-identification-card text-primary ph-fw"></i>
                </button>
            </form>
        </div>
    </div>

<?php } else { ?>
    <div class="btn-toolbar">
        <div class="btn-group btn-group-lg">
            <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/my-year/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('The year of ', 'Das Jahr von ') . $scientist['first'] ?> ">
                <i class="ph ph-calendar ph-fw"></i>
            </a>
            <a href="<?= ROOTPATH ?>/my-activities?user=<?= $user ?>" class="btn text-primary border-primary" data-toggle="tooltip" data-title="<?= lang('All activities of ', 'Alle Aktivitäten von ') . $scientist['first'] ?>">
                <i class="ph ph-folder-user ph-fw"></i>
            </a>
            <a href="<?= ROOTPATH ?>/visualize/coauthors?scientist=<?= $user ?>" class="btn text-primary border-primary" data-toggle="tooltip" data-title="<?= lang('Coauthor Network of ', 'Koautoren-Netzwerk von ') . $scientist['first'] ?>">
                <i class="ph ph-graph ph-fw"></i>
            </a>
            <?php if ($show_achievements) { ?>
                <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/achievements/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Achievements of ', 'Errungenschaften von ') . $scientist['first'] ?>">
                    <i class="ph ph-trophy ph-fw"></i>
                </a>
            <?php } ?>

        </div>
        <?php if ($Settings->featureEnabled('portal')) { ?>
            <div class="btn-group btn-group-lg">
                <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/preview/person/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Preview', 'Vorschau') ?>">
                    <i class="ph ph-eye ph-fw"></i>
                </a>
            </div>
        <?php } ?>
        <div class="btn-group btn-group-lg">
            <?php if ($Settings->hasPermission('user.edit')) { ?>
                <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Edit user profile', 'Bearbeite Profil') ?>">
                    <i class="ph ph-edit ph-fw"></i>
                </a>
            <?php } ?>
            <?php if (($scientist['is_active'] ?? true) && $Settings->hasPermission('user.inactive')) { ?>
                <a class="btn text-primary border-primary" href="<?= ROOTPATH ?>/user/delete/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Inactivate user', 'Nutzer:in inaktivieren') ?>">
                    <i class="ph ph-trash ph-fw"></i>
                </a>
            <?php } ?>

        </div>
    </div>

    <?php if (($Settings->hasPermission('report.dashboard')) && isset($scientist['approved'])) {
        $approvedQ = DB::doc2Arr($scientist['approved']);
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




<!-- TAB AREA -->

<nav class="pills mt-20 mb-0">
    <?php if ($currentuser) { ?>
        <a onclick="navigate('news')" id="btn-news" class="btn active">
            <i class="ph ph-star" aria-hidden="true"></i>
            <?= lang('News') ?>
        </a>
    <?php } ?>

    <a onclick="navigate('general')" id="btn-general" class="btn <?= $currentuser ? '' : 'active' ?>">
        <i class="ph ph-info" aria-hidden="true"></i>
        <?= lang('General', 'Allgemein') ?>
    </a>

    <?php
    $publication_filter = [
        'authors.user' => "$user",
        'type' => 'publication'
    ];
    $count_publications = $osiris->activities->count($publication_filter);

    if ($count_publications > 0) { ?>
        <a onclick="navigate('publications')" id="btn-publications" class="btn">
            <i class="ph ph-books" aria-hidden="true"></i>
            <?= lang('Publications', 'Publikationen')  ?>
            <span class="index"><?= $count_publications ?></span>
        </a>
    <?php } ?>

    <?php
    $coauthors = $osiris->activities->aggregate([
        ['$match' => ['type' => 'publication', 'authors.user' => $user, 'year' => ['$gte' => CURRENTYEAR - 4]]],
        ['$unwind' => '$authors'],
        ['$match' => ['authors.user' => ['$ne' => null]]],
        [
            '$group' => [
                '_id' => '$authors.user',
                'count' => ['$sum' => 1]
            ]
        ],
    ])->toArray();
    $count_coauthors = count($coauthors) - 1;
    if ($count_coauthors > 0) { ?>
        <a onclick="navigate('coauthors')" id="btn-coauthors" class="btn">
            <i class="ph ph-users" aria-hidden="true"></i>
            <?= lang('Coauthors', 'Koautoren')  ?>
            <span class="index"><?= $count_coauthors ?></span>
        </a>
    <?php } ?>

    <?php
    $activities_filter = [
        'authors.user' => "$user",
        'type' => ['$ne' => 'publication']
    ];
    $count_activities = $osiris->activities->count($activities_filter);

    if ($count_activities > 0) { ?>
        <a onclick="navigate('activities')" id="btn-activities" class="btn">
            <i class="ph ph-briefcase" aria-hidden="true"></i>
            <?= lang('Activities', 'Aktivitäten')  ?>
            <span class="index"><?= $count_activities ?></span>
        </a>
    <?php } ?>

    <?php
    $membership_filter = [
        'authors.user' => "$user",
        // 'end' => null,
        '$or' => array(
            ['type' => 'misc', 'subtype' => 'misc-annual'],
            ['type' => 'review', 'subtype' =>  'editorial'],
        )
    ];
    $count_memberships = $osiris->activities->count($membership_filter);
    if ($count_memberships > 0) { ?>
        <a onclick="navigate('memberships')" id="btn-memberships" class="btn">
            <i class="ph ph-user-list" aria-hidden="true"></i>
            <?= lang('Committee work', 'Gremienarbeit')  ?>
            <span class="index"><?= $count_memberships ?></span>
        </a>
    <?php } ?>

    <?php if ($Settings->featureEnabled('projects')) { ?>
        <?php
        $project_filter = [
            '$or' => array(
                ['contact' => $user],
                ['persons.user' => $user]
            ),
            "status" => ['$in' => ["approved", 'finished']]
        ];

        $count_projects = $osiris->projects->count($project_filter);
        if ($count_projects > 0) { ?>
            <a onclick="navigate('projects')" id="btn-projects" class="btn">
                <i class="ph ph-tree-structure" aria-hidden="true"></i>
                <?= lang('Projects', 'Projekte')  ?>
                <span class="index"><?= $count_projects ?></span>
            </a>
        <?php } ?>
    <?php } ?>


    <!-- Teaching activities -->
    <?php
    $teaching = $osiris->activities->aggregate([
        ['$match' => ['authors.user' => $user, 'type' => 'teaching', 'module_id' => ['$ne' => null]]],
        [
            '$group' => [
                '_id' => '$module_id',
                'count' => ['$sum' => 1],
                'doc' => ['$push' => '$$ROOT']
            ]
        ],
        ['$sort' => ['count' => -1]]
    ])->toArray();
    $count_teaching = count($teaching);

    if ($count_teaching > 0) { ?>
        <a onclick="navigate('teaching')" id="btn-teaching" class="btn">
            <i class="ph ph-graduation-cap" aria-hidden="true"></i>
            <?= lang('Teaching', 'Lehre')  ?>
            <span class="index"><?= $count_teaching ?></span>
        </a>
    <?php } ?>



    <?php if ($Settings->featureEnabled('wordcloud')) { ?>
        <?php
        $count_wordcloud = $osiris->activities->count(['title' => ['$exists' => true], 'authors.user' => $user, 'type' => 'publication']);
        if ($count_wordcloud > 0) { ?>
            <a onclick="navigate('wordcloud')" id="btn-wordcloud" class="btn">
                <i class="ph ph-cloud" aria-hidden="true"></i>
                <?= lang('Word cloud')  ?>
            </a>
        <?php } ?>
    <?php } ?>

    <?php if ($Settings->featureEnabled('concepts')) { ?>
        <?php
        $concepts = [];
        $concepts = $osiris->activities->aggregate(
            [
                ['$match' => ['authors.user' => $user, 'concepts' => ['$exists' => true]]],
                ['$project' => ['concepts' => 1]],
                [
                    '$group' => [
                        '_id' => null,
                        'total' => ['$sum' => 1],
                        'concepts' => ['$push' => '$concepts']
                    ]
                ],
                ['$unwind' => '$concepts'],
                ['$unwind' => '$concepts'],
                ['$group' => [
                    '_id' => '$concepts.display_name',
                    'count' => ['$sum' => 1],
                    'score' => ['$sum' => ['$divide' => [
                        ['$multiply' => ['$concepts.score', ['$sum' => 1]]],
                        '$total'
                    ]]],
                    'concept' => ['$first' => '$concepts']
                ]],
                ['$match' => ['score' => ['$gte' => 0.05]]],
                ['$sort' => ['score' => -1]]
            ]
        )->toArray();
        $count_concepts = count($concepts);
        if ($count_concepts > 0) { ?>
            <a onclick="navigate('concepts')" id="btn-concepts" class="btn">
                <i class="ph ph-lightbulb" aria-hidden="true"></i>
                <?= lang('Concepts', 'Konzepte')  ?>
                <span class="index"><?= $count_concepts ?></span>
            </a>
        <?php } ?>
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

                <form action="<?= ROOTPATH ?>/crud/users/update-expertise/<?= $user ?>" method="post" id="expertise-form">
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
                    <button class="btn secondary small" type="submit"><?= lang('Save changes', 'Änderungen speichern') ?></button>
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

<?php if ($currentuser) { ?>
    <section id="news">
        <div class="row row-eq-spacing my-0">
            <div class="col-md-6">
                <div class="box h-full">
                    <div class="content">
                        <h4 class="title">
                            <?= lang('Notifications', 'Benachrichtigungen') ?>
                        </h4>
                        <?php
                        $notification = false;
                        ?>

                        <p class="text-muted">
                            <?= lang('Here you can find the latest news about OSIRIS and your activities.', 'Hier findest du die neuesten Nachrichten über OSIRIS und deine Aktivitäten.') ?>
                        </p>


                        <?php
                        $issues = $DB->getUserIssues($user);
                        if (!empty($issues)) {
                            $notification = true;
                            // dump(array_merge(array_values($issues)), true;
                            $n_issues = array_sum(array_map("count", $issues));
                            $approvalDict = [
                                'approval' => lang('Approval of activities', 'Freigabe von Aktivitäten'),
                                'epub' => '<em>Online ahead of print</em>-' . lang('Publications', 'Publikationen'),
                                'students' => lang('Expired theses', 'Abgelaufene Abschlussarbeiten'),
                                'openend' => lang('Ongoing activities', 'Laufende Aktivitäten'),
                                'project-open' => lang('Open project applications', 'Offene Projektanträge'),
                                'project-end' => lang('Expired publications', 'Abgelaufene Projekte'),
                            ];
                        ?>
                            <a class="alert danger mt-10 d-block colorless" href='<?= ROOTPATH ?>/issues'>
                                <h5 class="title mb-10">
                                    <?= lang(
                                        "You have <b>$n_issues</b> " . ($n_issues == 1 ? 'message' : 'messages') . " for your activities.",
                                        "Du hast <b>$n_issues</b> " . ($n_issues == 1 ? 'Benachrichtigung' : 'Benachrichtigungen') . " zu deinen Aktivitäten."
                                    ) ?>
                                </h5>
                                <?= lang('Please review the following', 'Bitte überprüfe die folgenden Probleme') ?>:
                                <ul class="list danger mb-0">
                                    <?php foreach ($issues as $key => $val) {
                                        $val = count($val);
                                    ?>
                                        <li>
                                            <?= $approvalDict[$key] ?? lang('Issues', 'Probleme') ?>:
                                            <b><?= $val ?></b>
                                        </li>
                                    <?php } ?>
                                </ul>

                            </a>
                        <?php } ?>

                        <?php
                        $queue = $osiris->queue->count(['authors.user' => $user, 'duplicate' => ['$exists' => false]]);
                        if ($queue !== 0) {
                            $notification = true;
                        ?>
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
                            if (empty($scientist['lastversion'] ?? '') || $scientist['lastversion'] !== OSIRIS_VERSION) {
                                $notification = true;
                        ?>
                                <div class="alert secondary mt-10">
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
                            $approvedQ = DB::doc2Arr($scientist['approved']);
                        }


                        if ($Settings->hasPermission('scientist') && !in_array($lastquarter, $approvedQ)) {
                            $notification = true;
                        ?>
                            <div class="alert success bg-light mt-10">

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

                                <a class="btn success filled" href="<?= ROOTPATH ?>/my-year/<?= $user ?>?year=<?= $Y ?>&quarter=<?= $Q ?>">
                                    <?= lang('Review & Approve', 'Überprüfen & Freigeben') ?>
                                </a>
                            </div>
                        <?php } ?>


                        <?php
                        if ($show_achievements) {
                            $new = $Achievement->new;

                            if (!empty($new)) {
                                $notification = true;
                                echo '<div class="mt-20">';
                                echo '<h5 class="title font-size-16">' . lang('Congratulation, you achieved something new: ', 'Glückwunsch, du hast neue Errungenschaften erlangt:') . '</h5>';
                                foreach ($new as $i => $n) {
                                    $Achievement->snack($n);
                                }
                                $Achievement->save();
                                echo '</div>';
                            }
                        }
                        ?>
                        <?php if (!$notification) { ?>
                            <p>
                                <?= lang('There are no new notifications.', 'Es gibt keine neuen Benachrichtigungen.') ?>
                            </p>
                        <?php } ?>


                    </div>
                    <hr>
                    <div class="content">
                        <h4 class="title">
                            <?= lang('Newest publications', 'Neuste Publikationen') ?>
                        </h4>
                        <p class="text-muted">
                            <?= lang('Here you can find the latest publications from your institute.', 'Hier findest du die neusten Publikationen deines Instituts.') ?>
                        </p>

                        <?php
                        $pubs = $osiris->activities->find(
                            ['authors.aoi' => true, 'type' => 'publication'],
                            [
                                'sort' => ['year' => -1, 'month' => -1],
                                'limit' => 5,
                                'projection' => ['html' => '$rendered.web', 'date' => '$rendered.start']
                            ]
                        )->toArray();
                        ?>
                        <table class="table simple">
                            <?php foreach ($pubs as $doc) { ?>
                                <tr>
                                    <td>
                                        <small class="badge primary font-weight-bold"><?= format_date($doc['date']) ?></small><br>
                                        <?= $doc['html'] ?>
                                    </td>
                                </tr>
                            <?php } ?>

                        </table>

                        <a href="<?= ROOTPATH ?>/activities" class="btn primary">
                            <?= lang('All activities', 'Zeige alle Aktivitäten') ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="box h-full">
                    <div class="content">
                        <?php if ($Settings->hasPermission('conferences.edit')) { ?>
                            <a href="<?= ROOTPATH ?>/conferences#add-conference" class="float-md-right btn primary">
                                <i class="ph ph-plus"></i>
                                <?= lang('Add', 'Hinzufügen') ?>
                            </a>
                        <?php } ?>

                        <h4 class="title">
                            <a href="<?= ROOTPATH ?>/conferences" class="link">
                                <?= lang('Conferences', 'Konferenzen') ?>
                            </a>
                        </h4>
                        <p class="text-muted">
                            <?= lang('Shown are approaching conferences and conferences within the past three month.', 'Gezeigt sind zukünftige Konferenzen und vergangene aus den letzten drei Monaten.') ?>
                            <br>
                            <small> <?= lang('Conferences were added by users of the OSIRIS system.', 'Konferenzen wurden von Nutzenden des OSIRIS-Systems angelegt.') ?></small>
                        </p>

                        <?php
                        // conferences max past 3 month
                        $conferences = $osiris->conferences->find(
                            ['start' => ['$gte' => date('Y-m-d', strtotime('-3 month'))]],
                            ['sort' => ['start' => 1]]
                        )->toArray();
                        ?>
                        <table class="table simple">
                            <?php foreach ($conferences as $n => $c) {
                                // 
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="m-0">
                                                <a href="<?= ROOTPATH ?>/conferences/<?= $c['_id'] ?>">
                                                    <?= $c['title'] ?>
                                                </a>
                                                <?php if (!empty($c['url'] ?? null)) { ?>
                                                    <a href="<?= $c['url'] ?>" target="_blank" rel="noopener noreferrer">
                                                        <i class="ph ph-link"></i>
                                                    </a>
                                                <?php } ?>
                                            </h6>

                                            <!-- <a class="" onclick="toggleDetails(this)">
                                                <i class="ph ph-caret-down"></i>
                                            </a> -->
                                        </div>
                                        <p class="my-5 text-muted">
                                            <?= $c['title_full'] ?? '' ?>
                                        </p>
                                        <p class="my-5 text-muted">
                                            <small class="text- mr-10">
                                                <?= fromToDate($c['start'], $c['end']) ?>
                                            </small>
                                            <small>
                                                <?= $c['location'] ?>
                                            </small>
                                        </p>

                                        <div class="btn-toolbar font-size-12">
                                            <?php
                                            // check if conference is in the future
                                            if (strtotime($c['end']) > time()) {
                                                $days = ceil((strtotime($c['start']) - time()) / 86400);
                                                $days = $days > 0 ? $days : 0;
                                                $days = $days == 0 ? lang('today', 'heute') : 'in ' . $days . ' ' . lang('days', 'Tagen');

                                                // user is interested in conference
                                                $interest = in_array($user, DB::doc2Arr($c['interests'] ?? []));
                                                $participate = in_array($user, DB::doc2Arr($c['participants'] ?? []));
                                                $interestTooltip = $interest ? lang('Click to remove interest', 'Klicken um Interesse zu entfernen') : lang('Click to show interest', 'Klicken um Interesse zu zeigen');
                                                $participateTooltip = $participate ? lang('Click to remove participation', 'Klicken um Teilnahme zu entfernen') : lang('Click to show participation', 'Klicken um Teilnahme zu zeigen');
                                            ?>
                                                <div class="btn-group">
                                                    <small class="btn small cursor-default">
                                                        <?= $days ?>
                                                    </small>
                                                    <a class="btn small" href="<?= ROOTPATH ?>/conference/ics/<?= $c['_id'] ?>" data-toggle="tooltip" data-title="<?= lang('Add to calendar', 'Zum Kalender hinzufügen') ?>">
                                                        <i class="ph ph-calendar-plus"></i>
                                                    </a>
                                                </div>
                                                <div class="btn-group">
                                                    <a class="btn small <?= $interest ? 'active primary' : '' ?>" onclick="conferenceToggle(this, '<?= $c['_id'] ?>', 'interests')" data-toggle="tooltip" data-title="<?= $interestTooltip ?>">
                                                        <b><?= count($c['interests'] ?? []) ?></b>
                                                        <?= lang('Interested', 'Interessiert') ?>
                                                    </a>
                                                    <a class="btn small <?= $participate ? 'active primary' : '' ?>" onclick="conferenceToggle(this, '<?= $c['_id'] ?>', 'participants')" data-toggle="tooltip" data-title="<?= $participateTooltip ?>">
                                                        <b><?= count($c['participants'] ?? []) ?></b>
                                                        <?= lang('Participants', 'Teilnehmer') ?>
                                                    </a>
                                                </div>
                                            <?php } else { ?>
                                                <a class="btn small primary" href="<?= ROOTPATH ?>/add-activity?type=poster&conference=<?= $c['_id'] ?>">
                                                    <i class="ph ph-plus-circle"></i>
                                                    <?= lang('Add contribution', 'Beitrag hinzufügen') ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>



<section id="general" <?= $currentuser ? 'style="display:none"' : '' ?>>

    <div class="row row-eq-spacing my-0">
        <div class="col-md-6 col-lg-4">
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
                        <!-- <tr>
                            <td>
                                <span class="key"><?= lang('Username', 'Benutzername') ?></span>
                                <?= $user ?>
                            </td>
                        </tr> -->
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
                                    <?php foreach ($scientist['expertise'] ?? array() as $key) { ?><a href="<?= ROOTPATH ?>/expertise?search=<?= $key ?>" class="badge primary mr-5 mb-5"><?= $key ?></a><?php } ?>
                                    <?php if ($currentuser) { ?> <a href="#expertise" class=""><i class="ph ph-edit"></i></a> <?php } ?>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-6 col-lg-8">
            <div class="box h-full">
                <div class="content">

                    <h4 class="title">
                        <?= lang('Research interest', 'Forschungsinteressen') ?>
                        <?php if ($currentuser || $Settings->hasPermission('user.edit')) { ?>
                            <a class="font-size-14 ml-10" href="<?= ROOTPATH ?>/user/edit-bio/<?= $user ?>#research">
                                <i class="ph ph-note-pencil ph-lg"></i>
                            </a>
                        <?php } ?>
                    </h4>

                    <?php if (isset($scientist['research']) && !empty($scientist['research'])) {
                        $scientist['research_de'] = array_map(
                            fn($val1, $val2) => empty($val1) ? $val2 : $val1,
                            DB::doc2Arr($scientist['research_de'] ?? $scientist['research']),
                            DB::doc2Arr($scientist['research'])
                        );
                        $research = lang($scientist['research'], $scientist['research_de'] ?? null);
                    ?>
                        <ul class="list">
                            <?php foreach ($research as $key) { ?>
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
                        <?php if ($currentuser || $Settings->hasPermission('user.edit')) { ?>
                            <a class="font-size-14 ml-10" href="<?= ROOTPATH ?>/user/edit-bio/<?= $user ?>#cv">
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
        <?php if ($currentuser && $Settings->hasPermission('report.dashboard')) {

            $n_scientists = $osiris->persons->count(["roles" => 'scientist', "is_active" => true]);
            $n_approved = $osiris->persons->count(["roles" => 'scientist', "is_active" => true, "approved" => $lastquarter]);
        ?>
            <div class="col-6 col-md-3 ">
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
                                            '#00808395',
                                            '#f7810495',
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


            <?php if ($currentuser && $Settings->hasPermission('report.generate')) { ?>
                <div class="col-6 col-md-3 ">
                    <div class=" h-full">
                        <div class="py-10">
                            <div class="link-list">
                                <?php if ($Settings->hasPermission('report.dashboard')) { ?>
                                    <a class="border" href="<?= ROOTPATH ?>/dashboard"><?= lang('Dashboard', 'Dashboard') ?></a>
                                <?php } ?>

                                <?php if ($Settings->hasPermission('report.queue')) { ?>
                                    <a class="border" href="<?= ROOTPATH ?>/queue/editor"><?= lang('Queue', 'Warteschlange') ?></a>
                                <?php } ?>

                                <?php if ($Settings->hasPermission('report.generate')) { ?>
                                    <a class="border" href="<?= ROOTPATH ?>/reports"><?= lang('Reports', 'Berichte') ?></a>
                                <?php } ?>

                                <?php if ($Settings->hasPermission('activities.lock')) { ?>
                                    <a class="border" href="<?= ROOTPATH ?>/controlling"><?= lang('Lock activities', 'Aktivitäten sperren') ?></a>
                                <?php } ?>

                                <?php if ($Settings->hasPermission('admin.see')) { ?>
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
        if (($currentuser || $Settings->featureEnabled('user-metrics'))) { ?>
            <div class="col-md-6 col-lg-8" id="chart-impact">
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
        if (($currentuser || $Settings->featureEnabled('user-metrics'))) { ?>
            <div class="col-md-6 col-lg-4" id="chart-authors">
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

    <?php if (($currentuser || $Settings->featureEnabled('user-metrics'))) { ?>
        <div class="" id="chart-activities">
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
        $ongoing = [];
        $past = [];

        foreach ($memberships as $doc) {
            $element = [
                '_id' => $doc['_id'],
                'icon' => $doc['rendered']['icon'],
                'web' => $doc['rendered']['web'],
            ];
            if (empty($doc['end']) || new DateTime() < getDateTime($doc['end'])) {
                $ongoing[] = $element;
            } else {
                $past[] = $element;
            }
        }
    ?>

        <div class="">
            <?php if (!empty($ongoing)) { ?>
                <div class="box">
                    <div class="content">
                        <h4 class="title"><?= lang('Ongoing committee works', 'Laufende Gremienarbeit') ?></h4>
                    </div>
                    <table class="table simple">
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($ongoing as $doc) {
                                $id = $doc['_id'];
                            ?>
                                <tr id='tr-<?= $id ?>'>
                                    <td class="w-50"><?= $doc['icon']; ?></td>
                                    <td>
                                        <?= $doc['web'] ?>
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
            <?php } ?>
            <?php if (!empty($past)) { ?>
                <div class="box">
                    <div class="content">
                        <h4 class="title"><?= lang('Past committee works', 'Vergangene Gremienarbeiten') ?></h4>
                    </div>
                    <table class="table simple">
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($past as $doc) {
                                $id = $doc['_id'];
                            ?>
                                <tr id='tr-<?= $id ?>'>
                                    <td class="w-50"><?= $doc['icon']; ?></td>
                                    <td>
                                        <?= $doc['web'] ?>
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
            <?php } ?>

        </div>

    <?php } ?>


</section>



<?php if ($Settings->featureEnabled('projects')) { ?>
    <section id="projects" style="display:none">
        <h3 class="title">
            <?= lang('Timeline of all approved projects', 'Zeitstrahl aller bewilligten Projekte') ?>
        </h3>
        <div class="box">
            <div class="content">
                <div id="project-timeline"></div>
            </div>
        </div>
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
        ?>
            <?php if (!empty($ongoing)) { ?>
                <h2><?= lang('Ongoing projects', 'Laufende Projekte') ?></h2>

                <div class="row row-eq-spacing my-0">
                    <?php foreach ($ongoing as $html) { ?>
                        <div class="col-md-6">
                            <?= $html ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (!empty($past)) { ?>
                <h2><?= lang('Past projects', 'Vergangene Projekte') ?></h2>

                <div class="row row-eq-spacing my-0">
                    <?php foreach ($past as $html) { ?>
                        <div class="col-md-6">
                            <?= $html ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>


        <?php } ?>

    </section>
<?php } ?>


<section id="coauthors" style="display:none">
    <h2>
        <i class="ph ph-graph" aria-hidden="true"></i>
        <?= lang('Coauthor network of', 'Koautoren-Netzwerk von') ?> <?= $scientist['displayname'] ?>
    </h2>
    <p class="text-muted">
        <?= lang('Based on publications within the past 5 years.', 'Basierend auf Publikationen aus den vergangenen 5 Jahren.') ?>
    </p>
    <div class="box">
        <div class="row">
            <div class="col-md-8" style="max-width: 80rem">
                <div id="chord"></div>
            </div>
            <div class="col-md-4">
                <div id="legend"></div>
            </div>
        </div>
    </div>

</section>

<?php if ($count_teaching > 0) { ?>
    <section id="teaching" style="display: none;">

        <h2><?= lang('Teaching activities', 'Lehrtätigkeiten') ?></h2>

        <div class="row row-eq-spacing">
            <?php foreach ($teaching as $t) {
                $module = $osiris->teaching->findOne(['_id' => DB::to_ObjectID($t['_id'])]);
            ?>
                <div class="col-md-6">
                    <div class="box mb-0" id="<?= $t['_id'] ?>">
                        <div class="content">
                            <h5 class="mt-0">
                                <span class="highlight-text"><?= $module['module'] ?></span>
                                <?= $module['title'] ?>
                            </h5>

                            <em><?= $module['affiliation'] ?></em>
                        </div>

                        <hr>
                        <div class="content">
                            <?php
                            $activities = $t['doc'] ?? [];
                            if (count($activities) != 0) {
                            ?>
                                <table class="w-full">
                                    <?php foreach ($activities as $n => $doc) :
                                        if (!isset($doc['rendered'])) renderActivities(['_id' => $doc['_id']]);
                                    ?>
                                        <tr>
                                            <td class="pb-5">
                                                <?= $doc['rendered']['icon'] ?>
                                                <?= $doc['rendered']['web'] ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>


                            <?php } else { ?>

                                <?= lang('No activities connected.', 'Keine Aktivitäten verknüpft.') ?>

                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

    </section>
<?php } ?>


<?php if ($Settings->featureEnabled('concepts')) { ?>
    <section id="concepts" style="display:none">
        <?php if (!empty($concepts)) :
        ?>

            <h3 class=""><?= lang('Concepts', 'Konzepte') ?></h3>
            <div class="box" id="concepts">
                <div class="content">
                    <?php foreach ($concepts as $concept) {
                        $score =  round($concept['score'] * 100);
                    ?><span class="concept" target="_blank" data-score='<?= $score ?>' data-name='<?= $concept['_id'] ?>' data-count='<?= $concept['count'] ?>' data-wikidata='<?= $concept['concept']['wikidata'] ?>'>
                            <div role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100" style="--value: <?= $score ?>"></div>
                            <?= $concept['_id'] ?>
                        </span><?php } ?>
                </div>
            </div>
        <?php else : ?>
            <p>
                <?= lang('No concepts are assigned to this person.', 'Zu dieser Person sind keine Konzepte zugewiesen.') ?>
            </p>
        <?php endif; ?>
    </section>
<?php } ?>


<?php if ($Settings->featureEnabled('wordcloud')) { ?>
    <section id="wordcloud" style="display:none">
        <h3 class=""><?= lang('Word cloud') ?></h3>

        <p class="text-muted">
            <?= lang('Based on the title and abstract (if available) of publications in OSIRIS.', 'Basierend auf dem Titel und Abstract (falls verfügbar) von Publikationen in OSIRIS.') ?>
        </p>
        <div id="wordcloud-chart" style="max-width: 80rem" ;></div>
    </section>
<?php } ?>



<?php
if (isset($_GET['verbose'])) {
    dump($scientist, true);
}
?>