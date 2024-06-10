<?php

/**
 * Page to see details on a single project
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /project/<id>
 *
 * @package     OSIRIS
 * @since       1.2.2
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

require_once BASEPATH . "/php/Project.php";
$Project = new Project($project);

$Format = new Document(false, 'portal');
?>

<div class="container">
    
<style>
    td .key {
        display: block;
        color: var(--muted-color);
        font-size: 1.2rem;
    }
</style>

<h1>
    <?= $project['name'] ?>
</h1>

<div class="row row-eq-spacing mt-0">

    <div class="col-md-6">
        <h2>
            <?= lang('Project details', 'Projektdetails') ?>
        </h2>

        <table class="table">
            <tbody>
                <tr>
                    <td>
                        <span class="key"><?= lang('Short title', 'Kurztitel') ?></span>
                        <?= $project['name'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Full title of the project', 'Voller Titel des Projekts') ?></span>
                        <?= $project['title'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Type of the project', 'Projekt-Typ') ?></span>
                        <?= $project['type'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Status', 'Status') ?></span>
                        <?= $Project->getStatus() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Third-party funder', 'Drittmittelgeber') ?></span>
                        <?= $project['funder'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Funding organization', 'Förderorganisation') ?></span>
                        <?= $project['funding_organization'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Funding reference number', 'Förderkennzeichen') ?></span>
                        <?= $Project->getFundingNumbers('<br>') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Purpose of the project', 'Zwecks des Projekts') ?></span>
                        <?= $project['purpose'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Role of', 'Rolle von') ?> <?= $Settings->get('affiliation') ?></span>
                        <?= $Project->getRole() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Coordinator facility', 'Koordinator-Einrichtung') ?></span>
                        <?= $project['coordinator'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key">Projektbeginn</span>
                        <?= Document::format_date($project['start'] ?? null) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key">Projektende</span>
                        <?= Document::format_date($project['end'] ?? null) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Personnel measures planned', 'Geplante Personalmaßnahmen') ?></span>
                        <?= $project['personal'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Project website', 'Webseite des Projekts') ?></span>
                        <a href="<?= $project['website'] ?? '' ?>" target="_blank" rel="noopener noreferrer"> <?= $project['website'] ?? '-' ?></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="key"><?= lang('Abstract', 'Kurzbeschreibung') ?></span>
                        <?= $project['abstract'] ?? '-' ?>
                    </td>
                </tr>
               
            </tbody>
        </table>

    </div>
    <div class="col-md-6">

        <?php if (!empty($project['persons'] ?? array())) { ?>
            <h2>
                <?= lang('Persons', 'Personen') ?>
            </h2>

            <table class="table">
                <tbody>
                    <?php
                    foreach ($project['persons'] as $person) {
                        $username = strval($person['user']);
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                <?= $Settings->printProfilePicture($username, 'profile-img small mr-20') ?>
                                    <div class="">
                                        <h5 class="my-0">
                                            <a href="<?= PORTALPATH ?>/person/<?= $username ?>" class="colorless">
                                                <?= $person['name'] ?>
                                            </a>
                                        </h5>
                                        <?= Project::personRole($person['role']) ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        <?php } ?>




        <?php
        if (!empty($project['collaborators'] ?? array())) {
        ?>
            <h2>
                <?= lang('Collaborators', 'Kooperationspartner') ?>
            </h2>

            <table class="table">
                <tbody>
                    <?php foreach ($project['collaborators'] as $collab) { ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">

                                    <span data-toggle="tooltip" data-title="<?= $collab['type'] ?>" class="badge mr-10">
                                        <?= Project::getCollaboratorIcon($collab['type'], 'ph-fw ph-2x m-0') ?>
                                    </span>
                                    <div class="">
                                        <h5 class="my-0">
                                            <?= $collab['name'] ?>
                                        </h5>
                                        <?= $collab['location'] ?>
                                        <a href="<?= $collab['ror'] ?>" class="ml-10" target="_blank" rel="noopener noreferrer">ROR <i class="ph ph-arrow-square-out"></i></a>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>



        <?php
        $activities = $osiris->activities->find(['projects' => $project['name']], ['sort' => ['year' => -1, 'month' => -1, 'day' => -1]]);
        $activities = $activities->toArray();
        $N = count($activities);
        ?>

        <?php if ($N > 0) { ?>
            <h2>
                <?= lang('Connected activities', 'Verknüpfte Aktivitäten') ?>
                (<?= $N ?>)
            </h2>

            <table class="table">
                <tbody>
                    <?php
                    if (empty($activities)) {
                        echo "<tr><td>" . lang("No activities connected.", "Keine Aktivitäten verknüpft.") . "</td></tr>";
                    } else {
                        foreach ($activities as $doc) {
                            echo "<tr><td>";
                            echo $doc['rendered']['icon'];
                            echo "</td><td>";
                            // echo $doc['rendered']['web'];
                            $Format->setDocument($doc);
                            echo $Format->formatShort();
                            echo "</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php } ?>

    </div>
</div>
</div>