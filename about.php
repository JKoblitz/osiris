

<h1>OSIRIS</h1>


<h4 class="text-muted font-weight-normal mt-0">The Open, Simple and Integrated Research Information System</h4>

<p>

    <?=lang('OSIRIS is my attempt to create a light-weight and open-source research information system; made-to-measure the needs of the DSMZ members.', 'OSIRIS ist mein Ansatz, ein leichtgewichtiges Open-Source Forschungsinformationssystem zu schaffen; maßgeschneidert auf die Bedürfnisse der DSMZ.')?>
</p>

<hr>

<style>
    .check-list, .check-list ul {
        margin: 0;
        padding-left: 1.2rem;
      }
      
      .check-list li {
        position: relative;
        list-style-type: none;
        padding-left: 2.5rem;
        margin-bottom: 0.5rem;
      }
      
      .check-list li:before {
          content: '\f0c8';
          font-family: 'Font Awesome 6 Pro';
          font-weight: 400;
          display: block;
          position: absolute;
          color: var(--muted-color);
          left: 0;
          top: 0;
      }
      .check-list li.checked::before {
        content: '\f14a';
        color: var(--success-color);
      }
</style>

<h2>To-Do and Roadmap</h2>

<h3>Grundlegende Funktionen der Applikation</h3>

<ul class="check-list">
    <li>Alle Datenfelder implementieren
        <ul>
            <li class="checked">Publikationen</li>
            <li>Bücher und Zeitschriften</li>
            <li>Corrections?</li>
            <li>Poster</li>
            <li>Vorträge</li>
            <li class="checked">Reviews und Editorials</li>
            <li>Gäste</li>
            <li>Doktoranden-Betreuung</li>
            <li>Drittmittel und Projekte?</li>
            <li>Habilitationen und Preise?</li>
            <li>Sonstige Aktivitäten</li>
        </ul>
    </li>
    <li>Übersichtsseite eines Wissenschaftlers</li>
    <li>Import der DSMZ-Bibliographie</li>
    <li>Vermeidung von Datendoppelungen</li>
    <li class="checked">Quartal-Auswahl</li>
    <li>Knopf auf der Wissenschaftlerseite, um Daten zu bestätigen</li>
    <li>Übersetzung der Seite in zwei Sprachen</li>
    <li>Nutzerrollen zufügen (Admin, Controller, Group leader, Scientist)</li>
    <li>Zugriff auf verschiedene Seiten sind rollenspezifisch</li>
</ul>

<h3>Erweiterte Funktionen (Auswertung und Analyse)</h3>

<ul class="check-list">
    <li>Export-Funktionen:
        <ul>
            <li>Mgl. Formate: Word, PDF, CSV, BibTeX</li>
            <li>Alle Aktivitäten nur durch Controller</li>
            <li>Einzelaktivitäten für Forschende</li>
        </ul>
    </li>
    <li>Service: Impact factor-Abfrage (https://developer.clarivate.com/apis/wos-journal)</li>
    <li>Implementation einer API, um anderen Services der DSMZ Abfragen zu erlauben</li>
    <li>LOM-Punktesystem</li>
    <li>Ko-Autoren-Netzwerk</li>
    <li>Impact Factor-Verteilung</li>
    <li>Abteilungszusammenarbeit (als Netzwerk)</li>
</ul>