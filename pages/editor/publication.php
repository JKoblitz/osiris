<div class="content">

    <h1 class=""><i class="fa-regular fa-book-bookmark text-primary fa-lg mr-10"></i> <?= lang('My publications', 'Meine Publikationen') ?></h1>

    <a class="btn btn-link" href="<?= ROOTPATH ?>/my-publication/add"><i class="fas fa-plus"></i> <?= lang('Add publication', 'Publikation hinzufügen') ?></a>

    <table class="table">
        <thead>
            <tr>
                <td><?= lang('Quarter', 'Quartal') ?></td>
                <td><?= lang('Publication', 'Publikation') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php

            $collection = $osiris->publications;
            $options = ['sort' => ["year" => -1, "month"=> -1]];
            $cursor = $collection->find(['$or'=> [['authors.user' => $user], ['editors.user' => $user]]], $options);
            //, 'year' => intval(SELECTEDYEAR)
            if (empty($cursor)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No publications found.', 'Keine Publikationen gefunden.') . "</td></tr>";
            } else foreach ($cursor as $document) {
                $q = getQuarter($document['month']);
                $in_quarter = $q == SELECTEDQUARTER;
            ?>
                <tr class="<?= !$in_quarter ? 'row-muted' : '' ?>" id="<?=$document['_id']?>">
                    <td class="quarter">
                        <?=$document['year']?>Q<?= $q ?>
                    </td>
                    <td>
                        <?php echo format_publication($document); ?>
                    </td>
                    <td class="unbreakable">
                        <div class="dropdown">
                            <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-user-slash"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <button class="btn text-danger" onclick="todo()">
                                        <?= lang(
                                            'I am not author of this publication',
                                            'Ich bin nicht Autor dieser Publikation'
                                        ) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-regular fa-lg fa-handshake-slash"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <button class="btn text-danger" onclick="todo()">
                                        <?= lang(
                                            'I am not affiliated to the '.AFFILIATION.' in this publication',
                                            'Ich bin nicht der '.AFFILIATION.' zugehörig in dieser Publikation'
                                        ) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button class="btn btn-sm text-success" onclick="toggleEditForm('publication', '<?= $document['_id'] ?>')">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>

</div>



<datalist id="scientist-list">
    <?php
    $scientist = $osiris->users->find();
    foreach ($scientist as $s) { ?>
        <option><?= $s['last_name'] ?>, <?= $s['first_name'] ?></option>
    <?php } ?>
</datalist>