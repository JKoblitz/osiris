<div class="row row-eq-spacing-lg">
    <div class="col-lg-9">
        <div class="content">

            <a href="<?= ROOTPATH ?>/edit/<?= $page ?>/<?= $id ?>" class="btn btn-primary float-right"><i class="far fa-edit mr-5"></i> Edit entry</a>

            <h4><?= ucwords($page) ?>: #<?= $dataset[$idname] ?></h4>
        </div>
        <table class="table table-sm " id="">
            <tbody>
                <?php foreach ($dataset as $key => $value) {
                    $schema = $schemata[$key][0];
                ?>
                    <tr id="row-<?= $key ?>">
                        <td class="w-200">
                            <?= $key ?>
                        </td>
                        <td class="">
                            <?php
                            switch ($schema["DATA_TYPE"]) {
                                case 'boolean':
                                case 'tinyint':
                                    echo ($value == 0) ? 'no' : 'yes';
                                    break;
                                case 'date':
                                    $date = new DateTime($value);
                                    echo $date->format('d.m.Y');
                                    break;

                                default:
                                    echo $value;
                                    break;
                            }
                            if ($key == 'derivative_group' && isset($group) && !empty($group)) {
                                echo " <span class='badge badge-pill text-danger ml-10'>$group</span>";
                            } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="col-lg-3">
    <?php if ($page == 'publication' || $page == 'poster') {
            $stmt = $db->prepare("SELECT * FROM authors WHERE ${page}_id = ?");
            $stmt->execute([$id]);
            $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <div class="content">
                <h4><?= lang('Authors', 'Autoren') ?></h4>
            </div>

            <table class="table table-sm">
                <?php foreach ($authors as $author) { ?>
                    <tr>
                        <td><?= $author['last_name'] ?></td>
                        <td><?= $author['first_name'] ?></td>
                        <td><?= $author['position'] ?></td>
                        <td><?= $author['dsmz_affiliation'] == 1 ? 'DSMZ' : '' ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

</div>