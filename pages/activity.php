
<h2><?=lang('Formatted entry', 'Formatierter Eintrag')?></h2>

<p>
    <?=format($activity['type'], $activity)?>
</p>

<div class="alert alert-signal mt-20">
    <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn btn-signal">
    <i class="fas fa-edit"></i>
    <?= lang('Edit activity', 'Aktivität bearbeiten') ?>
</a>
    <?php if (isset($activity['authors'])) { 
        echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/authors" class="btn btn-signal">
        <i class="fas fa-users"></i>
        '.lang("Edit authors", "Autorenliste bearbeiten").
        '</a>';} ?>
    <?php if (isset($activity['editors'])) { 
        echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/editors" class="btn btn-signal">
        <i class="fas fa-users"></i>
        '.lang("Edit editors", "Editorenliste bearbeiten").
        '</a>';} ?>
    
</div>

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
            } else if ($key == 'file') {
                echo "<td>$key</td>";
                echo '<td><a href="' . ROOTPATH . '/activities/view/' . $id . '/file" class="btn">'.lang("Download file", "Datei herunterladen").'</a></td>';
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
                            echo "<td>".bool_icon($v)."</td>";
                        } else {
                            echo "<td>$v</td>";
                        }
                        echo "</tr>";
                    }
                }
            } else if (is_bool($value)) {
                echo "<td>$key</td>";
                echo "<td>".bool_icon($value)."</td>";
            } else {
                echo "<td>$key</td>";
                echo "<td>$value</td>";
            }
            echo "</tr>";
        } ?>

    </tbody>
</table>


<div class="alert alert-danger mt-20">
    <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post">
        <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
        <button type="submit" class="btn btn-danger"><?= lang('Delete activity', 'Lösche Aktivität') ?></button>
    </form>
</div>