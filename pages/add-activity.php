<?php

$form = $form ?? array();
$copy = $copy ?? false;
$preset = $form['authors'] ?? array($USER);
$authors = "";
foreach ($preset as $a) {
    $authors .= authorForm($a);
}

$preset_editors = $form['editors'] ?? array();
$editors = "";
foreach ($preset_editors as $a) {
    $editors .= authorForm($a, true);
}

$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id']) && !$copy) {
    $formaction .= "update/" . $form['_id'];
    $btntext = '<i class="fas fa-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/activities/view/" . $form['_id'];
} else {
    $formaction .= "create";
    $btntext = '<i class="fas fa-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/activities/view/*";
}

$dept = $form['dept'] ?? $USER['dept'] ?? '';
?>
<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>

<div class="content">
    <?php if (empty($form)) { ?>

        <button class="btn btn-tour float-right" id="tour">
            <i class="far fa-message-question mr-10"></i>
            <?= lang('How does this work?', 'Wie funktioniert das?') ?>
        </button>
        <!-- Create new activity -->
        <h3 class=""><?= lang('Add activity', 'Füge Aktivität hinzu') ?></h3>
        <form method="get" onsubmit="getPubData(event, this)">
            <div class="form-group">
                <label for="doi"><?= lang('Search publication by DOI or Pubmed-ID', 'Suche Publikation über die DOI oder Pubmed-ID') ?>:</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="10.1093/nar/gkab961" name="doi" value="" id="search-doi">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </form>

    <?php } elseif ($copy) { ?>
        <h3 class=""><?= lang('Copy activity', 'Kopiere Aktivität') ?></h3>
    <?php } else { ?>
        <!-- Edit existing activity -->
        <h3 class=""><?= lang('Edit activity', 'Bearbeite Aktivität') ?> <span class="text-signal">#<?= $id ?></span></h3>
    <?php } ?>

    <div class="my-20" id="select-btns">
        <button onclick="togglePubType('article')" class="btn btn-select text-primary" id="article-btn"><i class="fa-regular fa-file-lines"></i> <?= lang('Journal article') ?></button>
        <button onclick="togglePubType('magazine')" class="btn btn-select text-primary" id="magazine-btn"><i class="fa-regular fa-newspaper"></i> <?= lang('Magazine article') ?></button>
        <!-- <button onclick="togglePubType('book')" class="btn btn-select text-primary" id="book-btn"><i class="fa-regular fa-book"></i> <?= lang('Book', 'Buch') ?></button> -->
        <button onclick="togglePubType('book')" class="btn btn-select text-primary" id="book-btn"><i class="fa-regular fa-book"></i> <?= lang('Book', 'Buch') ?></button>
        <button onclick="togglePubType('chapter')" class="btn btn-select text-primary" id="chapter-btn"><i class="fa-regular fa-book-bookmark"></i> <?= lang('Book chapter', 'Buchkapitel') ?></button>

        <button onclick="togglePubType('poster')" class="btn btn-select text-danger" id="poster-btn"><i class="fa-regular fa-presentation-screen"></i><?= lang('Posters', 'Poster') ?></button>
        <button onclick="togglePubType('lecture')" class="btn btn-select text-signal" id="lecture-btn"><i class="fa-regular fa-keynote"></i><?= lang('Lectures', 'Vorträge') ?></button>
        <button onclick="togglePubType('review')" class="btn btn-select text-success" id="review-btn"><i class="fa-regular fa-book-open-cover"></i><?= lang('Reviews &amp; editorials', 'Reviews &amp; Editorials') ?></button>
        <button onclick="togglePubType('misc')" class="btn btn-select text-muted" id="misc-btn"><i class="fa-regular fa-icons"></i><?= lang('Misc') ?></button>
        <button onclick="togglePubType('students')" class="btn btn-select text-dark" id="students-btn"><i class="fa-regular fa-people"></i><?= lang('Students &amp; Guests', 'Studierende &amp; Gäste') ?></button>
        <button onclick="todo('software')" class="btn btn-select text-muted disabled" id="software-btn"><i class="fa-regular fa-desktop"></i><?= lang('Software') ?></button>

    </div>

    <div class="box box-primary add-form" style="display:none" id="publication-form">
        <div class="content">

            <?php if (!empty($form) && isset($_GET['epub'])) { ?>
                <div class="alert alert-signal mb-20">
                    <div class="title">
                        <?= lang('Please review this entry and mark it as "Not Epub".', 'Bitte überprüfe diesen Eintrag und markiere ihn als "nicht Epub".') ?>
                    </div>
                    <p>
                        <?= lang(
                            'Review carefully all data, especially the publication date, which has to be the <b>date of the issued publication</b> (not online publication)!',
                            'Überprüfe alle Daten sorgfältig, für den Fall, dass sich Änderungen ergeben haben. Besonders das Publikationsdatum muss überprüft und auf das <b>tatsächliche Datum der Publikation (nicht online)</b> gesetzt werden.'
                        ) ?>
                    </p>
                    <?php if (isset($form['doi']) && !empty($form['doi'])) { ?>
                        <p class="mb-0">
                            <a class="link" href="http://doi.org/<?= $form['doi'] ?>" target="_blank" rel="noopener noreferrer">
                                <?= lang(
                                    'Have a look at the publishers page of your publication for reference.',
                                    'Als Referenz kannst du hier die Seite des Publishers zu deiner Publikation sehen.'
                                ) ?>
                            </a>
                        </p>
                    <?php } ?>

                </div>

            <?php } ?>


            <form action="<?= $formaction ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">

                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label for="type"><?= lang('Type of activity', 'Art der Aktivität') ?>:</label>
                        <input type="text" class="form-control disabled" name="values[type]" id="type" placeholder="Type" readonly>
                    </div>
                    <div class="col-sm" data-visible="article,magazine,book,editor,chapter">
                        <label for="pubtype" class="required"><?= lang('Type of publication', 'Art der Publikation') ?>:</label>
                        <select class="form-control" name="values[pubtype]" id="pubtype">
                            <option value="article">Journal article (refereed)</option>
                            <option value="book"><?= lang('Book', 'Buch') ?></option>
                            <option value="chapter"><?= lang('Book chapter', 'Buchkapitel') ?></option>
                            <option value="preprint">Preprint (non refereed)</option>
                            <!-- <option value="conference"><?= lang('Conference preceedings', 'Konfrenzbeitrag') ?></option> -->
                            <option value="magazine"><?= lang('Magazine article (non refereed)', 'Magazin-Artikel (non-refereed)') ?></option>
                            <option value="others"><?= lang('Others', 'Weiteres') ?></option>
                        </select>
                    </div>
                </div>


                <div class="form-group lang-<?=lang('en', 'de')?>" data-visible="article,magazine,book,chapter,editor,lecture,poster,misc,students">
                    <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>

                    <div class="form-group title-editor"><?= $form['title'] ?? '' ?></div>
                    <input type="text" class="form-control hidden" name="values[title]" id="title" required value="<?= $form['title'] ?? '' ?>">
                </div>

                <div class="form-row row-eq-spacing" data-visible="students">
                    <div class="col-sm-5">
                        <label for="guest-name" class="required"><?= lang('Name of the guest/student (last name, given name)', 'Name des Gastes/Studierenden (Nachname, Vorname)') ?></label>
                        <input type="text" class="form-control" name="values[name]" id="guest-name" required value="<?= $form['name'] ?? '' ?>">
                    </div>
                    <div class="col-sm-5">
                        <label for="guest-affiliation" class="required"><?= lang('Affiliation (Name, City, Country)', 'Einrichtung (Name, Ort, Land)') ?></label>
                        <input type="text" class="form-control" name="values[affiliation]" id="guest-affiliation" required value="<?= $form['affiliation'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2">
                        <label for="guest-academic_title"><?= lang('Academ. title', 'Akadem. Titel') ?></label>
                        <input type="text" class="form-control" name="values[academic_title]" id="guest-academic_title" value="<?= $form['academic_title'] ?? '' ?>">
                    </div>
                </div>

                <div class="form-group" data-visible="article,magazine,book,editor,chapter,lecture,poster,misc,students">
                    <label for="author" class="required">
                        <span data-visible="students"><?= lang('Responsible scientist', 'Verantwortliche Person') ?></span>
                        <span data-visible="article,magazine,book,editor,chapter,lecture,poster,misc"><?= lang('Author(s)', 'Autor(en)') ?></span>
                        <?= lang('(in correct order, format: Last name, First name)', '(in korrekter Reihenfolge, Format: Nachname, Vorname)') ?>
                    </label>

                    <div class="float-right" data-visible="article" id="author-numbers">
                        <label for="first-authors"><?= lang('Number of first authors:', 'Anzahl der Erstautoren:') ?></label>
                        <input type="number" name="values[first_authors]" id="first-authors" value="1" class="form-control form-control-sm w-50 d-inline-block" autocomplete="off">
                        <label for="last-authors"><?= lang('last authors:', 'Letztautoren:') ?></label>
                        <input type="number" name="values[last_authors]" id="last-authors" value="1" class="form-control form-control-sm w-50 d-inline-block" autocomplete="off">
                    </div>
                    <div class="author-list">
                        <?= $authors ?>
                        <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                    </div>
                    <!-- <div class="float-right" id="department">
                        <label for="dept"><?= lang('Dept:', 'Abteilung:') ?></label>
                        <select name="values[dept]" id="dept" class="form-control form-control-sm w-150 d-inline-block">
                            <option value="">Abteilungsübergreifend</option>
                            <option value="BIDB" <?=$dept=='BIDB' ? 'selected' : ''?>>BIDB</option>
                            <option value="BUG" <?=$dept=='BUG' ? 'selected' : ''?>>BUG</option>
                            <option value="MIG" <?=$dept=='MIG' ? 'selected' : ''?>>MIG</option>
                            <option value="MIOS" <?=$dept=='MIOS' ? 'selected' : ''?>>MIOS</option>
                            <option value="MuTZ" <?=$dept=='MuTZ' ? 'selected' : ''?>>MuTZ</option>
                            <option value="MÖD" <?=$dept=='MÖD' ? 'selected' : ''?>>MÖD</option>
                            <option value="PFVI" <?=$dept=='PFVI' ? 'selected' : ''?>>PFVI</option>
                            <option value="NFG" <?=$dept=='NFG' ? 'selected' : ''?>>NFG</option>
                            <option value="Services" <?=$dept=='Services' ? 'selected' : ''?>>Services</option>
                        </select>
                    </div> -->
                    <small class="text-muted">
                        <?= lang('Note: A detailed author editor is available after adding the activity.', 'Anmerkung: Ein detaillierter Autoreneditor ist verfügbar, nachdem der Datensatz hinzugefügt wurde.') ?>
                    </small>
                </div>

                <div class="alert alert-signal mb-20 affiliation-warning" style="display: none;">
                    <h5 class="title">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= lang('Attention: No ' . AFFILIATION . " authors added.", 'Achtung: Keine ' . AFFILIATION . '-Autoren angegeben.') ?>
                    </h5>
                    <?= lang(
                        'Please click on every ' . AFFILIATION . ' author in the list above, to mark them as affiliated. Only affiliated authors will receive points and are shown in reports.',
                        'Bitte klicken Sie auf jeden ' . AFFILIATION . '-Autor in der Liste oben, um ihn als zugehörig zu markieren. Nur zugehörige Autoren erhalten Punkte und werden in Berichten berücksichtigt.'
                    ) ?>
                </div>

                <div class="form-row row-eq-spacing " data-visible="article,magazine,book,chapter">
                    <div class="col-sm">
                        <label for="year" class="required">Year</label>
                        <input type="number" min="1901" max="2155" step="1" class="form-control" name="values[year]" id="year" required value="<?= $form['year'] ?? '' ?>">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="month" class="required">Month</label>
                        <input type="number" min="1" max="12" step="1" class="form-control" name="values[month]" id="month" required value="<?= $form['month'] ?? '' ?>">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="day">Day</label>
                        <input type="number" min="1" max="31" step="1" class="form-control" name="values[day]" id="day" value="<?= $form['day'] ?? '' ?>">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                </div>
                <div class="form-row row-eq-spacing" data-visible="students">
                    <div class="col-sm">
                        <label for="category" class="required"><?= lang('Category', 'Kategorie') ?></label>
                        <select name="values[category]" id="category" class="form-control" required onchange="studentsEndQuestion()">
                            <option disabled>--- <?= lang('Thesis', 'Abschlussarbeiten') ?> ---</option>
                            <option <?= ($form['category'] ?? '') == 'Doktorand:in' ? 'selected' : '' ?>>Doktorand:in</option>
                            <option <?= ($form['category'] ?? '') == 'Master-Thesis' ? 'selected' : '' ?>>Master-Thesis</option>
                            <option <?= ($form['category'] ?? '') == 'Bachelor-Thesis' ? 'selected' : '' ?>>Bachelor-Thesis</option>
                            <option disabled>--- <?= lang('Guests', 'Gäste') ?> ---</option>
                            <option <?= ($form['category'] ?? '') == 'Gastwissenschaftler:in' ? 'selected' : '' ?>>Gastwissenschaftler:in</option>
                            <option <?= ($form['category'] ?? '') == 'Pflichtpraktikum im Rahmen des Studium' ? 'selected' : '' ?>>Pflichtpraktikum im Rahmen des Studium</option>
                            <option <?= ($form['category'] ?? '') == 'Vorlesung und Laborpraktikum' ? 'selected' : '' ?>>Vorlesung und Laborpraktikum</option>
                            <option <?= ($form['category'] ?? '') == 'Schülerpraktikum' ? 'selected' : '' ?>>Schülerpraktikum</option>
                        </select>
                    </div>
                    <div class="col-sm">
                        <label for="details"><?=lang('Details (scholarship, etc.)', 'Details (Stipendium, etc.)')?></label>
                        <input type="text" class="form-control" name="values[details]" id="details" value="<?= $form['details'] ?? '' ?>">
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="lecture,poster,misc,students">
                    <div class="col-sm" data-visible="lecture">
                        <label class="required" for="lecture_type"><?= lang('Type of lecture', 'Art des Vortrages') ?></label>
                        <select name="values[lecture_type]" id="lecture_type" class="form-control">
                            <option value="short" <?= $form['lecture_type'] ?? '' == 'short' ? 'selected' : '' ?>>short (15-25 min.)</option>
                            <option value="long" <?= $form['lecture_type'] ?? '' == 'long' ? 'selected' : '' ?>>long (> 30 min.)</option>
                            <option value="repetition" <?= $form['lecture_type'] ?? '' == 'repetition' || $copy ? 'selected' : '' ?>>repetition</option>
                        </select>
                    </div>
                    <div class="col-sm">
                        <label class="required" for="date_start"><?= lang('Date', 'Datum') ?></label>
                        <input type="date" class="form-control" name="values[start]" id="date_start" required value="<?= valueFromDateArray($form['start'] ?? '') ?>">
                    </div>
                    <div class="col-sm" data-visible="poster,misc">
                        <label for="date_end"><?= lang('End (leave empty if event was only one day)', 'Ende (leer lassen falls nur ein Tag)') ?></label>
                        <input type="date" class="form-control" name="values[end]" id="date_end" value="<?= valueFromDateArray($form['end'] ?? '') ?>">
                    </div>
                    <div class="col-sm" data-visible="students">
                        <label for="students_end" class="required"><?= lang('End', 'Ende') ?></label>
                        <input type="date" class="form-control" name="values[end]" id="students_end" value="<?= valueFromDateArray($form['end'] ?? '') ?>" required onchange="studentsEndQuestion()">
                        <div id="end-question" style="display: none;">
                            <div class="custom-radio d-none">
                                <input type="radio" name="values[status]" id="status-in-progress" value="in progress" checked="checked" value="1">
                                <label for="status-in-progress"><?= lang('Completed', 'Abgeschlossen') ?></label>
                            </div>

                            <div class="custom-radio d-inline-block">
                                <input type="radio" name="values[status]" id="status-completed" value="completed" value="1">
                                <label for="status-completed"><?= lang('Completed', 'Abgeschlossen') ?></label>
                            </div>

                            <div class="custom-radio d-inline-block">
                                <input type="radio" name="values[status]" id="status-aborted" value="aborted" value="1">
                                <label for="status-aborted"><?= lang('Aborted', 'Abgebrochen') ?></label>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="form-row row-eq-spacing" data-visible="lecture,poster,misc">
                    <div class="col-sm" data-visible="misc">
                        <label class="required" for="iteration"><?= lang('Iteration', 'Häufigkeit') ?></label>
                        <select name="values[iteration]" id="iteration" class="form-control" value="<?= $form['iteration'] ?? '' ?>">
                            <option value="once">once</option>
                            <option value="annual">annual</option>
                        </select>
                    </div>
                    <div class="col-sm" data-visible="lecture,poster">
                        <label for="conference"><?= lang('Conference', 'Konferenz') ?></label>
                        <input type="text" class="form-control" name="values[conference]" id="conference" placeholder="VAAM 2022" value="<?= $form['conference'] ?? '' ?>">
                    </div>
                    <div class="col-sm">
                        <label for="location"><?= lang('Location', 'Ort') ?></label>
                        <input type="text" class="form-control" name="values[location]" id="location" placeholder="online" value="<?= $form['location'] ?? '' ?>">
                    </div>
                </div>

                <!-- 
                <div class="form-group">
                    <label class="required" for="publication"><?= lang('Date of publication (print preferred)', 'Datum der Publikation (bevorzugt Print)') ?></label>
                    <input type="date" class="form-control" name="values[publication]" id="date_publication" required>
                </div> -->

                <div class="form-group" data-visible="article">
                    <label for="journal">Journal</label>
                    <input type="text" class="form-control" name="values[journal]" value="<?= $form['journal'] ?? '' ?>" id="journal" list="journal-list">
                    <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                </div>

                <div class="form-row row-eq-spacing" data-visible="magazine">
                    <div class="col-sm">
                        <label for="magazine">Magazine</label>
                        <input type="text" class="form-control" name="values[magazine]" value="<?= $form['magazine'] ?? '' ?>" id="magazine">
                    </div>
                    <div class="col-sm">
                        <label for="link">Link</label>
                        <input type="text" class="form-control" name="values[link]" value="<?= $form['link'] ?? '' ?>" id="link">
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="article,book,editor,chapter">
                    <div class="col-sm" data-visible="article">
                        <label for="issue">Issue</label>
                        <input type="text" class="form-control" name="values[issue]" value="<?= $form['issue'] ?? '' ?>" id="issue">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="volume">Volume</label>
                        <input type="text" class="form-control" name="values[volume]" value="<?= $form['volume'] ?? '' ?>" id="volume">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="pages">Pages</label>
                        <input type="text" class="form-control" name="values[pages]" value="<?= $form['pages'] ?? '' ?>" id="pages">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                </div>


                <div class="form-row row-eq-spacing" data-visible="book,editor,chapter">
                    <div class="col-sm" data-visible="chapter">
                        <label for="book" class="required">Book title</label>
                        <input type="text" class="form-control" name="values[book]" value="<?= $form['book'] ?? '' ?>" id="book" required>
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="edition">Edition</label>
                        <input type="number" class="form-control" name="values[edition]" value="<?= $form['edition'] ?? '' ?>" id="edition">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="publisher">Publisher</label>
                        <input type="text" class="form-control" name="values[publisher]" value="<?= $form['publisher'] ?? '' ?>" id="publisher">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="city">City</label>
                        <input type="text" class="form-control" name="values[city]" value="<?= $form['city'] ?? '' ?>" id="city">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                </div>
                <div class="form-group" data-visible="editor,chapter">
                    <label for="editor" class="required"><?= lang('Editor(s) (in correct order)', 'Editor(en) (in korrekter Reihenfolge)') ?></label>
                    <div class="author-list">
                        <?= $editors ?>
                        <input type="text" placeholder="Add editor ..." onkeypress="addAuthor(event, this, true);" id="add-editor" list="scientist-list">
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="article,book,editor,chapter">
                    <div class="col-sm">
                            <label for="doi">DOI</label>
                        <?php if (empty($form)) { ?>
                            <input type="text" class="form-control" name="values[doi]" value="<?= $form['doi'] ?? '' ?>" id="doi">
                            <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                        <?php } else { ?>
                            <div class="input-group">
                                <input type="text" class="form-control" name="values[doi]" value="<?= $form['doi'] ?? '' ?>" id="doi">
                                <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                                <div class="input-group-append" data-toggle="tooltip" data-title="<?=lang('Retreive updated information via DOI', 'Aktualisiere die Daten via DOI')?>">
                                    <button class="btn" type="button" onclick="getPubData(event, this)"><i class="fas fa-rotate"></i></button>
                                    <span class="sr-only">
                                        <?=lang('Retreive updated information via DOI', 'Aktualisiere die bibliographischen Daten via DOI')?>
                                    </span>
                                </div>
                            </div>
                         <?php } ?>
                       
                    </div>
                    <div class="col-sm">
                        <label for="pubmed">Pubmed</label>
                        <input type="number" class="form-control" name="values[pubmed]" value="<?= $form['pubmed'] ?? '' ?>" id="pubmed">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="isbn">ISBN</label>
                        <input type="text" class="form-control" name="values[isbn]" value="<?= $form['isbn'] ?? '' ?>" id="pubmed">
                    </div>
                </div>

                <div class="form-group" data-visible="article,book,chapter,editor">
                    <div class="custom-checkbox" id="open_access-div">
                        <input type="checkbox" id="open_access" value="1" name="values[open_access]" <?= ($form['open_access'] ?? false) ? 'checked' : '' ?>>
                        <label for="open_access">Open access</label>
                    </div>
                </div>

                <div class="form-group" data-visible="article">
                    <div class="custom-checkbox <?= isset($_GET['epub']) ? 'text-danger' : '' ?>" id="epub-div">
                        <input type="checkbox" id="epub" value="1" name="values[epub]" <?= (!isset($_GET['epub']) && ($form['epub'] ?? false)) ? 'checked' : '' ?>>
                        <label for="epub">Epub ahead of print</label>
                    </div>
                </div>
                <div class="form-group" data-visible="article">
                    <div class="custom-checkbox" id="correction-div">
                        <input type="checkbox" id="correction" value="1" name="values[correction]" <?= ($form['correction'] ?? false) ? 'checked' : '' ?>>
                        <label for="correction"><?= lang('Correction') ?></label>
                    </div>
                </div>

                <div class="" data-visible="review">

                    <div class="form-row row-eq-spacing-sm">
                        <div class="col-sm-3">
                            <label class="required" for="role-input">
                                <?= lang('Role', 'Rolle') ?>
                            </label>
                            <select class="form-control" id="role-input" name="values[role]" required autocomplete="off" onchange="reviewUIupdate(this)">
                                <option value="Reviewer" disabled selected>-- <?= lang('Select role', 'Wähle deine Rolle') ?> --</option>
                                <option value="Reviewer" <?= ($form['role'] ?? '') == 'Reviewer' ? 'selected' : '' ?>>Reviewer</option>
                                <option value="Editor" <?= ($form['role'] ?? '') == 'Editor' ? 'selected' : '' ?>>Editorial</option>
                            </select>

                        </div>
                        <div class="col-sm">
                            <label class="required" for="journal-input">
                                <?= lang('Journal') ?>
                            </label>
                            <input type="text" class="form-control" placeholder="Journal" id="journal-input" value="<?= $form['journal'] ?? '' ?>" name="values[journal]" list="journal-list" required>
                            <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->

                        </div>
                        <div class="col-sm-3">
                            <label class="required" for="username">
                                <?= lang('Scientist', 'Wissenschaftler:in') ?>
                            </label>
                            <select class="form-control" id="username" name="values[user]" required autocomplete="off">
                                <?php
                                $userlist = $osiris->users->find();
                                foreach ($userlist as $j) { ?>
                                    <option value="<?= $j['_id'] ?>" <?= $j['_id'] == $user ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="reviewer-role" style="display: none;">
                        <label class="required" for="date">
                            <?= lang('Review date', 'Review-Datum') ?>
                        </label>
                        <input type="date" class="form-control date" name="values[start]" id="date" value="<?= valueFromDateArray($form['start'] ?? '') ?>">
                        <small class="text-muted">
                            <?= lang('Only month and year are considered', 'Nur Monat und Jahr sind relevant') ?>
                        </small>
                    </div>

                    <div class="editor-role" style="display: none;">
                        <div class="form-row row-eq-spacing-sm">
                            <div class="col-sm">
                                <label class="required" for="start">
                                    <?= lang('Beginning of editorial activity', 'Anfang der Editor-Tätigkeit') ?>
                                </label>
                                <input type="date" class="form-control start" name="values[start]" id="start" value="<?= valueFromDateArray($form['start'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm">
                                <label class="" for="end">
                                    <?= lang('End', 'Ende') ?>
                                </label>
                                <input type="date" class="form-control" name="values[end]" id="end" value="<?= valueFromDateArray($form['end'] ?? '') ?>">
                            </div>
                            <div class="col-sm">
                                <label for="editor_type">
                                    <?= lang('Details', 'Details') ?>
                                </label>
                                <input type="text" class="form-control" name="values[editor_type]" id="editor_type" value="<?= $form['editor_type']??'' ?>" placeholder="Guest Editor for Research Topic 'XY'">
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($form) && isset($form['file']) && !empty($form['file']) && !$copy) { ?>
                    <p>
                        <?= lang('The following file is appended to this entry:', 'Die folgende Datei ist diesem Eintrag angehängt:') ?>
                        <a target="_blank" href="<?= ROOTPATH ?>/activities/view/<?= $id ?>/file" class="btn"><?= lang('FILE', 'DATEI') ?></a>
                        <?= lang('Uploading a new file will overwrite the existing one.', 'Wenn du eine neue Datei hochlädst, wird sie überschrieben.') ?>
                    </p>

                <?php } ?>

                <div class="custom-file mb-20" id="file-input-div" data-visible="article,magazine,book,editor,chapter,lecture,poster,misc">
                    <input type="file" id="file-input" name="file" accept=".pdf" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                    <label for="file-input"><?= lang('Append a file (in PDF format)', 'Hänge eine Datei an (im PDF-Format)') ?></label>
                </div>

                <button class="btn btn-primary" type="submit" id="submit-btn"><?= $btntext ?></button>

            </form>
        </div>
    </div>

</div>


<datalist id="journal-list">
    <?php
    $journal = $osiris->journals->find();
    foreach ($journal as $j) { ?>
        <option><?= $j['journal'] ?></option>
    <?php } ?>
</datalist>

<datalist id="scientist-list">
    <?php
    $scientist = $osiris->users->find();
    foreach ($scientist as $s) { ?>
        <option><?= $s['last'] ?>, <?= $s['first'] ?></option>
    <?php } ?>
</datalist>


<script>
    UPDATE = false;
    function studentsEndQuestion() {
        const date = $('#students_end').val()
        console.log(date);
        if (date.length === 0) {
            $('#end-question').hide()
            return;
        }
        const selecteddate = new Date(date);
        const today = new Date();
        var thesis = $('#category').val()
        thesis = thesis.includes('Thesis') || thesis == "Doktorand:in"
        if (selecteddate.setHours(0, 0, 0, 0) <= today.setHours(0, 0, 0, 0) && thesis) {
            // date is in the past
            $('#end-question').show()
        } else {
            $('#end-question').hide()
        }
    }

    function reviewUIupdate(el) {
        var form = $(el).closest('form')
        if (el.value == "Reviewer") {
            $('.editor-role').slideUp()
            $('.editor-role').find('input.start').attr('required', false).attr('disabled', true)
            $('.reviewer-role').slideDown()
            $('.reviewer-role').find('input.date').attr('required', true).attr('disabled', false)
        } else {
            $('.editor-role').slideDown()
            $('.editor-role').find('input.start').attr('required', true).attr('disabled', false)
            $('.reviewer-role').slideUp()
            $('.reviewer-role').find('input.date').attr('required', false).attr('disabled', true)
        }
    }
</script>

<?php if (!empty($form)) { ?>
    <script>
        UPDATE = true
        togglePubType('<?= $form['type'] == 'publication' ? $form['pubtype'] : $form['type'] ?>');

        <?php if (isset($form['role'])) { ?>
            reviewUIupdate(document.getElementById('role-input'));
        <?php } ?>

        
        $('input').each(function (el){
            el =  $(this)
            if (!isEmpty(el.val()))
                el.attr("data-value", el.val())
        })
        

$('[data-value]').on("update blur", function () {
    var el = $(this)
    var old = el.attr("data-value").trim()
    var name = el.attr('name')
    if (old !== undefined) {
        if (old != el.val().trim() && !el.hasClass("is-valid")) {
            el.addClass("is-valid")
            el.next().removeClass('hidden')
        } else if (old == el.val().trim() && el.hasClass("is-valid")) {
            el.removeClass("is-valid")
            el.next().addClass('hidden')
        }
    }
})
    </script>

<?php } elseif (isset($_GET['type'])) { ?>
    <script>
        togglePubType('<?= $_GET['type'] ?>');
    </script>
<?php } ?>

<script src="<?= ROOTPATH ?>/js/tour/add-activity.js"></script>