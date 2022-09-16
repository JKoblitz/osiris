
<div class="modal" id="download-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-transparent border-0">
      <a href="#/" class="close" role="button" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </a>
      <!-- <h5 class="title">Modal title</h5> -->
      <?php
        include BASEPATH."/pages/export/publications.php";
      ?>
      
    </div>
  </div>
</div>



<div class="content">

    <h1 class=""><i class="fa-regular fa-book-bookmark text-primary fa-lg mr-10"></i> <?= lang('My publications', 'Meine Publikationen') ?></h1>


    <div class="form-group with-icon mb-10 mw-full w-350 d-inline-block">
        <input type="search" class="form-control" placeholder="<?= lang('Filter') ?>" oninput="filter_results(this.value)">
        <i class="fas fa-arrow-rotate-left" onclick="$(this).prev().val(''); filter_results('')"></i>
    </div>

    <a href="#download-modal" class="btn"><i class="fas fa-lg fa-download"></i> Download</a>

<br>
    <a class="btn btn-link" href="<?= ROOTPATH ?>/publication/add"><i class="fas fa-plus"></i> <?= lang('Add publication', 'Publikation hinzufügen') ?></a>

    <table class="table" id="result-table">
        <thead>
            <tr>
                <td><?= lang('Quarter', 'Quartal') ?></td>
                <td><?=lang('Type', 'Typ')?></td>
                <td><?= lang('Publication', 'Publikation') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php

            $options = ['sort' => ["year" => -1, "month" => -1]];
            if ($USER['is_controlling']) {
                // controlling sees everything from the current year
                $filter = ['year' => SELECTEDYEAR];
            } else {
                // everybody else sees their own work (all)
                $filter = ['$or' => [['authors.user' => $user], ['editors.user' => $user]]];
            }
            $cursor = $osiris->publications->find($filter, $options);
            //, 'year' => intval(SELECTEDYEAR)
            if (empty($cursor)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No publications found.', 'Keine Publikationen gefunden.') . "</td></tr>";
            } else foreach ($cursor as $document) {
                $id = $document['_id'];
                $q = getQuarter($document['month']);
                $in_quarter = $q == SELECTEDQUARTER;
                $author=getUserAuthor($document['authors'] ?? array(), $user);
            ?>
                <tr class="<?= !$in_quarter ? 'row-muted' : '' ?>" id="<?= $id ?>">
                    <td class="quarter">
                        <?= $document['year'] ?>Q<?= $q ?>
                    </td>
                    <td class="text-center">
                        <?php
                            echo publication_icon($document['type']);
                        ?>
                        
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
                                    <button class="btn text-danger" onclick="_approve('publication', '<?= $id ?>', 3)">
                                        <?= lang(
                                            'I am not author of this publication',
                                            'Ich bin nicht Autor dieser Publikation'
                                        ) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($author['aoi']) && ($author['aoi'] === 0 || $author['aoi'] === false)) { ?>
                            
                            
                        <div class="dropdown">
                            <button class="btn btn-sm text-success" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-handshake"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <button class="btn text-danger" onclick="_approve('publication', '<?= $id ?>', 1)">
                                        <?= lang(
                                            'I am affiliated to the ' . AFFILIATION . ' in this publication',
                                            'Ich bin der ' . AFFILIATION . ' zugehörig in dieser Publikation'
                                        ) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="dropdown">
                            <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-handshake-slash"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <button class="btn text-danger" onclick="_approve('publication', '<?= $id ?>', 2)">
                                        <?= lang(
                                            'I am not affiliated to the ' . AFFILIATION . ' in this publication',
                                            'Ich bin nicht der ' . AFFILIATION . ' zugehörig in dieser Publikation'
                                        ) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <button class="btn btn-sm text-success" onclick="toggleEditForm('publication', '<?= $id ?>')">
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