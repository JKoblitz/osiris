
    <div class="content">

        <h3 class="title"><i class="far fa-books mr-5"></i>
            <?= lang('Scientist overview (selected quarter)', 'Übersicht der Forschenden (ausgewähltes Quartal)') ?>
        </h3>

    </div>
    <table class="table table-simple table-sm">
        <tbody>
            <?php
            if (isset($_GET['q']) && isset($_GET['y'])){
                $q = $_GET['y']. "Q" . $_GET['q'];
            } else {
                $q = CURRENTYEAR. "Q" . CURRENTQUARTER;
            }

            $cursor = $osiris->users->find(
                ['is_scientist' => true],
                ['sort' => ["last" => 1]]
            );
            if (empty($cursor)) {
                echo "<div class='content'>" . lang('No scientists found.', 'Keine Forschenden gefunden.') . "</div>";
            } else foreach ($cursor as $s) {
                // $s = MongoDB\BSON\toJSON($s);
                // $s = $doc->bsonSerialize();
                // var_dump($s);
                $approved = isset($s['approved']) && in_array($q, $s['approved']->bsonSerialize());
            ?>
                <tr class="row-<?=$approved ? 'success':''?>">
                    <td>
                        <a href="<?= ROOTPATH ?>/scientist/<?= $s['_id'] ?>">
                            <?= $s['last'] ?>, <?= $s['first'] ?>
                        </a>
                    </td>
                    <td>
                        <?=bool_icon($approved)?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>