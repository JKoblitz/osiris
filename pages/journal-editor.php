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
        <div class="col-sm">
            <label for="open_access">Open access <?= lang('seit', 'since') ?></label>
            <?php
                $oa = $data['oa'] ?? 'false';
                if ($oa === false) $oa = 'false';
            ?>
            
            <input type="text" name="values[oa]" id="oa" class="form-control" value="<?= $oa ?>">
            <small class="text-muted">
                </code>
                <?= lang(
                    'enter <code class="code">false</code> if not open access, <code class="code">0</code> if always, <code class="code">2010</code> if since 2010 ',
                    'Trage ein: <code class="code">false</code> wenn nicht open access, <code class="code">0</code> wenn immer OA, <code class="code">2010</code> wenn OA seit 2010 '
                ) ?>
            </small>
        </div>

    </div>
    <!-- 
    <div class="my-10">
        <span class="mr-20"><?= lang('Open Access only', 'Immer Open Access') ?>:</span>
        <?php
        $oa = $data['open_access'] ?? false;
        ?>

            <div class="custom-radio d-inline-block" id="open_access-div">
                <input type="radio" id="open_access-0" value="false" name="values[open_access]" <?= $oa ? '' : 'checked' ?>>
                <label for="open_access-0">
                <?= lang('No', 'Nein') ?>
            </label>
            </div>
            <div class="custom-radio d-inline-block ml-20" id="open_access-div">
                <input type="radio" id="open_access" value="true" name="values[open_access]" <?= $oa ? 'checked' : '' ?>>
                <label for="open_access"><i class="icon-open-access text-success"></i> <?= lang('Yes, always', 'Ja, immer') ?> Open access</label>
            </div>
    </div> -->



    <button type="submit" class="btn btn-primary">
        Update
    </button>
</form>