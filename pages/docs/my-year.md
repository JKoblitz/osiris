
# <i class="ph ph-calendar text-osiris"></i> Mein Jahr


Der Zweck der Seite "Mein Jahr" ist es, die bis Ende 2022 in der DSMZ verwendeten Excel-Tabellen zur Abfrage der Forschungsaktivitäten abzulösen. Auf dieser Seite sollen alle Aktivitäten des vergangenen Quartals noch einmal überprüft und bestätigt werden. 


## Die Warnmeldung auf der Profilseite
Wenn ein Quartal vergangen ist, erscheint auf der Startseite eines jeden Wissenschaftlers und einer jeden Wissenschaftlerin eine Warnmeldung, dass das vergangene Quartal noch nicht freigegeben wurde. Diese sieht wie folgt aus:

<div class="demo">
    <div class="alert alert-muted">
        <div class="title">Das vergangene Quartal (2022Q4) wurde von dir noch nicht freigegeben.</div>
        <p>Für das Quartalscontrolling musst du bestätigen, dass alle Aktivitäten aus dem vergangenen Quartal in OSIRIS hinterlegt und korrekt gespeichert sind.
        Gehe dazu in dein Jahr und überprüfe deine Aktivitäten. Danach kannst du über den grünen Button das Quartal freigeben.</p>
        <span class="btn btn-success">
            Überprüfen &amp; Freigeben</span>
    </div>
</div>

Diese Meldung ist der Startschuss für die quartalsweise Überprüfung. Zu diesem Zeitpunkt müssen alle vergangenen Aktivitäten überprüft und ggf. ergänzt werden. Dazu folgt man am besten den Link zu "Mein Jahr", der sich hinter dem grünen Knopf verbirgt. Wenn du hier drauf klickt, wird dir auch direkt das richtige Quartal angezeigt. Andernfalls musst du:

## Ein Quartal auswählen

Wenn du direkt auf "Mein Jahr" im Menü klickst, wird dir das aktuelle Quartal im aktuellen Jahr angezeigt. Das wird unter anderem dadurch deutlich, dass du das Quartal nicht bestätigen kannst, denn der Button dafür sieht so aus:

<a href="#close-modal" class="btn disabled"><i class="ph ph-check mr-5"></i> Gewähltes Quartal ist noch nicht zu Ende.</a>

Um nun das korrekte Quartal zu sehen, musst du es in folgendem Formular auswählen, welches du oben rechts auf der Seite findest:

<div class="demo w-400">
    <div class="form-group">
        <label for="year">
            Ändere Jahr und Quartal:
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text" data-toggle="tooltip" data-title="Wähle ein Quartal aus">
                    <i class="ph-regular ph-calendar-day"></i>
                </div>
            </div>
            <select name="year" id="year" class="form-control">
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023" selected="">2023</option>
            </select>
            <select name="quarter" id="quarter" class="form-control">
                <option value="1" selected="">Q1</option>
                <option value="2">Q2</option>
                <option value="3">Q3</option>
                <option value="4">Q4</option>
            </select>
            <div class="input-group-append">
                <button class="btn btn-primary"><i class="ph ph-check"></i></button>
            </div>
        </div>
        <p class="text-muted font-size-12 mt-0">
            Das gesamte Jahr ist hier gezeigt. Aktivitäten außerhalb des gewählten Quartals sind ausgegraut.
        </p>
    </div>
</div>

## Forschungsaktivitäten überprüfen

Die Seite mein Jahr ist folgendermaßen aufgebaut:

Ganz oben findest du eine visualle Übersicht über das Jahr, die dir auf einen Blick zeigt, wann du die meisten Aktivitäten hattest. 

Es folgen Boxen zu jeder einzelnen Aktivitätskategorie, die wir in OSIRIS haben. Das hat folgende Gründe: zum einen ist es einfacher, sich über jede Kategorie einzeln Gedanken zu machen. Wenn sie alle ungeordnet aufeinander folgen, wie es in "Meine Aktivitäten" der Fall ist, wird es schnell unübersichtlich. Deshalb kann man hier eine Kategorie nach der anderen betrachten. Zum anderen fällt auf diese Art direkt auf, sollten in einer Kategorie keine Aktivitäten sein. Wenn ich nur anzeige, was da ist, würde es vielleicht nicht auffallen, dass ich keine Poster eingetragen habe. Wenn ich aber explizit darauf hingewiesen werde, dass ich keine Poster habe, fällt mir vielleicht doch ein, dass da doch diese eine Konferenz war ...

Jede Aktivität ist gleich dargestellt. Das führt zu einer verbesserten Übersicht und Struktur. Gleich zu Beginn steht das Quartal, in dem die Aktivität *begonnen* hat. Sollte es sich um eine mehrjährige Aktivität handeln, bei der der Anfang der Aktivität in einem anderen Jahr liegt, ist hier zusätzlich das Jahr angegeben.
Aktivitäten, die nicht im ausgewählten Quartal gestartet sind, sind leicht ausgegraut. 
Es folgt die standardmäßige Darstellung der Aktivitäten, die Nutzer:innen im Profil einstellen können. Dann folgen die im Folgenden beschriebenen drei Schaltflächen und die Anzahl an Coins, dir man für die Aktivität erhalten hat. Beim Hovern über die Coins gibt ein Tooltip weitere Informationen.

<i class="ph ph-regular ph-arrow-fat-line-right mr-10 ph-fw ph-lg text-primary"></i> Hier gelangt man zur Übersichtsseite der Aktivität, auf der alle Details dargestellt werden. Außerdem gibt es dort weitere Links zum Bearbeiten und Kopieren einer Aktivität, sowie zum Autoreneditor.

<i class="ph ph-regular ph-pencil-simple-line mr-10 ph-fw ph-lg text-primary"></i> Hier gelangt man direkt zur Bearbeitung einer Aktivität.

<i class="ph ph-cart-plus mr-10 ph-fw ph-lg text-primary"></i> Mit diesem Knopf lässt sich die Aktivität zum Download-Korb hinzufügen, um sie mit anderen gesammelt herunterzuladen.

Am Ende einer jeden Kategorie gibt es zwei weitere Knöpfe: Mit Klick auf den ersten kann man sich alle eigenen Aktivitäten der gewählten Kategorie anzeigen lassen. Mit Klick auf das Plus kann man direkt eine Aktivität der gewählten Kategorie hinzufügen. 


## Probleme beseitigen

Solange auf der Seite "Mein Jahr" Warnungen angezeigt werden, kann das Quartal nicht bestätigt werden. Dies gilt auch, wenn die Aktivität mit der Warnung gar nicht im gewählten Quartal liegt. Eine Warnung wird zurzeit folgendermaßen unterhalb einer Aktivität dargestellt:

<div class="demo">
    Media<i>Dive</i>: the expert-curated cultivation media database
    <br>
    <small class="text-muted d-block">
    <span class="d-block">Koblitz, J., Halama, P., Spring, S., Thiel, V., Baschien, C., Hahnke, R.L., Pester, M., Overmann, J., Reimer, L.C.</span> <i>Nucleic acids research</i> <i class="icon-open-access text-success" title="Open Access"></i>
    </small>
    <br>
    <b class="text-danger">
        Diese Aktivität hat ungelöste Warnungen. <a class="link">Review</a>
    </b>       
</div>

Mit Klick auf <a class="link">Review</a> kommst du direkt zur Seite mit allen Warnungen und kannst dich entsprechend darum kümmern. Wie genau mit Warnungen umgegangen wird und warum sie überhaupt angezeigt werden, zeige ich dir [hier](warnings).


## Das Quartal freigeben

Nachdem du alle Aktivitäten überprüft und ggf. ergänzt, sowie alle Warnungen beseitigt hast, kannst du das Quartal bestätigen. Dazu klickst du auf den großen, grünen Button oben auf der Seite:

<a class="btn btn-lg btn-success">
    <i class="ph ph-question mr-5"></i>
    Aktuelles Quartal freigeben
</a>

Daraufhin öffnet sich ein Fenster, in dem du noch einmal bestätigen musst, dass du das Quartal tatsächlich überprüft hast.
Solltest du doch noch ungelöste Warnungen haben, werden diese im Fenster dargestellt:

<div class="demo">
    <div class="modal-content w-600 mw-full" style="border: 2px solid var(--success-color);">
        <a href="#close-modal" class="btn float-right" role="button" aria-label="Close">
            <span aria-hidden="true">×</span>
        </a>
        <h5 class="title text-success">Quartal 4 freigeben</h5>
        <p>Die folgenden Aktivitäten haben ungelöste Warnungen. Bitte <a href="#cancel" class="link">kläre alle Probleme</a> bevor du das aktuelle Quartal freigeben kannst.</p>
        <table class="table table-simple"><tbody>
        <tr><td class="px-0">
        Media<i>Dive</i>: the expert-curated cultivation media database
        <br>
        <span class="badge badge-publication"><i class="ph ph-lg text-publication ph-file-lines"></i> Journal article</span><a class="badge badge-danger filled ml-5" href="#t">Online ahead of print</a></td></tr><tr><td class="px-0">
        MediaDive: The expert-curated cultivation media database
        <br>
        <span class="badge badge-poster"><i class="ph ph-lg text-poster ph-presentation-screen"></i> Poster</span><a class="badge badge-danger filled ml-5" href="#t">Überprüfung nötig</a></td></tr><tr><td class="px-0">
        Reviewer für  <i>Artificial Intelligence Review</i>. 
        <br>
        <span class="badge badge-review"><i class="ph ph-lg text-review ph-file-lines"></i> Peer-Review</span><a class="badge badge-danger filled ml-5" href="#t">Nicht-standardisiertes Journal</a></td></tr>
        </tbody></table>
    </div>
</div>

Du kannst hier entweder auf <a href="#cancel" class="link">kläre alle Probleme</a> klicken oder auf den Fehlertyp, der dir in dem roten Label angezeigt wird, um zur Überprüfung zu kommen.


Wenn du alle Probleme beseitigst, sieht das Fenster so aus:

<div class="demo">
<div class="modal-content w-600 mw-full" style="border: 2px solid var(--success-color);">
    <a href="#close-modal" class="btn float-right" role="button" aria-label="Close">
        <span aria-hidden="true">×</span>
    </a>
    <h5 class="title text-success">Quartal 4 freigeben</h5>
        <p>
            Du bist dabei, das aktuelle Quartal freizugeben. Deine Daten werden an das Controlling übermittelt und du bestätigst hiermit, dass du alle meldungspflichtigen Aktivitäten für dieses Jahr eingetragen bzw. überprüft hast und alle Daten korrekt sind. Dieser Vorgang kann nicht rückgängig gemacht werden und alle Änderungen am Quartal im Nachhinein müssen dem Controlling gemeldet werden.
        </p>
        <button class="btn btn-success">Freigeben</button>
    </div>
</div>

Mit einem Klick auf freigeben <span class="btn btn-success btn-sm">Freigeben</span> schließt du den Vorgang ab. Du hast das Quartal bestätigt und deine Aktivitäten wurden ans Controlling übermittelt. Bei den Kolleg:innen wird dort jetzt bei dir ein grüner Haken angezeigt.