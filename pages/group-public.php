<?php

/**
 * Page to edit external view on group
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /groups/new
 *
 * @package     OSIRIS
 * @since       1.3.5
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */


$form = $form ?? array();

$level = 0;

$formaction = ROOTPATH;
$formaction .= "/crud/groups/update/" . $form['_id'];
$btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
$url = ROOTPATH . "/groups/view/" . $form['_id'];
$title = lang('Edit group: ', 'Gruppe bearbeiten: ') . $id;

$level = $Groups->getLevel($id);


function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}

function sel($index, $value)
{
    return val($index) == $value ? 'selected' : '';
}

?>


<h3 class="title">
    <?= $title ?>
</h3>

<form action="<?= $formaction ?>" method="post" id="group-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">

    <fieldset>
        <legend>
            <?= lang('Visibility on Website', 'Darstellung auf der Webseite') ?>
        </legend>

        <div class="form-group">
            <div class="custom-switch">
                <input type="checkbox" id="hide-check" <?= val('hide') ? 'checked' : '' ?> name="values[hide]" value="1" onchange="toggleVisibility()">
                <label for="hide-check">
                    <?= lang('Hide group from public view', 'Gruppe nicht öffentlich anzeigen') ?>
                </label>
            </div>
        </div>
    </fieldset>


    <div class="row row-eq-spacing mb-0">
        <div class="col-md-6">
            <fieldset>
                <legend class="d-flex"><?= lang('German', 'Deutsch') ?> <img src="<?= ROOTPATH ?>/img/de.svg" alt="DE" class="flag"></legend>
                <div class="form-group">
                    <label for="name_de" class="required">
                        <?= lang('Full Name', 'Voller Name') ?> (DE)
                    </label>
                    <input type="text" class="form-control large" name="values[name_de]" id="name_de" required value="<?= val('name_de') ?>">
                </div>
                <div class="form-group">
                    <label for="description_de"><?= lang('Description', 'Beschreibung') ?> (DE)</label>
                    <textarea name="values[description_de]" id="description_de" cols="30" rows="10" class="form-control"><?= val('description_de') ?></textarea>
                    <small class="text-muted">
                        <a href="https://www.markdownguide.org/basic-syntax/" target="_blank" rel="noopener noreferrer">
                            <?= lang('Markdown supported', 'Markdown unterstützt') ?> <i class="ph ph-info"></i>
                        </a>
                    </small>
                </div>
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset>
                <legend class="d-flex"><?= lang('English', 'Englisch') ?> <img src="<?= ROOTPATH ?>/img/gb.svg" alt="EN" class="flag"></legend>
                <div class="form-group">
                    <label for="name" class="required">
                        <?= lang('Full Name', 'Voller Name') ?> (EN)
                    </label>
                    <input type="text" class="form-control large" name="values[name]" id="name" required value="<?= val('name') ?>">
                </div>

                <div class="form-group">
                    <label for="description"><?= lang('Description', 'Beschreibung') ?> (EN)</label>
                    <textarea name="values[description]" id="description" cols="30" rows="10" class="form-control"><?= val('description') ?></textarea>
                    <small class="text-muted">
                        <a href="https://www.markdownguide.org/basic-syntax/" target="_blank" rel="noopener noreferrer">
                            <?= lang('Markdown supported', 'Markdown unterstützt') ?> <i class="ph ph-info"></i>
                        </a>
                    </small>
                </div>
            </fieldset>
        </div>
    </div>


    <h3><?= lang('Research interest', 'Forschungsinteressen') ?></h3>
    <div id="research-list">
        <?php
        if (isset($form['research']) && !empty($form['research'])) {

            foreach ($form['research'] as $i => $con) { ?>

                <div class="alert mb-10">

                    <div class="row row-eq-spacing my-0">
                        <div class="col-md-6">
                            <h5 class="mt-0 ">Deutsch <img src="<?= ROOTPATH ?>/img/de.svg" alt="DE" class="flag"></h5>
                            <div class="form-group my-10">
                                <input name="values[research][<?= $i ?>][title_de]" type="text" class="form-control large" value="<?= htmlspecialchars($con['title_de'] ?? '') ?>" placeholder="Title">
                            </div>
                            <div class="form-group mb-0">
                                <textarea name="values[research][<?= $i ?>][info_de]" id="" cols="30" rows="5" class="form-control" value="" placeholder="Information (Markdown support)"><?= htmlspecialchars($con['info_de'] ?? '') ?></textarea>
                                <small class="text-muted">
                                    <a href="https://www.markdownguide.org/basic-syntax/" target="_blank" rel="noopener noreferrer">
                                        <?= lang('Markdown supported', 'Markdown unterstützt') ?> <i class="ph ph-info"></i>
                                    </a>
                                </small>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <h5 class="mt-0 ">English <img src="<?= ROOTPATH ?>/img/gb.svg" alt="EN" class="flag"></h5>
                            <div class="form-group my-10">
                                <input name="values[research][<?= $i ?>][title]" type="text" class="form-control large" value="<?= htmlspecialchars($con['title'] ?? '') ?>" placeholder="Title" required>
                            </div>
                            <div class="form-group mb-0">
                                <textarea name="values[research][<?= $i ?>][info]" id="" cols="30" rows="5" class="form-control" value="" placeholder="Information (Markdown support)" required><?= htmlspecialchars($con['info'] ?? '') ?></textarea>
                                <small class="text-muted">
                                    <a href="https://www.markdownguide.org/basic-syntax/" target="_blank" rel="noopener noreferrer">
                                        <?= lang('Markdown supported', 'Markdown unterstützt') ?> <i class="ph ph-info"></i>
                                    </a>
                                </small>
                            </div>

                        </div>
                    </div>
                    <button class="btn danger small my-10" type="button" onclick="$(this).closest('.alert').remove()"><i class="ph ph-trash"></i></button>
                </div>
        <?php }
        } ?>

    </div>
    <button class="btn" type="button" onclick="addResearchrow(event, '#research-list')"><i class="ph ph-plus text-success"></i> <?= lang('Add entry', 'Eintrag hinzufügen') ?></button>


    <script>
        var i = <?= $i ?? 0 ?>

        var CURRENTYEAR = <?= CURRENTYEAR ?>;

        function addResearchrow(evt, parent) {
            i++;
            var el = `
            <div class="alert mb-10">
                <div class="row row-eq-spacing my-0">
                    <div class="col-md-6">
                        <h5 class="mt-0 ">Deutsch <img src="<?= ROOTPATH ?>/img/de.svg" alt="DE" class="flag"></h5>
                        <div class="form-group">
                            <input name="values[research][${i}][title_de]" type="text" class="form-control large" value="" placeholder="Title">
                        </div>
                        <div class="form-group">
                            <textarea name="values[research][${i}][info_de]" id="" cols="30" rows="5" class="form-control" value="" placeholder="Information (Markdown support)"></textarea>
                            <small class="text-muted">
                                <a href="https://www.markdownguide.org/basic-syntax/" target="_blank" rel="noopener noreferrer">
                                    <?= lang('Markdown supported', 'Markdown unterstützt') ?> <i class="ph ph-info"></i>
                                </a>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mt-0 ">English <img src="<?= ROOTPATH ?>/img/gb.svg" alt="EN" class="flag"></h5>
                        <div class="form-group">
                            <input name="values[research][${i}][title]" type="text" class="form-control large" value="" placeholder="Title" required>
                        </div>
                        <div class="form-group">
                            <textarea name="values[research][${i}][info]" id="" cols="30" rows="5" class="form-control" value="" placeholder="Information (Markdown support)" required></textarea>
                            <small class="text-muted">
                                <a href="https://www.markdownguide.org/basic-syntax/" target="_blank" rel="noopener noreferrer">
                                    <?= lang('Markdown supported', 'Markdown unterstützt') ?> <i class="ph ph-info"></i>
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
                <button class="btn danger" type="button" onclick="$(this).closest('.alert').remove()"><i class="ph ph-trash"></i></button>
            </div>

                `;
            $(parent).append(el);
        }

        function toggleVisibility() {
            var hide = $('#hide-check').prop('checked');
            if (hide) {
                $('#research').hide();
            } else {
                $('#research').show();
            }
        }
        toggleVisibility();
    </script>



    <button class="btn primary" type="submit" id="submit-btn">
        <i class="ph ph-check"></i> <?= lang("Save", "Speichern") ?>
    </button>

</form>