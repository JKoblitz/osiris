<?php
include_once BASEPATH . "/php/Publication.php";

$subpage = $_GET['add'] ?? 'view';
?>


<div class="content">

    <h1 class=""><i class="fa-regular fa-book-bookmark text-primary fa-lg mr-10"></i>  <?= lang('My publications', 'Meine Publikationen') ?></h1>


<a href="<?=ROOTPATH?>/my-publication/add"><i class="fas fa-plus"></i> <?=lang('Add publication', 'Publikation hinzufügen')?></a>

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
            $publication = new Publication;
            $stmt = $db->prepare(
                "SELECT publication_id, q_id FROM `authors` 
                    INNER JOIN publication USING (publication_id) 
                    WHERE user LIKE ? ORDER BY q_id DESC"
            );
            $stmt->execute([$user]);
            $activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($activity)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No publications found.', 'Keine Publikationen gefunden.') . "</td></tr>";
            } else foreach ($activity as $act) {
                $selected = ($act['q_id'] == SELECTEDYEAR . "Q" . SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td class="quarter">
                        <?= str_replace('Q', ' Q', $act['q_id']) ?>
                    </td>
                    <td>
                        <?php $publication->print($act['publication_id']); ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm text-danger" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-user-slash"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">

                                    <button class="btn text-danger" onclick="todo()">
                                        <?= lang(
                                            'I am not author of this publication',
                                            'Ich bin nicht Author dieser Publikation'
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



<datalist id="scientist-list">
    <?php
    $stmt = $db->prepare("SELECT CONCAT(last_name, ', ', first_name) FROM `users` ORDER BY last_name ASC");
    $stmt->execute();
    $scientist = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($scientist as $s) { ?>
        <option><?= $s ?></option>
    <?php } ?>
</datalist>