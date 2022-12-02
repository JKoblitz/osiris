<h1>
    <?= $data['journal'] ?>
</h1>

<!-- 
<?php
dump($data, true);
?> -->


<form action="<?= ROOTPATH ?>/update-journal/<?= $id ?>" method="post">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

    <div class="form-row row-eq-spacing-sm">
        <div class="col-sm">
            <label for="journal"><?= lang('Journal name', 'Name des Journals') ?></label>
            <input type="text" name="values[journal]" id="journal" class="form-control" value="<?= $data['journal'] ?? '' ?>">
        </div>
        <div class="col-sm">
            <label for="abbr"><?= lang('Abbr. name', 'Abkürzung') ?></label>
            <input type="text" name="values[abbr]" id="abbr" class="form-control" value="<?= $data['abbr'] ?? '' ?>">
        </div>
    </div>

    <div class="form-row row-eq-spacing-sm">
        <div class="col-sm">
            <label for="issn"><?= lang('ISSN') ?></label>
            <?php
            $issn = "";
            if (isset($data['issn'])) {
                $issn = $data['issn'];
                try {
                    $issn = $issn->bsonSerialize();
                } catch (\Throwable $th) {
                }
                if (is_array($issn)) $issn = implode(' ', $issn);
            }
            ?>
            <input type="text" name="values[issn]" id="issn" class="form-control" value="<?= $issn ?>">
        </div>
        <div class="col-sm">
            <label for="publisher">Publisher</label>
            <input type="text" name="values[publisher]" id="publisher" class="form-control" value="<?= $data['publisher'] ?? '' ?>">
        </div>

    </div>

    <button type="submit" class="btn btn-primary">
        Update
    </button>
</form>