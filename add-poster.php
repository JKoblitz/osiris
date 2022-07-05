<div class="content">

    <h3 class=""><?=lang('Add poster', 'Füge Poster hinzu')?></h3>

    <form action="#" id="poster-form" method="post">
    <div class="form-group">
        <label for="title" class="required"><?=lang('Title', 'Titel')?></label>
        <input type="text" class="form-control" name="title" id="title" required>
    </div>

    <div class="form-group">
    <div class="float-right">
        <?=lang('Presenting author:', 'Präsentierender Autor:')?> #
        <input type="number" name="first_authors" value="1" class="form-control form-control-sm w-50 d-inline-block">
    </div>
        <label for="author" class="required"><?=lang('Author(s)', 'Autor(en)')?></label>
        <div class="author-list">
            <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
        </div>
    </div>
    <div class="form-row row-eq-spacing">
        <div class="col-sm">
        <label for="conference"><?=lang('Conference', 'Konferenz')?></label>
        <input type="text" class="form-control" name="conference" id="conference" placeholder="VAAM 2022">
        </div>
        <div class="col-sm">
            <label for="location"><?=lang('Location', 'Ort')?></label>
            <input type="text" class="form-control" name="location" id="location" placeholder="online">
        </div>
    </div>


    <div class="form-row row-eq-spacing">
        <div class="col-sm">
            <label class="required" for="date_start"><?=lang('Start', 'Anfang')?></label>
            <input type="date" class="form-control" name="date_start" id="date_start" required>
        </div>
        <div class="col-sm">
            <label for="date_end"><?=lang('End (leave empty if event was only one day)', 'Ende (leer lassen falls nur ein Tag)')?></label>
            <input type="date" class="form-control" name="date_end" id="date_end">
        </div>
    </div>

    <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> <?= lang('Add poster', 'Füge Poster hinzu') ?></button>

    </form>
</div>


<datalist id="scientist-list">
    <?php 
    $stmt = $db->prepare("SELECT CONCAT(last_name, ', ', first_name) FROM `scientist` ORDER BY last_name ASC");
    $stmt->execute();
    $scientist = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($scientist as $s) { ?>
        <option><?=$s?></option>
    <?php } ?>
</datalist>