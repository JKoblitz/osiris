<div class="content">

    <h3 class=""><?=lang('Add publication', 'Füge Publikation hinzu')?></h3>
    <form method="get" onsubmit="getPubData(event, this)">
        <div class="form-group">
            <label for="doi"><?= lang('Search publication by DOI', 'Suche Publikation über die DOI') ?>:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">https://doi.org/</span>
                </div>
                <input type="text" class="form-control" placeholder="10.1093/nar/gkab961" name="doi" value="10.1093/nar/gkab961">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </form>

    <hr>

    <!-- <h5><?= lang('Enter manually:', 'Trage manuell ein:') ?></h5> -->
    <style>
        .author-list {
            width: 100%;
            background-color: white;
            border: 1px solid #afafaf;
            padding-left: 0.8rem;
            padding-right: 0.8rem;
            border-radius: 2pt;
            vertical-align: middle;
        }

        .author-list .author,
        .author-list input {
            display: inline-block;
            background-color: var(--body-color);
            padding: .4rem .6rem;
            margin: .4rem .3rem;
            height: 2.8rem;
            border: 1px solid #afafaf;
            border-radius: 2pt;
            cursor: pointer;
        }

        .author.author-dsmz {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .author.author-dsmz::after {
            content: 'DSMZ';
            font-size: 1.2rem;
            font-weight: 600;
            margin-left: .6rem;
        }
    </style>

    <form action="#" id="publication-form" method="post">
    <div class="form-group">
        <label for="title" class="required"><?=lang('Title', 'Titel')?></label>
        <input type="text" class="form-control" name="title" id="title" required>
    </div>

    <div class="form-group">
    <div class="float-right">
        <?=lang('Number of first authors:', 'Anzahl der Erstautoren:')?>
        <input type="number" name="first_authors" value="1" class="form-control form-control-sm w-50 d-inline-block">
    </div>
        <label for="author" class="required"><?=lang('Author(s) (in correct order)', 'Autor(en) (in korrekter Reihenfolge)')?></label>
        <div class="author-list">
            <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author"  list="scientist-list">
        </div>
    </div>

    <div class="form-group">
        <label for="journal">Journal</label>
        <input type="text" class="form-control" name="journal" id="journal" list="journal-list">
    </div>
    <div class="form-group">
        <label class="required" for="date_publication"><?=lang('Date of publication (print preferred)', 'Datum der Publikation (bevorzugt Print)')?></label>
        <input type="date" class="form-control" name="date_publication" id="date_publication" required>
    </div>
    <div class="form-row row-eq-spacing">
        <div class="col-sm">
            <label for="issue">Issue</label>
            <input type="text" class="form-control" name="issue" id="issue">
        </div>
        <div class="col-sm">
            <label for="volume">Volume</label>
            <input type="text" class="form-control" name="volume" id="volume">
        </div>
        <div class="col-sm">
            <label for="pages">Pages</label>
            <input type="text" class="form-control" name="pages" id="pages">
        </div>
    </div>
    <div class="form-group">
        <label for="doi">DOI</label>
        <input type="text" class="form-control" name="doi" id="doi">
    </div>
    <div class="form-group">
        <label for="pubmed">Pubmed</label>
        <input type="number" class="form-control" name="pubmed" id="pubmed">
    </div>
    <div class="form-group">
        <label for="type">Type TODO: enum</label>
        <input type="text" class="form-control" name="type" id="type">
    </div>
    <div class="form-group">
        <label for="book_title">Book title</label>
        <input type="text" class="form-control" name="book_title" id="book_title">
    </div>
    <div class="form-group">
        <div class="custom-checkbox">
            <input type="checkbox" id="open_access" value=""  name="open_access">
            <label for="open_access">Open access</label>
        </div>
    </div>
    <div class="form-group">
        <div class="custom-checkbox">
            <input type="checkbox" id="epub" value=""  name="epub">
            <label for="epub">Epub ahead of print</label>
        </div>
    </div>
    <button class="btn btn-primary" type="submit"><i class="fas fa-book-medical"></i> <?= lang('Add publication', 'Füge Publikation hinzu') ?></button>

    </form>
</div>


<datalist id="journal-list">
    <?php 
    $stmt = $db->prepare("SELECT journal_name FROM `journal` ORDER BY journal_name ASC");
    $stmt->execute();
    $journals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($journals as $j) { ?>
        <option><?=$j?></option>
    <?php } ?>
</datalist>
<datalist id="scientist-list">
    <?php 
    $stmt = $db->prepare("SELECT CONCAT(last_name, ', ', first_name) FROM `scientist` ORDER BY last_name ASC");
    $stmt->execute();
    $scientist = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($scientist as $s) { ?>
        <option><?=$s?></option>
    <?php } ?>
</datalist>