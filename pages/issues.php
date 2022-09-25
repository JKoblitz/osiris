<h5><?= lang('We found the following issues with your activities.', 'Wir haben die folgenden Probleme mit deinen Aktivitäten gefunden.') ?></h5>

<table class="table">

    <?php
    // $filter = [
    //     "authors" => [
    //         '$elemMatch' => [
    //             "user" => "$user",
    //             "approved" => false
    //         ]
    //     ]
    // ];

    $user = $_SESSION['username'];
    $filter = ['$or' => [['authors.user' => "$user"], ['editors.user' => "$user"], ['user' => "$user"]]];
    $options = ['sort' => ["year" => -1, "month" => -1]];

    $collection = $osiris->activities;
    $cursor = $collection->find($filter);

    foreach ($cursor as $doc) {
        $id = $doc['_id'];
        $type = $doc['type'];
        $approval = !is_approved($doc, $user);
        $epub = ($doc['epub'] ?? false);
        // $doc['epub-delay'] = "2022-08-01";
        if ($epub && isset($doc['epub-delay'])) {
            $startTimeStamp = strtotime($doc['epub-delay']);
            $endTimeStamp = strtotime(date('Y-m-d'));
            $timeDiff = abs($endTimeStamp - $startTimeStamp);
            $numberDays = intval($timeDiff / 86400);  // 86400 seconds in one day
            if ($numberDays < 30) {
                $epub = false;
            }
        }
        $teaching = ($type == "teaching" && $doc['status'] == 'in progress' && new DateTime() > getDateTime($doc['end']));
        if ($approval || $epub || $teaching) { ?>
            <tr id="tr-<?= $id ?>">
                <td>
                    <?= format($doc['type'], $doc); ?>

                    <?php if ($approval) { ?>
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
                    <?php } ?>

                    <?php if ($epub) { ?>
                        <div class='alert alert-signal' id="approve-<?= $id ?>">
                            <?= lang(
                                'This publication is still marked as <q>Epub ahead of print</q>. Is it still not officially published?',
                                'Diese Aktivität ist noch immer markiert als <q>Epub ahead of print</q>. Ist sie noch immer nicht offiziell publiziert?'
                            ) ?>
                            <!-- <br> -->
                            <button class="btn btn-sm ml-20" onclick="todo('<?= $id ?>', 1)">
                                <i class="fas fa-check"></i>
                                <?= lang('Yes, still epub (ask again later).', 'Ja, noch immer Epub (frag erneut in einem Monat).') ?>
                            </button>

                            <div class="input-group input-group-sm w-500">
                                <input type="date" class="form-control" value="<?=valueFromDateArray(["year" => $doc['year'], "month"=>$doc['month'], "day"=> $doc['day']??1])?>">
                                <div class="input-group-append">
                                    <button class="btn" type="button" onclick="todo()">
                                    <i class="fas fa-xmark"></i>
                                        <?=lang('No longer Epub and officially issued under this date.', 'Kein Epub mehr und unter diesem Datum offiziell veröffentlicht.')?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($teaching) { ?>
                        <div class='alert alert-signal' id="approve-<?= $id ?>">
                            <?= lang(
                                "<b>Attention</b>: the Thesis of $doc[name] has ended. Please confirm if the work has been successfully completed or not or extend the time frame.",
                                "<b>Achtung</b>: die Abschlussarbeit von $doc[name] ist zu Ende. Bitte bestätige den Erfolg/Misserfolg der Arbeit oder verlängere den Zeitraum."
                            )  ?>
                            <form action="update/<?= $id ?>" method="post" class="form-inline mt-5">
                                <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                                <label class="required" for="end"><?= lang('Ended at / Extend until', 'Geendet am / Verlängern bis') ?>:</label>
                                <input type="date" class="form-control w-200" name="values[end]" id="date_end" value="<?= valueFromDateArray($doc['end'] ?? '') ?>" required>
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
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>

    <?php } ?>
</table>