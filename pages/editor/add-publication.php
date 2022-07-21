<div class="content">

    <h3 class=""><?= lang('Add publication', 'Füge Publikation hinzu') ?></h3>
    <form method="get" onsubmit="getPubData(event, this)">
        <div class="form-group">
            <label for="doi"><?= lang('Search publication by DOI or Pubmed-ID', 'Suche Publikation über die DOI oder Pubmed-ID') ?>:</label>
            <div class="input-group">
                <!-- <div class="input-group-prepend">
                    <span class="input-group-text">https://doi.org/</span>
                </div> -->
                <input type="text" class="form-control" placeholder="10.1093/nar/gkab961" name="doi" value="10.1093/nar/gkab961">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </form>


    <div class="my-20" id="select-btns">
        <button onclick="togglePubType('article')" class="btn" id="article-btn"><i class="fa-regular fa-memo"></i> <?= lang('Journal article (refereed)') ?></button>
        <button onclick="togglePubType('magazine')" class="btn" id="magazine-btn"><i class="fa-regular fa-newspaper"></i> <?= lang('Magazine article (non-refereed)') ?></button>
        <button onclick="togglePubType('book')" class="btn" id="book-btn"><i class="fa-regular fa-book"></i> <?= lang('Book', 'Buch') ?></button>
        <button onclick="togglePubType('editor')" class="btn" id="editor-btn"><i class="fa-regular fa-pen-nib"></i> <?= lang('Book editor', 'Bucheditor') ?></button>
        <button onclick="togglePubType('chapter')" class="btn" id="chapter-btn"><i class="fa-regular fa-book-bookmark"></i> <?= lang('Book chapter', 'Buchkapitel') ?></button>
    </div>


    <div class="box box-primary" style="display:none" id="publication-form">
        <div class="content">
            <form action="#" method="post">
                <div class="form-group">
                    <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>

                <div class="form-group" data-visible="article,magazine,book,chapter">
                    <div class="float-right">
                        <?= lang('Number of first authors:', 'Anzahl der Erstautoren:') ?>
                        <input type="number" name="first_authors" id="first-authors" value="1" class="form-control form-control-sm w-50 d-inline-block">
                    </div>
                    <label for="author" class="required"><?= lang('Author(s) (in correct order)', 'Autor(en) (in korrekter Reihenfolge)') ?></label>
                    <div class="author-list">
                        <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                    </div>
                </div>

                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label for="year" class="required">Year</label>
                        <input type="number" min="1901" max="2155" step="1" class="form-control" name="year" id="year" required>
                    </div>
                    <div class="col-sm">
                        <label for="month" class="required">Month</label>
                        <input type="number" min="1" max="12" step="1" class="form-control" name="month" id="month" required>
                    </div>
                    <div class="col-sm">
                        <label for="day">Day</label>
                        <input type="number" min="1" max="31" step="1" class="form-control" name="day" id="day">
                    </div>
                </div>
<!-- 
                <div class="form-group">
                    <label class="required" for="date_publication"><?= lang('Date of publication (print preferred)', 'Datum der Publikation (bevorzugt Print)') ?></label>
                    <input type="date" class="form-control" name="date_publication" id="date_publication" required>
                </div> -->

                <div class="form-group">
                    <label for="type">Type:</label>
                    <input type="text" class="form-control disabled" name="type" id="type">
                </div>

                <div class="form-group" data-visible="article,magazine">
                    <label for="journal">Journal</label>
                    <input type="text" class="form-control" name="journal" id="journal" list="journal-list">
                </div>
                <div class="form-row row-eq-spacing" data-visible="article,book,editor,chapter">
                    <div class="col-sm" data-visible="article">
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

                <div class="form-row row-eq-spacing" data-visible="article,book,editor,chapter">
                    <div class="col-sm">
                        <label for="doi">DOI</label>
                        <input type="text" class="form-control" name="doi" id="doi">
                    </div>
                    <div class="col-sm">
                        <label for="pubmed">Pubmed</label>
                        <input type="number" class="form-control" name="pubmed" id="pubmed">
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="book,editor,chapter">
                    <div class="col-sm"  data-visible="chapter">
                        <label for="book">Book title</label>
                        <input type="text" class="form-control" name="book" id="book">
                    </div>
                    <div class="col-sm">
                        <label for="edition">Edition</label>
                        <input type="text" class="form-control" name="edition" id="edition">
                    </div>
                    <div class="col-sm">
                        <label for="publisher">Publisher</label>
                        <input type="number" class="form-control" name="publisher" id="publisher">
                    </div>
                    <div class="col-sm">
                        <label for="city">City</label>
                        <input type="number" class="form-control" name="city" id="city">
                    </div>
                </div>
                <div class="form-group" data-visible="editor,chapter">
                    <label for="editor" class="required"><?= lang('Editor(s) (in correct order)', 'Editor(en) (in korrekter Reihenfolge)') ?></label>
                    <div class="author-list">
                        <input type="text" placeholder="Add editor ..." onkeypress="addAuthor(event, this);" id="add-editor" list="scientist-list">
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="open_access" value="" name="open_access">
                        <label for="open_access">Open access</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="epub" value="" name="epub">
                        <label for="epub">Epub ahead of print</label>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit"><i class="fas fa-book-medical"></i> <?= lang('Add publication', 'Füge Publikation hinzu') ?></button>

            </form>
        </div>
    </div>

</div>


<datalist id="journal-list">
    <?php
    $stmt = $db->prepare("SELECT journal FROM `journal` ORDER BY journal ASC");
    $stmt->execute();
    $journals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($journals as $j) { ?>
        <option><?= $j ?></option>
    <?php } ?>
</datalist>
<datalist id="scientist-list">
    <?php
    $stmt = $db->prepare("SELECT CONCAT(last_name, ', ', first_name) FROM `users` ORDER BY last_name ASC");
    $stmt->execute();
    $scientist = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($scientist as $s) { ?>
        <option><?= $s ?></option>
    <?php } ?>
</datalist>