<?php

/**
 * Page to see all projects
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /projects
 *
 * @package     OSIRIS
 * @since       1.2.2
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

require_once BASEPATH . "/php/Project.php";
$Project = new Project();

// $Format = new Document(true);
$form = $form ?? array();

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}

?>

<h1 class="mt-0">
    <i class="ph ph-tree-structure text-osiris"></i>
    <?= lang('Projects', 'Projekte') ?>
</h1>

<a href="<?= ROOTPATH ?>/projects/new" class="mb-10 d-inline-block">
    <i class="ph ph-plus"></i>
    <?= lang('Add new project', 'Neues Projekt anlegen') ?>
</a>

<table class="table">
    <thead>
        <th><?= lang('ID', 'ID') ?></th>
        <!-- <th><?= lang('Title', 'Title') ?></th> -->
        <th><?= lang('Third-party funder', 'Drittmittelgeber') ?></th>
        <th><?= lang('Project time', 'Projektlaufzeit') ?></th>
        <th><?= lang('Role', 'Rolle') ?></th>
        <th><?= lang('Contact person', 'Kontaktperson') ?></th>
        <th><?= lang('# activities', '# Aktivitäten') ?></th>
        <th><?= lang('Status') ?></th>
    </thead>
    <tbody>
        <?php
        $projects = $osiris->projects->find([], ['sort'=> ['start'=>-1]]);
        foreach ($projects as $project) {
            $Project->setProject($project);
        ?>
            <tr id="<?= $project['_id'] ?>">
                <td>
                    <a href="<?= ROOTPATH ?>/projects/view/<?= $project['_id'] ?>">
                        <?= $project['name'] ?>
                    </a>
                </td>
                <td>
                    <?= $project['funder'] ?? '-' ?>
                    (<?= $project['funding_number'] ?? '-' ?>)
                </td>
                <td>
                    <?= $Project->getDateRange() ?>
                </td>
                <td>
                    <?= $Project->getRole() ?>
                </td>
                <td>
                    <a href="<?= ROOTPATH ?>/profile/<?= $project['contact'] ?? '' ?>"><?= $DB->getNameFromId($project['contact'] ?? '') ?></a>
                </td>
                <td>
                    <?php
                    echo $osiris->activities->count(['projects' => strval($project['name'])]);
                    ?>
                </td>
                <td>
                    <?= $Project->getStatus() ?>
                </td>

            </tr>

        <?php } ?>
    </tbody>
</table>