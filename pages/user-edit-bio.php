<?php

/**
 * Page to edit user information
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /user/edit/<username>
 *
 * @package     OSIRIS
 * @since       1.2.3
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */
?>

<h1 class="mt-0">
    <i class="ph ph-student"></i>
    <?= $data['name'] ?>
</h1>

<form action="<?= ROOTPATH ?>/crud/users/update/<?= $data['username'] ?>" method="post">
    <div class="box w-600 mw-full">
        <div class="content">

            <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

            <p>
                <b>Username:</b> <?= $data['username'] ?? '' ?>
            </p>

            <h4 class="title mt-0" id="position">
                <?= lang('Current Position', 'Aktuelle Position') ?>
            </h4>


            <div class="form-group">
                <div class="row row-eq-spacing my-0">
                    <div class="col-md-6">
                        <label for="position_de" class="d-flex">Deutsch <img src="<?= ROOTPATH ?>/img/de.svg" alt="DE" class="flag"></label>
                        <input name="values[position_de]" id="position_de" type="text" class="form-control" value="<?= htmlspecialchars($data['position_de'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="position" class="d-flex">English <img src="<?= ROOTPATH ?>/img/gb.svg" alt="EN" class="flag"></label>
                        <input name="values[position]" id="position" type="text" class="form-control" value="<?= htmlspecialchars($data['position'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="content">

            <h4 class="title mt-0" id="research">
                <?= lang('Research interest', 'Forschungsinteressen') ?>
            </h4>
            
            <small class="text-muted">Max. 5</small><br>
            <table class="table simple">
                <thead>
                    <tr>
                        <th><label for="position" class="d-flex">English <img src="<?= ROOTPATH ?>/img/gb.svg" alt="EN" class="flag"></label></th>
                        <th><label for="position_de" class="d-flex">Deutsch <img src="<?= ROOTPATH ?>/img/de.svg" alt="DE" class="flag"></label></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="research-interests">
                    <?php 
                    $data['research_de'] = $data['research_de'] ?? array();
                    foreach (($data['research'] ?? array()) as $i => $n) {
                        $n_de = $data['research_de'][$i] ?? '';
                    ?>
                        <tr class="research-interest">
                            <td>
                                <input type="text" name="values[research][]" value="<?= $n ?>" list="research-list" required class="form-control">
                            </td>
                            <td>
                                <input type="text" name="values[research_de][]" value="<?= $n_de ?>" list="research-list-de" class="form-control">
                            </td>
                            <td><a class="btn text-danger" onclick="$(this).closest('.research-interest').remove();"><i class="ph ph-trash"></i></a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <button class="btn" type="button" onclick="addResearchInterest(event);">
                <i class="ph ph-plus"></i>
            </button>

            <datalist id="research-list">
                <?php
                foreach ($osiris->persons->distinct('research') as $d) { ?>
                    <option><?= $d ?></option>
                <?php } ?>
            </datalist>
            <datalist id="research-list-de">
                <?php
                foreach ($osiris->persons->distinct('research-de') as $d) { ?>
                    <option><?= $d ?></option>
                <?php } ?>
            </datalist>

            <script>
                function addResearchInterest(evt) {
                    if ($('.research-interest').length >= 5) {
                        toastError(lang('Max. 5 research interests.', 'Maximal 5 Forschungsinteressen können angegeben werden.'));
                        return;
                    }

                    var tr = `
                        <tr class="research-interest">
                            <td>
                                <input type="text" name="values[research][]" list="research-list" required class="form-control">
                            </td>
                            <td>
                                <input type="text" name="values[research_de][]" list="research-list-de" class="form-control">
                            </td>
                            <td><a class="btn text-danger" onclick="$(this).closest('.research-interest').remove();"><i class="ph ph-trash"></i></a></td>
                        </tr>
                        `;
                    $('#research-interests').append(tr);
                }
            </script>



        </div>
        <hr>
        <div class="content">

            <h4 class="title mt-0" id="cv">
                <?= lang('Curriculum Vitae') ?>
            </h4>


            <button class="btn" type="button" onclick="addCVrow(event, '#cv-list')"><i class="ph ph-plus text-success"></i> <?= lang('Add entry', 'Eintrag hinzufügen') ?></button>
            <br>
            <small class="text-muted float-right"><?= lang('Sorting will be done automatically', 'Wir sortieren das automatisch für dich') ?></small>
            <br>
            <div id="cv-list">
                <?php
                if (isset($data['cv']) && !empty($data['cv'])) {

                    foreach ($data['cv'] as $i => $con) { ?>

                        <div class="alert mb-10">
                            <div class="input-group my-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?= lang('From', 'Von') ?></span>
                                </div>
                                <input type="number" name="values[cv][<?= $i ?>][from][month]" value="<?= $con['from']['month'] ?? '' ?>" class="form-control" placeholder="month *" min="1" max="12" step="1" id="from-month" required>
                                <input type="number" name="values[cv][<?= $i ?>][from][year]" value="<?= $con['from']['year'] ?? '' ?>" class="form-control" placeholder="year *" min="1900" max="<?= CURRENTYEAR ?>" step="1" id="from-year" required>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?= lang('to', 'bis') ?></span>
                                </div>
                                <input type="number" name="values[cv][<?= $i ?>][to][month]" value="<?= $con['to']['month'] ?? '' ?>" class="form-control" placeholder="month" min="1" max="12" step="1" id="to-month">
                                <input type="number" name="values[cv][<?= $i ?>][to][year]" value="<?= $con['to']['year'] ?? '' ?>" class="form-control" placeholder="year" min="1900" step="1" id="to-year">
                            </div>

                            <div class="form-group mb-10">
                                <input name="values[cv][<?= $i ?>][position]" type="text" class="form-control" value="<?= $con['position'] ?? '' ?>" placeholder="Position *" required>
                            </div>
                            <div class="form-group mb-0">
                                <input name="values[cv][<?= $i ?>][affiliation]" type="text" class="form-control" value="<?= $con['affiliation'] ?? '' ?>" placeholder="Affiliation *" list="affiliation-list" required>
                            </div>

                            <small class="text-muted">* <?= lang('required', 'benötigt') ?></small><br>

                            <!-- checkbox to hide from portfolio -->

                            <?php if ($Settings->featureEnabled('portal')) { ?>
                                <div class="custom-checkbox ml-10">
                                    <input type="checkbox" id="hide-<?= $i ?>" <?= ($con['hide'] ?? false) ? 'checked' : '' ?> name="values[cv][<?= $i ?>][hide]">
                                    <label for="hide-<?= $i ?>">
                                        <?= lang('Hide in portfolio', 'Im Portfolio verstecken') ?>
                                    </label>
                                </div>
                            <?php } ?>


                            <button class="btn danger my-10" type="button" onclick="$(this).closest('.alert').remove()"><i class="ph ph-trash"></i></button>
                        </div>
                <?php }
                } ?>
            </div>

            <script>
                var i = <?= $i ?? 0 ?>

                var CURRENTYEAR = <?= CURRENTYEAR ?>;

                function addCVrow(evt, parent) {
                    i++;
                    var el = `
            <div class="alert mb-10">
                    <div class="input-group my-10">
                        <div class="input-group-prepend">
                            <span class="input-group-text">${lang('From', 'Von')}</span>
                        </div>
                        <input type="number" name="values[cv][${i}][from][month]" class="form-control" placeholder="month *" min="1" max="12" step="1" id="from-month" required>
                        <input type="number" name="values[cv][${i}][from][year]" class="form-control" placeholder="year *" min="1900" max="${CURRENTYEAR}" step="1" id="from-year" required>
                        <div class="input-group-prepend">
                            <span class="input-group-text">${lang('to', 'bis')}</span>
                        </div>
                        <input type="number" name="values[cv][${i}][to][month]" class="form-control" placeholder="month" min="1" max="12" step="1" id="to-month">
                        <input type="number" name="values[cv][${i}][to][year]" class="form-control" placeholder="year" min="1900" step="1" id="to-year">
                    </div>

                    <div class="form-group mb-10">
                        <input name="values[cv][${i}][position]" type="text" class="form-control" placeholder="Position *" required>
                    </div>

                    <div class="form-group mb-0">
                        <input name="values[cv][${i}][affiliation]" type="text" class="form-control" placeholder="Affiliation *" list="affiliation-list" required>
                    </div>

                    <small class="text-muted">* required</small><br>

                    <button class="btn danger my-10" type="button" onclick="$(this).closest('.alert').remove()"><i class="ph ph-trash"></i></button>
                </div>
                `;
                    $(parent).prepend(el);
                }
            </script>


            <datalist id="affiliation-list">
                <?php
                foreach ($osiris->persons->distinct('cv.affiliation') as $d) { ?>
                    <option><?= $d ?></option>
                <?php } ?>
            </datalist>

        </div>
        <hr>
        <div class="content">




            <button type="submit" class="btn secondary">
                Update
            </button>

        </div>
    </div>
</form>