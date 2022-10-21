<?php
$limit =  filter_var($_GET["limit"] ?? 10, FILTER_VALIDATE_INT);
$p =  filter_var($_GET["p"] ?? 1, FILTER_VALIDATE_INT);
$raw =  $_GET['filter'] ?? array();
$filter = array();

if (isset($raw['search']) && !empty($raw['search'])) {
    $filter['$or'] = array(
        ['last' => new MongoDB\BSON\Regex($raw['search'], 'i')],
        ['first' => new MongoDB\BSON\Regex($raw['search'], 'i')],
        ['username' => $raw['search']],
    );
}

if (isset($raw['scientist']) && !empty($raw['scientist'])) {
    $filter['is_scientist'] = true;
}

if (isset($raw['dept']) && !empty($raw['dept'])) {
    $filter['dept'] = $raw['dept'];
}

?>


<form action="" method="get" class="  mb-5">
    <?php
    hiddenFieldsFromGet(['filter']);
    ?>
    <div class="input-group d-inline-flex w-600 mw-full" id="search">
        <input type="search" class="form-control" placeholder="<?= lang('Search by name or user', 'Suche nach Namen oder Nutzerküzel') ?>" name="filter[search]" value="<?= $raw['search'] ?? '' ?>">
        <select name="filter[dept]" id="dept-search" class="form-control">
            <option value=""><?=lang('All departments', 'Alle Abteilungen')?></option>
            <?php
            $depts = [
                "BIDB", "IT", "Services", "MIG", "Verwaltung", "MIOS", "BUG", "MuTZ", "Patente", "PFVI", "MÖD", "Presse und Kommunikation"
            ];
            sort($depts);
            $dept_filter = $raw['dept'] ?? '';
            foreach ($depts as $dept) {
                echo "<option ".($dept_filter == $dept ? 'selected': '').">$dept</option>";
            }
            ?>
        </select>
        <div class="input-group-append">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </div>


    <div class="custom-checkbox d-inline-block">
        <input type="checkbox" id="scientist-1" value="1" name="filter[scientist]" <?= isset($raw['scientist']) ? 'checked' : '' ?>>
        <label for="scientist-1"><?= lang('Only scientists', 'Nur Wissenschaftler') ?></label>
    </div>
</form>


<table class="table" id="result-table">
    <thead>
        <th>user</th>
        <th><?= lang('Last name', 'Nachname') ?></th>
        <th><?= lang('First name', 'Vorname') ?></th>
        <th><?= lang('Dept', 'Abteilung') ?></th>
        <th><?= lang('Details') ?></th>
        <th><?= lang('Unit', 'Einheit') ?></th>
        <th><?= lang('Scientist', 'Wissenschaftler:in') ?></th>
        <th></th>
    </thead>
    <tbody>

        <?php
        $result = $osiris->users->find($filter)->toArray();

        $count = count($result);
        $last = ceil($count / $limit);
        if ($p > $last) {
            $p = $last;
        } elseif ($p < 1) {
            $p = 1;
        }
        $offset = $p * $limit - $limit;


        // $depts = array_column($result, 'department');
        // $depts = array_unique($depts);
        // dump($depts, true);
        // $a = array();
        // foreach ($result as $doc) {
        //     if (!preg_match('/^[a-z]{3}[0-9]{0,2}$/', $doc['_id'])){
        //         $a[] = $doc['_id'];
        //     }
        // }
        // dump($a, true);

        $result = array_slice($result, $offset, min($limit, $count - $offset));

        foreach ($result as $document) {
        ?>
            <tr>
                <td><a href="<?= ROOTPATH ?>/scientist/<?= $document['_id'] ?>"><?= $document['_id'] ?></a></td>
                <td><?= $document['academic_title'] ?? '' ?> <?= $document['last'] ?></td>
                <td><?= $document['first'] ?></td>
                <td>
                    <?php if ($document['is_leader']) { ?>
                        <strong><?= $document['dept'] ?></strong>
                    <?php } else { ?>
                        <?= $document['dept'] ?>
                    <?php } ?>

                </td>
                <td><?= $document['department'] ?></td>
                <td><?= $document['unit'] ?></td>
                <td><?= bool_icon($document['is_scientist']) ?></td>
                <!-- <td><?= bool_icon($document['is_active']) ?></td> -->
                <td>
                    <a href="<?=ROOTPATH?>/edit/user/<?=$document['_id']?>" class="btn btn-link">
                <i class="fas fa-edit"></i>
                </a>
                </td>
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