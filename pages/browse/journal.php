<?php
$limit =  filter_var($_GET["limit"] ?? 10, FILTER_VALIDATE_INT);
$p =  filter_var($_GET["p"] ?? 1, FILTER_VALIDATE_INT);
$raw =  $_GET['filter'] ?? array();
$filter = array();

if (isset($raw['search']) && !empty($raw['search'])) {
    $filter['$or'] = array(
        ['journal' => new MongoDB\BSON\Regex($raw['search'], 'i')],
        ['issn' => $raw['search']],
    );
}

?>


<form action="" method="get" class=" w-600 mw-full d-inline-block mb-5">
    <?php
    hiddenFieldsFromGet(['filter']);
    ?>
    <div class="input-group" id="search">
        <input type="search" class="form-control" placeholder="<?= lang('Search by journal name', 'Suche nach Journal') ?>" name="filter[search]" value="<?= $raw['search'] ?? '' ?>">
        <div class="input-group-append">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </div>

</form>


<table class="table">
    <thead>
        <th>ID</th>
        <th>Journal name</th>
        <th>Abbr</th>
        <th>ISSN</th>
    </thead>
    <tbody>

        <?php
        $result = $osiris->journals->find($filter)->toArray();

        $count = count($result);
        $last = ceil($count / $limit);
        if ($p > $last) {
            $p = $last;
        } elseif ($p < 1) {
            $p = 1;
        }
        $offset = $p * $limit - $limit;

        $result = array_slice($result, $offset, min($limit, $count - $offset));

        foreach ($result as $document) {
        ?>
            <tr>
                <td><a href="<?= ROOTPATH ?>/view/journal/<?= $document['_id'] ?>"><?= $document['_id'] ?></a></td>
                <td><?= $document['journal'] ?></td>
                <td><?= $document['journal_abbr'] ?></td>
                <td><?= implode(', ', $document['issn']->bsonSerialize()) ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>



<div class="text-right">
    <?php
    if ($count <= 0) {
        echo lang("No results", "Keine Ergebnisse");
    } elseif (isset($offset)) {
        echo "Show " . ($offset + 1) . " to " . min($offset + $limit, $count) . " (" . $count . " total)";
    }
    ?>
</div>

<div class="table-footer justify-content-between">
    <nav class="d-inline-block">
        <form action="" method="get" class="">
            <ul class="pagination">

                <?php
                hiddenFieldsFromGet(['p']);
                ?>
                <button type="submit" name="p" value="1" class="direction <?= ($p <= 1 ? "disabled" : "") ?>" tabindex="-1" title="first">&lt;&lt;</button>
                <button type="submit" name="p" value="<?= ($p - 1) ?>" class="direction <?= ($p <= 1 ? "disabled" : "") ?>" tabindex="-1" title="previous">&lt;</button>
                <?php
                if ($p - 1 > 1 && $p == $last) {
                    echo '<button type="submit" name="p" value="' . ($p - 2) . '" class="">' . ($p - 2) . '</button>';
                }
                if ($p > 1) {
                    echo '<button type="submit" name="p" value="' . ($p - 1) . '" class="">' . ($p - 1) . '</button>';
                }
                echo '<button type="submit" name="p" value="' . ($p) . '" class="active">' . ($p) . '</button>';
                if ($p < $last) {
                    echo '<button type="submit" name="p" value="' . ($p + 1) . '" class="">' . ($p + 1) . '</button>';
                }
                if ($p + 1 < $last && $p == 1) {
                    echo '<button type="submit" name="p" value="' . ($p + 2) . '" class="">' . ($p + 2) . '</button>';
                }
                ?>
                <button type="submit" name="p" value="<?= ($p + 1) ?>" class="direction <?= ($p >= $last ? "disabled" : "") ?>" title="next">&gt;</button>
                <button type="submit" name="p" value="<?= ($last) ?>" class="direction <?= ($p >= $last ? "disabled" : "") ?>" title="last">&gt;&gt;</button>
            </ul>
        </form>
    </nav>

    <form action="" method="get" class="d-inline-block float-md-right">
        <?php
        hiddenFieldsFromGet(['limit']);
        ?>

        <div class="input-group">
            <div class="input-group-prepend">
                <small class="input-group-text"><?= lang('Results per page', 'Ergebnisse pro Seite') ?></small>
            </div>
            <select name="limit" class="form-control">
                <option value="10" <?= ($limit == "10" ? 'selected' : '') ?>>10</option>
                <option value="20" <?= ($limit == "20" ? 'selected' : '') ?>>20</option>
                <option value="50" <?= ($limit == "50" ? 'selected' : '') ?>>50</option>
                <option value="100" <?= ($limit == "100" ? 'selected' : '') ?>>100</option>
            </select>
            <div class="input-group-append">
                <button class="btn" type="submit"><i class="fas fa-check"></i></button>
            </div>
        </div>
    </form>
</div>