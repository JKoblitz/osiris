<style>
    .custom-radio input#open_access:checked~label::before {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }

    .custom-radio input#open_access-0:checked~label::before {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }
</style>
<?php

$form = $form ?? array();
$copy = $copy ?? false;
$preset = $form['authors'] ?? array($USER);

$first = 1;
$last = 1;
if (!empty($form) && $form['type'] == 'publication' && !empty($form['authors'])) {
    if (!is_array($form['authors'])) {
        $form['authors'] = $form['authors']->bsonSerialize();
    }
    if (is_array($form['authors'])) {
        $pos = array_count_values(array_column($form['authors'], 'position'));
        $first = $pos['first'] ?? 1;
        $last = $pos['last'] ?? 1;
    }
}

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

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}
?>
<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>
<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>


<div class="modal" id="author-help" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" href="#" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="modal-title">
                <?= lang('How to edit the author list', 'Wie bearbeite ich die Autorenliste') ?>?
            </h5>
            <?php if (lang("en", "de") == "en") { ?>
                <p>
                    To <b>add an author</b>, you have to enter him in the field marked "Add author ...". Please use the format <code>last name, first name</code>, so that OSIRIS can assign the authors correctly. DSMZ authors are suggested in a list. An author from the list will be automatically assigned to DSMZ.
                </p>

                <p>
                    To <b>remove an author</b>, you have to click on the X after his name.
                </p>
                <p>
                    To <b>change the author order</b>, you can take an author and drag and drop it to the desired position.
                </p>
                <p>
                    To <b>mark an author as belonging to the DSMZ</b>, you can simply double click on it. The name will then be highlighted in blue and the word DSMZ will appear in front of it. It is important for reporting that all authors are marked according to their affiliation! If authors are DSMZ employees but were not at the time of the activity, they must not be marked as a DSMZ author!
                </p>
            <?php } else { ?>
                <p>
                    Um einen <b>Autor hinzuzufügen</b>, musst du ihn in das Feld eintragen, das mit "Add author ..." gekennzeichnet ist. Nutze dafür bitte das Format <code>Nachname, Vorname</code>, damit OSIRIS die Autoren korrekt zuordnen kann. DSMZ-Autoren werden in einer Liste vorgeschlagen. Ein Autor aus der Liste wird automatisch zur DSMZ zugeordnet.
                </p>

                <p>
                    Um einen <b>Autor zu entfernen</b>, musst du auf das X hinter seinem Namen klicken.
                </p>
                <p>
                    Um die <b>Autorenreihenfolge zu ändern</b>, kannst du einen Autoren nehmen und ihn mittels Drag & Drop an die gewünschte Position ziehen.
                </p>
                <p>
                    Um einen <b>Autor zur DSMZ zugehörig zu markieren</b>, kannst du ihn einfach mit Doppelklick anklicken. Der Name wird dann blau markiert und das Wort DSMZ taucht davor auf. Es ist wichtig für die Berichterstattung, dass alle Autoren ihrer Zugehörigkeit nach markiert sind! Wenn Autoren zwar Beschäftigte der DSMZ sind, es aber zum Zeitpunkt der Aktivität nicht waren, dürfen sie nicht als DSMZ-Autor markiert werden!
                </p>

                <p>
                    Verschrieben? Ein Autor wird nicht korrekt einem Nutzer zugeordnet? Nachdem du den Datensatz hinzugefügt hast, kannst du die Autorenliste <b>im Detail noch einmal bearbeiten</b>.
                </p>
            <?php } ?>
        </div>
    </div>
</div>


<div class="modal" id="journal-select" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" href="#" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>

            <label for="journal-search"><?=lang('Search Journal by name or ISSN', 'Suche Journal nach Name oder ISSN')?></label>
            <input type="text" class="form-control" onchange="getJournal(this.value)" list="journal-list" id="journal-search" value="<?=$form['journal']??''?>">

            <table class="table table-simple">
                <tbody id="journal-suggest">

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="content">
    <?php if (empty($form)) { ?>

        <button class="btn btn-tour float-right" id="tour">
            <i class="far fa-message-question mr-10"></i>
            <?= lang('How does this work?', 'Wie funktioniert das?') ?>
        </button>
        <!-- Create new activity -->
        <h2 class="">
            <i class="icon-activity-plus"></i>
            <?= lang('Add activity', 'Füge Aktivität hinzu') ?>
        </h2>
        <form method="get" onsubmit="getPubData(event, this)">
            <div class="form-group">
                <label for="doi"><?= lang('Search by DOI or Pubmed-ID', 'Suche über die DOI oder Pubmed-ID') ?>:</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="10.1093/nar/gkab961" name="doi" value="" id="search-doi" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </form>

        <div class="my-20 select-btns" id="select-btns">
            <button onclick="togglePubType('article')" class="btn btn-select text-publication" id="publication-btn"><?= activity_icon('publication', false) ?><?= lang('Publication', 'Publikation') ?></button>
            <button onclick="togglePubType('poster')" class="btn btn-select text-poster" id="poster-btn"><?= activity_icon('poster', false) ?><?= lang('Posters', 'Poster') ?></button>
            <button onclick="togglePubType('lecture')" class="btn btn-select text-lecture" id="lecture-btn"><?= activity_icon('lecture', false) ?><?= lang('Lectures', 'Vorträge') ?></button>
            <button onclick="togglePubType('review')" class="btn btn-select text-review" id="review-btn"><?= activity_icon('review', false) ?><?= lang('Reviews &amp; editorials', 'Reviews &amp; Editorials') ?></button>
            <button onclick="togglePubType('teaching')" class="btn btn-select text-teaching" id="teaching-btn"><?= activity_icon('teaching', false) ?></i><?= lang('Teaching', 'Lehre') ?></button>
            <button onclick="togglePubType('students')" class="btn btn-select text-students" id="students-btn"><?= activity_icon('students', false) ?><?= lang('Students &amp; Guests', 'Studierende &amp; Gäste') ?></button>
            <button onclick="togglePubType('software')" class="btn btn-select text-software" id="software-btn"><?= activity_icon('software', false) ?><?= lang('Software &amp; Data') ?></button>
            <button onclick="togglePubType('misc-once')" class="btn btn-select text-misc" id="misc-btn"><?= activity_icon('misc', false) ?><?= lang('Misc') ?></button>
        </div>

    <?php } elseif ($copy) { ?>
        <h3 class=""><?= lang('Copy activity', 'Kopiere Aktivität') ?></h3>
    <?php } else { ?>
        <!-- Edit existing activity -->
        <h3 class=""><?= lang('Edit activity', 'Bearbeite Aktivität') ?> <span class="text-signal">#<?= $id ?></span></h3>
    <?php } ?>



    <div class="box box-primary add-form" style="display:none" id="publication-form">
        <div class="content">
            <button class="btn btn-osiris btn-sm mb-10" onclick="$('#publication-form').toggleClass('show-examples')"><?= lang('Examples', 'Beispiele') ?></button>

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

            <div class="mb-20 select-btns" data-visible="article,preprint,magazine,book,chapter,dissertation,others">
                <button onclick="togglePubType('article')" class="btn btn-select text-publication" id="article-btn"><i class="fa-regular fa-file-lines"></i> <?= lang('Journal article') ?></button>
                <button onclick="togglePubType('magazine')" class="btn btn-select text-publication" id="magazine-btn"><i class="fa-regular fa-newspaper"></i> <?= lang('Magazine article') ?></button>
                <!-- <button onclick="togglePubType('book')" class="btn btn-select text-publication" id="book-btn"><i class="fa-regular fa-book"></i> <?= lang('Book', 'Buch') ?></button> -->
                <button onclick="togglePubType('book')" class="btn btn-select text-publication" id="book-btn"><i class="fa-regular fa-book"></i> <?= lang('Book', 'Buch') ?></button>
                <button onclick="togglePubType('chapter')" class="btn btn-select text-publication" id="chapter-btn"><i class="fa-regular fa-book-bookmark"></i> <?= lang('Book chapter', 'Buchkapitel') ?></button>
                <button onclick="togglePubType('preprint')" class="btn btn-select text-publication" id="preprint-btn"><i class="fa-regular fa-file"></i> <?= lang('Preprint') ?></button>
                <button onclick="togglePubType('dissertation')" class="btn btn-select text-publication" id="dissertation-btn"><i class="fa-regular fa-book-user"></i> <?= lang('Thesis') ?></button>
                <button onclick="togglePubType('others')" class="btn btn-select text-publication" id="others-btn"><i class="fa-regular fa-memo-pad"></i> <?= lang('Others', 'Weitere') ?></button>

            </div>

            <div class="mb-20 select-btns" data-visible="review,editorial,grant-rev,thesis-rev">
                <button onclick="togglePubType('review')" class="btn btn-select text-review" id="review-btn"><i class="fa-regular fa-file-lines"></i> <?= lang('Paper review') ?></button>
                <button onclick="togglePubType('editorial')" class="btn btn-select text-review" id="editorial-btn"><i class="fa-regular fa-book-open-cover"></i> <?= lang('Editorial board') ?></button>
                <button onclick="togglePubType('grant-rev')" class="btn btn-select text-review" id="grant-rev-btn"><i class="fa-regular fa-file-chart-pie"></i> <?= lang('Grant proposal') ?></button>
                <button onclick="togglePubType('thesis-rev')" class="btn btn-select text-review" id="thesis-rev-btn"><i class="fa-regular fa-graduation-cap"></i> <?= lang('Thesis review') ?></button>
            </div>

            <div class="mb-20 select-btns" data-visible="misc-once,misc-annual">
                <button onclick="togglePubType('misc-once')" class="btn btn-select text-misc" id="misc-once-btn"><i class="fa-regular fa-calendar-day"></i> <?= lang('Once', 'Einmalig') ?></button>
                <button onclick="togglePubType('misc-annual')" class="btn btn-select text-misc" id="misc-annual-btn"><i class="fa-regular fa-repeat"></i> <?= lang('Frequently', 'Stetig') ?></button>
            </div>

            <div class="mb-20 select-btns" data-visible="students,guests">
                <button onclick="togglePubType('students')" class="btn btn-select text-students" id="students-once-btn"><i class="fa-regular fa-user-graduate"></i> <?= lang('Theses', 'Abschlussarbeiten') ?></button>
                <button onclick="togglePubType('guests')" class="btn btn-select text-students" id="students-annual-btn"><i class="fa-regular fa-user-tie"></i> <?= lang('Guests & interns', 'Gäste & Praktika') ?></button>
            </div>

            <div id="examples" class="mb-20">
                <b><?= lang('Example', 'Beispiel') ?></b>:

                <p data-visible="article">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Spring, S., Rohde, M., Bunk, B., Spröer, C., Will, S. E. and Neumann-Schaal, M. </span>
                    (<span class="element-time" data-element="<?= lang('Year', 'Jahr') ?>">2022</span>)
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">New insights into the energy metabolism and taxonomy of Deferribacteres revealed by the characterization of a new isolate from a hypersaline microbial mat</span>.
                    <span class="element-cat" data-element="<?= lang('Journal') ?>">Environmental microbiology </span>
                    <span data-element="<?= lang('Issue, Volume, Pages') ?>">24(5):2543-2575</span>.
                    DOI: http://dx.doi.org/<span class="element-link" data-element="<?= lang('DOI') ?>">10.1111/1462-2920.15999</span>
                </p>
                <p data-visible="magazine">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Wolf, J., Öztürk, B., Koblitz, J. and Neumann-Schaal, M.</span>
                    (<span class="element-time" data-element="<?= lang('Year', 'Jahr') ?>">2021</span>)
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">Systembiologischer Fokus auf Nachhaltigkeit - Wie uns interdisziplinäre Forschung beim Verständnis hilft</span>.
                    <span class="element-cat" data-element="<?= lang('Magazine') ?>">GIT Labor-Fachzeitschrift</span>.
                    <span class="element-link" data-element="<?= lang('Link') ?>">https://analyticalscience.wiley.com/do/10.1002/was.000600102/full/</span>
                </p>
                <p data-visible="book">
                    TODO
                </p>
                <p data-visible="chapter">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Overmann, J.</span>
                    (<span class="element-time" data-element="<?= lang('Year', 'Jahr') ?>">2022</span>)
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">Mikrobielle Vielfalt, Evolution und Systematik</span>.
                    In: <span class="element-author" data-element="<?= lang('Editor(s)', 'Editor(en)') ?>">G. Fuchs, H. G. Schlegel and M. Bramkamp</span> (eds)
                    <span class="element-cat" data-element="<?= lang('Book title', 'Buchtitel') ?>">Allgemeine Mikrobiologie</span>.
                    (<span data-element="<?= lang('Edition') ?>">11</span>th ed.,
                    pp. <span data-element="<?= lang('Pages') ?>">602-674</span>).
                    <span data-element="<?= lang('Location', 'Ort') ?>">Stuttgart</span>
                    <span data-element="<?= lang('Publisher', 'Verlag') ?>">Georg Thieme Verlag</span>.
                </p>

                <p data-visible="preprint">
                    TODO
                </p>
                <p data-visible="dissertation">
                    TODO
                </p>
                <p data-visible="poster">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Lissin, A., Podstawka, A., Reimer, L. C., Koblitz, J., Bunk, B. and Overmann, J. </span>
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">"Who is who?" A central database for resolving microbial strain identifiers</span>.
                    <span class="element-" data-element="<?= lang('Conference', 'Konferenz') ?>">GCB 2022</span>,
                    <span class="element-" data-element="<?= lang('Location', 'Ort') ?>">Halle (Saale)</span>.
                    <span class="element-time" data-element="<?= lang('Date', 'Datum') ?>">06.-08.09.2022</span>.
                </p>
                <p data-visible="lecture">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Koblitz, J., Halama, P., Spring, S., Thiel, V., Baschien, C., Hahnke, R., Pester, M., Reimer, L. C. and Overmann, J.</span>
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">MediaDive: the expert-curated cultivation media database</span>.
                    <span class="element-" data-element="<?= lang('Conference', 'Konferenz') ?>">ECCO 2022</span>,
                    <span class="element-" data-element="<?= lang('Location', 'Ort') ?>">Braunschweig</span>.
                    <span class="element-time" data-element="<?= lang('Date', 'Datum') ?>">28.09.2022</span>.
                    (<span class="element-cat" data-element="<?= lang('Type of lecture', 'Art des Vortrages') ?>">short</span>)
                </p>
                <p data-visible="misc-annual">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Steenpaß, L.</span>
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">Mitglied im Advisory Board der Core Facility der Medizinischen Universität Graz</span>,
                    von <span class="element-time" data-element="<?= lang('Date', 'Datum') ?>">01.08.2021</span> bis heute.
                </p>
                <p data-visible="misc-once">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Overmann, J.</span>
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">Teilnahme an der Podiumsdiskussion zum Thema Digitale Sequenzinformation auf der Jahrestagung der VAAM</span>,
                    <span class="element-time" data-element="<?= lang('Start Date', 'Startdatum') ?>">21.02.2022</span>,
                    <span class="element-" data-element="<?= lang('Location', 'Ort') ?>">virtuell<span>.
                </p>
                <p data-visible="misc-once">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Riedel, T.</span>
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">Organisation des CD-biOmics Workshop</span>,
                    <span class="element-time" data-element="<?= lang('Start and end date', 'Start- und Endatum') ?>">13.-19.03.2022</span>,
                    <span class="element-" data-element="<?= lang('Location', 'Ort') ?>">Leibniz Institut DSMZ, Braunschweig<span>.
                </p>
                <p data-visible="teaching">
                    <span class="element-author" data-element="<?= lang('Responsible scientist', 'Verantwortliche Person') ?>">Neumann-Schaal, M.</span>
                    <span class="element-title" data-element="<?= lang('Topic / Title / Description', 'Thema / Titel / Beschreibung') ?>">MI01: Grundlagen der Mikrobiologie</span>,
                    <span class="element-cat" data-element="<?= lang('Type of lecture', 'Art des Vortrages') ?>">TU Braunschweig</span>
                    (<span class="element-time" data-element="<?= lang('Start and end date', 'Start- und Endatum') ?>">01.10.2022 - 31.03.2023</span>).


                </p>
                <p data-visible="students">
                    <span class="element-" data-element="<?= lang('Name of the student', 'Name des Studierenden') ?>">Halama, Philipp</span>,
                    <span class="element-" data-element="<?= lang('Affiliation of the student', 'Einrichtung des Studierenden') ?>">TU Braunschweig</span>.
                    <span class="element-title" data-element="<?= lang('Topic / Title / Description', 'Thema / Titel / Beschreibung') ?>">Genomic Sphingomonas</span>;
                    <span class="element-cat" data-element="<?= lang('Category', 'Kategorie') ?>">Master-Thesis</span>.
                    <span class="element-time" data-element="<?= lang('Start and end date', 'Start- und Endatum') ?>">01.12.2021-30.09.2022</span>
                    (<span class="element-" data-element="<?= lang('Status') ?>">in progress</span>),
                    betreut von <span class="element-author" data-element="<?= lang('Responsible scientist', 'Verantwortliche Person') ?>">Bunk, B.</span>
                </p>
                <p data-visible="guests">
                    <span class="element-" data-element="<?= lang('Name of the guest', 'Name des Gastes') ?>">Herrera, Fabio</span>,
                    <span class="element-" data-element="<?= lang('Affiliation of the guest', 'Einrichtung des Gastes') ?>">Universidad de los Andes, Kolumbien</span>.
                    <span class="element-title" data-element="<?= lang('Topic / Title / Description', 'Thema / Titel / Beschreibung') ?>">BMBF-Projekt: Workshop AVAnce</span>;
                    <span class="element-cat" data-element="<?= lang('Category', 'Kategorie') ?>">Gastwissenschaftler:in</span>.
                    <span class="element-time" data-element="<?= lang('Start and end date', 'Start- und Endatum') ?>">15.-22.07.2022</span>,
                    betreut von <span class="element-author" data-element="<?= lang('Responsible scientist', 'Verantwortliche Person') ?>">Overmann, J.</span>
                </p>
                <p data-visible="software">
                    <span class="element-author" data-element="<?= lang('Author(s)', 'Autor(en)') ?>">Koblitz, J.</span>
                    (<span class="element-time" data-element="<?= lang('Date', 'Datum') ?>">2020</span>)
                    <span class="element-title" data-element="<?= lang('Title', 'Titel') ?>">MetaboMAPS: Pathway Sharing and Multi-omics Data Visualization in Metabolic Context</span>
                    (Version <span class="element-" data-element="<?= lang('Version') ?>">1.1</span>)
                    [<span class="element-cat" data-element="<?= lang('Type of software', 'Art der Software') ?>">Computer software</span>].
                    <span class="element-" data-element="<?= lang('Publication venue', 'Ort der Veröffentlichung') ?>">Zenodo</span>.
                    DOI: https://doi.org/<span class="element-link" data-element="<?= lang('DOI') ?>">10.5281/zenodo.3742817</span>
                </p>
                <p data-visible="review">
                    <span class="element-author" data-element="<?= lang('Scientist', 'Wissenschaftler:in') ?>">Pester, M.</span>
                    Reviewer for
                    <span class="element-title" data-element="Journal ">Frontiers in Microbiology</span>.
                    <span class="element-time" data-element="<?= lang('Date', 'Datum') ?>">October 2021</span>.
                </p>
                <p data-visible="editorial">
                    <span class="element-author" data-element="<?= lang('Scientist', 'Wissenschaftler:in') ?>">Thiel, V.</span>
                    Mitglied des Editorial Board von <span class="element-title" data-element="Journal ">Microganisms</span>
                    (<span class="element-cat" data-element="<?= lang('Details', 'Details') ?> ">Guest Editor for Special Issue 'Phototrophic Bacteria 2.0'</span>),
                    von <span class="element-time" data-element="<?= lang('Start date', 'Startdatum') ?>">Juli 2022</span> bis <span class="element-time" data-element="<?= lang('End date', 'Enddatum') ?>">März 2023</span>.
                </p>
                <p data-visible="grant-rev">
                    <span class="element-author" data-element="<?= lang('Scientist', 'Wissenschaftler:in') ?>">Sikorski, J.</span>
                    Reviewer of Grant Proposals:
                    <span class="element-title" data-element="Title/ Description/ Details">National Science Foundation USA</span>.
                    <span class="element-time" data-element="<?= lang('Date', 'Datum') ?>">October 2021</span>.
                </p>

                <p data-visible="thesis-rev">
                    <span class="element-author" data-element="<?= lang('Scientist', 'Wissenschaftler:in') ?>">Mast, Y.</span>
                    Reviewer for Doctoral Thesis:
                    <span class="element-title" data-element="Title/ Description/ Details ">Ira Handayani, Eberhard Karls, Universität Tübingen</span>.
                    <span class="element-time" data-element="<?= lang('Date', 'Datum') ?>">October 2021</span>.
                </p>


            </div>


            <form action="<?= $formaction ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">
                <input type="hidden" class="form-control disabled" name="values[type]" id="type" readonly>

                <div class="form-row row-eq-spacing" data-visible="article,preprint,magazine,book,chapter,dissertation,others">
                    <!-- <div class="col-sm">
                        <label for="type"><?= lang('Type of activity', 'Art der Aktivität') ?>:</label>
                    </div> -->
                    <div class="col-sm">
                        <label for="pubtype" class="required"><?= lang('Type of publication', 'Art der Publikation') ?>:</label>
                        <select class="form-control" name="values[pubtype]" id="pubtype" onchange="togglePubType(this.value)">
                            <option value="article">Journal article (refereed)</option>
                            <option value="book"><?= lang('Book', 'Buch') ?></option>
                            <option value="chapter"><?= lang('Book chapter', 'Buchkapitel') ?></option>
                            <option value="preprint">Preprint (non refereed)</option>
                            <!-- <option value="conference"><?= lang('Conference preceedings', 'Konfrenzbeitrag') ?></option> -->
                            <option value="magazine"><?= lang('Magazine article (non refereed)', 'Magazin-Artikel (non-refereed)') ?></option>
                            <option value="dissertation"><?= lang('Thesis') ?></option>
                            <option value="others"><?= lang('Others', 'Weiteres') ?></option>
                        </select>
                    </div>
                </div>


                <div class="form-group lang-<?= lang('en', 'de') ?>" data-visible="article,preprint,magazine,book,chapter,lecture,poster,dissertation,others,misc-once,misc-annual,students,guests,teaching,software">
                    <label for="title" class="required element-title">
                        <span data-visible="article,preprint,magazine,book,chapter,dissertation,others,lecture,poster,software"><?= lang('Title', 'Titel') ?></span>
                        <span data-visible="misc-once,misc-annual,students,guests,teaching"><?= lang('Topic / Title / Description', 'Thema / Titel / Beschreibung') ?></span>
                    </label>

                    <div class="form-group title-editor"><?= $form['title'] ?? '' ?></div>
                    <input type="text" class="form-control hidden" name="values[title]" id="title" required value="<?= val('title') ?>">
                </div>


                <div class="form-group" data-visible="article,preprint,magazine,book,chapter,dissertation,others,lecture,poster,misc-once,misc-annual,students,guests,teaching,software">
                    <label for="author" class="element-author">
                        <span data-visible="students,guests,teaching"><?= lang('Responsible scientist', 'Verantwortliche Person') ?></span>
                        <span data-visible="article,preprint,magazine,book,dissertation,others,chapter,lecture,poster,misc-once,misc-annual,software"><?= lang('Author(s)', 'Autor(en)') ?></span>
                        <?= lang('(in correct order, format: Last name, First name)', '(in korrekter Reihenfolge, Format: Nachname, Vorname)') ?>
                        <a class="" href="#author-help"><i class="fas fa-question-circle"></i> <?= lang('Help', 'Hilfe') ?></a>
                    </label>

                    <div class="float-right" data-visible="article,preprint" id="author-numbers">
                        <label for="first-authors"><?= lang('Number of first authors:', 'Anzahl der Erstautoren:') ?></label>
                        <input type="number" name="values[first_authors]" id="first-authors" value="<?= $first ?>" class="form-control form-control-sm w-50 d-inline-block" autocomplete="off">
                        <label for="last-authors"><?= lang('last authors:', 'Letztautoren:') ?></label>
                        <input type="number" name="values[last_authors]" id="last-authors" value="<?= $last ?>" class="form-control form-control-sm w-50 d-inline-block" autocomplete="off">
                    </div>
                    <div class="author-list">
                        <?= $authors ?>
                        <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                    </div>
                    <!-- <div class="float-right" id="department">
                        <label for="dept"><?= lang('Dept:', 'Abteilung:') ?></label>
                        <select name="values[dept]" id="dept" class="form-control form-control-sm w-150 d-inline-block">
                            <option value="">Abteilungsübergreifend</option>
                            <option value="BIDB" <?= $dept == 'BIDB' ? 'selected' : '' ?>>BIDB</option>
                            <option value="BUG" <?= $dept == 'BUG' ? 'selected' : '' ?>>BUG</option>
                            <option value="MIG" <?= $dept == 'MIG' ? 'selected' : '' ?>>MIG</option>
                            <option value="MIOS" <?= $dept == 'MIOS' ? 'selected' : '' ?>>MIOS</option>
                            <option value="MuTZ" <?= $dept == 'MuTZ' ? 'selected' : '' ?>>MuTZ</option>
                            <option value="MÖD" <?= $dept == 'MÖD' ? 'selected' : '' ?>>MÖD</option>
                            <option value="PFVI" <?= $dept == 'PFVI' ? 'selected' : '' ?>>PFVI</option>
                            <option value="NFG" <?= $dept == 'NFG' ? 'selected' : '' ?>>NFG</option>
                            <option value="Services" <?= $dept == 'Services' ? 'selected' : '' ?>>Services</option>
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
                        'Please double click on every ' . AFFILIATION . ' author in the list above, to mark them as affiliated. Only affiliated authors will receive points and are shown in reports.',
                        'Bitte doppelklicken Sie auf jeden ' . AFFILIATION . '-Autor in der Liste oben, um ihn als zugehörig zu markieren. Nur zugehörige Autoren erhalten Punkte und werden in Berichten berücksichtigt.'
                    ) ?>
                </div>


                <div class="form-row row-eq-spacing" data-visible="students,guests,teaching">
                    <div class="col" data-visible="students,guests">
                        <label for="guest-name" class="required element-other">
                            <?= lang('Name of the', 'Name des') ?>
                            <span data-visible="guests"><?= lang('guest', 'Gastes') ?></span>
                            <span data-visible="students"><?= lang('student', 'Studierenden') ?></span>
                            <?= lang('(last name, given name)', '(Nachname, Vorname)') ?>
                        </label>
                        <input type="text" class="form-control" name="values[name]" id="guest-name" required value="<?= val('name') ?>">
                    </div>
                    <div class="col">
                        <label for="guest-affiliation" class="required element-other"><?= lang('Affiliation (Name, City, Country)', 'Einrichtung (Name, Ort, Land)') ?></label>
                        <input type="text" class="form-control" name="values[affiliation]" id="guest-affiliation" required value="<?= val('affiliation') ?>">
                    </div>
                    <div class="col-sm-2" data-visible="students,guests">
                        <label for="guest-academic_title"><?= lang('Academ. title', 'Akadem. Titel') ?></label>
                        <input type="text" class="form-control" name="values[academic_title]" id="guest-academic_title" value="<?= val('academic_title') ?>">
                    </div>
                    <div class="col-sm-5" data-visible="teaching">
                        <label for="teaching-sws" class="required"><?= lang('SWS (Semesterwochenstunden)') ?></label>
                        <input type="number" step="0.1" class="form-control" name="values[sws]" id="teaching-sws" value="<?= val('sws') ?>" required>
                    </div>
                </div>

                <div class="form-row row-eq-spacing " data-visible="article,preprint,magazine,book,dissertation,others,chapter">
                    <div class="col-sm">
                        <label for="year" class="required element-time">Year</label>
                        <input type="number" min="1901" max="2155" step="1" class="form-control" name="values[year]" id="year" required value="<?= val('year') ?>">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="month" class="required ">Month</label>
                        <input type="number" min="1" max="12" step="1" class="form-control" name="values[month]" id="month" required value="<?= val('month') ?>">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="day" class="">Day</label>
                        <input type="number" min="1" max="31" step="1" class="form-control" name="values[day]" id="day" value="<?= val('day') ?>">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                </div>
                <div class="form-row row-eq-spacing" data-visible="students,guests">
                    <div class="col-sm" data-visible="guests">
                        <label for="category-guest" class="required element-cat"><?= lang('Category', 'Kategorie') ?></label>
                        <select name="values[category]" id="category-guest" class="form-control" required>
                            <option value="guest scientist" <?= val('category') == 'guest scientist' ? 'selected' : '' ?>><?= lang('Guest Scientist', 'Gastwissenschaftler:in') ?></option>
                            <!-- <option value="mandatory internship" <?= val('category') == 'Pflichtpraktikum im Rahmen des Studium' ? 'selected' : '' ?>>Pflichtpraktikum im Rahmen des Studium')?></option> -->
                            <option value="lecture internship" <?= val('category') == 'lecture internship' ? 'selected' : '' ?>><?= lang('Lecture Internship', 'Vorlesung und Laborpraktikum') ?></option>
                            <option value="student internship" <?= val('category') == 'student internship' ? 'selected' : '' ?>><?= lang('Student Internship', 'Schülerpraktikum') ?></option>
                        </select>
                    </div>
                    <div class="col-sm" data-visible="students">
                        <label for="category-students" class="required element-cat"><?= lang('Category', 'Kategorie') ?></label>
                        <select name="values[category]" id="category-students" class="form-control" required>
                            <option value="doctoral thesis" <?= val('category') == 'doctoral thesis' ? 'selected' : '' ?>><?= lang('Doctoral Thesis', 'Doktorand:in') ?></option>
                            <option value="master thesis" <?= val('category') == 'master thesis' ? 'selected' : '' ?>><?= lang('Master Thesis', 'Master-Thesis') ?></option>
                            <option value="bachelor thesis" <?= val('category') == 'bachelor thesis' ? 'selected' : '' ?>><?= lang('Bachelor Thesis', 'Bachelor-Thesis') ?></option>
                        </select>
                    </div>
                    <div class="col-sm" data-visible="students">
                        <label for="details"><?= lang('Details (scholarship, etc.)', 'Details (Stipendium, etc.)') ?></label>
                        <input type="text" class="form-control" name="values[details]" id="details" value="<?= val('details') ?>">
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="lecture,poster,misc-once,misc-annual,students,guests,teaching,software">
                    <div class="col-sm" data-visible="lecture">
                        <label class="required element-cat" for="lecture_type"><?= lang('Type of lecture', 'Art des Vortrages') ?></label>
                        <select name="values[lecture_type]" id="lecture_type" class="form-control" autocomplete="off">
                            <option value="short" <?= val('lecture_type') == 'short' ? 'selected' : '' ?>>short (15-25 min.)</option>
                            <option value="long" <?= val('lecture_type') == 'long' ? 'selected' : '' ?>>long (> 30 min.)</option>
                            <option value="repetition" <?= val('lecture_type' == 'repetition' || $copy) ? 'selected' : '' ?>>repetition</option>
                        </select>
                    </div>

                    <div class="col-sm" data-visible="lecture">
                        <label class="" for="lecture_type"><?= lang('Invited lecture') ?></label>
                        <select name="values[invited_lecture]" id="invited_lecture" class="form-control" autocomplete="off">
                            <option value="0" <?= val('invited_lecture', false) ? '' : 'selected' ?>><?= lang('No', 'Nein') ?></option>
                            <option value="1" <?= val('invited_lecture', false) ? 'selected' : '' ?>><?= lang('Yes', 'Ja') ?></option>
                        </select>
                    </div>
                    <div class="col-sm">
                        <label class="required element-time" for="date_start"><?= lang('Date', 'Datum') ?></label>
                        <input type="date" class="form-control" name="values[start]" id="date_start" required value="<?= valueFromDateArray(val('start')) ?>">
                    </div>
                    <div class="col-sm" data-visible="poster,misc-once,misc-annual">
                        <label for="date_end" class="element-time">
                            <?= lang('End', 'Ende') ?>
                            <span data-visible="poster,misc-once">
                                <?= lang('(leave empty if event was only one day)', '(leer lassen falls nur ein Tag)') ?>
                            </span>
                            <span data-visible="misc-annual">
                                <?= lang('(leave empty if activity in progress)', '(leer lassen solange die Aktivität im Gange ist)') ?>
                            </span>
                        </label>
                        <input type="date" class="form-control" name="values[end]" id="date_end" value="<?= valueFromDateArray(val('end')) ?>">
                    </div>
                    <div class="col-sm" data-visible="students,guests,teaching">
                        <label for="students_end" class="required element-time"><?= lang('End', 'Ende') ?></label>
                        <input type="date" class="form-control" name="values[end]" id="students_end" value="<?= valueFromDateArray(val('end')) ?>" required>
                        <div id="end-question" data-visible="students">
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

                    <div class="col-sm" data-visible="software">
                        <label class="element-cat" for="software_type"><?= lang('Type of software', 'Art der Software') ?></label>
                        <select name="values[software_type]" id="software_type" class="form-control">
                            <option value="" <?= empty(val('software_type')) ? 'selected' : '' ?>>Not specified</option>
                            <option value="software" <?= val('software_type') == 'software' ? 'selected' : '' ?>>Computer Software</option>
                            <option value="database" <?= val('software_type') == 'database' ? 'selected' : '' ?>>Database</option>
                            <option value="dataset" <?= val('software_type') == 'dataset' ? 'selected' : '' ?>>Dataset</option>
                            <option value="webtool" <?= val('software_type') == 'webtool' ? 'selected' : '' ?>>Website</option>
                            <option value="report" <?= val('software_type') == 'report' ? 'selected' : '' ?>>Report</option>
                        </select>
                    </div>
                </div>


                <div class="form-row row-eq-spacing" data-visible="software">
                    <div class="col-sm-">
                        <label class="element-other" for="software_venue"><?= lang('Publication venue, e.g. GitHub, Zenodo ...', 'Ort der Veröffentlichung, z.B. GitHub, Zenodo ...') ?></label>
                        <input type="text" class="form-control" name="values[software_venue]" id="software_venue" value="<?= val('software_venue') ?>">
                    </div>

                    <div class="col-sm">
                        <label class="element-link" for="software_link"><?= lang('Complete link to the software/database', 'Kompletter Link zur Software/Datenbank') ?></label>
                        <input type="text" class="form-control" name="values[link]" id="software_link" value="<?= val('link') ?>">
                    </div>

                    <!-- <div class="col-sm">
                        <label for="software_doi" class="element-link">DOI</label>
                        <input type="text" class="form-control" name="values[doi]" value="<?= val('doi') ?>" id="software_doi" placeholder="10.5281/zenodo.3742817">
                    </div> -->

                    <div class="col-sm-2">
                        <label class="element-other" for="software_version"><?= lang('Version') ?></label>
                        <input type="text" class="form-control" name="values[version]" id="software_version" value="<?= val('version') ?>">
                    </div>

                </div>


                <div class="form-row row-eq-spacing" data-visible="lecture,poster,misc-once,misc-annual">
                    <div class="col-sm" data-visible="misc-once,misc-annual">
                        <label class="required" for="iteration"><?= lang('Iteration', 'Häufigkeit') ?></label>
                        <select name="values[iteration]" id="iteration" class="form-control" value="<?= val('iteration') ?>" onchange="togglePubType('misc-'+this.value)">
                            <option value="once">once</option>
                            <option value="annual">annual</option>
                        </select>
                    </div>
                    <div class="col-sm" data-visible="lecture,poster">
                        <label for="conference" class="element-other"><?= lang('Conference', 'Konferenz') ?></label>
                        <input type="text" class="form-control" name="values[conference]" id="conference" placeholder="VAAM 2022" value="<?= val('conference') ?>">
                    </div>
                    <div class="col-sm">
                        <label for="location" class="element-other"><?= lang('Location', 'Ort') ?></label>
                        <input type="text" class="form-control" name="values[location]" id="location" placeholder="online" value="<?= val('location') ?>">
                    </div>
                </div>

                <!-- 
                <div class="form-group">
                    <label class="required" for="publication"><?= lang('Date of publication (print preferred)', 'Datum der Publikation (bevorzugt Print)') ?></label>
                    <input type="date" class="form-control" name="values[publication]" id="date_publication" required>
                </div> -->

                <div class="form-row row-eq-spacing" data-visible="article,preprint">
                    <div class="col-sm">
                        <label for="journal" class="element-cat required">Journal</label>
                        <div class="input-group">
                            <input type="text" class="form-control disabled" name="values[journal]" value="<?= val('journal') ?>" id="journal" list="journal-list" required readonly>
                            <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                            <div class="input-group-append" data-toggle="tooltip" data-title="<?= lang('Edit Journal', 'Bearbeite Journal') ?>">
                                <a class="btn" href="#journal-select"><i class="fas fa-edit"></i></a>
                            </div>
                        </div>

                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>

                    <div class="col-sm">
                        <label for="journal_id" class="element-cat required">Journal ID</label>
                        <input type="text" class="form-control disabled" name="values[journal_id]" value="<?= val('journal_id') ?>" id="journal_id" required readonly>
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>

                    <div class="col-sm">
                        <label for="issn" class="element-cat">ISSN (<?= lang('space-seperated', 'getrennt durch Leerzeichen') ?></label>
                        <?php
                        $issn = "";
                        if (isset($form['issn'])) {
                            $issn = $form['issn'];
                            try {
                                $issn = $issn->bsonSerialize();
                            } catch (\Throwable $th) {
                            }
                            if (is_array($issn)) $issn = implode(' ', $issn);
                        }
                        ?>

                        <input type="text" class="form-control disabled" name="values[issn]" value="<?= $issn ?>" id="issn" readonly>
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="magazine">
                    <div class="col-sm">
                        <label for="magazine" class="element-cat">Magazine</label>
                        <input type="text" class="form-control" name="values[magazine]" value="<?= val('magazine') ?>" id="magazine">
                    </div>
                    <div class="col-sm">
                        <label for="link" class="element-link">Link</label>
                        <input type="text" class="form-control" name="values[link]" value="<?= val('link') ?>" id="link">
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="article,book,chapter">
                    <div class="col-sm" data-visible="article">
                        <label for="issue" class="element-other">Issue</label>
                        <input type="text" class="form-control" name="values[issue]" value="<?= val('issue') ?>" id="issue">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="volume" class="element-other">Volume</label>
                        <input type="text" class="form-control" name="values[volume]" value="<?= val('volume') ?>" id="volume">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="pages" class="element-other">Pages</label>
                        <input type="text" class="form-control" name="values[pages]" value="<?= val('pages') ?>" id="pages">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                </div>


                <div class="form-row row-eq-spacing" data-visible="book,chapter,dissertation">
                    <div class="col-sm" data-visible="chapter">
                        <label for="book" class="required element-cat"><?= lang('Book title', 'Buchtitel') ?></label>
                        <input type="text" class="form-control" name="values[book]" value="<?= val('book') ?>" id="book" required>
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm" data-visible="book,chapter">
                        <label for="edition" class="element-other">Edition</label>
                        <input type="number" class="form-control" name="values[edition]" value="<?= val('edition') ?>" id="edition">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="publisher" class="element-other">
                            <span data-visible="book,chapter"><?= lang('Publisher', 'Verlag') ?></span>
                            <span data-visible="book,chapter,dissertation"><?= lang('University', 'Universität') ?></span>
                        </label>
                        <input type="text" class="form-control" name="values[publisher]" value="<?= val('publisher') ?>" id="publisher">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm">
                        <label for="city" class="element-other"><?= lang('Location', 'Ort') ?></label>
                        <input type="text" class="form-control" name="values[city]" value="<?= val('city') ?>" id="city">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                </div>
                <div class="form-group" data-visible="chapter">
                    <label for="editor" class="required element-author"><?= lang('Editor(s) (in correct order)', 'Editor(en) (in korrekter Reihenfolge)') ?></label>
                    <div class="author-list">
                        <?= $editors ?>
                        <input type="text" placeholder="Add editor ..." onkeypress="addAuthor(event, this, true);" id="add-editor" list="scientist-list">
                    </div>
                </div>

                <div class="form-row row-eq-spacing" data-visible="article,preprint,magazine,book,chapter,lecture,poster,dissertation,others,misc-once,misc-annual,software">
                    <div class="col-sm">
                        <label for="doi" class="element-link">DOI</label>
                        <?php if (empty($form)) { ?>
                            <input type="text" class="form-control" name="values[doi]" value="<?= val('doi') ?>" id="doi">
                            <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                        <?php } else { ?>
                            <div class="input-group">
                                <input type="text" class="form-control" name="values[doi]" value="<?= val('doi') ?>" id="doi">
                                <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                                <div class="input-group-append" data-toggle="tooltip" data-title="<?= lang('Retreive updated information via DOI', 'Aktualisiere die Daten via DOI') ?>">
                                    <button class="btn" type="button" onclick="getPubData(event, this)"><i class="fas fa-rotate"></i></button>
                                    <span class="sr-only">
                                        <?= lang('Retreive updated information via DOI', 'Aktualisiere die bibliographischen Daten via DOI') ?>
                                    </span>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                    <div class="col-sm" data-visible="article,preprint,magazine,book,chapter,others">
                        <label for="pubmed">Pubmed</label>
                        <input type="number" class="form-control" name="values[pubmed]" value="<?= val('pubmed') ?>" id="pubmed">
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>
                    <div class="col-sm" data-visible="book,chapter,dissertation">
                        <label for="isbn">ISBN</label>
                        <input type="text" class="form-control" name="values[isbn]" value="<?= val('isbn') ?>" id="pubmed">
                    </div>
                    <div class="col-sm" data-visible="others">
                        <label for="doc_type"><?= lang('Document type', 'Dokumententyp') ?></label>
                        <input type="text" class="form-control" name="values[doc_type]" value="<?= val('doc_type') ?>" id="doctype" placeholder="Report">
                    </div>
                </div>

                <div class="form-group" data-visible="article,preprint,book,chapter">
                    <div class="custom-radio d-inline-block" id="open_access-div">
                        <input type="radio" id="open_access-0" value="false" name="values[open_access]" <?= val('open_access', false) ? '' : 'checked' ?>>
                        <label for="open_access-0"><i class="icon-closed-access text-danger"></i> Closed access</label>
                    </div>
                    <div class="custom-radio d-inline-block ml-20" id="open_access-div">
                        <input type="radio" id="open_access" value="true" name="values[open_access]" <?= val('open_access', false) ? 'checked' : '' ?>>
                        <label for="open_access"><i class="icon-open-access text-success"></i> Open access</label>
                    </div>
                </div>

                <div class="form-group" data-visible="article">
                    <div class="custom-checkbox <?= isset($_GET['epub']) ? 'text-danger' : '' ?>" id="epub-div">
                        <input type="checkbox" id="epub" value="1" name="values[epub]" <?= (!isset($_GET['epub']) && val('epub', false)) ? 'checked' : '' ?>>
                        <label for="epub">Epub ahead of print</label>
                    </div>
                </div>
                <div class="form-group" data-visible="article">
                    <div class="custom-checkbox" id="correction-div">
                        <input type="checkbox" id="correction" value="1" name="values[correction]" <?= val('correction', false) ? 'checked' : '' ?>>
                        <label for="correction"><?= lang('Correction') ?></label>
                    </div>
                </div>

                <div class="" data-visible="review,editorial,grant-rev,thesis-rev">

                    <div class="form-row row-eq-spacing-sm">
                        <div class="col-sm-3">
                            <label class="required" for="role-input">
                                <?= lang('Role', 'Rolle') ?>
                            </label>
                            <select class="form-control" id="role-input" name="values[role]" required autocomplete="off" onchange="togglePubType(this.value)">
                                <option value="review" disabled selected>-- <?= lang('Select role', 'Wähle deine Rolle') ?> --</option>
                                <option value="review" <?= strtolower(val('role')) == 'review' ? 'selected' : '' ?>>Reviewer</option>
                                <option value="editorial" <?= strtolower(val('role')) == 'editorial' ? 'selected' : '' ?>>Editorial board</option>
                                <option value="grant-rev" <?= strtolower(val('role')) == 'grant-rev' ? 'selected' : '' ?>>Grant proposal</option>
                                <option value="thesis-rev" <?= strtolower(val('role')) == 'thesis-rev' ? 'selected' : '' ?>>Thesis review</option>
                            </select>

                        </div>
                        <div class="col-sm" data-visible="review,editorial">
                            <label class="required element-title" for="journal-input">
                                <?= lang('Journal') ?>
                            </label>
                            <div class="input-group">
                            <input type="text" class="form-control disabled" name="values[journal]" value="<?= val('journal') ?>" id="journal-input" list="journal-list" required readonly>
                            <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                            <div class="input-group-append" data-toggle="tooltip" data-title="<?= lang('Edit Journal', 'Bearbeite Journal') ?>">
                                <a class="btn" href="#journal-select"><i class="fas fa-edit"></i></a>
                            </div>
                        </div>
                            <!-- <input type="text" class="form-control" placeholder="Journal" id="journal-input" value="<?= val('journal') ?>" name="values[journal]" list="journal-list" required> -->
                        </div>
                        <div class="col-sm">
                        <label for="journal_rev_id" class="element-cat required">Journal ID</label>
                        <input type="text" class="form-control disabled" name="values[journal_id]" value="<?= val('journal_id') ?>" id="journal_rev_id" required readonly>
                        <!-- <i class="fas fa-arrow-rotate-left" onclick="resetInput(this)"></i> -->
                    </div>

                        <div class="col-sm" data-visible="grant-rev,thesis-rev">
                            <label class="required element-title" for="title-input">
                                <?= lang('Title/Description/Details', 'Titel/Beschreibung/Details') ?>
                            </label>
                            <input type="text" class="form-control" id="title-input" value="<?= val('title') ?>" name="values[title]" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="required element-author" for="username">
                                <?= lang('Scientist', 'Wissenschaftler:in') ?>
                            </label>
                            <select class="form-control" id="username" name="values[user]" required autocomplete="off">
                                <?php
                                $userlist = $osiris->users->find([], ['sort' => ["last" => 1]]);
                                foreach ($userlist as $j) { ?>
                                    <option value="<?= $j['_id'] ?>" <?= $j['_id'] == ($form['user'] ?? $user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="reviewer-role" data-visible="review,grant-rev,thesis-rev">
                        <label class="required element-time" for="date">
                            <?= lang('Date', 'Datum') ?>
                        </label>
                        <input type="date" class="form-control date" name="values[start]" id="date" value="<?= valueFromDateArray(val('start')) ?>">
                        <small class="text-muted">
                            <?= lang('Only month and year are considered', 'Nur Monat und Jahr sind relevant') ?>
                        </small>
                    </div>

                    <div class="editor-role" data-visible="editorial">
                        <div class="form-row row-eq-spacing-sm">
                            <div class="col-sm">
                                <label class="required element-time" for="start">
                                    <?= lang('Beginning of editorial activity', 'Anfang der Editor-Tätigkeit') ?>
                                </label>
                                <input type="date" class="form-control start" name="values[start]" id="start" value="<?= valueFromDateArray(val('start')) ?>" required>
                            </div>
                            <div class="col-sm">
                                <label class=" element-time" for="end">
                                    <?= lang('End', 'Ende') ?>
                                </label>
                                <input type="date" class="form-control" name="values[end]" id="end" value="<?= valueFromDateArray(val('end')) ?>">
                            </div>
                            <div class="col-sm">
                                <label for="editor_type" class="element-cat">
                                    <?= lang('Details', 'Details') ?>
                                </label>
                                <input type="text" class="form-control" name="values[editor_type]" id="editor_type" value="<?= val('editor_type') ?>" placeholder="Guest Editor for Research Topic 'XY'">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <?php if (!empty($form) && isset($form['file']) && !empty($form['file']) && !$copy) { ?>
                    <p>
                        <?= lang('The following file is appended to this entry:', 'Die folgende Datei ist diesem Eintrag angehängt:') ?>
                        <a target="_blank" href="<?= ROOTPATH ?>/activities/view/<?= $id ?>/file" class="btn"><?= lang('FILE', 'DATEI') ?></a>
                        <?= lang('Uploading a new file will overwrite the existing one.', 'Wenn du eine neue Datei hochlädst, wird sie überschrieben.') ?>
                    </p>

                <?php } ?> -->
                <!-- 
                <div class="custom-file mb-20" id="file-input-div" data-visible="article,preprint,magazine,book,chapter,lecture,poster,misc-once,misc-annual">
                    <input type="file" id="file-input" name="file" accept=".pdf" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                    <label for="file-input"><?= lang('Append a file (in PDF format)', 'Hänge eine Datei an (im PDF-Format)') ?></label>
                </div>
                <div data-visible="article,preprint,magazine,book,chapter,lecture,poster,misc-once,misc-annual">
                    <input type="file" id="file-input" name="file" accept=".pdf" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                    <label for="file-input"><?= lang('Append a file (in PDF format)', 'Hänge eine Datei an (im PDF-Format)') ?></label>
                </div> -->

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
</script>

<?php if (!empty($form)) {

    $t = $form['type'];
    if ($t == 'publication') $t = $form['pubtype'];
    if ($t == 'students') $t = $form['category'] ?? 'doctoral thesis';
    if ($t == 'review') $t = $form['role'] ?? 'review';
    // dump($t);
?>
    <script>
        UPDATE = true
        togglePubType('<?= $t ?>');


        $('input').each(function(el) {
            el = $(this)
            if (!isEmpty(el.val()))
                el.attr("data-value", el.val())
        })


        $('[data-value]').on("update blur", function() {
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