<h1>
    Journals
</h1>

<a href="<?= ROOTPATH ?>/journal/browse" class="btn btn-osiris"><?= lang('Browse Journals', 'Durchsuche Journale') ?></a>


<?php

define('IMPACT_YEAR', 2021);

$journals = [];
$warnings = [];

$filter = ['journal' => ['$exists' => true]];
$options = ['sort' => ["year" => -1]];
$cursor = $osiris->activities->find($filter, $options);

foreach ($cursor as $doc) {
    $name = $doc['journal'];
    $if = null;

    if (!array_key_exists($name, $journals)) {
        $j = getJournal($doc);


        if (!empty($j)) {
            $name = $j['journal'];

            if (!isset($doc['journal_id']) || $doc['journal_id'] !== strval($j['_id'])) {
                $updated = $osiris->activities->updateOne(
                    ['_id' => $doc['_id']],
                    ['$set' => ['journal_id' => strval($j['_id']), 'journal' => $name]]
                );
            }
        } else {
            $warnings[] = $doc['journal'] . " does not exist.";
        }
        $journals[$name] = [
            'count' => 1,
            'id' => strval($j['_id'] ?? ''),
            'impact' => impact_from_year($j ?? [], IMPACT_YEAR)
        ];
    }
    $journals[$name]['count']++;
}

arsort($journals);
?>


<table class="table">
    <tbody>
        <?php foreach ($journals as $j => $data) { ?>
            <tr>
                <td>
                    <?php if (!empty($data['id'])) { ?>
                        <a href="<?= ROOTPATH ?>/journal/view/<?= $data['id'] ?>"><?= $j ?></a>
                    <?php } else { ?>
                        <?= $j ?> <br>
                        <span class="text-danger"><?= lang('Does not exist in DB!', 'Existiert nicht in DB!') ?></span>
                    <?php } ?>
                </td>
                <td>
                    <a href="<?= ROOTPATH ?>/activities?q=<?= $j ?>#type=publication">
                        <?= $data['count'] ?>
                    </a>
                </td>
                <td>
                    <?= $data['impact'] ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>

</table>