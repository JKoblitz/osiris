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
            "quartal" => "Quartal",
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
            "quartal" => "Quartal"
            // 'title' => "Titel",
            // 'conference' => 'Conference',
            // 'location' => 'Location',
            // 'date_start' => 'Date (Start)',
        ];
        break;
    case 'journal':
        $datafields = [
            'journal_name' => "Journal name",
            'journal_abbr' => 'Abbr.',
            'impact_factor' => 'Impact factor'
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

// $sql = "FROM `$page` c ";
// if ($page == "metabolite" || $page == "derivative") {
//     $sql .= "LEFT JOIN Derivative_Group g ON c.Derivative_Group = g.id ";
// }

// $where = array();
// $values = array();

// if (!empty($search)) {
//     // if (is_numeric($search)) {
//     //     $where[] = "retention_index = ?";
//     //     $values[] = floatval($search);
//     // } else {
//     //     if ($page == "metabolite" || $page == "derivative") {
//     //         $where[] = "(name LIKE ? OR derivative_group_name LIKE ?)";
//     //         $values[] = "%$search%";
//     //         $values[] = "%$search%";
//     //     } else {
//     //         $where[] = "name LIKE ?";
//     //         $values[] = "%$search%";
//     //     }

//     // }
// }

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

$stmt = $db->prepare("SELECT * FROM `$page`");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = count($result);
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
                    <?php if ($page=='poster') {
                        echo "<td>";
                        print_poster($row['poster_id']);
                        echo "</td>";
                     } elseif ($page=='publication') { 
                        echo "<td>";
                        print_publication($row['publication_id']);
                        echo "</td>";
                      } ?>
                </tr>
            <?php } ?>
        </table>
    </div>



    <div class="text-right">
        <?php
        if ($count <= 0) {
            echo "No results";
        } else {
            echo "$count result(s)";
        }
        ?>
    </div>

</div>