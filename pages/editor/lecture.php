<div class="content">

    <h1 class="">
        <i class="fa-regular fa-keynote text-signal fa-lg mr-10"></i> 
        <?= lang('My lectures', 'Meine Vorträge') ?>
    </h1>
    
<div class="form-group with-icon mb-10 mw-full w-350">
        <input type="search" class="form-control" placeholder="<?= lang('Filter') ?>" oninput="filter_results(this.value)">
        <i class="fas fa-arrow-rotate-left" onclick="$(this).prev().val(''); filter_results('')"></i>
    </div>


    <div class="box box-primary add-form" id="lecture-form" style="display:none">
        <div class="content">
            <p class="text-muted">
                <?= lang(
                    '
                <b>Note</b>: If you repeated a previously held talk, please click on <i class="fa-regular fa-lg fa-calendar-plus"></i> 
                at the respective lecture below to create a repetition.',
                    '<b>Hinweis</b>: Falls du einen zuvorgehaltenen Vortrag wiederholt hast, klicke bitte beim entsprechenden Vortrag auf 
                <i class="fa-regular fa-lg fa-calendar-plus"></i>, um eine Wiederholung hinzuzufügen.'
                ) ?>
            </p>

            <?php
            include BASEPATH . "/components/form-lecture.php"
            ?>

        </div>
    </div>

    <div id="add-btn-row">
        <button class="btn btn-link" onclick="$('#lecture-form').slideToggle() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
    </div>

    <table class="table" id="result-table">
        <thead>
            <tr>
                <td><?= lang('Quarter', 'Quartal') ?></td>
                <td><?= lang('Lecture') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($USER['is_controlling']) {
                // controlling sees everything from the current year
                $filter = ['start.year' => SELECTEDYEAR];
            } else {
                // everybody else sees their own work (all)
                $filter = ['authors.user' => $user];
            }
            $cursor = $osiris->lectures->find($filter);
            // dump($cursor);
            if (empty($cursor)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No lectures found.', 'Keine Vorträge gefunden.') . "</td></tr>";
            } else foreach ($cursor as $document) {
                $q = getQuarter($document['start']['month']);
                $in_quarter = $q == SELECTEDQUARTER;
            ?>
                <tr class="<?= !$in_quarter ? 'row-muted' : '' ?>" id="<?= $document['_id'] ?>">
                    <td class="quarter">
                        <?= $document['start']['year'] ?>Q<?= $q ?>
                    </td>
                    <td>
                        <?php echo format_lecture($document); ?>
                    </td>
                    <td class="unbreakable">
                        <div class="dropdown">
                            <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-lg fa-calendar-plus"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-center" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <input type="hidden" name="repetition" value="<?= $act['lecture_id'] ?>">
                                    <label class="required" for="start"><?= lang('Repeated at', 'Wiederholt am') ?></label>
                                    <input type="date" class="form-control" name="start" id="start-<?= $document['_id'] ?>" required>
                                    <button class="btn mt-20" type="button" onclick="_addRepetition('<?= $document['_id'] ?>')"><?= lang('Add repetition', 'Wiederholung hinzufügen') ?></button>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-sm text-success" onclick="toggleEditForm('lecture', '<?= $document['_id'] ?>')">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </button>

                        <div class="dropdown">
                            <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-trash-alt"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <button class="btn text-danger" onclick="_delete('lecture', '<?= $document['_id'] ?>')">
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

<script>
    function _addRepetition(id) {
        console.log(id);
        var date = $('#' + id).find('input[name=start]').val().trim()
        if (date.length === 0) {
            toastError('Date is required.')
            return
        }
        $.ajax({
            type: "POST",
            data: {
                date: date
            },
            dataType: "json",
            url: ROOTPATH + '/add-repetition/lecture/' + id,
            success: function(response) {
                console.log(response);
                toastSuccess("Added repetition.")
                // $('#'+id).remove();
                // $('#'+id).fadeOut();
                prependRow('<td></td><td>' + response.result + '</td>')
                // const element = '<tr><td></td><td>'+response.result+'</td></tr>'
                //.prepend(element)
            },
            error: function(response) {
                toastError(response.responseText)
                 $('.loader').removeClass('show') 
            }
        })
    }
</script>