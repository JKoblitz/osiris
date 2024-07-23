<?php

/**
 * Page to edit public information of a project.
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /projects/public/<id>
 *
 * @package     OSIRIS
 * @since       1.3.5
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1><?= lang('Public Information', 'Öffentliche Informationen') ?></h1>
        </div>
    </div>
    <?php
    /**
     * fields for the following information:
     * - public_title (text)
     * - public_abstract (textarea, mardown supported)
     * - website (url)
     * - public_image (image upload)
     */
    ?>
    <div class="row">
        <div class="col-12">
            <form action="<?= ROOTPATH ?>/crud/projects/update-public/<?= $project['_id'] ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="public-check" <?= ($project['public'] ?? false) ? 'checked' : '' ?> name="values[public]">
                        <label for="public-check">
                            Zustimmung zur Internetpräsentation des bewilligten Vorhaben
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="public_title"><?= lang('Title', 'Titel') ?></label>
                    <input type="text" class="form-control" id="public_title" name="values[public_title]" value="<?= $project['public_title'] ?? $project['name'] ?>">
                </div>
                <div class="form-group">
                    <label for="public_subtitle"><?= lang('Subtitle', 'Untertitel') ?></label>
                    <input type="text" class="form-control" id="public_subtitle" name="values[public_subtitle]" value="<?= $project['public_subtitle'] ?? $project['title'] ?? '' ?>">

                </div>
                <div class="form-group">
                    <label for="public_abstract"><?= lang('Abstract', 'Zusammenfassung') ?></label>
                    <textarea class="form-control" id="public_abstract" name="values[public_abstract]" rows="10"><?= $project['public_abstract'] ?? $project['abstract'] ?? '' ?></textarea>
                    <small class="text-muted">
                        <?= lang('Markdown supported', 'Markdown wird für die Formatierung unterstützt') ?>
                    </small>
                </div>
                <div class="form-group">
                    <label for="website"><?= lang('Website', 'Webseite') ?></label>
                    <input type="url" class="form-control" id="website" name="values[website]" value="<?= $project['website'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <!-- show current image if any -->
                    <?php if (!empty($project['public_image'])) : ?>
                        <img src="<?= ROOTPATH . '/uploads/' . $project['public_image'] ?>" alt="<?= $project['public_title'] ?>" class="w-400">
                    <?php endif; ?>
                    <div class="custom-file">
                        <input type="file" id="public_image" name="file" accept=".jpg,.png,.gif" data-default-value="<?= lang('Upload image', 'Bild hochladen') ?>">
                        <label for="public_image"><?= lang('Upload image', 'Bild hochladen') ?></label>
                    </div>
                </div>
                <button type="submit" class="btn secondary"><?= lang('Save', 'Speichern') ?></button>
            </form>
        </div>
    </div>
</div>