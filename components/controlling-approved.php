
    <div class="content">

        <h3 class="title"><i class="far fa-books mr-5"></i>
            <?= lang('Scientist overview (selected quarter)', 'Übersicht der Forschenden (ausgewähltes Quartal)') ?>
        </h3>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php

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
                $approved = isset($s['approved']) && in_array(SELECTEDYEAR, $s['approved']->bsonSerialize());
            ?>
                <tr class="row-<?=$approved ? 'success':''?>">
                    <td>
                        <a href="<?= ROOTPATH ?>/view/scientist/<?= $s['_id'] ?>">
                            <?= $s['last'] ?>, <?= $s['first'] ?>
                        </a>
                    </td>
                    <td>
                        <?php if ($approved) { ?>
                            <i class="fas fa-lg fa-check text-success"></i>
                        <?php } else { ?>
                            <i class="fas fa-lg fa-xmark text-danger"></i>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>