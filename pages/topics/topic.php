<?php

/**
 * The detail view of a topic
 * Created in cooperation with bicc
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 *
 * @package     OSIRIS
 * @since       1.3.8
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */
?>

<style>
    .topic-image {
        width: 100%;
        overflow: hidden;
        position: relative;
    }

    .topic-image img {
        width: 100%;
        height: auto;
    }
</style>

<div class="topic" style="--topic-color: <?= $topic['color'] ?? '#333333' ?>">

    <div class="topic-image">
        <?php if (!empty($topic['image'] ?? null)) : ?>
            <img src="<?= ROOTPATH . '/uploads/' . $topic['image'] ?>" alt="<?= $topic['name'] ?>">
        <?php else : ?>
            <img src="<?= ROOTPATH ?>/img/osiris-topic-banner.png" alt="No topic image set">
        <?php endif; ?>
        <?php if ($Settings->hasPermission('topics.edit')) { ?>
            <a href="#upload-image" class="btn circle position-absolute bottom-0 right-0 m-10"><i class="ph ph-edit"></i></a>
        <?php } ?>
    </div>


    <h1 class="title">
        <span class="topic-icon"></span>
        <?= lang($topic['name'], $topic['name_de']?? null) ?>
    </h1>

    <h2 class="subtitle">
        <?= lang($topic['subtitle'], $topic['subtitle_de'] ?? null) ?>
    </h2>

    <p class="font-size-12 text-muted">
        <?= get_preview(lang($topic['description'], $topic['description_de'] ?? null), 400) ?>
    </p>

    <?php if ($Settings->hasPermission('topics.edit')) { ?>
        <a href="<?= ROOTPATH ?>/topics/edit/<?= $topic['_id'] ?>">
            <i class="ph ph-edit"></i>
            <?= lang('Edit', 'Bearbeiten') ?>
        </a>
    <?php } ?>
</div>


<!-- Persons -->
<h2 class="title"><?= lang('Persons', 'Personen') ?></h2>


<link rel="stylesheet" href="<?= ROOTPATH ?>/css/usertable.css">
<table class="table cards w-full" id="user-table">
    <thead>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </thead>
    <tbody>

    </tbody>
</table>

<script>
    userTable('#user-table', {
        filter: {
            topics: '<?= $topic['id'] ?>'
        }
    });
</script>

<!-- Projects -->
<h2 class="title"><?= lang('Projects', 'Projekte') ?></h2>


<table class="table" id="project-table">
    <thead>
        <th></th>
        <th><?= lang('ID', 'ID') ?></th>
        <th><?= lang('Type', 'Art') ?></th>
        <th><?= lang('Funder', 'Mittelgeber') ?></th>
        <th><?= lang('Project time', 'Projektlaufzeit') ?></th>
        <th><?= lang('Role', 'Rolle') ?></th>
        <th><?= lang('Applicant', 'Antragsteller:in') ?></th>
        <th><?= lang('Status') ?></th>
    </thead>
    <tbody>
        <tr>
            <td colspan="8" class="text-center">
                <i class="ph ph-spinner-third text-muted"></i>
                <?= lang('Loading projects', 'Lade Projekte') ?>
            </td>
        </tr>
    </tbody>
</table>
<script>
    initProjects('#project-table', {
        filter: {
            topics: '<?= $topic['id'] ?>'
        }
    });
</script>

<!-- activities -->
<h2 class="title"><?= lang('Activities', 'Aktivitäten') ?></h2>

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

<script>
    initActivities('#activities-table', {
        page: 'all-activities',
        display_activities: 'web',
        filter: {
            topics: '<?= $topic['id'] ?>'
        }
    });
</script>


<!-- modal -->
<div id="upload-image" class="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <h3 class="title"><?= lang('Upload Image', 'Bild hochladen') ?></h3>
            <form action="<?= ROOTPATH ?>/crud/topics/upload/<?= $topic['_id'] ?>" method="post" enctype="multipart/form-data">
                <div class="custom-file">
                    <input type="file" id="image" name="file" accept=".jpg,.png,.gif" data-default-value="<?= lang('No image uploaded', 'Kein Bild hochgeladen') ?>">
                    <label for="image"><?= lang('Select image', 'Bild auswählen') ?></label>
                </div>
                <button type="submit" class="btn"><?= lang('Upload', 'Hochladen') ?></button>
            </form>
        </div>
    </div>
</div>