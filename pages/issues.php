
    <h5><?= lang('Other users have added the following activities for you.', 'Andere Nutzer:innen haben die folgenden Aktivitäten für dich hinzugefügt.') ?></h5>

    <table class="table">

        <?php
        $filter = [
            "authors" => [
                '$elemMatch' => [
                    "user" => "lor15",
                    "approved" => false
                ]
            ]
        ];


        $collection = $osiris->activities;
        $cursor = $collection->find($filter);

        foreach ($cursor as $document) {
            $id = $document['_id'] ?>
            <tr id="tr-<?= $id ?>">
                <td>
                    <?= format($document['type'], $document); ?>
                    <br>
                    <div class='alert alert-signal' id="approve-<?= $id ?>">
                        <?= lang('Is this your activity?', 'Ist dies deine Aktivität?') ?>
                        <!-- <br> -->
                        <button class="btn btn-sm text-success ml-20" onclick="_approve('<?= $id ?>', 1)">
                            <i class="fas fa-check"></i>
                            <?= lang('Yes, this is me and I was affiliated to the' . AFFILIATION, 'Ja, das bin ich und ich war der ' . AFFILIATION . ' angehörig') ?>
                        </button>
                        <button class="btn btn-sm text-danger" onclick="_approve('<?= $id ?>', 2)">
                            <i class="fas fa-handshake-slash"></i>
                            <?= lang('Yes, but I was not affiliated to the ' . AFFILIATION, 'Ja, aber ich war nicht der ' . AFFILIATION . ' angehörig') ?>
                        </button>
                        <button class="btn btn-sm text-danger" onclick="_approve('<?= $id ?>', 3)">
                            <i class="fas fa-xmark"></i>
                            <?= lang('No, this is not me', 'Nein, das bin ich nicht') ?>
                        </button>
                    </div>
                    <?php
                    if ($document['type'] == "teaching") {
                        if ($document['status'] == 'in progress' && new DateTime() > getDateTime($document['end'])) {

                            $approval_needed[] = array(
                                'type' => 'teaching',
                                'id' => $document['_id'],
                                'title' => $document['title']
                            );
                    ?>
                            <div class='alert alert-signal' id="approve-<?= $id ?>">
                                <?= lang(
                                    "<b>Attention</b>: the Thesis of $document[name] has ended. Please confirm if the work has been successfully completed or not or extend the time frame.",
                                    "<b>Achtung</b>: die Abschlussarbeit von $document[name] ist zu Ende. Bitte bestätige den Erfolg/Misserfolg der Arbeit oder verlängere den Zeitraum."
                                )  ?>
                                <form action="update/<?= $id ?>" method="post" class="form-inline mt-5">
                                    <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                                    <label class="required" for="end"><?= lang('Ended at / Extend until', 'Geendet am / Verlängern bis') ?>:</label>
                                    <input type="date" class="form-control w-200" name="values[end]" id="date_end" value="<?= valueFromDateArray($document['end'] ?? '') ?>" required>
                                    <div>
                                        <div class="custom-radio d-inline">
                                            <input type="radio" name="values[status]" id="status-in-progress-<?= $id ?>" value="in progress" checked="checked">
                                            <label for="status-in-progress-<?= $id ?>"><?= lang('In progress', 'In Arbeit') ?></label>
                                        </div>

                                        <div class="custom-radio d-inline">
                                            <input type="radio" name="values[status]" id="status-completed-<?= $id ?>" value="completed">
                                            <label for="status-completed-<?= $id ?>"><?= lang('Completed', 'Abgeschlossen') ?></label>
                                        </div>

                                        <div class="custom-radio mr-10 d-inline">
                                            <input type="radio" name="values[status]" id="status-aborted-<?= $id ?>" value="aborted">
                                            <label for="status-aborted-<?= $id ?>"><?= lang('Aborted', 'Abgebrochen') ?></label>
                                        </div>
                                    </div>
                                    <button class="btn" type="submit"><?= lang('Submit', 'Bestätigen') ?></button>
                                </form>
                            </div>
                    <?php
                        }
                    } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

TODO: teaching that is not completed but has ended.

TODO: Epubs are still open?