<div class="content">

<h1><i class="fa-regular fa-book-open-cover text-success fa-lg mr-10"></i> <?= lang('My reviews &amp; editorial boards', 'Meine Reviews &amp; Editorial Boards') ?></h1>

<table class="table">
        <thead>
            <tr>
                <td><?= lang('Quarter', 'Quartal') ?></td>
                <td><?= lang('Activity', 'Aktivität') ?></td>
                <td><?= lang('Count of reviews', 'Anzahl d. Reviews') ?></td>
            </tr>
        </thead>
        <tr id="add-btn-row">
            <td colspan="3">
                <button class="btn" onclick="$('#add-btn-row').hide();$('#interface-row').show() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
            </td>
        </tr>
        <tr id="interface-row" style="display:none">
            <td class="quarter">
                <?= SELECTEDQUARTER ?>
                <input type="hidden" name="quarter" value="<?= SELECTEDQUARTER ?>">
                <input type="hidden" name="user" value="<?= $user ?>">
                <input type="hidden" name="type" value="review-add">
            </td>
            <td>
                <div class="input-group">
                    <select class="form-control" id="type-input" name="activity" style="max-width: 15rem;" required>
                        <option value="review" selected>Reviewer</option>
                        <option value="editor">Editorial</option>
                    </select>
                    <input type="text" class="form-control" placeholder="Journal" id="journal-input" name="journal" list="journal-list" required>
                </div>
            </td>
            <td>
                <input type="number" name="review_count" id="review_count" value="1" class="form-control w-50 d-inline-block">

                <button class="btn btn-success ml-10" onclick="addRow2db(this)">
                    <i class="fa-regular fa-check"></i>
                </button>
            </td>
        </tr>

        <?php
        $stmt = $db->prepare(
            "SELECT * FROM `review`
        LEFT JOIN journal USING (journal_id)
        WHERE user LIKE ? ORDER BY q_id DESC, `type` DESC"
        );
        $stmt->execute([$user]);
        $review = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($review)) {
            echo "<p>" . lang('No reviews or editorials found.', 'Keine Reviews oder Editorials gefunden.') . "</p>";
        } else foreach ($review as $pub) {
            $selected = ($pub['q_id'] == SELECTEDQUARTER);
            $review_id = $pub['review_id'];
        ?>
            <tr class="<?= !$selected ? 'row-muted' : '' ?>" id="review<?=$review_id?>">
                <td class="quarter">
                    <?= str_replace('Q', ' Q', $pub['q_id']) ?>
                </td>
                <td>
                    <?= $pub['type'] == 'editor' ? 'Member of the Editorial Board of ' : 'Reviewer for ' ?>
                    <?= $pub['journal'] ?>
                </td>
                <td>
                    <?php if ($pub['type'] == 'review') { ?>
                        <span><?= $pub['review_count'] ?></span>
                        <?php if ($selected) { ?>
                            <div class="btn-group ml-10" role="group" aria-label="Basic example">
                                <button class="btn btn-sm text-success" data-toggle="tooltip" data-title="<?= lang('Add one', 'Füge eins hinzu') ?>" onclick="updateReview('<?=$review_id?>', 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <?php if ($pub['review_count'] > 1) { ?>
                                <button class="btn btn-sm text-danger" data-toggle="tooltip" data-title="<?= lang('Remove one', 'Entferne eins') ?>" onclick="updateReview('<?=$review_id?>', -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <?php } ?>
                                
                            </div>
                        <?php } ?>

                    <?php } ?>
                    <?php if (!$selected) { ?>
                        <button class="btn btn-sm text-success ml-10" data-toggle="tooltip" data-title="<?= lang('Copy one to current quarter', 'Kopiere eins ins aktuelle Quartal') ?>"  onclick="todo()">
                            <i class="fa-regular fa-calendar-plus"></i>
                        </button>

                    <?php } ?>

                <?php if ($selected) { ?>
                        <button class="btn btn-sm text-danger ml-20" data-toggle="tooltip" data-title="<?= lang('Remove activity', 'Entferne Aktivität') ?>"  onclick="todo()">
                            <i class="fa-regular fa-trash-alt"></i>
                        </button>

                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
       
    </table>
</div>


<datalist id="journal-list">
    <?php
    $stmt = $db->prepare("SELECT journal FROM `journal` ORDER BY journal ASC");
    $stmt->execute();
    $journals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($journals as $j) { ?>
        <option><?= $j ?></option>
    <?php } ?>
</datalist>