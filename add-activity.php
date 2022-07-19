<div class="content">

    <h3 class=""><?= lang('Add activity', 'Füge Aktivität hinzu') ?></h3>


    <p><?= lang('What kind of activity?', 'Welche Art von Aktivität?') ?></p>

    <a class="btn <?= isset($_GET['lecture']) ? 'btn-primary' : '' ?>" href="?lecture">Lecture</a>
    <a class="btn <?= isset($_GET['editor']) ? 'btn-primary' : '' ?>" href="?editor">Editorial board</a>
    <a class="btn <?= isset($_GET['review']) ? 'btn-primary' : '' ?>" href="?review">Reviewer</a>
    <a class="btn <?= isset($_GET['misc']) ? 'btn-primary' : '' ?>" href="?misc">Miscellaneous</a>

<br>
<br>
    <?php if (isset($_GET['lecture']) || isset($_GET['editor']) || isset($_GET['misc']) || isset($_GET['review'])) { ?>

        <form action="#" id="activity-form" method="post">

            <div class="form-group">
                <label for="author" class="required"><?= lang('Author(s)', 'Autor(en)') ?></label>
                <div class="author-list">
                    <div class="author author-dsmz">Koblitz, Julia<input type="hidden" name="author[]" value="<?=$userClass->last?>;<?=$userClass->first?>;1"><a onclick="removeAuthor(event, this)">&times;</a></div>
                    <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                </div>
            </div>


            <?php if (isset($_GET['review']) || isset($_GET['editor'])) { ?>
                <div id="reviewer-select" class="form-group">
                    <label for="category" class="required"><?= lang('Journal') ?></label>
                    <input type="text" class="form-control" name="category" id="category" list="journal-list">
                </div>
                <?php if (isset($_GET['review'])) { ?>
                    <div class="form-group">
                        <label for="review_number" class="required"><?= lang('Count', 'Anzahl') ?></label>
                        <input type="number" name="review_number" id="review_number" value="1" class="form-control w-auto" required>
                    </div>
                <?php } ?>

            <?php } elseif (isset($_GET['misc'])) { ?>
                <div id="misc-select" class="form-group">
                    <label for="category" class="required"><?= lang('Frequency', 'Frequenz') ?></label>
                    <select name="category" id="category" class="form-control">
                        <option value="once">once</option>
                        <option value="annual">annual</option>
                        <option value="monthly">monthly</option>
                        <option value="quarterly">quarterly</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>
            <?php } elseif (isset($_GET['lecture'])) { ?>

                <div id="lecture-select" class="form-group">
                    <label for="category" class="required"><?= lang('Type of lecture', 'Art der Lehrveranstaltung') ?></label>
                    <select name="category" id="category" class="form-control">
                        <option value="long">long</option>
                        <option value="short">short</option>
                        <option value="repetition">repetition</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>

            <?php } ?>

            <!-- <div class="form-group">
                <label class="" for="time"><?= lang('Date', 'Datum') ?></label>
                <input type="date" class="form-control" name="time" id="time">
            </div> -->

            <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>

        </form>
    <?php } ?>


</div>


<datalist id="scientist-list">
    <?php
    $stmt = $db->prepare("SELECT CONCAT(last_name, ', ', first_name) FROM `users` ORDER BY last_name ASC");
    $stmt->execute();
    $scientist = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($scientist as $s) { ?>
        <option><?= $s ?></option>
    <?php } ?>
</datalist>

<datalist id="journal-list">
    <?php
    $stmt = $db->prepare("SELECT journal FROM `journal` ORDER BY journal ASC");
    $stmt->execute();
    $journals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($journals as $j) { ?>
        <option><?= $j ?></option>
    <?php } ?>
</datalist>