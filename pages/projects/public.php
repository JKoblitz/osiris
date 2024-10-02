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
<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>

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

                <div class="row row-eq-spacing mb-0">
                    <div class="col-md-6">
                        <fieldset>
                            <legend class="d-flex"><?= lang('English', 'Englisch') ?> <img src="<?= ROOTPATH ?>/img/gb.svg" alt="EN" class="flag"></legend>
                            <div class="form-group">
                                <label for="name" class="required">
                                    <?= lang('Title', 'Titel') ?> (EN)
                                </label>
                                <input type="text" class="form-control large" name="values[public_title]" id="public_title" required value="<?= $project['public_title'] ?? $project['name'] ?>">
                            </div>

                            <div class="form-group">
                                <label for="name" class="required">
                                    <?= lang('Subtitle', 'Untertitel') ?> (EN)
                                </label>
                                <input type="text" class="form-control" name="values[public_subtitle]" id="public_subtitle" required value="<?= $project['public_subtitle'] ?? $project['title'] ?? ''  ?>">
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset>
                            <legend class="d-flex"><?= lang('German', 'Deutsch') ?> <img src="<?= ROOTPATH ?>/img/de.svg" alt="DE" class="flag"></legend>
                            <div class="form-group">
                                <label for="name_de">
                                    <?= lang('Title', 'Titel') ?> (DE)
                                </label>
                                <input type="text" class="form-control large" name="values[public_title_de]" id="public_title_de" value="<?= $project['public_title_de'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="name_de">
                                    <?= lang('Subtitle', 'Untertitel') ?> (DE)
                                </label>
                                <input type="text" class="form-control" name="values[public_subtitle_de]" id="public_subtitle_de" value="<?= $project['public_subtitle_de'] ?? '' ?>">
                            </div>
                        </fieldset>
                    </div>
                </div>



                <div class="form-group">
                    <label for="website"><?= lang('Project Website', 'Projekt-Webseite') ?></label>
                    <input type="url" class="form-control" id="website" name="values[website]" value="<?= $project['website'] ?? '' ?>">
                </div>

                <hr>
                
                <div class="form-group">
                    <h5>
                        <?= lang('Image', 'Bild') ?>
                    </h5>
                    <p>
                        <?= lang('Upload an image (e.g.) Logo for the project. The image will be displayed in the metadata.', 'Lade ein Bild (z.B. ein Logo) für das Projekt hoch, das bei den Metadaten auf der Projektseite gezeigt wird.') ?>
                    </p>
                    <!-- show current image if any -->
                    <?php if (!empty($project['public_image'])) : ?>
                        <img src="<?= ROOTPATH . '/uploads/' . $project['public_image'] ?>" alt="<?= $project['public_title'] ?>" class="w-400">
                    <?php endif; ?>
                    <div class="custom-file">
                        <input type="file" id="public_image" name="file" accept=".jpg,.png,.gif" data-default-value="<?= lang('No image uploaded', 'Kein Bild hochgeladen') ?>">
                        <label for="public_image"><?= lang('Upload image', 'Bild hochladen') ?></label>
                    </div>
                </div>

                <hr>

                <h4>
                    <?= lang('Abstract', 'Zusammenfassung') ?>
                    in <span class="d-inline-flex"><?= lang('English', 'Englisch') ?> <img src="<?= ROOTPATH ?>/img/gb.svg" alt="EN" class="flag"></span>
                </h4>
                <div class="form-group">
                    <div id="public_abstract-quill"><?= $project['public_abstract'] ?? $project['abstract'] ?? '' ?></div>
                    <input type="hidden" id="public_abstract" name="values[public_abstract]" value="<?= $project['public_abstract'] ?? $project['abstract'] ?? '' ?>">

                    <script>
                        const quill = new Quill('#public_abstract-quill', {
                            modules: {
                                toolbar: [
                                    [{
                                        header: [1, 2, false]
                                    }],
                                    ['bold', 'italic', 'underline'],
                                    [{
                                        'list': 'ordered'
                                    }, {
                                        'list': 'bullet'
                                    }],
                                    [{
                                        'script': 'sub'
                                    }, {
                                        'script': 'super'
                                    }],
                                    ['link', 'image'],
                                    ['clean']
                                ],
                            },
                            formats: ['italic', 'bold', 'underline', 'script', 'symbol', 'link', 'image', 'list', 'bullet', 'header'],
                            placeholder: 'Compose an abstract...',
                            theme: 'snow', // or 'bubble' 
                        });
                        quill.on('text-change', (delta, oldDelta, source) => {
                            document.getElementById('public_abstract').value = quill.getSemanticHTML();
                        });
                    </script>
                </div>


                <h4>
                    <?= lang('Abstract', 'Zusammenfassung') ?>
                    in <span class="d-inline-flex"><?= lang('German', 'Deutsch') ?> <img src="<?= ROOTPATH ?>/img/de.svg" alt="DE" class="flag"></span>
                </h4>
                <div class="form-group">
                    <div id="public_abstract_de-quill"><?= $project['public_abstract_de'] ?? '' ?></div>
                    <input type="hidden" id="public_abstract_de" name="values[public_abstract_de]" value="<?= $project['public_abstract_de'] ?? '' ?>">

                    <script>
                        const quill_de = new Quill('#public_abstract_de-quill', {
                            modules: {
                                toolbar: [
                                    [{
                                        header: [1, 2, false]
                                    }],
                                    ['bold', 'italic', 'underline'],
                                    [{
                                        'list': 'ordered'
                                    }, {
                                        'list': 'bullet'
                                    }],
                                    [{
                                        'script': 'sub'
                                    }, {
                                        'script': 'super'
                                    }],
                                    ['link', 'image'],
                                    ['clean']
                                ],
                            },
                            formats: ['italic', 'bold', 'underline', 'script', 'symbol', 'link', 'image', 'list', 'bullet', 'header'],
                            placeholder: 'Compose an abstract...',
                            theme: 'snow', // or 'bubble' 
                        });
                        quill_de.on('text-change', (delta, oldDelta, source) => {
                            document.getElementById('public_abstract_de').value = quill_de.getSemanticHTML();
                        });
                    </script>
                </div>
                <button type="submit" class="btn secondary"><?= lang('Save', 'Speichern') ?></button>
            </form>
        </div>
    </div>
</div>