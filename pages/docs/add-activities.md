

# <i class="icon-activity-plus text-osiris"></i>    Aktivitäten hinzufügen


##  Eine Aktivität mittels einer DOI hinzufügen

Am einfachsten ist es, eine Aktivität über eine DOI oder einer Pubmed-ID hinzuzufügen.
    Dafür trägst du die DOI bzw. Pubmed-ID in den Suchschlitz ein:

<div class="demo">
    <div class="form-group">
        <label for="doi">Suche über die DOI oder Pubmed-ID:</label>
        <div class="input-group">
            <input type="text" class="form-control" value="10.1093/nar/gkab961" name="doi"
                id="search-doi">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>
</div>

OSIRIS sucht die ID in einem von drei verschiedenen Services (CrossRef, DataCite, Pubmed) und erkennt automatisch, um welche Art von Aktivität es sich handelt. Aus CrossRef und Pubmed werden ausschließlich Publikationen abgefragt. Über DataCite können aber auch andere Aktivitäten abgefragt werden, beispielsweise Poster, Präsentationen oder Software-Pakete. Um eine DOI für eine solche Aktivität zu bekommen, kann man die Dateien bei einem Anbieter wie [Zenodo](https://zenodo.org/) hochladen. Wichtig ist dabei, dass die Metadaten korrekt hinterlegt werden (z.B. Autoren).

Wenn OSIRIS die Aktivität im externen Service gefunden hat, wird das Formular so gut es geht vorausgefüllt. Dies klappt mit DOIs meist besser als bei Pubmed, da bei letzterem Autoreninformationen oft nur unzureichend hinterlegt sind. In jedem Fall sollen <b>alle Informationen noch einmal manuell überprüft</b> und ggf. korrigiert werden. Beim Titel ist auch auf eine korrekte Rechtschreibung und Formatierung zu achten. Die Formatierung sollte nach Möglichkeit der Formatierung im Orginaltitel entsprechen.

Einige Felder können nicht automatisch ausgefüllt werden, beispielsweise die Information, ob eine Publikation Open Access ist oder nicht. Automatisch ausgefüllte Felder werden deshalb grün unterlegt, auch wenn die Daten in der öffentlichen Datenbank leer waren (beispielsweise bei fehlenden Seitenzahlen). Nicht markierte Felder müssen ggf. nachgetragen werden.

Falls eine Publikation abgerufen wird, deren Journal zurzeit noch nicht in der Datenbank vorhanden ist, öffnet sich das Fenster zur Journal-Auswahl. Mehr Infos dazu gibt es [weiter unten](#das-Journal-bearbeiten).

## Eine Aktivität manuell hinzufügen

Selbstverständlich kann eine Aktivität auch manuell hinzugefügt werden.

### Eine Kategorie auswählen

Beim manuellen Hinzufügen muss zuerst ausgewählt werden, um welche Art von Aktivität es sich handelt. 
Zurzeit unterstützt OSIRIS 8 verschiedene Kategorien. 
Zur Auswahl wird auf die entsprechende Schaltfläche geklickt:

<div class="demo">
    <div class="select-btns" id="select-btns">
        <button class="btn btn-select text-publication" id="publication-btn"><i class="far fa-lg text-publication fa-book-bookmark"></i>Publikation</button>
        <button class="btn btn-select text-poster" id="poster-btn"><i class="far fa-lg text-poster fa-presentation-screen"></i>Poster</button>
        <button class="btn btn-select text-lecture" id="lecture-btn"><i class="far fa-lg text-lecture fa-keynote"></i>Vorträge</button>
        <button class="btn btn-select text-review" id="review-btn"><i class="far fa-lg text-review fa-file-lines"></i>Reviews &amp; Editorials</button>
        <button class="btn btn-select text-teaching" id="teaching-btn"><i class="far fa-lg text-teaching fa-chalkboard-user"></i>Lehre</button>
        <button class="btn btn-select text-students" id="students-btn"><i class="far fa-lg text-students fa-user-graduate"></i>Studierende &amp; Gäste</button>
        <button class="btn btn-select text-software" id="software-btn"><i class="far fa-lg text-software fa-desktop"></i>Software &amp; Data</button>
        <button class="btn btn-select text-misc" id="misc-btn"><i class="far fa-lg text-misc fa-icons"></i>Misc</button>
    </div>
</div>

Nachdem eine Aktivität ausgewählt wurde, öffnet sich ein Formular mit allen Datenfeldern, die für die Aktivität relevant sein können. Benötigte Datenfelder sind mit <span class="text-danger">*</span> markiert und können nicht leer gelassen werden.

Bei den Aktivitäten 
<span class="text-publication">Publikation</span>, <span class="text-review">Reviews &amp; Editorials</span>, <span class="text-students">Studierende &amp; Gäste</span>, <span class="text-misc">Misc</span> und <span class="text-software">Software &amp; Data</span> gibt es Unterkategorien, die einen Einfluss auf die Datenfelder haben. So kann man bei <span class="text-publication">Publikationen</span> noch den Publikationstyp auswählen, beispielsweise Journalartikel, Buch oder Dissertation. Bei den <span class="text-review">Reviews</span> kann man zusätzlich zu Peer-Reviews auch Thesis- und Grant-Reviews hinzufügen, sowie Mitgliedschaften im Editorial Board.

Die Unterkategorien werden mit ähnlichen Knöpfen wie oben zu sehen sind gesteuert und können erst ausgewählt werden, nachdem die Oberkategorie ausgewählt wurde. 


### Beispiele nutzen

Ganz oben im Formular gibt es einen kleinen Knopf, mit dem die <button class="btn btn-osiris btn-sm">Beispiele</button> umgeschaltet werden können. Bei aktivierten Beispielen erscheint ein formatierter Text über dem Formular. Dieser Text ist abhängig, von der ausgewählten Aktivitätskategorie (und eventuell Unterkategorie). Hier als Beispiel ein Journalartikel:

<div class="demo">
    <span class="element-author" data-element="Autor(en)">Spring, S., Rohde, M., Bunk, B., Spröer, C., Will, S. E. and Neumann-Schaal, M. </span>
    (<span class="element-time" data-element="Jahr">2022</span>)
    <span class="element-title" data-element="Titel">New insights into the energy metabolism and taxonomy of Deferribacteres revealed by the characterization of a new isolate from a hypersaline microbial mat</span>.
    <span class="element-cat" data-element="Journal">Environmental microbiology </span>
    <span data-element="Issue, Volume, Pages">24(5):2543-2575</span>.
    DOI: http://dx.doi.org/<span class="element-link" data-element="DOI">10.1111/1462-2920.15999</span>
</div>

Der Text ist einerseits farblich markiert. Die entsprechenden Stellen im Formular, in denen die dafür relevanten Daten eingetragen werden, sind im gleichen Farbschema markiert. So kann auf einen Blick zugeordnet werden, welche Information aus dem Formular in die formatierte Aktivität an welcher Stelle einfließt. Wenn man über ein markiertes Beispiel hovert, bekommt man des Weiteren den genauen Namen des Datenfeldes als Tooltip angezeigt.


### Den Titel formatieren

Der Titel kann über einen Rich-Text-Editor bearbeitet und formatiert werden. Dabei ist es wichtig, dass Rechtschreibung und Formatierung mit der Originalarbeit übereinstimmen. Unterstützt werden dabei folgende Formatierungen: kursiv, unterstrichen, hochgestellt und tiefgestellt. Außerdem gibt es Shortcuts für die gängigsten griechischen Sonderzeichen. Hier kann mit dem Editor etwas herumgespielt werden:

<div class="demo">
    <label for="title" class="required element-title">
        <span style="">Titel</span>
    </label>
    <div class="form-group title-editor"></div>
    <input type="text" class="form-control hidden" name="values[title]" id="title" required="" value="">   
</div>

### Autoren bearbeiten

Um die Autorenliste zu bearbeiten, steht ein einfacher Autoreneditor zur Verfügung. Hier ist ein funktionierender Editor zum Ausprobieren:

<div class="demo">
    <label for="author" class="element-author">
        Autor(en)
        (in korrekter Reihenfolge, Format: Nachname, Vorname)
    </label>
    <div class="author-list ui-sortable">
        <div class="author author-aoi ui-sortable-handle" ondblclick="toggleAffiliation(this);">
            Koblitz, Julia<input type="hidden" name="values[authors][]" value="Koblitz;Julia;1">
            <a onclick="removeAuthor(event, this);">×</a>
        </div>
        <input type="text"
            placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list"
            class="ui-sortable-handle">
    </div>
</div>


Um einen **Autor hinzuzufügen**, musst du ihn in das Feld eintragen, das mit "Add author ..." gekennzeichnet ist. Nutze dafür bitte das Format <code>Nachname, Vorname</code>, damit OSIRIS die Autoren korrekt zuordnen kann. DSMZ-Autoren werden in einer Liste vorgeschlagen. Ein Autor aus der Liste wird automatisch zur DSMZ zugeordnet.

Um einen **Autor zu entfernen**, musst du auf das &times; hinter seinem Namen klicken.

Um die **Autorenreihenfolge zu ändern**, kannst du einen Autoren nehmen und ihn mittels Drag & Drop an die gewünschte Position ziehen.

Um einen **Autor zur DSMZ zugehörig zu markieren**, kannst du ihn einfach mit Doppelklick anklicken. Der Name wird dann blau markiert und das Wort DSMZ taucht davor auf. Es ist wichtig für die Berichterstattung, dass alle Autoren ihrer Zugehörigkeit nach markiert sind! Wenn Autoren zwar Beschäftigte der DSMZ sind, es aber zum Zeitpunkt der Aktivität nicht waren, dürfen sie nicht als DSMZ-Autor markiert werden!

Verschrieben? Ein Autor wird nicht korrekt einem Nutzer zugeordnet? Nachdem du den Datensatz hinzugefügt hast, kannst du die Autorenliste im Detail noch einmal bearbeiten. Lies dazu den [folgenden Abschnitt](#der-autoren-editor).

### Der Autoren-Editor

Nachdem eine Aktivität hinzugefügt wurde, steht ein detaillierter Autoren-Editor zur Verfügung. Dazu geht man auf der Übersichtsseite auf folgenden Knopf:

<div class="demo">
    <h2>Authors</h2>
    <span class="btn btn-osiris active">
        <i class="fa-regular fa-user-pen"></i>
        Autorenliste bearbeiten
    </span>
</div>

Bei editierten Büchern gibt es ein gleichen Pendant für Editoren.

Im Autoreneditor öffnet sich nun eine Tabelle, mit allen Details zu den Autoren einer Aktivität. Diese Tabelle kann folgendermaßen aussehen:

<div class="demo">
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>Last name</th>
                <th>First name</th>
                <th>Position</th>
                <th>DSMZ</th>
                <th>Username</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="author-detail-editor" class="ui-sortable">
            <tr>
                <td>
                    <i class="fas fa-grip-dots-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[0][last]" type="text" class="form-control" value="Becker">
                </td>
                <td>
                    <input name="authors[0][first]" type="text" class="form-control" value="Patrick">
                </td>
                <td>
                    <select name="authors[0][position]" class="form-control">
                        <option value="first" selected="">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-0" name="authors[0][aoi]" value="1">
                        <label for="checkbox-0" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[0][user]" type="text" class="form-control" list="user-list" value="">
                    <input name="authors[0][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="fas fa-grip-dots-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[1][last]" type="text" class="form-control" value="Kirstein">
                </td>
                <td>
                    <input name="authors[1][first]" type="text" class="form-control" value="Sarah">
                </td>
                <td>
                    <select name="authors[1][position]" class="form-control">
                        <option value="first" selected="">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-1" name="authors[1][aoi]" value="1">
                        <label for="checkbox-1" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[1][user]" type="text" class="form-control" list="user-list" value="sak20">
                    <input name="authors[1][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="fas fa-grip-dots-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[3][last]" type="text" class="form-control" value="Koblitz">
                </td>
                <td>
                    <input name="authors[3][first]" type="text" class="form-control" value="Julia">
                </td>
                <td>
                    <select name="authors[3][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle" selected="">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-3" name="authors[3][aoi]" value="1" checked="">
                        <label for="checkbox-3" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[3][user]" type="text" class="form-control" list="user-list" value="juk20">
                    <input name="authors[3][approved]" type="hidden" class="form-control" value="1">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="fas fa-grip-dots-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[4][last]" type="text" class="form-control" value="Buschen">
                </td>
                <td>
                    <input name="authors[4][first]" type="text" class="form-control" value="Ramona">
                </td>
                <td>
                    <select name="authors[4][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle" selected="">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-4" name="authors[4][aoi]" value="1">
                        <label for="checkbox-4" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[4][user]" type="text" class="form-control" list="user-list" value="">
                    <input name="authors[4][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="fas fa-grip-dots-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[20][last]" type="text" class="form-control" value="Neumann-Schaal">
                </td>
                <td>
                    <input name="authors[20][first]" type="text" class="form-control" value="Meina">
                </td>
                <td>
                    <select name="authors[20][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding" selected="">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-20" name="authors[20][aoi]" value="1" checked="">
                        <label for="checkbox-20" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[20][user]" type="text" class="form-control" list="user-list" value="men17">
                    <input name="authors[20][approved]" type="hidden" class="form-control" value="1">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="fas fa-grip-dots-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[21][last]" type="text" class="form-control" value="Rabus">
                </td>
                <td>
                    <input name="authors[21][first]" type="text" class="form-control" value="Ralf">
                </td>
                <td>
                    <select name="authors[21][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last" selected="">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-21" name="authors[21][aoi]" value="1">
                        <label for="checkbox-21" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[21][user]" type="text" class="form-control" list="user-list" value="">
                    <input name="authors[21][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr id="last-row">
                <td></td>
                <td colspan="6">
                    <button class="btn" type="button" onclick="addAuthorRow()"><i class="fas fa-plus"></i> Autor
                        hinzufügen</button>
                </td>
            </tr>
        </tfoot>
    </table>
    <script>
        var counter = 10;
        function addAuthorRow() {
            counter++;
            var tr = $('<tr>')
            tr.append('<td><i class="fas fa-grip-dots-vertical text-muted handle"></i></td>')
            tr.append('<td><input name="authors[' + counter + '][last]" type="text" class="form-control"></td>')
            tr.append('<td><input name="authors[' + counter + '][first]" type="text" class="form-control"></td>')
            tr.append('<td><select name="authors[' + counter + '][position]" class="form-control"><option value="first">first</option><option value="middle" selected>middle</option><option value="corresponding">corresponding</option><option value="last">last</option></select></td>')
            tr.append('<td><div class="custom-checkbox"><input type="checkbox" id="checkbox-' + counter + '" name="authors[' + counter + '][aoi]" value="1"><label for="checkbox-' + counter + '" class="blank"></label></div></td>')
            tr.append('<td> <input name="authors[' + counter + '][user]" type="text" class="form-control" list="user-list"></td>')
            var btn = $('<button class="btn" type="button">').html('<i class="fas fa-trash-alt"></i>').on('click', function() {
                $(this).closest('tr').remove();
            });
            tr.append($('<td>').append(btn))
            $('#author-detail-editor').append(tr)
        }
        $(document).ready(function() {
            $('#author-detail-editor').sortable({
                handle: ".handle",
            });
        })
    </script>
</div>


Gleich am Anfang jeder Zeile gibt es einen sog. Handle (<i class="grip-dots-vertical"></i>), mit dem die Reihenfolge der Autoren durch Drag & Drop verändert werden kann. Es folgen Vor- und Nachname des Autoren. Der Nachname ist ein Pflichtfeld, der Vorname kann (beispielsweise bei Konsortien) weggelassen werden. 

Die Position des Autors kann durch ein Dropdown-Menü ausgewählt werden. Auf diese Weise können mehrere Autoren als Erst- bzw. Letztautoren festgelegt werden (bei geteilter Autorenschaft). Hier kann auch ein Corresponding author festgelegt werden, falls dieser weder Erst- noch Letztautor ist. 

Es folgt eine Checkbox, in der angegeben wird, ob der Autor beim Zeitpunkt der Veröffentlichung Angehöriger der DSMZ war oder nicht. Im nächsten Feld kann der Nutzeraccount hinterlegt werden, mit dem dieser Autor verknüpft ist. Im oben gezeigten Beispiel ist zu sehen, dass man nicht der DSMZ zugehörig sein muss, um die Aktivitäten mit dem Nutzerprofil zu verknüpfen. Die zweite Erstautorin ist einem Nutzerkonto zugeordnet, war aber zum Zeitpunkt der Arbeit nicht bei der DSMZ angestellt. Die Aktivität ist mit ihrem Account verknüpft und wird in ihrem Profil dargestellt, sie wird jedoch im Report nicht als DSMZ-Autorin markiert und bekommt für die Aktivität keine Coins.

Zu guter Letzt ist eine Schaltfläche zu sehen (<i class="fas fa-trash-alt"></i>), mit der ein Autor komplett gelöscht werden kann.

Am Fuß der Tabelle können über die Schaltfläche <span class="btn btn-sm">+ Autor hinzufügen</span> weitere Autoren der Tabelle hinzugefügt werden. Neue Zeilen mit Autoren haben die gleichen Funktionen wie oben gezeigt.


### Das Journal bearbeiten

Journale sind bei <span class="text-publication">Journalartikeln</span> und <span class="text-review">Reviews &amp; Editorials</span> relevant. 

Aus Gründen der Standardisierung kann ein Journal nicht mehr als Freitext-Feld eingetragen werden. Stattdessen sind folgende geschützte Formularfelder zu finden:

<div class="demo">
    <div class="form-row row-eq-spacing" data-visible="article,preprint" style="">
        <div class="col-sm">
            <label for="journal" class="element-cat required">Journal</label>
            <div class="input-group">
                <input type="text" class="form-control disabled" name="values[journal]" value="mSystems" id="journal" list="journal-list" required="" readonly="" data-value="mSystems">
                <div class="input-group-append" data-toggle="tooltip" data-title="Bearbeite Journal">
                    <a class="btn" href="#journal-select"><i class="fas fa-edit"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <label for="journal_id" class="element-cat required">Journal ID</label>
            <input type="text" class="form-control disabled" name="values[journal_id]" value="638f62000d695c5938287574" id="journal_id" required="" readonly="" data-value="638f62000d695c5938287574">
        </div>
        <div class="col-sm">
            <label for="issn" class="element-cat">ISSN (getrennt durch Leerzeichen</label>
            <input type="text" class="form-control disabled" name="values[issn]" value="2379-5077 2379-5077" id="issn" readonly="" data-value="2379-5077 2379-5077">
        </div>
    </div>
</div>

Diese können nur durch Klick auf <span class="btn btn-sm"><i class="fas fa-edit"></i></span> bearbeitet werden. Dadurch öffnet sich folgendes Fenster, indem man mittels Namen oder ISSN nach einem Journal in OSIRIS suchen kann. 
Im folgenden Beispiel sind in OSIRIS zwei Journale vorhanden, die dem Suchterm entsprechen:

<div class="demo" id="journal-select">
    <div class="modal-content">
        <label for="journal-search">Suche Journal nach Name oder ISSN</label>
        <div class="input-group">
            <input type="text" class="form-control is-valid" list="journal-list" id="journal-search" value="Nucleic acid" data-value="Nucleic acid">
            <div class="input-group-append">
                <button class="btn"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <table class="table table-simple">
            <tbody id="journal-suggest"><tr><td class="w-50"><button class="btn" title="select"><i class="fas fa-lg fa-check"></i></button></td><td><h5 class="m-0">Nucleic acid therapeutics</h5><span class="float-right">Mary Ann Liebert, Inc.</span><span class="text-muted">2159-3345, 2159-3337, 1545-4576</span></td></tr><tr><td class="w-50"><button class="btn" title="select"><i class="fas fa-lg fa-check"></i></button></td><td><h5 class="m-0">Nucleic acids research</h5><span class="float-right">Oxford University Press</span><span class="text-muted">1362-4962, 0305-1048</span></td></tr><tr><td><button class="btn">Suche im NLM-Katalog</button></td></tr></tbody>
        </table>
    </div>
<div class="text-muted text-center mt-10">Dieses Fenster ist aus technischen Gründen nicht funktional.</div>
</div>

Ein Journal kann ausgewählt werden, indem man auf den Haken <span class="btn btn-sm"><i class="fas fa-check"></i></span> klickt. Das Fenster schließt sich automatisch und die drei oben dargestellten Formularfelder werden automatisch ausgefüllt. 

Sollte das gesuchte Journal nicht in OSIRIS gefunden werden, kann man durch Klick auf <span class="btn btn-sm">Suche im NLM-Katalog</span> eine erweiterte Suche starten. Dabei werden alle bei NLM indizierten Journale durchsucht und vorgeschlagen. Hier wird ebenfalls wieder über den Haken bestätigt. Sollte das ausgewählte Journal bereits in OSIRIS vorhanden sein (möglicherweise unter einem etwas anderen Namen), wird das bereits vorhandene Journal ausgewählt. Der entsprechende Abgleich findet über die ISSN statt und du wirst darüber durch ein Pop-Up informiert.

## Erweiterte Funktionen

### Dokumente hinterlegen
Nachdem eine Aktivität hinzugefügt wurde, können Dokumente hinterlegt werden. Idealerweise werden Publikationen, Poster und Vorträge mit einem PDF-Anhang supplementiert. Dafür geht man auf der Detailseite einer Aktivität (auf der man nach dem Erstellen landet), auf den Knopf zum Hochladen von Dateien:

<div class="demo">
<h2 class="mt-0">Details</h2>
    <span class="btn btn-osiris mr-5">
        <i class="icon-activity-pen"></i>
        Aktivität bearbeiten            
    </span>
    <span class="btn btn-osiris active">
        <i class="far fa-upload"></i>
        Dateien hochladen
    </span>
</div>

Auf der folgenden Seite findet sich ganz oben eine Übersicht zu der Aktivität, die man gerade bearbeitet. Es folgt eine Liste mit eventuell bereits vorhandenen Dateien, die an dieser Stelle auch heruntergeladen (<i class="fas fa-download text-primary"></i>) bzw. gelöscht  (<i class="fas fa-trash-alt text-danger"></i>) werden können. 

Weiter unten findet sich ein Formular, mit dem neue Dokumente hochgeladen werden können. Diese dürfen eine Maximalgröße von 16 MB nicht überschreiten und sollten sich nach Möglichkeit in einem Standardformat befinden. Gute Beispiele sind PDF (bevorzugt), PPTX, XLSX, DOCX. 

Bitte bemerke, dass sich PPTX und DOCX über die Exportieren-Funktion in Microsoft Office ganz einfach in PDF umwandeln lassen (Datei > Exportieren > PDF/XPS-Dokument erstellen). **Aus den folgenden Gründen empfehlen wir, PDF-Dokumente hochzuladen**:
- PDF kann direkt im Browser geöffnet werden
- PDF wird auf allen Geräten gleich dargestellt (unabhängig vom Betriebssystem, veknüpften Bildern und installierten Schriftarten)
- PDF hat durch die Komprimierung oftmals eine geringere Dateigröße als andere Formate
- PDF-Dokumente lassen sich nicht so leicht versehentlich bearbeiten
- PDF ist weniger anfällig für Viren-Befall



### Autorennotizen

Es ist möglich, in OSIRIS eigene Notizen zu einer Aktivität zu hinterlassen. 
Diese Notiz ist nur den Autoren der Aktivität und den Admins (a.k.a Julia und das Controlling) sichtbar. 
Um eine Notiz zu hinterlassen, muss beim Hinzufügen bzw. Bearbeiten einer Aktivität die folgende Box ausgeklappt werden (ganz unten vor dem Knopf zum Abschicken des Formulars):

<div class="demo">
    <a onclick="$(this).next().toggleClass('hidden')">
        <label onclick="$(this).next().toggleClass('hidden')" for="comment" class="cursor-pointer">
            <i class="fas fa-plus"></i> Notiz (Nur sichtbar für Autoren und Admins)
        </label>
    </a>
    <textarea name="values[comment]" id="comment" cols="30" rows="2" class="form-control hidden"></textarea>

</div>

Beispiele für Notizen ist der Titel von Publikationen, die gereviewed wurden, Kommentare zum vorraussichtlichen Veröffentlichungstermin von Online ahead of print-Artikeln, o.ä.

### Editorkommentare

Wenn du eine Aktivität abänderst, werden alle deine Koautoren darüber benachrichtigt und müssen eventuell erneut bestätigen, dass sie Autoren sind und die Aktivität überprüft haben. Um ihnen diesen Prozess zu vereinfachen, kannst du mitteilen, was genau du geändert hast. Dafür gibt es einen Bearbeitungs-Bereich im Formular, der nur sichtbar wird, wenn du eine vorhandene Aktivität bearbeitest. In folgendem Beispiel wurde nur die Rechtschreibung des Titels verändert:

<div class="demo">
    <div class="alert alert-signal p-10 mb-10">
        <div class="title">
            Bearbeitungs-Bereich 
        </div>
        <label for="editor-comment">Editor-Kommentar (teile deinen Ko-Autoren mit, was du geändert hast)</label>
        <textarea name="values[editor-comment]" id="editor-comment" cols="30" rows="2" class="form-control">Als Open Access markiert.</textarea>
        <div class="mt-10">
            <div class="custom-checkbox" id="minor-div">
                <input type="checkbox" id="minor" value="1" name="minor" data-value="1">
                <label for="minor">Änderungen sind minimal und Koautoren müssen nicht benachrichtigt werden.</label>
            </div>
            <small class="text-muted">
                Bitte beachte, dass Änderungen an den Autoren ignoriert werden, wenn dieser Haken gesetzt ist.
            </small>
        </div>
    </div>
</div>

So sieht diese Information jetzt auf der Prüfseite deiner Koautoren aus:

<div class="demo">
    <p class="m-0">
        Lorenz Christian Reimer hat die folgende Aktivität bearbeitet.<br><b>Editor-Kommentar:</b> <em>Als Open Access markiert.</em> 
    </p>
    <p>
        <span class="mr-20"><span data-toggle="tooltip" data-title="Journal article">
            <i class="far fa-lg text-publication fa-file-lines"></i>
        </span></span>
        <b>Koblitz, J.</b>, Halama, P., Spring, S., Thiel, V., Baschien, C., Hahnke, R.L., Pester, M., Overmann, J. and Reimer, L.C. (2022) Media<i>Dive</i>: the expert-curated cultivation media database.  <i>Nucleic Acids Research</i> DOI: <a target="_blank" href="https://doi.org/10.1093/nar/gkac803">https://doi.org/10.1093/nar/gkac803</a> <span style="color:#B61F29;">[Online ahead of print]</span> <i class="icon-open-access text-success" title="Open Access"></i> 
        <a href="/uploads/632da4672199cd3df8dbc166/gkac803_after proof.pdf" target="_blank" data-toggle="tooltip" data-title="pdf: gkac803_after proof.pdf" class="file-link"><i class="far fa-file fa-file-pdf"></i></a> 
    </p>
    <div class="" id="approve-632da4672199cd3df8dbc166">
                                    Bitte bestätige (evtl. erneut), dass du Autor:in bist:                                                <br>
        <span class="btn btn-sm text-success" data-toggle="tooltip" data-title="Ja, und ich war der DSMZ angehörig">
            <i class="fas fa-fw fa-check"></i>
        </span>
        <span class="btn btn-sm text-danger" data-toggle="tooltip" data-title="Ja, aber ich war nicht der DSMZ angehörig">
            <i class="far fa-fw fa-handshake-slash"></i>
        </span>
        <span class="btn btn-sm text-danger" data-toggle="tooltip" data-title="Nein, das bin ich nicht">
            <i class="fas fa-fw fa-xmark"></i>
        </span>
        <span class="btn btn-sm text-primary ml-10" data-toggle="tooltip" data-title="Aktivität bearbeiten">
            <i class="icon-activity-pen"></i>
        </span>
        <span class="btn btn-sm text-primary" data-toggle="tooltip" data-title="Aktivität ansehen">
            <i class="icon-activity-search"></i>
        </span>
    </div>
</div>


Falls du der Meinung sind, dass deine Änderungen zu minimal sind, um eine Kontrolle durch die Koautoren erforderlich zu machen, kannst du auch den entsprechenden Haken setzen. In den meisten Fällen gehört es aber zum guten Ton, seine Koautoren über etwaige Änderungen zu informieren.