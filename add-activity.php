<div class="content">

    <h3 class=""><?= lang('Add activity', 'Füge Aktivität hinzu') ?></h3>

    <p class="text-danger lead">Achtung: Diese Seite ist noch in Bearbeitung.</p>

    <form action="#" id="activity-form" method="post">
        <div class="form-group">
            <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>
            <input type="text" class="form-control" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="author" class="required"><?= lang('Author(s)', 'Autor(en)') ?></label>
            <div class="author-list">
                <div class="author author-dsmz">Koblitz, Julia<input type="hidden" name="author[]" value="Koblitz;Julia;1"><a onclick="removeAuthor(event, this)">&times;</a></div>
                <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
            </div>
        </div>

        <div class="form-row row-eq-spacing">
            <div class="col-sm">
                <div class="form-group">
                    <label for="activity" class="required"><?= lang('Type of activity', 'Art der Aktivität') ?></label>
                    <!-- <select name="activity" id="activity" class="form-control"> -->
                    <div class="nav-pills">
                        <div class="">
                            <label class="btn btn-primary" for="review">
                                <input type="radio" name="activity" value="review" checked>Reviewer
                            </label>
                        </div>
                        <div class="">
                            <label class="btn" for="misc">
                                <input type="radio" name="activity" value="misc">Miscellaneous
                            </label>
                        </div>
                        <div class="">
                            <label class="btn" for="lecture">
                                <input type="radio" name="activity" value="lecture">Lecture
                            </label>
                        </div>
                        <div class="">
                            <label class="btn" for="editor">
                                <input type="radio" name="activity" value="editor">Editorial board
                            </label>
                        </div>
                    </div>
                    <!-- </select> -->
                </div>
            </div>
            <div class="col-sm">

                <div id="lecture-select">
                    <label for="category" class="required"><?= lang('Type of lecture', 'Art der Lehrveranstaltung') ?></label>
                    <select name="category" id="category" class="form-control">
                        <option value="long">long</option>
                        <option value="short">short</option>
                        <option value="repetition">repetition</option>
                    </select>
                </div>

                <div id="misc-select">
                    <label for="category" class="required"><?= lang('Frequency', 'Frequenz') ?></label>
                    <select name="category" id="category" class="form-control">
                        <option value="once">once</option>
                        <option value="annual">annual</option>
                        <option value="monthly">monthly</option>
                        <option value="quarterly">quarterly</option>
                    </select>
                </div>

                <div id="reviewer-select">
                    <label for="category" class="required"><?= lang('Journal') ?></label>
                    <input type="text" class="form-control" name="category" id="category" list="journal-list">
                </div>
            </div>
        </div>



<!-- 

        <div class="form-row row-eq-spacing">
            <div class="col-sm">
                <label class="required" for="date_start"><?= lang('Start', 'Anfang') ?></label>
                <input type="date" class="form-control" name="date_start" id="date_start" required>
            </div>
            <div class="col-sm">
                <label for="date_end"><?= lang('End (leave empty if event was only one day)', 'Ende (leer lassen falls nur ein Tag)') ?></label>
                <input type="date" class="form-control" name="date_end" id="date_end">
            </div>
        </div> -->

        <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>

    </form>
</div>


<datalist id="scientist-list">
    <?php
    $stmt = $db->prepare("SELECT CONCAT(last_name, ', ', first_name) FROM `scientist` ORDER BY last_name ASC");
    $stmt->execute();
    $scientist = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($scientist as $s) { ?>
        <option><?= $s ?></option>
    <?php } ?>
</datalist>

<datalist id="journal-list">
    <?php
    $stmt = $db->prepare("SELECT journal_name FROM `journal` ORDER BY journal_name ASC");
    $stmt->execute();
    $journals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($journals as $j) { ?>
        <option><?= $j ?></option>
    <?php } ?>
</datalist>