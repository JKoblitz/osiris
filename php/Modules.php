<?php
include_once "_config.php";
include_once "_db.php";

class Modules
{
    public $form = array();
    private $copy = false;
    private $authors = "";
    private $editors = "";
    private $first = 1;
    private $last = 1;
    private $authorcount = 0;
    private $user = '';
    private $userlist = array();

    public $all_modules = array(
        "authors" => [
            "fields" => ["authors"],
            "name" => "Authors",
            "name_de" => "Autoren"
        ],
        "book-series" => [
            "fields" => ["series"],
            "name" => "Book-Series",
            "name_de" => "Bücherreihe"
        ],
        "book-title" => [
            "fields" => ["book"],
            "name" => "Book Title",
            "name_de" => "Buchtitel"
        ],
        "city" => [
            "fields" => ["city"],
            "name" => "City",
            "name_de" => "Stadt"
        ],
        "conference" => [
            "fields" => ["conference"],
            "name" => "Conference",
            "name_de" => "Konferenz"
        ],
        "correction" => [
            "fields" => ["correction"],
            "name" => "Correction",
            "name_de" => "Correction"
        ],
        "date-range" => [
            "fields" => ["start", "end"],
            "name" => "Date Range",
            "name_de" => "Zeitspanne"
        ],
        "date-range-ongoing" => [
            "fields" => ["start", "end"],
            "name" => "Date Range",
            "name_de" => "Zeitspanne"
        ],
        "date" => [
            "fields" => ["year", "month", "day"],
            "name" => "Date",
            "name_de" => "Datum"
        ],
        "details" => [
            "fields" => ["details"],
            "name" => "Details",
            "name_de" => "Details"
        ],
        "doctype" => [
            "fields" => ["doc_type"],
            "name" => "Doctype",
            "name_de" => "Doctype"
        ],
        "doi" => [
            "fields" => ["doi"],
            "name" => "DOI",
            "name_de" => "DOI"
        ],
        "edition" => [
            "fields" => ["edition"],
            "name" => "Edition",
            "name_de" => "Edition"
        ],
        "editor" => [
            "fields" => ["editors"],
            "name" => "Editors",
            "name_de" => "Editoren"
        ],
        "editorial" => [
            "fields" => ["editor_type"],
            "name" => "Editorial",
            "name_de" => "Editorenschaft"
        ],
        "guest" => [
            "fields" => ["category"],
            "name" => "Category",
            "name_de" => "Kategorie"
        ],
        "isbn" => [
            "fields" => ["isbn"],
            "name" => "ISBN",
            "name_de" => "ISBN"
        ],
        "issue" => [
            "fields" => ["issue"],
            "name" => "Issue",
            "name_de" => "Issue"
        ],
        "iteration" => [
            "fields" => ["iteration"],
            "name" => "Iteration",
            "name_de" => "Häufigkeit"
        ],
        "journal" => [
            "fields" => ["journal", "journal_id"],
            "name" => "Journal",
            "name_de" => "Journal"
        ],
        "lecture-invited" => [
            "fields" => ["invited_lecture"],
            "name" => "Invited lecture",
            "name_de" => "Eingeladener Vortrag"
        ],
        "lecture-type" => [
            "fields" => ["lecture_type"],
            "name" => "Lecture-Type",
            "name_de" => "Vortragsart"
        ],
        "link" => [
            "fields" => ["link"],
            "name" => "Link",
            "name_de" => "Link"
        ],
        "location" => [
            "fields" => ["location"],
            "name" => "Location",
            "name_de" => "Ort"
        ],
        "magazine" => [
            "fields" => ["magazine"],
            "name" => "Magazine",
            "name_de" => "Magazin"
        ],
        "online-ahead-of-print" => [
            "fields" => ["epub"],
            "name" => "Online Ahead Of Print",
            "name_de" => "Online Ahead Of Print"
        ],
        "openaccess" => [
            "fields" => ["open_access"],
            "name" => "Open-Access",
            "name_de" => "Open-Access"
        ],
        "pages" => [
            "fields" => ["pages"],
            "name" => "Pages",
            "name_de" => "Seiten"
        ],
        "person" => [
            "fields" => ["name", "affiliation", "academic_title"],
            "name" => "Person",
            "name_de" => "Person"
        ],
        "publisher" => [
            "fields" => ["publisher"],
            "name" => "Publisher",
            "name_de" => "Verlag"
        ],
        "pubmed" => [
            "fields" => ["pubmed"],
            "name" => "Pubmed-ID",
            "name_de" => "Pubmed-ID"
        ],
        "pubtype" => [
            "fields" => ["pubtype"],
            "name" => "Pubtype",
            "name_de" => "Pubtype"
        ],
        "review-description" => [
            "fields" => ["title"],
            "name" => "Decription",
            "name_de" => "Beschreibung"
        ],
        "review-type" => [
            "fields" => ["review-type"],
            "name" => "Review Type",
            "name_de" => "Review-Art"
        ],
        "scientist" => [
            "fields" => ["authors"],
            "name" => "Scientist",
            "name_de" => "Wissenschaftler_in"
        ],
        "semester-select" => [
            "fields" => [],
            "name" => "",
            "name_de" => ""
        ],
        "software-link" => [
            "fields" => ["link"],
            "name" => "Link",
            "name_de" => "Link"
        ],
        "software-type" => [
            "fields" => ["software_type"],
            "name" => "Type",
            "name_de" => "Type"
        ],
        "software-venue" => [
            "fields" => ["software_venue"],
            "name" => "Venue",
            "name_de" => "Veröffentlichungsort"
        ],
        "status" => [
            "fields" => ["status"],
            "name" => "Status",
            "name_de" => "Status"
        ],
        "student-category" => [
            "fields" => ["category"],
            "name" => "Category",
            "name_de" => "Kategorie"
        ],
        "supervisor" => [
            "fields" => ["authors"],
            "name" => "Supervisor",
            "name_de" => "Betreuer_in"
        ],
        "teaching-category" => [
            "fields" => ["category"],
            "name" => "Category",
            "name_de" => "Category"
        ],
        "teaching-course" => [
            "fields" => ["title", "module", "module_id"],
            "name" => "Course",
            "name_de" => "Modul"
        ],
        "title" => [
            "fields" => ["title"],
            "name" => "Title",
            "name_de" => "Titel"
        ],
        "university" => [
            "fields" => ["publisher"],
            "name" => "University",
            "name_de" => "Universität"
        ],
        "version" => [
            "fields" => ["version"],
            "name" => "Version",
            "name_de" => "Version"
        ],
        "volume" => [
            "fields" => ["volume"],
            "name" => "Volume",
            "name_de" => "Volume"
        ],
    );

    function __construct($form = array())
    {
        global $USER;
        global $osiris;
        $this->form = $form;

        $this->user = $_SESSION['username'] ?? '';

        $copy = $copy ?? false;
        $preset = $form['authors'] ?? array(
            [
                'last' => $USER['last'],
                'first' => $USER['first'],
                'aoi' => true,
                'user' => strtolower($USER['username'])
            ]
        );

        if (!empty($form) && !empty($form['authors'])) {
            if ($form['authors'] instanceof MongoDB\Model\BSONArray) {
                $form['authors'] = $form['authors']->bsonSerialize();
            }
            if (is_array($form['authors'])) {
                $pos = array_count_values(array_column($form['authors'], 'position'));
                $this->first = $pos['first'] ?? 1;
                $this->last = $pos['last'] ?? 1;
            }
            $this->authorcount = count($form['authors']);
        }
        foreach ($preset as $a) {
            $this->authors .= $this->authorForm($a, false);
        }

        $preset_editors = $form['editors'] ?? array();
        foreach ($preset_editors as $a) {
            $this->editors .= $this->authorForm($a, true);
        }

        $this->userlist = $osiris->users->find([], ['sort' => ["last" => 1]]);
    }

    private function val($index, $default = '')
    {
        $val = $this->form[$index] ?? $default;
        if (is_string($val)) {
            return htmlspecialchars($val);
        }
        return $val;
    }

    function authorForm($a, $is_editor = false)
    {
        $name = $is_editor ? 'editors' : 'authors';
        $aoi = $a['aoi'] ?? false;
        return "<div class='author " . ($aoi ? 'author-aoi' : '') . "' ondblclick='toggleAffiliation(this);'>
            $a[last], $a[first]<input type='hidden' name='values[$name][]' value='$a[last];$a[first];$aoi'>
            <a onclick='removeAuthor(event, this);'>&times;</a>
            </div>";
    }

    
    function get_name($module)
    {
        if (!isset($this->all_modules[$module]['name'])) return ucfirst($module);
        return lang($this->all_modules[$module]['name'], $this->all_modules[$module]['name_de']);
    }

    function get_fields($modules)
    {
        $return = array();
        foreach ($modules as $module) {
            $fields = $this->all_modules[$module]['fields'] ?? array();
            foreach ($fields as $field) {
                $val = $this->form[$field] ?? '';
                if (!is_array($val))
                    $return[ucfirst($field)] = $val;
            }
        }
        return $return;
    }

    function print_all_modules()
    {
        foreach ($this->all_modules as $module => $def) {
            $this->print_module($module);
        }
    }

    function print_modules($modules)
    {
        foreach ($modules as $module) {
            $this->print_module($module);
        }
    }

    function print_module($module, $req=false)
    {
        if (str_ends_with($module, '*')){
            $module = str_replace('*', '', $module);
            $req = true;
        }
        $required = ($req ? "required" : "");
        switch ($module) {
            case "title":
?>
                <div class="data-module col-12" data-module="title">
                    <div class="lang-<?= lang('en', 'de') ?>">
                        <label for="title" class="<?=$required?> element-title">
                            <?= lang('Title / Topic / Description', 'Titel / Thema / Beschreibung') ?>
                        </label>

                        <div class="form-group title-editor"><?= $this->form['title'] ?? '' ?></div>
                        <input type="text" class="form-control hidden" name="values[title]" id="title" <?=$required?> value="<?= $this->val('title') ?>">
                    </div>
                </div>
            <?php
                break;

            case "pubtype":
            ?>
                <div class="hidden data-module col-12" data-module="pubtype">
                    <!-- not visible, is selected via subtype buttons -->
                    <select class="form-control" name="values[pubtype]" id="pubtype" readonly <?=$required?>>
                        <option value="article">Journal article (refereed)</option>
                        <option value="book"><?= lang('Book', 'Buch') ?></option>
                        <option value="chapter"><?= lang('Book chapter', 'Buchkapitel') ?></option>
                        <option value="preprint">Preprint (non refereed)</option>
                        <option value="conference"><?= lang('Conference preceedings', 'Konfrenzbeitrag') ?></option>
                        <option value="magazine"><?= lang('Magazine article (non refereed)', 'Magazin-Artikel (non-refereed)') ?></option>
                        <option value="dissertation"><?= lang('Thesis') ?></option>
                        <option value="others"><?= lang('Others', 'Weiteres') ?></option>
                    </select>
                </div>
            <?php
                break;

            case "teaching-course":
            ?>
                <div class="data-module col-12" data-module="teaching-course">
                    <label for="teaching" class="element-cat <?=$required?>">
                        <?= lang('Course for the following module', 'Veranstaltung zu folgendem Modul') ?>
                    </label>
                    <a href="#teaching-select" id="teaching-field" class="module">
                        <span class="float-right text-primary"><i class="ph ph-edit"></i></span>

                        <div id="selected-teaching">
                            <?php if (!empty($this->form) && $this->form['type'] == 'teaching' && isset($this->form['module_id'])) :
                                $module = getConnected('teaching', $this->form['module_id']);
                            ?>
                                <h5 class="m-0"><span class="highlight-text"><?= $module['module'] ?></span> <?= $module['title'] ?></h5>
                                <span class="text-muted"><?= $module['affiliation'] ?></span>
                            <?php else : ?>
                                <span class="title"><?= lang('No module selected', 'Kein Modul ausgewählt') ?></span>

                            <?php endif; ?>
                        </div>

                        <input type="hidden" class="form-control hidden" name="values[title]" value="<?= $this->val('title') ?>" id="module-title" <?=$required?> readonly>
                        <input type="hidden" class="form-control hidden" name="values[module]" value="<?= $this->val('module') ?>" id="module" <?=$required?> readonly>
                        <input type="hidden" class="form-control hidden" name="values[module_id]" value="<?= $this->val('module_id') ?>" id="module_id" <?=$required?> readonly>
                    </a>
                </div>
            <?php
                break;

            case "supervisor":
            ?>
                <div class="data-module col-12" data-module="supervisor">
                    <div class="module p-0 mt-20">
                        <table class="table table-simple table-sm">
                            <thead>
                                <tr>
                                    <th><?= lang('Supervisor', 'Betreuer_in') ?></th>
                                    <th>
                                        <?= lang('SWS', 'Anteil in SWS') ?> (Semesterwochenstunden)
                                        <a href="#sws-calc" class="btn btn-link"><i class="ph ph-regular ph-calculator"></i></a>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($this->form)) { ?>
                                    <tr>
                                        <td>
                                            <select class="form-control" id="username" name="values[authors][]" autocomplete="off">
                                                <?php
                                                foreach ($this->userlist as $j) { ?>
                                                    <option value="<?= $j['_id'] ?>" <?= $j['_id'] == ($this->form['user'] ?? $this->user) ? 'selected' : '' ?>><?= $j['displayname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.1" class="form-control" name="values[sws][]" id="teaching-sws" value="0">
                                        </td>
                                        <td>
                                            <button class="btn btn-link" type="button" onclick="removeRow(this)"><i class="ph ph-trash text-danger"></i></button>
                                        </td>
                                    </tr>
                                <?php } else foreach ($this->form['authors'] ?? [] as $author) { ?>

                                    <tr>
                                        <td>
                                            <select class="form-control" id="username" name="values[authors][]" autocomplete="off">
                                                <?php
                                                foreach ($this->userlist as $j) { ?>
                                                    <option value="<?= $j['_id'] ?>" <?= $j['_id'] == $author['user'] ? 'selected' : '' ?>><?= $j['displayname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.1" class="form-control" name="values[sws][]" id="teaching-sws" value="<?= $author['sws'] ?? 0 ?>">
                                        </td>
                                        <td>
                                            <button class="btn btn-link" type="button" onclick="removeRow(this)"><i class="ph ph-trash text-danger"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        <button class="btn text-primary" type="button" onclick="addSupervisor(this)"><i class="ph ph-regular ph-plus"></i></button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <script>
                        function removeRow(el) {
                            // check if row is the only one left
                            if ($(el).closest('tbody').find('tr').length > 1) {
                                $(el).closest('tr').remove()
                            } else {
                                toastError(lang('At least one supervisor is needed.', 'Mindestens ein Betreuer muss angegeben werden.'))
                            }
                        }

                        function addSupervisor(btn) {
                            // just copy one table row
                            var table = $(btn).closest('table').find('tbody')
                            var el = table.find('tr').first().clone()
                            table.append(el)
                        }
                    </script>
                </div>
            <?php
                break;

            case "teaching-category":
            ?>
                <div class="data-module col-sm-6" data-module="teaching-category">
                    <label for="teaching-cat" class="<?=$required?> element-cat"><?= lang('Category', 'Kategorie') ?></label>
                    <select name="values[category]" id="teaching-cat" class="form-control" <?=$required?>>
                        <option value="lecture" <?= $this->val('category') == 'lecture' ? 'selected' : '' ?>><?= lang('Lecture', 'Vorlesung') ?></option>
                        <option value="practical" <?= $this->val('category') == 'practical' ? 'selected' : '' ?>><?= lang('Practical course', 'Praktikum') ?></option>
                        <option value="practical-lecture" <?= $this->val('category') == 'practical-lecture' ? 'selected' : '' ?>><?= lang('Lecture and practical course', 'Vorlesung und Praktikum') ?></option>
                        <option value="seminar" <?= $this->val('category') == 'seminar' ? 'selected' : '' ?>><?= lang('Seminar') ?></option>
                        <option value="other" <?= $this->val('category') == 'other' ? 'selected' : '' ?>><?= lang('Other', 'Sonstiges') ?></option>
                    </select>
                </div>
            <?php
                break;

            case "semester-select":
            ?>
                <div class="data-module col-sm-6" data-module="semester-select">
                    <label for="teaching-cat" class=""><?= lang('Fast select time', 'Schnellwahl Zeit') ?></label>

                    <div class="btn-group d-flex">
                        <button class="btn" type="button" onclick="selectSemester('SS', '<?= CURRENTYEAR - 1 ?>')">SS <?= CURRENTYEAR - 1 ?></button>
                        <button class="btn" type="button" onclick="selectSemester('WS', '<?= CURRENTYEAR - 1 ?>')">WS <?= CURRENTYEAR - 1 ?></button>
                        <button class="btn" type="button" onclick="selectSemester('SS', '<?= CURRENTYEAR ?>')">SS <?= CURRENTYEAR ?></button>
                        <button class="btn" type="button" onclick="selectSemester('WS', '<?= CURRENTYEAR ?>')">WS <?= CURRENTYEAR ?></button>
                    </div>
                    <script>
                        function selectSemester(sem, year) {
                            year = parseInt(year)
                            var start = year + '-'
                            start += (sem == 'WS' ? '10-01' : '04-01')
                            $('#date_start').val(start)

                            var end = (sem == 'WS' ? year + 1 : year) + '-'
                            end += (sem == 'WS' ? '03-31' : '09-30')
                            $('#date_end').val(end)
                        }
                    </script>
                </div>
            <?php
                break;

            case "authors":
            ?>
                <div class="data-module col-12" data-module="authors">
                    <a class="float-right" href="#author-help"><i class="ph ph-question" style="line-height:0;"></i> <?= lang('Help', 'Hilfe') ?></a>
                    <label for="author" class="element-author <?=$required?>">
                        <!-- <span data-visible="students,guests"><?= lang('Responsible scientist', 'Verantwortliche Person') ?></span> -->
                        <?= lang('Author(s) / Responsible person', 'Autor(en) / Verantwortliche Person') ?>
                        <small class="text-muted"><?= lang('(in correct order, format: Last name, First name)', '(in korrekter Reihenfolge, Format: Nachname, Vorname)') ?></small>
                    </label>

                    <div class="border" id="author-widget">
                        <div class="author-list p-10" id="author-list">
                            <?= $this->authors ?>
                        </div>
                        <div class="p-10 bg-light border-top d-flex">

                            <div class="input-group input-group-sm d-inline-flex w-auto">
                                <input type="text" placeholder="<?= lang('Add author ...', 'Füge Autor hinzu ...') ?>" onkeypress="addAuthor(event);" id="add-author" list="scientist-list">
                                <div class="input-group-append">
                                    <button class="btn btn-primary h-full" type="button" onclick="addAuthor(event);">
                                        <i class="ph ph-regular ph-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="ml-auto" id="author-numbers">
                                <label for="first-authors"><?= lang('Number of first authors:', 'Anzahl der Erstautoren:') ?></label>
                                <input type="number" name="values[first_authors]" id="first-authors" value="<?= $this->first ?>" class="form-control form-control-sm w-50 d-inline-block mr-10" autocomplete="off">
                                <label for="last-authors"><?= lang('last authors:', 'Letztautoren:') ?></label>
                                <input type="number" name="values[last_authors]" id="last-authors" value="<?= $this->last ?>" class="form-control form-control-sm w-50 d-inline-block" autocomplete="off">
                            </div>
                        </div>

                    </div>
                    <small class="text-muted">
                        <?= lang('Note: A detailed author editor is available after adding the activity.', 'Anmerkung: Ein detaillierter Autoreneditor ist verfügbar, nachdem der Datensatz hinzugefügt wurde.') ?>
                    </small>
                    <div class="alert alert-danger mb-20 affiliation-warning" style="display: none;">
                        <h5 class="title">
                            <i class="ph ph-warning-circle"></i>
                            <?= lang("Attention: No affiliated authors added.", 'Achtung: Keine affilierten Autoren angegeben.') ?>
                        </h5>
                        <?= lang(
                            'Please double click on every affiliated author in the list above, to mark them as affiliated. Only affiliated authors will receive points and are shown in reports.',
                            'Bitte doppelklicken Sie auf jeden affilierten Autor in der Liste oben, um ihn als zugehörig zu markieren. Nur zugehörige Autoren erhalten Punkte und werden in Berichten berücksichtigt.'
                        ) ?>
                    </div>
                </div>
            <?php
                break;

            case "person":
            ?>
                <div class="data-module col-12 row" data-module="person">
                    <div class="col-sm-5">
                        <label for="guest-name" class="<?=$required?> element-other">
                            <?= lang('Name of the student', 'Name des Studierenden') ?>
                            <?= lang('(last name, given name)', '(Nachname, Vorname)') ?>
                        </label>
                        <input type="text" class="form-control" name="values[name]" id="guest-name" <?=$required?> value="<?= $this->val('name') ?>">
                    </div>
                    <div class="col-sm-5">
                        <label for="guest-affiliation" class="<?=$required?> element-other"><?= lang('Affiliation (Name, City, Country)', 'Einrichtung (Name, Ort, Land)') ?></label>
                        <input type="text" class="form-control" name="values[affiliation]" id="guest-affiliation" <?=$required?> value="<?= $this->val('affiliation') ?>">
                    </div>
                    <div class="col-sm-2">
                        <label for="guest-academic_title"><?= lang('Academ. title', 'Akadem. Titel') ?></label>
                        <input type="text" class="form-control" name="values[academic_title]" id="guest-academic_title" value="<?= $this->val('academic_title') ?>">
                    </div>
                </div>
            <?php
                break;

            case "student-category":
            ?>
                <div class="data-module col-sm-6" data-module="student-category">
                    <label for="category-students" class="<?=$required?> element-cat"><?= lang('Category', 'Kategorie') ?></label>
                    <select name="values[category]" id="category-students" class="form-control" <?=$required?>>
                        <option value="doctoral thesis" <?= $this->val('category') == 'doctoral thesis' ? 'selected' : '' ?>><?= lang('Doctoral Thesis', 'Doktorand:in') ?></option>
                        <option value="master thesis" <?= $this->val('category') == 'master thesis' ? 'selected' : '' ?>><?= lang('Master Thesis', 'Master-Thesis') ?></option>
                        <option value="bachelor thesis" <?= $this->val('category') == 'bachelor thesis' ? 'selected' : '' ?>><?= lang('Bachelor Thesis', 'Bachelor-Thesis') ?></option>
                    </select>
                </div>
            <?php
                break;

            case "status":
            ?>
                <div class="data-module col-sm-6" data-module="status" style="align-self: center;">
                <label for="status" class="<?=$required?>">Status</label>
                    <div id="end-question">
                        <div class="custom-radio d-inline-block">
                            <input type="radio" name="values[status]" id="status-in-progress" value="in progress" checked="checked" value="1">
                            <label for="status-in-progress"><?= lang('In progress', 'In Progress') ?></label>
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
            <?php
                break;

            case "guest":
            ?>
                <div class="data-module col-sm-6" data-module="guest">
                    <label for="category-guest" class="<?=$required?> element-cat"><?= lang('Category', 'Kategorie') ?></label>
                    <select name="values[category]" id="category-guest" class="form-control" <?=$required?>>
                        <option value="guest scientist" <?= $this->val('category') == 'guest scientist' ? 'selected' : '' ?>><?= lang('Guest Scientist', 'Gastwissenschaftler:in') ?></option>
                        <option value="lecture internship" <?= $this->val('category') == 'lecture internship' ? 'selected' : '' ?>><?= lang('Lecture Internship', 'Pflichtpraktikum im Rahmen des Studium') ?></option>
                        <option value="student internship" <?= $this->val('category') == 'student internship' ? 'selected' : '' ?>><?= lang('Student Internship', 'Schülerpraktikum') ?></option>
                        <option value="other" <?= $this->val('category') == 'other' ? 'selected' : '' ?>><?= lang('Other', 'Sonstiges') ?></option>
                    </select>
                </div>
            <?php
                break;

            case "details":
            ?>
                <div class="data-module col-sm-6" data-module="details">
                    <label for="details" class="<?=$required?>">
                        <?= lang('Details') ?>
                    </label>
                    <input type="text" class="form-control" name="values[details]" id="details" <?=$required?> value="<?= $this->val('details') ?>">
                </div>
            <?php
                break;

            case "date":
            ?>
                <div class="data-module col-12 row" data-module="date">
                    <div class="col-sm">
                        <label for="year" class="<?=$required?> element-time">Year</label>
                        <input type="number" min="1901" max="2155" step="1" class="form-control" name="values[year]" id="year" <?=$required?> value="<?= $this->val('year') ?>">
                    </div>
                    <div class="col-sm">
                        <label for="month" class="<?=$required?> element-time">Month</label>
                        <input type="number" min="1" max="12" step="1" class="form-control" name="values[month]" id="month" <?=$required?> value="<?= $this->val('month') ?>">
                    </div>
                    <div class="col-sm">
                        <label for="day" class="element-time">Day</label>
                        <input type="number" min="1" max="31" step="1" class="form-control" name="values[day]" id="day" value="<?= $this->val('day') ?>">
                    </div>
                </div>
            <?php
                break;

            case "lecture-type":
            ?>
                <div class="data-module col-sm-6" data-module="lecture-type">
                    <label class="<?=$required?> element-cat" for="lecture_type"><?= lang('Type of lecture', 'Art des Vortrages') ?></label>
                    <select name="values[lecture_type]" id="lecture_type" class="form-control" autocomplete="off">
                        <option value="short" <?= $this->val('lecture_type') == 'short' ? 'selected' : '' ?>><?= lang('short', 'kurz') ?> (15-25 min.)</option>
                        <option value="long" <?= $this->val('lecture_type') == 'long' ? 'selected' : '' ?>><?= lang('long', 'lang') ?> (> 30 min.)</option>
                        <option value="repetition" <?= $this->val('lecture_type') == 'repetition' || $this->copy ? 'selected' : '' ?>><?= lang('repetition', 'Wiederholung') ?></option>
                    </select>
                </div>
            <?php
                break;

            case "lecture-invited":
            ?>
                <div class="data-module col-sm-6" data-module="lecture-invited">
                    <label class="<?=$required?>" for="lecture_type"><?= lang('Invited lecture') ?></label>
                    <select name="values[invited_lecture]" id="invited_lecture" class="form-control" autocomplete="off" <?=$required?>>
                        <option value="0" <?= $this->val('invited_lecture', false) ? '' : 'selected' ?>><?= lang('No', 'Nein') ?></option>
                        <option value="1" <?= $this->val('invited_lecture', false) ? 'selected' : '' ?>><?= lang('Yes', 'Ja') ?></option>
                    </select>
                </div>
            <?php
                break;

            case "date-range":
            ?>
                <div class="data-module col-sm-8 col-md-6" data-module="date-range">
                    <label class="<?=$required?> element-time" for="date_start">
                        <?= lang('Date range', "Zeitspanne") ?>
                        <span data-toggle="tooltip" data-title="<?= lang('Leave end date empty if only one day', 'Ende leer lassen, falls es nur ein Tag ist') ?>"><i class="ph ph-question" style="line-height:0;"></i></span>
                    </label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="values[start]" id="date_start" <?=$required?> value="<?= valueFromDateArray($this->val('start')) ?>">
                        <input type="date" class="form-control" name="values[end]" id="date_end" value="<?= valueFromDateArray($this->val('end')) ?>">
                    </div>
                </div>
            <?php
                break;

            case "date-range-ongoing":
            ?>
                <div class="data-module col-sm-8 col-md-6" data-module="date-range-ongoing">
                    <label class="<?=$required?> element-time" for="date_start">
                        <?= lang('Date range', "Zeitspanne") ?>
                        <span data-toggle="tooltip" data-title="<?= lang('Leave end date empty ongoing activity', 'Ende leer lassen, falls es eine laufende Aktivität ist') ?>"><i class="ph ph-question" style="line-height:0;"></i></span>
                    </label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="values[start]" id="date_start" <?=$required?> value="<?= valueFromDateArray($this->val('start')) ?>">
                        <input type="date" class="form-control" name="values[end]" id="date_end" value="<?= valueFromDateArray($this->val('end')) ?>">
                    </div>
                </div>
            <?php
                break;

            case "software-venue":
            ?>
                <div class="data-module col-sm-4" data-module="software-venue">
                    <label class="element-other <?=$required?>" for="software_venue"><?= lang('Publication venue, e.g. GitHub, Zenodo ...', 'Ort der Veröffentlichung, z.B. GitHub, Zenodo ...') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[software_venue]" id="software_venue" value="<?= $this->val('software_venue') ?>">
                </div>
            <?php
                break;

            case "software-link":
            ?>
                <div class="data-module col-sm-6" data-module="software-link">
                    <label class="element-link <?=$required?>" for="software_link"><?= lang('Complete link to the software/database', 'Kompletter Link zur Software/Datenbank') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[link]" id="software_link" value="<?= $this->val('link') ?>">
                </div>
            <?php
                break;

            case "version":
            ?>
                <div class="data-module col-sm-2" data-module="version">
                    <label class="element-other <?=$required?>" for="software_version"><?= lang('Version') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[version]" id="software_version" value="<?= $this->val('version') ?>">
                </div>
            <?php
                break;

            case "software-type":
            ?>
                <div class="data-module col-sm-4" data-module="software-type">
                    <label class="element-cat <?=$required?>" for="software_type"><?= lang('Type of software', 'Art der Software') ?></label>
                    <select name="values[software_type]" id="software_type" class="form-control" <?=$required?>>
                        <option value="" <?= empty($this->val('software_type')) ? 'selected' : '' ?>>Not specified</option>
                        <option value="software" <?= $this->val('software_type') == 'software' ? 'selected' : '' ?>>Computer Software</option>
                        <option value="database" <?= $this->val('software_type') == 'database' ? 'selected' : '' ?>>Database</option>
                        <option value="dataset" <?= $this->val('software_type') == 'dataset' ? 'selected' : '' ?>>Dataset</option>
                        <option value="webtool" <?= $this->val('software_type') == 'webtool' ? 'selected' : '' ?>>Website</option>
                        <option value="report" <?= $this->val('software_type') == 'report' ? 'selected' : '' ?>>Report</option>
                    </select>
                </div>
            <?php
                break;

            case "iteration":
            ?>
                <div class="data-module col-sm-4 hidden" data-module="misc">
                    <label class="<?=$required?>" for="iteration"><?= lang('Iteration', 'Häufigkeit') ?></label>
                    <select name="values[iteration]" id="iteration" class="form-control" <?=$required?> value="<?= $this->val('iteration') ?>" onchange="togglePubType('misc-'+this.value)">
                        <option value="once"><?= lang('once', 'einmalig') ?></option>
                        <option value="annual"><?= lang('continously', 'stetig') ?></option>
                    </select>
                </div>
            <?php
                break;

            case "conference":
            ?>
                <div class="data-module col-sm-6" data-module="conference">
                    <label for="conference" class="element-other <?=$required?>"><?= lang('Conference', 'Konferenz') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[conference]" id="conference" list="conference-list" placeholder="VAAM 2022" value="<?= $this->val('conference') ?>">
                </div>
            <?php
                break;

            case "location":
            ?>
                <div class="data-module col-sm-6" data-module="location">
                    <label for="location" class="element-other <?=$required?>"><?= lang('Location', 'Ort') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[location]" id="location" placeholder="Berlin, Germany" value="<?= $this->val('location') ?>">
                </div>
            <?php
                break;

            case "journal":
            ?>
                <div class="data-module col-12" data-module="journal">
                    <a href="<?= ROOTPATH ?>/docs/add-activities#das-journal-bearbeiten" target="_blank" class="<?=$required?> float-right">
                        <i class="ph ph-question"></i> <?= lang('Help', 'Hilfe') ?>
                    </a>
                    <label for="journal" class="element-cat <?=$required?>">Journal</label>
                    <a href="#journal-select" id="journal-field" class="module">
                        <!-- <a class="btn btn-link" ><i class="ph ph-edit"></i> <?= lang('Edit Journal', 'Journal bearbeiten') ?></a> -->
                        <span class="float-right text-primary"><i class="ph ph-edit"></i></span>

                        <div id="selected-journal">
                            <?php if (!empty($this->form) && isset($this->form['journal_id'])) :
                                $journal = getConnected('journal', $this->form['journal_id']);
                            ?>
                                <h5 class="m-0"><?= $journal['journal'] ?></h5>
                                <span class="float-right text-muted"><?= $journal['publisher'] ?></span>
                                <span class="text-muted">ISSN: <?= print_list($journal['issn']) ?></span>
                            <?php else : ?>
                                <span class="title"><?= lang('No Journal selected', 'Kein Journal ausgewählt') ?></span>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" class="form-control hidden" name="values[journal]" value="<?= $this->val('journal') ?>" id="journal" list="journal-list" <?=$required?> readonly>
                        <input type="hidden" class="form-control hidden" name="values[journal_id]" value="<?= $this->val('journal_id') ?>" id="journal_id" <?=$required?> readonly>

                    </a>
                </div>
            <?php
                break;

            case "magazine":
            ?>
                <div class="data-module col-sm-6" data-module="magazine">
                    <label for="magazine" class="element-cat <?=$required?>"><?= lang('Magazine / Venue', 'Zeitschrift / Veröffentlichungsort') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[magazine]" value="<?= $this->val('magazine') ?>" id="magazine">
                </div>
            <?php
                break;

            case "link":
            ?>
                <div class="data-module col-sm-6" data-module="link">
                    <label for="link" class="element-link <?=$required?>">Link</label>
                    <input type="text" class="form-control" <?=$required?> name="values[link]" value="<?= $this->val('link') ?>" id="link">
                </div>
            <?php
                break;

            case "book-title":
            ?>
                <div class="data-module col-12" data-module="book-title">
                    <label for="book" class="<?=$required?> element-cat"><?= lang('Book title', 'Buchtitel') ?></label>
                    <input type="text" class="form-control" name="values[book]" value="<?= $this->val('book') ?>" id="book" <?=$required?>>
                </div>
            <?php
                break;

            case "book-series":
            ?>
                <div class="data-module col-12" data-module="book-series">
                    <label for="series" class="element-other <?=$required?>"><?= lang('Series', 'Buchreihe') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[series]" value="<?= $this->val('series') ?>" id="series">
                </div>
            <?php
                break;

            case "edition":
            ?>
                <div class="data-module col-sm-4" data-module="edition">
                    <label for="edition" class="element-other <?=$required?>">Edition</label>
                    <input type="number" class="form-control" <?=$required?> name="values[edition]" value="<?= $this->val('edition') ?>" id="edition">
                </div>
            <?php
                break;

            case "issue":
            ?>
                <div class="data-module col-sm-4" data-module="issue">
                    <label for="issue" class="element-other <?=$required?>">Issue</label>
                    <input type="text" class="form-control" <?=$required?> name="values[issue]" value="<?= $this->val('issue') ?>" id="issue">
                </div>
            <?php
                break;

            case "volume":
            ?>
                <div class="data-module col-sm-4" data-module="volume">
                    <label for="volume" class="element-other <?=$required?>">Volume</label>
                    <input type="text" class="form-control" <?=$required?> name="values[volume]" value="<?= $this->val('volume') ?>" id="volume">
                </div>
            <?php
                break;

            case "pages":
            ?>
                <div class="data-module col-sm-4" data-module="pages">
                    <label for="pages" class="element-other <?=$required?>">Pages</label>
                    <input type="text" class="form-control" <?=$required?> name="values[pages]" value="<?= $this->val('pages') ?>" id="pages">
                </div>
            <?php
                break;

            case "publisher":
            ?>
                <div class="data-module col-sm-6" data-module="publisher">
                    <label for="publisher" class="element-other <?=$required?>">
                        <?= lang('Publisher', 'Verlag') ?>
                    </label>
                    <input type="text" class="form-control" <?=$required?> name="values[publisher]" value="<?= $this->val('publisher') ?>" id="publisher">
                </div>
            <?php
                break;

            case "university":
            ?>
                <div class="data-module col-sm-6" data-module="university">
                    <label for="publisher" class="element-other <?=$required?>">
                        <?= lang('University', 'Universität') ?>
                    </label>
                    <input type="text" class="form-control" <?=$required?> name="values[publisher]" value="<?= $this->val('publisher') ?>" id="publisher">
                </div>
            <?php
                break;

            case "city":
            ?>
                <div class="data-module col-sm-6" data-module="city">
                    <label for="city" class="element-other <?=$required?>"><?= lang('Location (City, Country)', 'Ort (Stadt, Land)') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[city]" value="<?= $this->val('city') ?>" id="city">
                </div>
            <?php
                break;

            case "editor":
            ?>
                <div class="data-module col-12" data-module="editor">
                    <label for="editor" class="<?=$required?> element-author"><?= lang('Editor(s) (in correct order)', 'Herausgeber (in korrekter Reihenfolge)') ?></label>
                    <div class="border" id="editor-widget">
                        <div class="author-list p-10" id="editor-list">
                            <?= $this->editors ?>
                        </div>
                        <div class="p-10 bg-light border-top d-flex">
                            <div class="input-group input-group-sm d-inline-flex w-auto">
                                <input type="text" placeholder="<?= lang('Add editor ...', 'Füge Editor hinzu ...') ?>" onkeypress="addAuthor(event, true);" id="add-editor" list="scientist-list">
                                <div class="input-group-append">
                                    <button class="btn btn-primary h-full" type="button" onclick="addAuthor(event, true);">
                                        <i class="ph ph-regular ph-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                break;

            case "doi":
            ?>
                <div class="data-module col-sm-6" data-module="doi">
                    <label for="doi" class="element-link <?=$required?>">DOI</label>
                    <?php if (empty($this->form)) { ?>
                        <input type="text" class="form-control" <?=$required?> name="values[doi]" value="<?= $this->val('doi') ?>" id="doi">
                    <?php } else { ?>
                        <div class="input-group">
                            <input type="text" class="form-control" <?=$required?> name="values[doi]" value="<?= $this->val('doi') ?>" id="doi">
                            <div class="input-group-append" data-toggle="tooltip" data-title="<?= lang('Retreive updated information via DOI', 'Aktualisiere die Daten via DOI') ?>">
                                <button class="btn" type="button" onclick="getPubData(event, this)"><i class="ph ph-arrows-clockwise"></i></button>
                                <span class="sr-only">
                                    <?= lang('Retreive updated information via DOI', 'Aktualisiere die bibliographischen Daten via DOI') ?>
                                </span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php
                break;

            case "pubmed":
            ?>
                <div class="data-module col-sm-6" data-module="pubmed">
                    <label for="pubmed" class="<?=$required?>">Pubmed</label>
                    <input type="number" class="form-control" <?=$required?> name="values[pubmed]" value="<?= $this->val('pubmed') ?>" id="pubmed">
                </div>
            <?php
                break;

            case "isbn":
            ?>
                <div class="data-module col-sm-6" data-module="isbn">
                    <label for="isbn" class="<?=$required?>">ISBN</label>
                    <input type="text" class="form-control" <?=$required?> name="values[isbn]" value="<?= $this->val('isbn') ?>" id="isbn">
                </div>
            <?php
                break;

            case "doctype":
            ?>
                <div class="data-module col-sm-6" data-module="doctype">
                    <label for="doc_type" class="<?=$required?>"><?= lang('Document type', 'Dokumententyp') ?></label>
                    <input type="text" class="form-control" <?=$required?> name="values[doc_type]" value="<?= $this->val('doc_type') ?>" id="doctype" placeholder="Report">
                </div>
            <?php
                break;

            case "openaccess":
            ?>
                <div class="data-module col-12" data-module="openaccess">
                    <div class="custom-radio d-inline-block" id="open_access-div">
                        <input type="radio" id="open_access-0" value="false" name="values[open_access]" <?= $this->val('open_access', false) ? '' : 'checked' ?>>
                        <label for="open_access-0"><i class="icon-closed-access text-danger"></i> Closed access</label>
                    </div>
                    <div class="custom-radio d-inline-block ml-20" id="open_access-div">
                        <input type="radio" id="open_access" value="true" name="values[open_access]" <?= $this->val('open_access', false) ? 'checked' : '' ?>>
                        <label for="open_access"><i class="icon-open-access text-success"></i> Open access</label>
                    </div>
                </div>
            <?php
                break;

            case "online-ahead-of-print":
            ?>
                <div class="data-module col-12" data-module="online-ahead-of-print">
                    <div class="custom-checkbox <?= isset($_GET['epub']) ? 'text-danger' : '' ?>" id="epub-div">
                        <input type="checkbox" id="epub" value="1" name="values[epub]" <?= (!isset($_GET['epub']) && $this->val('epub', false)) ? 'checked' : '' ?>>
                        <label for="epub">Online ahead of print</label>
                    </div>
                </div>
            <?php
                break;

            case "correction":
            ?>
                <div class="data-module col-12" data-module="correction">
                    <div class="custom-checkbox" id="correction-div">
                        <input type="checkbox" id="correction" value="1" name="values[correction]" <?= $this->val('correction', false) ? 'checked' : '' ?>>
                        <label for="correction"><?= lang('Correction') ?></label>
                    </div>
                </div>
            <?php
                break;

            case "scientist":
            ?>
                <div class="data-module col-sm-4" data-module="scientist">
                    <select class="form-control hidden" id="role-input" name="values[role]" <?=$required?> autocomplete="off" onchange="togglePubType(this.value)">
                        <!-- <option value="review" disabled selected>-- <?= lang('Select role', 'Wähle deine Rolle') ?> --</option> -->
                        <option value="review" <?= strtolower($this->val('role')) == 'review' ? 'selected' : '' ?>>Reviewer</option>
                        <option value="editorial" <?= strtolower($this->val('role')) == 'editorial' ? 'selected' : '' ?>>Editorial board</option>
                        <option value="thesis-rev" <?= strtolower($this->val('role')) == 'thesis-rev' ? 'selected' : '' ?>>Thesis review</option>
                        <option value="grant-rev" <?= strtolower($this->val('role')) == 'grant-rev' ? 'selected' : '' ?>><?= lang('Other review', 'Sonstiges Review') ?></option>
                    </select>


                    <label class="<?=$required?> element-author" for="username">
                        <?= lang('Scientist', 'Wissenschaftler:in') ?>
                    </label>
                    <select class="form-control" id="username" name="values[user]" <?=$required?> autocomplete="off">
                        <?php
                        foreach ($this->userlist as $j) { ?>
                            <option value="<?= $j['_id'] ?>" <?= $j['_id'] == ($this->form['user'] ?? $this->user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php
                break;

            case "review-description":
            ?>
                <div class="data-module col-sm-8" data-module="review-description">
                    <label class="<?=$required?> element-title" for="title-input">
                        <?= lang('Title/Description/Details', 'Titel/Beschreibung/Details') ?>
                    </label>
                    <input type="text" class="form-control" id="title-input" value="<?= $this->val('title') ?>" name="values[title]" <?=$required?>>
                </div>
            <?php
                break;

            case "review-type":
            ?>
                <div class="data-module col-sm-8" data-module="review-type">
                    <label class="element-cat <?=$required?>" for="review-type">
                        <?= lang('Type of review', 'Art des Review') ?>
                    </label>
                    <input type="text" class="form-control" id="review-type" value="<?= $this->val('review-type', 'Begutachtung eines Forschungsantrages') ?>" name="values[review-type]" <?=$required?>>
                </div>
            <?php
                break;

            case "editorial":
            ?>
                <div class="data-module col-sm-6" data-module="editorial">
                    <label for="editor_type" class="element-cat <?=$required?>">
                        <?= lang('Details', 'Details') ?>
                    </label>
                    <input type="text" class="form-control" <?=$required?> name="values[editor_type]" id="editor_type" value="<?= $this->val('editor_type') ?>" placeholder="Guest Editor for Research Topic 'XY'">
                </div>
            <?php
                break;

            default:
            ?>
                <div class="data-module alert alert-danger col-12">
                    <?= lang('Module ' . $module . ' is not defined.', 'Modul ' . $module . ' existiert nicht.') ?>
                </div>
<?php
                break;
        }
    }
}
