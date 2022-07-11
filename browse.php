<?php
// search database and return results

// $page = ucwords(strtolower($page));


$search = trim(filter_var($_GET['search'] ?? "", FILTER_SANITIZE_STRING));
$order = trim(filter_var($_GET['order'] ?? "", FILTER_SANITIZE_STRING));
$limit =  filter_var($_GET["limit"] ?? 10, FILTER_VALIDATE_INT);
$p =  filter_var($_GET["p"] ?? 1, FILTER_VALIDATE_INT);
$asc =  filter_var($_GET["asc"] ?? 1, FILTER_VALIDATE_INT);

// 'asc'   => [FILTER_VALIDATE_INT, array('options' => array('default' => 1))],


switch ($page) {
    case 'activity':
        $datafields = [
            'user' => "User name",
            'category' => 'Category',
            'title' => 'Title',
            'activity_count' => 'Count'
        ];
        break;
    case 'publication':
        $datafields = [
            "q_id" => "Quarter",
            // "title" => "Title",
            // "journal_id" => "Journal-ID",
            // "year" => "Year",
            // "date_publication" => "Date of Publication",
            // "issue" => "Issue",
            // "pages" => "Pages",
            // "volume" => "Volume",
            // "doi" => "DOI",
            // "type" => "Type",
            // "book_title" => "Book Title",
            // "open_access " => "Open Access",
        ];
        break;
    case 'poster':
        $datafields = [
            "q_id" => "Quarter"
            // 'title' => "Titel",
            // 'conference' => 'Conference',
            // 'location' => 'Location',
            // 'date_start' => 'Date (Start)',
        ];
        break;
    case 'journal':
        $datafields = [
            'journal' => "Journal name",
            'journal_abbr' => 'Abbr.',
            'issn' => 'ISSN'
        ];
        break;
    case 'scientist':
        $datafields = [
            'user' => "User name",
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'dept' => 'Department'
        ];
        break;
    default:
        die();
        break;
}


// switch ($order) {
//     case "name":
//     case "derivative_group":
//     case "retention_index":
//     case "quantification_ion":
//     case "molecular_weight":
//     case "sum_formula":
//     case "class":
//         $orderby = "c." . $order;
//         break;
//     case "derivative_group_name":
//         $orderby = "g." . $order;
//         break;
//     default:
//         $orderby = "c.id";
//         break;
// }
// $orderby .= $asc == 1 ? " ASC" : " DESC";

$sql = "FROM `$page` c ";
// if ($page == "metabolite" || $page == "derivative") {
//     $sql .= "LEFT JOIN Derivative_Group g ON c.Derivative_Group = g.id ";
// }

$where = array();
$values = array();

if (!empty($search)) {
    // if (is_numeric($search)) {
    //     $where[] = "retention_index = ?";
    //     $values[] = floatval($search);
    // } else {
    //     if ($page == "metabolite" || $page == "derivative") {
    //         $where[] = "(name LIKE ? OR derivative_group_name LIKE ?)";
    //         $values[] = "%$search%";
    //         $values[] = "%$search%";
    //     } else {
    //         $where[] = "name LIKE ?";
    //         $values[] = "%$search%";
    //     }

    // }
}

// if (!empty($where)) {
//     $sql .= " WHERE " . implode(' AND ', $where);
// }

// $stmt = $db->prepare("SELECT COUNT(*) " . $sql);
// $stmt->execute($values);
// $count = $stmt->fetch(PDO::FETCH_COLUMN);

// $last = ceil($count / $limit);

// if ($last && $p > $last) {
//     $p = $last;
// } elseif ($p < 1) {
//     $p = 1;
// }

// $offset = $p * $limit - $limit;

// $select = "c.*";
// if ($page == "metabolite" || $page == "derivative") {
//     $select .= ", g.derivative_group_name";
// }

$stmt = $db->prepare("SELECT * FROM $table");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$count = count($result);
$last = ceil($count / $limit);
if ($p > $last) {
    $p = $last;
} elseif ($p < 1) {
    $p = 1;
}
$offset = $p * $limit - $limit;

$result = array_slice($result, $offset, min($limit, $count-$offset));

?>


<div class="content">

    <!-- <a href="<?= ROOTPATH ?>/history/<?= $page ?>" class="btn btn-secondary float-right"><i class="far fa-history mr-5"></i> History</a> -->

    <h2>
        <?= ucfirst($page) ?>
    </h2>

    <p class="text-muted">TODO: add search and filter options</p>

    <!-- <div>
        <form action="" method="get" class="d-inline-block w-500 mw-full">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search" name="search" value="<?= $search ?>">
                <div class="input-group-append">
                    <button class="btn btn-success text-white" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        <a href="<?= ROOTPATH ?>/browse/<?= $page ?>" class="btn" title="remove all filter"><i class="fas fa-filter-slash"></i></a>
    </div> -->


    <div>

        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <?php
                    $n = count($datafields);
                    foreach ($datafields as $key => $value) {
                    ?>
                        <th><?= $value ?>
                            <!-- <?php sortbuttons($key); ?> -->
                        </th>
                    <?php } ?>
                    <th></th>
                </tr>
            </thead>

            <?php foreach ($result as $row) { ?>
                <tr>
                    <td>
                        <a href="<?= ROOTPATH ?>/view/<?= $page ?>/<?= $row[$idname] ?>" class="btn btn-sm"><i class="far fa-search"></i></a>
                        <!-- <a href="<?= ROOTPATH ?>/edit/<?= $page ?>/" class="btn btn-sm"><i class="far fa-edit"></i></a> -->
                    </td>
                    <?php foreach ($datafields as $key => $name) { ?>
                        <td>
                            <?= $row[$key] ?>
                        </td>
                    <?php } ?>
                    <?php if ($page == 'poster') {
                        echo "<td>";
                        $activity->print($row['poster_id']);
                        echo "</td>";
                    } elseif ($page == 'publication') {
                        echo "<td>";
                        $activity->print($row['publication_id']);
                        echo "</td>";
                    } ?>
                </tr>
            <?php } ?>
        </table>
    </div>



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

</div>