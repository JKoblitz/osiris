<div class="content">

    <h1><i class="fa-regular fa-icons text-muted fa-lg mr-10"></i> <?= lang('My other research activities', 'Meine anderen Forschungsaktivitäten') ?></h1>


    <div class="box box-primary" id="misc-form" style="display:none">
        <div class="content">

            <?php
            include BASEPATH . "/components/form-misc.php"
            ?>

        </div>
    </div>
    <div id="add-btn-row">
        <button class="btn btn-link" onclick="$('#misc-form').slideToggle() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
    </div>
    
    <table class="table" id="activity-table">
        <thead>
            <tr>
                <!-- <td><?= lang('Quarter', 'Quartal') ?></td> -->
                <td><?= lang('Activity', 'Aktivität') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>


            <?php
            $cursor = $osiris->miscs->find(['authors.user' => $user]);
            // dump($cursor);
            if (empty($cursor)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No activities found.', 'Keine Aktivitäten gefunden.') . "</td></tr>";
            } else foreach ($cursor as $document) {
                // $q = getQuarter($document[]['start']['month']);
                // $in_quarter = $q == SELECTEDQUARTER;
                $in_quarter = true;
            ?>
                <tr class="<?= !$in_quarter ? 'row-muted' : '' ?>" id="<?= $document['_id'] ?>">
                    <td>
                        <?php echo format_misc($document); ?>
                    </td>
                    <td>
                        <?php if ($document['iteration'] == 'once') { ?>
                            <div class="dropdown">
                                <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa-regular fa-lg fa-calendar-plus"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-center" aria-labelledby="dropdown-1">
                                    <div class="content">
                                        <form action="<?= ROOTPATH ?>/push-dates/misc/<?= $document['_id'] ?>" method="post">
                                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                                            <div class="form-group">
                                                <label class="required" for="start"><?= lang('Start', 'Anfang') ?></label>
                                                <input type="date" class="form-control" name="values[start]" id="start" required>
                                            </div>


                                            <div class="form-group">
                                                <label for="end"><?= lang('End', 'Ende') ?></label>
                                                <input type="date" class="form-control" name="values[end]" id="end">
                                            </div>

                                            <button class="btn"><?= lang('Add date', 'Datum hinzufügen') ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php } else { ?>
                            <div class="dropdown">
                                <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa-regular fa-lg fa-calendar-pen"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-center" aria-labelledby="dropdown-1">
                                    <div class="content">
                                        <form action="<?= ROOTPATH ?>/push-dates/misc/<?= $document['_id'] ?>" method="post">
                                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">


                                            <div class="form-group">
                                                <label class="required" for="start"><?= lang('Start', 'Anfang') ?></label>
                                                <input type="date" class="form-control" name="values[start]" id="start" required value="<?= valueFromDateArray($document['dates'][0]['start'] ?? '') ?>">
                                            </div>


                                            <div class="form-group">
                                                <label for="end"><?= lang('End', 'Ende') ?></label>
                                                <input type="date" class="form-control" name="values[end]" id="end" value="<?= valueFromDateArray($document['dates'][0]['end'] ?? '') ?>">
                                            </div>

                                            <button class="btn"><?= lang('Update', 'Aktualisieren') ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                        <button class="btn btn-sm text-danger" data-toggle="tooltip" data-title="<?= lang('Remove activity', 'Entferne Aktivität') ?>" onclick="_delete('misc', '<?= $document['_id'] ?>')">
                            <i class="fa-regular fa-lg fa-trash-alt"></i>
                        </button>

                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>

</div>