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
 * @since       1.2.1
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$Format = new Document(true);
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
<!-- <script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script> -->
<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>




<div class="modal" id="add-projects" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" href="#close-modal" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h3 class="title">
                <?= lang('Add new project', 'Neues Drittmittelprojekt') ?>
            </h3>

            <form action="<?= ROOTPATH ?>/create-projects" method="post" enctype="multipart/form-data" id="activity-form">
                <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                <div class="form-row row-eq-spacing" data-visible="students,guests,projects">

                    <div class="col-sm-12">
                        <label for="project" class="required element-other"><?= lang('Project ID', 'Projekt-ID') ?></label>
                        <input type="text" class="form-control" name="values[name]" id="name" required value="<?= val('name') ?>" placeholder="DiASPora">
                        <small class="text-muted">
                            <?= lang('The ID is a short and unique identifier, used to easily find projects.', 'Die ID ist ein kurzer und einzigartiger Identifikator, um ein Projekt schnell zu finden.') ?>
                        </small>
                    </div>

                    <div class="col-sm-12">

                        <div class=" lang-<?= lang('en', 'de') ?>" data-visible="article,preprint,magazine,book,chapter,lecture,poster,dissertation,others,misc-once,misc-annual,students,guests,projects,software">
                            <label for="title" class="required element-title">
                                <?= lang('Full title of the project', 'Voller Titel des Projekts') ?>
                            </label>

                            <div class="form-group title-editor" id="title-editor"><?= $form['title'] ?? '' ?></div>
                            <input type="text" class="form-control hidden" name="values[title]" id="title" required value="<?= val('title') ?>">
                        </div>
                        
                <script>
                    initQuill(document.getElementById('title-editor'));
                </script>
                    </div>

                    <div class="col-sm-4">
                        <label class="required element-author" for="username">
                            <?= lang('Contact person', 'Ansprechpartner:in') ?>
                        </label>
                        <select class="form-control" id="username" name="values[contact_person]" required autocomplete="off">
                            <?php
                            $userlist = $osiris->persons->find([], ['sort' => ["last" => 1]]);
                            foreach ($userlist as $j) { ?>
                                <option value="<?= $j['_id'] ?>" <?= $j['_id'] == ($form['user'] ?? $user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                            <?php } ?>
                        </select>
                    </div>


                    <div class="col-sm-4">
                        <label for="">
                            Bewilligungssumme
                        </label>
                        <input type="number" step="1" class="form-control">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Drittmitteleinnahmen
                        </label>
                        <input type="number" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Drittmittelerträge
                        </label>
                        <input type="number" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Förderkennzeichen des Drittmittelprojekts
                        </label>
                        <input type="text" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Förderorganisation
                        </label>
                        <input type="text" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Drittmittelgeber/-in
                        </label>
                        <select class="form-control" name="values[]">
                            <option>EU</option>
                            <option>Sonstige öffentliche internationale Förderorganisationen</option>
                            <option>DFG</option>
                            <option>Bund</option>
                            <option>Bundesländer</option>
                            <option>Gewerbliche Wirtschaft und sonstige Bereiche</option>
                            <option>Nicht erklärt (Private Mittelgeber/-innen)</option>
                            <option>Nicht erklärt (Öffentliche Mittelgeber/-innen)</option>
                            <option>Sonstige Drittmittelgeber/-innen</option>
                        </select>
                    </div>

                    <div class="col-sm-4">
                        <label for="">
                            Wissenschaftliche Projektleiter/-in
                        </label>
                        <input type="text" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Zwecks des Projekts
                        </label>
                        <input type="text" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="koordination-check" value="">
                            <label for="koordination-check">
                                Koordinationsrolle (Einrichtung)
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Koordinator-Einrichtung
                        </label>
                        <input type="text" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Projektbeginn
                        </label>
                        <input type="date" class="form-control" name="values[]">
                    </div>
                    <div class="col-sm-4">
                        <label for="">
                            Projektende
                        </label>
                        <input type="date" class="form-control" name="values[]">
                    </div>




                </div>

                <button class="btn btn-primary" type="submit" id="submit-btn"><i class="ph ph-regular ph-check"></i> <?= lang("Save", "Speichern") ?></button>

            </form>
        </div>
    </div>
</div>


<div class="content">
    <!-- <a target="_blank" href="<?= ROOTPATH ?>/docs/add-activities" class="btn btn-tour float-right ml-5" id="docs-btn">
        <i class="ph ph-regular ph-lg ph-question mr-5"></i>
        <?= lang('Read the Docs', 'Zur Hilfeseite') ?>
    </a> -->
    <h2 class="mt-0">
        <i class="ph ph-tree-structure text-osiris"></i>
        <?= lang('Projects', 'Projekte') ?>
    </h2>

    <?php if (false) : ?>
        <a href="#add-projects">
            <i class="ph ph-regular ph-plus"></i>
            <?= lang('Add new project', 'Neues Projekt anlegen') ?>
        </a>
    <?php endif; ?>

    <p>
        Hier entstehen demnächst Drittmittelprojekte.
    </p>

</div>

<div class="row row-eq-spacing-md">

    <?php
    $projects = $osiris->projects->find();
    foreach ($projects as $project) {
        $contact = $DB->getPerson($project['contact_person'] ?? '', true);
    ?>
        <div class="col-md-6">
            <div class="box" id="<?= $project['_id'] ?>">
                <div class="content">
                    <h5 class="mt-0">
                        <span class="highlight-text"><?= $project['project'] ?></span>
                        <?= $project['title'] ?>
                    </h5>

                    <span><?= $project['affiliation'] ?></span>
                    |
                    <a class="" href="<?= ROOTPATH ?>/profile/<?= $project['contact_person'] ?? '' ?>"><?= $contact['displayname'] ?? '' ?></a>

                    <div class="float-right ">
                        <a href="<?= ROOTPATH ?>/activities/new?type=projects&projects=<?= $project['project'] ?>" class="btn text-projects btn-sm">
                            <i class="ph ph-regular ph-lg ph-chalkboard-simple-user"></i>
                            <i class="ph ph-regular ph-plus"></i>
                            <span class="sr-only"><?= lang('Add course', 'Veranstaltung hinzufügen') ?></span>
                        </a>
                    </div>
                </div>
                <?php
                $activities = $osiris->activities->find(['project_id' => strval($project['_id'])]);
                if (!empty($activities)) :
                ?>
                    <hr>
                    <div class="content">

                        <?php foreach ($activities as $doc) :
                            $Format->setDocument($doc);
                        ?>
                            <?= $Format->activity_icon() ?>
                            <?= $Format->formatShort() ?>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>


            </div>
        </div>
    <?php } ?>

</div>