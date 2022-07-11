<?php

$required = ['journal', 'user', 'type'];

foreach ($required as $key) {
    if (!isset($_POST[$key])) {
        echo "Error! $key is required.";
        die();
    }
}

$journal = $_POST['journal'];
$journal_id = addJournal($journal);
$user = $_POST['user'];
$type = $_POST['activity'];
$review_count = $_POST['review_count'] ?? 1;
$quartal = $_POST['quarter'] ?? CURRENTQUARTER;

$q = explode('Q', $quartal, 2);
if (count($q) !== 2 || !is_numeric($q[0]) || !is_numeric($q[1])) {
    echo "Error! Quarter is not formatted correctly. Please choose a quarter from the title bar.";
}
$year = $q[0];
$quarter = $q[1];

$stmt = $db->prepare(
    "INSERT INTO `review` 
    (`type`, `user`, `review_count`, `journal_id`, `q_id`) 
    VALUES (?, ?, ?, ?, ?)"
);
$stmt->execute([
    $type, $user, $review_count, $journal_id, $quartal
]);
$review_id = $db->lastInsertId();

?>

<tr>
    <td class="quarter">
        <?= $year ?> Q<?= $quarter ?>
    </td>
    <td>
        <?= $type == 'editor' ? 'Member of the Editorial Board of ' : 'Reviewer for ' ?>
        <?= $journal ?>
    </td>
    <td>
        <?php if ($type == 'review') { ?>
            <?= $review_count ?>
            <div class="btn-group ml-10" role="group" aria-label="Basic example">
                <button class="btn btn-sm text-success" data-toggle="tooltip" data-title="<?= lang('Add one', 'Füge eins hinzu') ?>">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="btn btn-sm text-danger" data-toggle="tooltip" data-title="<?= lang('Remove one', 'Entferne eins') ?>">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        <?php } ?>

    </td>
</tr>