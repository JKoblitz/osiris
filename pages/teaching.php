<?php

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




<div class="modal" id="add-teaching" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" href="#" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>


            <form action="<?= ROOTPATH ?>/create-teaching" method="post" enctype="multipart/form-data" id="activity-form">
                <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                <div class="form-group lang-<?= lang('en', 'de') ?>" data-visible="article,preprint,magazine,book,chapter,lecture,poster,dissertation,others,misc-once,misc-annual,students,guests,teaching,software">
                    <label for="title" class="required element-title">
                        <?= lang('Name of the module', 'Name des Moduls') ?>
                    </label>

                    <div class="form-group title-editor"><?= $form['title'] ?? '' ?></div>
                    <input type="text" class="form-control hidden" name="values[title]" id="title" required value="<?= val('title') ?>">
                </div>



                <div class="form-row row-eq-spacing" data-visible="students,guests,teaching">

                    <div class="col-sm-2">
                        <label for="module" class="required element-other"><?= lang('Module number', 'Modulnummer') ?></label>
                        <input type="text" class="form-control" name="values[module]" id="module" required value="<?= val('module') ?>" placeholder="MB05">
                    </div>

                    <div class="col-sm">
                        <label for="teaching-affiliation" class="required element-other"><?= lang('Affiliation (Name, City, Country)', 'Einrichtung (Name, Ort, ggf. Land)') ?></label>
                        <input type="text" class="form-control" name="values[affiliation]" id="teaching-affiliation" required value="<?= val('affiliation') ?>" placeholder="TU Braunschweig">
                    </div>

                    <div class="col-sm-4">
                        <label class="required element-author" for="username">
                            <?= lang('Contact person', 'Ansprechpartner:in') ?>
                        </label>
                        <select class="form-control" id="username" name="values[contact_person]" required autocomplete="off">
                            <?php
                            $userlist = $osiris->users->find([], ['sort' => ["last" => 1]]);
                            foreach ($userlist as $j) { ?>
                                <option value="<?= $j['_id'] ?>" <?= $j['_id'] == ($form['user'] ?? $user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                            <?php } ?>
                        </select>
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
        <i class="ph ph-regular ph-chalkboard-simple text-osiris mr-5"></i>
        <?= lang('Teaching Modules', 'Lehrveranstaltungen') ?>
    </h2>
    <a href="#add-teaching"><i class="ph ph-regular ph-plus"></i> Neues Modul anlegen</a>
</div>

<div class="row row-eq-spacing-md">

    <?php
    $modules = $osiris->teaching->find();
    foreach ($modules as $module) {
        $contact = getUserFromId($module['contact_person'] ?? '', true);
    ?>
        <div class="col-md-6">
            <div class="box" id="<?= $module['_id'] ?>">
                <div class="content">
                    <h5 class="mt-0">
                        <span class="highlight-text"><?= $module['module'] ?></span>
                        <?= $module['title'] ?>
                    </h5>

                    <span><?= $module['affiliation'] ?></span>
                    |
                    <a class="" href="<?= ROOTPATH ?>/profile/<?= $module['contact_person'] ?? '' ?>"><?= $contact['displayname'] ?? '' ?></a>

                    <div class="float-right ">
                        <a href="<?= ROOTPATH ?>/activities/new?type=teaching&teaching=<?= $module['module'] ?>" class="btn text-teaching btn-sm">
                            <i class="ph ph-regular ph-lg ph-chalkboard-simple-user"></i>
                            <i class="ph ph-regular ph-plus"></i>
                            <span class="sr-only"><?= lang('Add course', 'Veranstaltung hinzufügen') ?></span>
                        </a>
                    </div>
                </div>
                <?php
                $activities = $osiris->activities->find(['module_id' => strval($module['_id'])]);
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