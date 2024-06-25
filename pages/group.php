<?php

/**
 * Page to view a selected group
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /groups/view/<id>
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

include_once BASEPATH . "/php/MyParsedown.php";
$parsedown = new Parsedown;

//  $children = $Groups->getChildren($id);
//  dump($children, true);

//  dump($Groups->tree, true);
$level = $Groups->getLevel($id);

$children = $Groups->getChildren($group['id']);
$persons = $osiris->persons->find(['depts' => ['$in' => $children], 'is_active' => true], ['sort' => ['last' => 1]])->toArray();

if (isset($group['head'])) {

    $head = $group['head'];
    if (is_string($head)) $head = [$head];
    else $head = DB::doc2Arr($head);


    usort($persons, function ($a, $b) use ($head) {
        return in_array($a['username'], $head)  ? -1 : 1;
    });
} else {
    $head = [];
}

$users = array_column($persons, 'username');

$show_general = (isset($group['description']) || isset($group['description_de']) || (isset($group['research']) && !empty($group['research'])));
?>

<link rel="stylesheet" href="<?= ROOTPATH ?>/css/usertable.css">

<!-- all necessary javascript -->
<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>
<script src="<?= ROOTPATH ?>/js/d3.v4.min.js"></script>
<script src="<?= ROOTPATH ?>/js/popover.js"></script>
<script src="<?= ROOTPATH ?>/js/d3-chords.js?v=2"></script>
<script src="<?= ROOTPATH ?>/js/d3.layout.cloud.js"></script>
<script src="<?= ROOTPATH ?>/js/d3-graph.js"></script>

<!-- all variables for this page -->
<script>
    const USERS = <?= json_encode($users) ?>;
    const DEPT_TREE = <?= json_encode($children) ?>;
    const DEPT = '<?= $id ?>';
</script>
<script src="<?= ROOTPATH ?>/js/units.js?v=2"></script>


<style>
    .dept-icon {
        border-radius: 10rem;
        color: white;
        width: 1.6em;
        height: 1.6em;
        display: inline-block;
        background-color: var(--highlight-color);
        text-align: center;
    }

    .dept-icon i.ph {
        margin: 0;
    }

    h1 {
        color: var(--highlight-color);
    }
    
blockquote {
  font-style: italic;
  border-left: 5px solid var(--primary-color);
  padding-left: 1rem;
  margin: 1rem 0;
}
</style>

<!-- modal to add person -->
<div id="add-person-modal" class="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><?= lang('Add person', 'Person hinzufügen') ?></h2>
            <form action="<?= ROOTPATH ?>/crud/groups/addperson/<?= $id ?>" method="post">
                <div class="form-group">
                    <label for="username"><?= lang('Username', 'Benutzername') ?></label>
                    <!-- select for distinct user names from DB -->
                    <select name="username" id="username" class="form-control">
                        <?php foreach ($osiris->persons->find(['is_active' => true], ['sort' => ['last' => 1]]) as $person) { ?>
                            <option value="<?= $person['username'] ?>"><?= $person['last'] . ', ' . $person['first'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn"><?= lang('Add', 'Hinzufügen') ?></button>
            </form>
        </div>
    </div>
</div>

<div <?= $Groups->cssVar($id) ?> class="">
<div class="btn-toolbar">

<?php if ($Settings->hasPermission('units.add') || (isset($form['head']) && $form['head'] == $_SESSION['username'])) { ?>
    <a class="btn" href="<?= ROOTPATH ?>/groups/edit/<?= $id ?>">
        <i class="ph ph-note-pencil ph-fw"></i>
        <?= lang('Edit', 'Bearbeiten') ?>
    </a>
    <a class="btn" href="<?= ROOTPATH ?>/groups/public/<?= $id ?>">
        <i class="ph ph-note-pencil ph-fw"></i>
        <?= lang('Public', 'Öffentliche Darstellung') ?>
    </a>
    <a class="btn" href="#add-person-modal">
        <i class="ph ph-user-plus ph-fw"></i>
        <?= lang('Add person', 'Person hinzufügen') ?>
    </a>
<?php } ?>


<?php if ($Settings->featureEnabled('portal')) { ?>
    <a class="btn" href="<?= ROOTPATH ?>/preview/group/<?= $id ?>">
        <i class="ph ph-eye ph-fw"></i>
        <?= lang('Preview', 'Vorschau') ?>
    </a>
<?php } ?>
</div>
    
    <h1>
        <?= lang($group['name'] ?? '-', $group['name_de'] ?? null) ?>

    </h1>
    <h3 class="subtitle">
        <?= $group['unit'] ?>
    </h3>



    <!-- TAB AREA -->

    <nav class="pills mt-20 mb-0">
        <?php if ($show_general) { ?>
            <a onclick="navigate('general')" id="btn-general" class="btn active">
                <i class="ph ph-info" aria-hidden="true"></i>
                <?= lang('General', 'Allgemein') ?>
            </a>
        <?php } ?>

        <a onclick="navigate('persons')" id="btn-persons" class="btn <?= !$show_general ? 'active' : '' ?>">
            <i class="ph ph-users" aria-hidden="true"></i>
            <?= lang('Persons', 'Personen') ?>
            <span class="index"><?= count($users) ?></span>
        </a>

        <a onclick="navigate('graph')" id="btn-graph" class="btn">
            <i class="ph ph-graph" aria-hidden="true"></i>
            <?= lang('Graph')  ?>
        </a>

        <?php if ($level !== 0) { ?>

            <?php
            $publication_filter = [
                'authors.user' => ['$in' => $users],
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
            $activities_filter = [
                'authors.user' => ['$in' => $users],
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

            <?php if ($Settings->featureEnabled('projects')) { ?>
                <?php
                $project_filter = [
                    '$or' => array(
                        ['contact' => ['$in' => $users]],
                        ['persons.user' => ['$in' => $users]]
                    ),
                    "status" => ['$ne' => "rejected"]
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


            <?php if ($Settings->featureEnabled('wordcloud')) { ?>
                <?php
                $count_wordcloud = $osiris->activities->count([
                    'title' => ['$exists' => true],
                    'authors.user' => ['$in' => $users],
                    'type' => 'publication'
                ]);
                if ($count_wordcloud > 0) { ?>
                    <a onclick="navigate('wordcloud')" id="btn-wordcloud" class="btn">
                        <i class="ph ph-cloud" aria-hidden="true"></i>
                        <?= lang('Word cloud')  ?>
                    </a>
                <?php } ?>
            <?php } ?>

        <?php } ?>

        <a onclick="navigate('collab')" id="btn-collab" class="btn">
            <i class="ph ph-users-three" aria-hidden="true"></i>
            <?= lang('Other units', 'Andere Einheiten')  ?>
        </a>

    </nav>

    <section id="general">
        <div class="row row-eq-spacing">
            <div class="col-md">
                <?php if (isset($group['description']) || isset($group['description_de'])) { ?>


                    <h3><?= lang('Information', 'Informationen') ?></h3>

                    <p>
                        <?= $parsedown->text(lang($group['description'] ?? '-', $group['description_de'] ?? null)) ?>
                    </p>

                <?php } ?>

                <?php if (!empty($head)) { ?>
                    <h3><?= lang('Head', 'Leitung') ?></h3>
                    <div class="list">
                        <?php foreach ($head as $h) { ?>
                            <a href="<?= ROOTPATH ?>/profile/<?= $h ?>" class="colorless d-inline-flex align-items-center border bg-white p-10 rounded">

                                <?= $Settings->printProfilePicture($h, 'profile-img small mr-20') ?>
                                <div class="">
                                    <h5 class="my-0">
                                            <?= $DB->getNameFromId($h) ?>
                                    </h5>
                                </div>
                        </a>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

            <?php if (isset($group['research']) && !empty($group['research'])) {
            ?>
                <style>
                    details {
                        background-color: white;
                    }

                    .collapse-header,
                    .collapse-header:focus {
                        background-color: white;
                        font-weight: bold;
                        font-size: 1.6rem;
                    }

                    .collapse-header:focus::after {
                        box-shadow: none;
                    }
                </style>
                <div class="col-md">

                    <h3><?= lang('Research interests', 'Forschungsinteressen') ?></h3>

                    <!-- Level <?= $level ?? '?' ?> -->
                    <div class="collapse-group">
                        <?php foreach ($group['research'] as $r) { ?>
                            <details class="collapse-panel">
                                <summary class="collapse-header">
                                    <?= $r['title'] ?>
                                </summary>
                                <div class="collapse-content">
                                    <?= $parsedown->text($r['info']) ?>
                                </div>
                            </details>

                        <?php } ?>
                    </div>


                </div>
            <?php } ?>

        </div>

    </section>


    <section id="persons" style="display: none;">

        <h3><?= lang('Employees', 'Mitarbeitende Personen') ?></h3>

        <table class="table cards w-full" id="user-table">
            <thead>
                <th></th>
                <th></th>
            </thead>
            <tbody>
            </tbody>
        </table>
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


    </section>



    <?php if ($Settings->featureEnabled('projects')) { ?>
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
                            $past[] = $Project->widgetSmall();
                        } else {
                            $ongoing[] = $Project->widgetSmall();
                        }
                    }
                    $i = 0;
                    $breakpoint = ceil($count_projects / 2);
                ?>
                    <div class="col-md-6">
                        <?php if (!empty($ongoing)) { ?>

                            <h2><?= lang('Ongoing projects', 'Laufende Projekte') ?></h2>
                            <?php foreach ($ongoing as $html) { ?>
                                <?= $html ?>
                            <?php
                                $i++;
                                if ($i == $breakpoint) {
                                    echo "</div><div class='col-md-6'>";
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

            <!-- <h3 class="title">
            <?= lang('Timeline of all approved projects', 'Zeitstrahl aller bewilligten Projekte') ?>
        </h3>
        <div class="box">
            <div class="content">
                <div id="project-timeline"></div>
            </div>
        </div> -->
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


    <section id="collab" style="display:none">

        <div class="row row-eq-spacing">
            <div class="col-md-6">

                <h3><?= lang('Relevant units', 'Verwandte Einheiten') ?></h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>
                                <span class="key"><?= lang('Parent unit', 'Übergeordnete Einheit') ?></span>
                                <?php if ($group['parent']) { ?>
                                    <a href="<?= ROOTPATH ?>/groups/view/<?= $group['parent'] ?>"><?= $Groups->getName($group['parent']) ?></a>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="key"><?= lang('Child units', 'Untereinheiten') ?></span>
                                <?php
                                $children = $osiris->groups->find(['parent' => $id])->toArray();
                                ?>
                                <?php if (!empty($children)) { ?>
                                    <ul class="list">
                                        <?php foreach ($children as $child) { ?>
                                            <li>
                                                <a href="<?= ROOTPATH ?>/groups/view/<?= $child['id'] ?>" class="colorless font-weight-bold"><?= $child['name'] ?></a><br>
                                                <span class="text-muted"><?= $child['unit'] ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    -
                                <?php } ?>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
                <?php if ($level !== 0) { ?>

                    <h3><?= lang('Collaboration with other groups', 'Zusammenarbeit mit anderen Gruppen') ?></h3>
                    <p class="text-muted">
                        <?= lang('Based on publications within the past 5 years.', 'Basierend auf Publikationen aus den vergangenen 5 Jahren.') ?>
                    </p>
                    <div id="collab-chart" style="max-width: 60rem"></div>

                <?php } ?>


            </div>
        </div>



    </section>


    <section id="graph" style="display:none">
        <h3><?= lang('Graph', 'Graph') ?></h3>

        <p class="text-muted m-0">
            <?= lang('Based on publications with associated affiliations.', 'Basierend auf affilierten Publikationen.') ?>
        </p>
        <div id="collabGraph"></div>

    </section>

</div>

<?php if (!$show_general) { ?>
    <script>
        navigate('persons');
    </script>
<?php } ?>


<?php

if (isset($_GET['verbose'])) {
    dump($group, true);
}
?>