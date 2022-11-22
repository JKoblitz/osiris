<?php

$Format = new Format(true);
?>

<div class="content">

    <h2><?= lang('Formatted entry', 'Formatierter Eintrag') ?></h2>

    <p>
        <?= $Format->format($activity) ?>
    </p>

    <div class="alert alert-signal mt-20">
        <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn">
            <i class="fas fa-edit"></i>
            <?= lang('Edit activity', 'Aktivität bearbeiten') ?>
        </a>
        <?php if (isset($activity['authors'])) {
            echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/authors" class="btn">
        <i class="fas fa-users"></i>
        ' . lang("Edit authors", "Autorenliste bearbeiten") .
                '</a>';
        } ?>
        <?php if (isset($activity['editors'])) {
            echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/editors" class="btn">
        <i class="fas fa-users"></i>
        ' . lang("Edit editors", "Editorenliste bearbeiten") .
                '</a>';
        } ?>


        <?php if (in_array($activity['type'], ['poster', 'lecture', 'review', 'misc', 'students'])) {
            echo '<a href="' . ROOTPATH . '/activities/copy/' . $id . '" class="btn">
        <i class="fas fa-copy"></i>
        ' . lang("Add a copy", "Eine Kopie anlegen") .
                '</a>';
        }

        ?>

        <?php if (in_array($activity['type'], ['publication', 'poster', 'lecture', 'misc'])) { ?>
            <a href="<?= ROOTPATH ?>/activities/files/<?= $id ?>" class="btn">
                <i class="fas fa-upload"></i>
                <?= lang('Edit files', 'Dateien hochladen') ?>
            </a>
        <?php } ?>
    </div>
</div>

<div class="row row-eq-spacing-md">
    <div class="col-md-8">

        <h2>Details</h2>


        <table class="table">

            <tbody>
                <?php
                $activity = json_decode(json_encode($activity->getArrayCopy()), true);

                foreach ($activity as $key => $value) {
                    echo "<tr>";

                    if ($key == '_id') {
                        echo "<td>$key</td>";
                        echo "<td>$id</td>";
                    } else if ($key == 'authors') {
                        continue;
                    } else if ($key == 'files') {
                        continue;
                    } else if ($key == 'file') {
                        echo "<td>$key</td>";
                        echo '<td><a href="' . ROOTPATH . '/activities/view/' . $id . '/file" class="btn">' . lang("Download file", "Datei herunterladen") . '</a></td>';
                    } else if (is_array($value)) {
                        if (isset($value[0])) {
                            echo "<td>$key</td>
                <td>
                <table class='table table-simple'>";
                            echo "<tr>";
                            foreach ($value[0] ?? [] as $head => $tr) {
                                echo "<th>$head</th>";
                            }
                            echo "</tr>";
                            foreach ($value as $k => $tr) {
                                echo "<tr>";
                                foreach ($tr as $td) {
                                    echo "<td>$td</td>";
                                }
                                echo "</tr>";
                            }
                            echo "</table></td>";
                        } else {
                            foreach ($value as $k => $v) {
                                echo "<tr>";
                                echo "<td>$key > $k</td>";
                                if (is_bool($v)) {
                                    echo "<td>" . bool_icon($v) . "</td>";
                                } else {
                                    echo "<td>$v</td>";
                                }
                                echo "</tr>";
                            }
                        }
                    } else if (is_bool($value)) {
                        echo "<td>$key</td>";
                        echo "<td>" . bool_icon($value) . "</td>";
                    } else {
                        echo "<td>$key</td>";
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
                if (isset($activity['files'])) {
                    foreach ($activity['files'] as $i => $file) {
                        echo '<tr>';
                        echo "<td>File " . ($i + 1) . "</td>";
                        echo '<td><a href="' . $file['filepath'] . '" class="">' . $file['filename'] . '</a> 
                        <a href="' . ROOTPATH . '/activities/files/' . $id . '" class="btn btn-sm"><i class="fas fa-edit"></i></a>
                        </td>';
                        echo '</tr>';
                    }
                }

                ?>

            </tbody>
        </table>

    </div>
    <div class="col-md-4">
        <?php foreach (['authors', 'editors'] as $role) { ?>
            <?php if (isset($activity[$role])) { ?>
                <h2><?= ucfirst($role) ?></h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Last name</th>
                            <th>First name</th>
                            <th>Position</th>
                            <th>Username</th>
                        </tr>
                    </thead>
                    <tbody id="<?= $role ?>">
                        <?php foreach ($activity[$role] as $i => $author) { ?>
                            <tr class="<?= (($author['aoi'] ?? 0) == '1' ? 'row-primary' : '') ?>">
                                <td>
                                    <?= $author['last'] ?? '' ?>
                                </td>
                                <td>
                                    <?= $author['first'] ?? '' ?>
                                </td>
                                <td>
                                    <?= $author['position'] ?? '' ?>
                                </td>
                                <td>
                                    <?php if (isset($author['user']) && !empty($author['user'])) { ?>
                                        <a href="<?= ROOTPATH ?>/profile/<?= $author['user'] ?>"><?= $author['user'] ?></a>
                                        <span data-toggle="tooltip" data-title="<?= lang('Author approved activity?', 'Autor hat die Aktivität bestätigt?') ?>">
                                            <?= bool_icon($author['approved'] ?? 0) ?>
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        <?php } ?>

    </div>
</div>


<div class="alert alert-danger mt-20">
    <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post">
        <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
        <button type="submit" class="btn btn-danger"><?= lang('Delete activity', 'Lösche Aktivität') ?></button>
    </form>
</div>