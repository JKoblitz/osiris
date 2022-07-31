
<div class="content">

<h1><i class="fa-regular fa-presentation-screen text-danger fa-lg mr-10"></i> <?= lang('My poster', 'Meine Poster') ?></h1>

    <div class="box box-danger" id="poster-form" style="display:none">
        <div class="content">
           <?php
            include BASEPATH . "/components/form-poster.php"
           ?>
        </div>
    </div>

    <div id="add-btn-row">
        <button class="btn btn-link" onclick="$('#poster-form').slideToggle() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <td><?= lang('Quarter', 'Quartal') ?></td>
                <td><?= lang('Poster') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $cursor = $osiris->posters->find(['authors.user' => $user]);
            // dump($cursor);
            if (empty($cursor)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No posters found.', 'Keine Poster gefunden.') . "</td></tr>";
            } else foreach ($cursor as $document) {
                $q = getQuarter($document['start']['month']);
                $in_quarter = $q == SELECTEDQUARTER;
            ?>
                <tr class="<?= !$in_quarter ? 'row-muted' : '' ?>" id="<?=$document['_id']?>">
                    <td class="quarter">
                        <?=$document['start']['year']?>Q<?= $q ?>
                    </td>
                    <td>
                        <?php echo format_poster($document); ?>
                    </td>
                   
                    <td class="unbreakable">
                        <button class="btn btn-sm text-success" onclick="toggleEditForm('poster', '<?= $document['_id'] ?>')">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </button>

                        <div class="dropdown">
                            <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-trash-alt"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <button class="btn text-danger" onclick="_delete('poster', '<?= $document['_id'] ?>')">
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