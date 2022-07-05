<div class="content">
    <h2>
        <i class="fad fa-history text-success"></i>
        History of <?= ucfirst($dbname) ?>
    </h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Comment</th>
                <th>Affected columns</th>
            </tr>
        </thead>

        <?php foreach ($history as $row) { ?>
            <tr>
                <td><a href="<?= ROOTPATH ?>/browse/<?= $dbname ?>/<?= $row['entry_id'] ?>"><?= $row['name'] ?></a></td>
                <td><?= format_date($row['time']) ?></td>
                <td><?= $row['note'] ?></td>
                <td>
                    <?php foreach (explode(",", $row['columns']) as $col) { ?>
                        <span class="badge badge-pill"><?= $col ?></span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>

    </table>

</div>