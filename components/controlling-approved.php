<div class="content">

    <h3 class="title">
        <?= lang('Scientist overview (selected quarter)', 'Übersicht der Forschenden (ausgewähltes Quartal)') ?>
    </h3>

</div>
<table class="table simple small">
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
        $cursor = $osiris->persons->find(
            ['roles' => 'scientist', 'is_active' => true],
            ['sort' => ["approved" => -1, "last" => 1]]
        );
        if (empty($cursor)) {
            echo "<div class='content'>" . lang('No scientists found.', 'Keine Forschenden gefunden.') . "</div>";
        } else foreach ($cursor as $s) {
            $approved = isset($s['approved']) && in_array($yq, DB::doc2Arr($s['approved']));
        ?>
            <tr class="row-<?= $approved ? 'success' : '' ?>">
                <td>
                    <a href="<?= ROOTPATH ?>/my-year/<?= $s['username'] ?>?year=<?= $Y ?>&quarter=<?= $Q ?>">
                        <?= $DB->getNameFromId($s['username']) ?>
                    </a>
                </td>
                <td>
                    <?= bool_icon($approved) ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>