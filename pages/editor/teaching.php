<div class="content">

    <h1 class=""><i class="fa-regular fa-people text-muted fa-lg mr-10"></i> <?= lang('Teaching &amp; Guests', 'Lehre &amp; Gäste') ?></h1>

    <div class="form-group with-icon mb-10 mw-full w-350">
        <input type="search" class="form-control" placeholder="<?= lang('Filter') ?>" oninput="filter_results(this.value)">
        <i class="fas fa-arrow-rotate-left" onclick="$(this).prev().val(''); filter_results('')"></i>
    </div>

    <div class="box box-primary" id="teaching-form" style="display:none">

        <?php
        include BASEPATH . "/components/form-teaching.php"
        ?>
    </div>

    <button class="btn btn-link" onclick="$('#add-btn-row').hide();$('#teaching-form').slideDown() " id="add-btn-row">
        <i class="fas fa-plus"></i> <?= lang('Add stay', 'Füge Aufenthalt hinzu') ?>
    </button>

    <table class="table" id="result-table">
        <thead>
            <tr>
                <!-- <td>Zuname, Vorname</td>
                <td>Einrichtung (Name, Ort, Land)</td>
                <td>Kategorie</td>
                <td>Titel des Programms/der Arbeit bzw. Grund des Aufenthalts</td>
                <td>Zeit des Aufenthalts</td>
                <td>Verantw. Wissenschaftler</td>
                <td>Abteilung(en)</td> -->
            </tr>
        </thead>
        <tbody>


            <?php
            if ($USER['is_controlling']) {
                // controlling sees everything from the current year
                $filter = [
                    "start.year" => array('$lte' => SELECTEDYEAR),
                    '$or' => array(
                        ['end.year' => array('$gte' => SELECTEDYEAR)],
                        ['end' => null]
                    )
                ];
            } else {
                // everybody else sees their own work (all)
                $filter = ['authors.user' => $user];
            }
            $cursor = $osiris->teachings->find($filter);
            // $cursor = $collection->find([
            //     'authors.user' => $user,
            //     "start.year" => array('$lte' => SELECTEDYEAR),
            //     '$or' => array(
            //         ['end.year' => array('$gte' => SELECTEDYEAR)],
            //         ['end' => null]
            //     )
            // ]);
            // dump($cursor);
            foreach ($cursor as $document) {
                $q = getQuarter($document['start']['month']);
                $in_quarter = $q == SELECTEDQUARTER;
            ?>
                <tr class="<?= !$in_quarter ? 'row-muted' : '' ?>" id="<?= $document['_id'] ?>">
                    <td class="quarter">
                        <?= $document['start']['year'] ?>Q<?= $q ?>
                    </td>
                    <td>
                        <?php echo format_teaching($document, true); ?>
                    </td>
                    <td class="unbreakable">
                        <div class="dropdown">
                            <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-calendar-pen"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <form action="" method="post">
                                        <input type="hidden" name="repetition" value="<?= $id ?>">
                                        <label class="required" for="end"><?= lang('Ended at / Extend until', 'Geendet am / Verlängern bis') ?>:</label>
                                        <input type="date" class="form-control" name="end" id="date_end" value="<?= valueFromDateArray($document['end'] ?? '') ?>" required>
                                        <br>
                                        <div>
                                            <div class="custom-radio mb-5">
                                                <input type="radio" name="status" id="status-in-progress-<?= $id ?>" value="in progress" checked="checked">
                                                <label for="status-in-progress-<?= $id ?>"><?= lang('In progress', 'In Arbeit') ?></label>
                                            </div>

                                            <div class="custom-radio mb-5">
                                                <input type="radio" name="status" id="status-completed-<?= $id ?>" value="completed">
                                                <label for="status-completed-<?= $id ?>"><?= lang('Completed', 'Abgeschlossen') ?></label>
                                            </div>

                                            <div class="custom-radio mb-5">
                                                <input type="radio" name="status" id="status-aborted-<?= $id ?>" value="aborted">
                                                <label for="status-aborted-<?= $id ?>"><?= lang('Aborted', 'Abgebrochen') ?></label>
                                            </div>
                                        </div>
                                        <button class="btn mt-20" type="button" onclick="todo()"><?= lang('Submit', 'Bestätigen') ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <button class="btn btn-sm text-success" onclick="toggleEditForm('teaching', '<?= $document['_id'] ?>')">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </button>

                        <div class="dropdown">
                            <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-trash-alt"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <button class="btn text-danger" onclick="_delete('teaching', '<?= $document['_id'] ?>')">
                                        <?= lang(
                                            'Delete entry',
                                            'Lösche Eintrag'
                                        ) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

            <?php } ?>
        </tbody>

    </table>

</div>