<div class="content">

    <h1>
        <i class="fa-regular fa-book-open-cover text-success fa-lg mr-10"></i>
        <?= lang('My reviews &amp; editorial boards', 'Meine Reviews &amp; Editorial Boards') ?>
    </h1>

    <div class="form-group with-icon mb-10 mw-full w-350">
        <input type="search" class="form-control" placeholder="<?= lang('Filter') ?>" oninput="filter_results(this.value)">
        <i class="fas fa-arrow-rotate-left" onclick="$(this).prev().val(''); filter_results('')"></i>
    </div>


    <div class="box box-primary  add-form" id="review-form" style="display:none">
        <div class="content">
            <?php
            include BASEPATH . "/components/form-review.php"
            ?>
        </div>
    </div>


    <div id="add-btn-row">
        <button class="btn btn-link" onclick="$('#review-form').slideToggle() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
    </div>

    <table class="table" id="result-table">
        <thead>
            <tr>
                <!-- <td><?= lang('Quarter', 'Quartal') ?></td> -->
                <td><?= lang('Activity', 'Aktivität') ?></td>
                <td><?= lang('Count of reviews', 'Anzahl d. Reviews') ?></td>
            </tr>
        </thead>

        <?php
            if ($USER['is_controlling']) {
                // controlling sees everything from the current year
                $filter = [
                    '$or' => array(
                        ['end.year' => array('$gte' => SELECTEDYEAR)],
                        ['end' => null],
                        ['dates.year' => SELECTEDYEAR]
                    )
                ];
            } else {
                // everybody else sees their own work (all)
                $filter = ['user' => $user];
            }
            $cursor = $osiris->reviews->find($filter);
        // dump($cursor);
        if (empty($cursor)) {
            echo "<tr class='row-danger'><td colspan='3'>" . lang('No reviews found.', 'Keine Reviews gefunden.') . "</td></tr>";
        } else foreach ($cursor as $document) {
            // $q = getQuarter($document['start']['month']);
            // $in_quarter = $q == SELECTEDQUARTER;
            $in_quarter = true;
        ?>
            <tr class="<?= !$in_quarter ? 'row-muted' : '' ?>" id="<?= $document['_id'] ?>">
                <td>
                    <?php echo format("review", $document); ?>
                    <?php if (isset($document['dates'])) { ?>
                        <a onclick="$(this).next().slideToggle()"><i class="fas fa-asterisk"></i></a>
                        <div style="display:none;" class="text-muted">
                            <?php 
                            $dates = [];
                            foreach ($document['dates'] as $date) { 
                                $d = getDateTime($date);
                                $dates[] = date_format($d, "m/Y");
                             }
                             echo commalist($dates, lang('and', 'und'));
                             ?>

                        </div>
                    <?php } ?>

                </td>
                <td class="unbreakable">
                <?php if ($document['role'] == 'Reviewer') { ?>
                            <div class="dropdown">
                                <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa-regular fa-lg fa-calendar-plus"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-center" aria-labelledby="dropdown-1">
                                    <div class="content">
                                        <form action="<?= ROOTPATH ?>/push-dates/review/<?= $document['_id'] ?>" method="post">
                                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                                            <div class="form-group">
                                                <label class="required" for="date"><?= lang('Date', 'Datum') ?></label>
                                                <input type="date" class="form-control" name="values[dates]" id="date" required>
                                            </div>

                                            <button class="btn"><?= lang('Add review', 'Review hinzufügen') ?></button>
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
                                        <form action="<?= ROOTPATH ?>/push-dates/review/<?= $document['_id'] ?>" method="post">
                                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">


                                            <div class="form-group">
                                                <label class="required" for="start"><?= lang('Start', 'Anfang') ?></label>
                                                <input type="date" class="form-control" name="values[start]" id="start" required value="<?= valueFromDateArray($document['start'] ?? '') ?>">
                                            </div>


                                            <div class="form-group">
                                                <label for="end"><?= lang('End', 'Ende') ?></label>
                                                <input type="date" class="form-control" name="values[end]" id="end" value="<?= valueFromDateArray($document['end'] ?? '') ?>">
                                            </div>

                                            <button class="btn"><?= lang('Update', 'Aktualisieren') ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                    <div class="dropdown">
                        <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-regular fa-lg fa-trash-alt"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                            <div class="content">
                                <button class="btn text-danger" onclick="_delete('review', '<?= $document['_id'] ?>')">
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

    </table>
</div>