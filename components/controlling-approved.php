<div class="content">

    <h3 class="title">
        <?= lang('Scientist overview (selected quarter)', 'Übersicht der Forschenden (ausgewähltes Quartal)') ?>
    </h3>

</div>
<table class="table table-simple table-sm">
    <tbody>
        <?php
        if (isset($_GET['q']) && isset($_GET['y'])) {
            $Y = $_GET['y'];
            $Q =  $_GET['q'];
        } else {
            $Y = CURRENTYEAR;
            $Q = CURRENTQUARTER;
        }
        $yq = $Y . "Q" . $Q;
        $cursor = $osiris->accounts->find(
            ['is_scientist' => true, 'is_active' => true],
            ['sort' => ["approved" => -1, "last" => 1]]
        );
        if (empty($cursor)) {
            echo "<div class='content'>" . lang('No scientists found.', 'Keine Forschenden gefunden.') . "</div>";
        } else foreach ($cursor as $s) {
            // $s = MongoDB\BSON\toJSON($s);
            // $s = $doc->bsonSerialize();
            // var_dump($s);
            $approved = isset($s['approved']) && in_array($yq, $s['approved']->bsonSerialize());
        ?>
            <tr class="row-<?= $approved ? 'success' : '' ?>">
                <td>
                    <a href="<?= ROOTPATH ?>/my-year/<?= $s['_id'] ?>?year=<?= $Y ?>&quarter=<?= $Q ?>">
                        <?= getNameFromId($s['_id']) ?>
                    </a>
                </td>
                <td>
                    <?= bool_icon($approved) ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>