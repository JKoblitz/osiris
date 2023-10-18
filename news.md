# Neuigkeiten

<a id="version-1.2.1"></a>
## 18.10.2023
`Version 1.2.1`

Diese Version beinhaltet vornehmlich Bug Fixes und Optimierungen im Hintergrund. Außerdem wurde das Layout einiger Seiten verbessert, sowie die Schriftart für Überschriften verändert. 

<a id="version-1.2.0"></a>
## 04.10.2023
`Version 1.2.0`

<i class="ph ph-users text-osiris"></i>
**Rollensystem**
- Ein ausgeklügeltes Rollensystem wurde hinzugefügt
- Einem Nutzer können jetzt mehrere Rollen zugewiesen werden (im Nutzer-Editor)
- Welche Rechte eine Rolle hat, kann jetzt feingranular im Admin-Panel eingstellt werden (neuer Reiter "Rollen")
- Die Einstellungen wurden im Hintergund verbessert


<i class="ph ph-user-minus text-osiris"></i>
**Verbessertes Inaktivieren von Nutzern**
- Nutzer können jetzt leichter als "Inaktiv" markiert werden
- Dazu wurde ein Knopf zur Toolbar im Nutzerprofil hinzugefügt (nur für Nutzer mit entsprechenden Rechten sichtbar)
- Persönliche Daten werden (abgesehen von Namen und Abteilung) gelöscht, sobald ein Nutzer inaktiviert wird
- Um einen Nutzer wieder zu aktivieren, kann man in "Nutzerprofil bearbeiten" einen entsprechenden Haken setzen.


<i class="ph ph-circles-three-plus text-osiris"></i>
**Forschungsdaten**
- Nein, wir fügen zu OSIRIS keine Forschungsdaten hinzu. Niemals.
- Stattdessen kann man Foschungsdaten mit Aktivitäten verknüpfen. Das geht über Entität (z.B. Genomsequenz), Freitext und Link. Geht dazu auf die Übersichtsseite einer Aktivität und klickt auf "Verknüpfen".
- Eine umfassende Suche für Forschungsdaten wurde hinzugefügt. Ihr müsst wissen, wie viele Genomsequenzen ihr im Jahr 2022 veröffentlicht habt? Mit OSIRIS ist das jetzt möglich (solange ihr die Daten eingepflegt habt).


<i class="ph ph-gear text-osiris"></i>
**Mehr Einstellungen im Admin-Panel**
- Coins und Achievements lassen sich jetzt global ausstellen
- Fremde Nutzermetriken lassen sich jetzt im Profil ausstellen. Daraufhin sind die Graphen nur noch für einen selbst sichtbar.

<i class="ph ph-copy text-osiris"></i>
**Verbesserungen bei der Dubletten-Erkennung**
- Die beste Lösung für Dubletten ist zu verhindern, dass sie entstehen. OSIRIS warnt euch, falls ihr dabei seid, gerade eine Dublette anzulegen.


<i class="ph ph-chalkboard-simple text-osiris"></i>
**Verbesserung der Lehrveranstaltungen**
- Lehrveranstaltungen wurden optisch überarbeitet
- Ein Filter wurde hinzugefügt
- Es wurde eine Möglichkeit hinzugefügt, um Lehrveranstaltungen zu löschen (nur wenn keine Aktivitäten verknüpft sind)

<i class="ph ph-textbox text-osiris"></i>
**Neue Module**
- Open Access Status
- Abstract
- Gender
- Country (nach ISO-Standard)

<i class="ph ph-star text-osiris"></i>
**Kleinere Features und Bug Fixes**
- Im Header ist nun ein Suchfeld für Aktivitäten zu finden
- Nutzer können ihre Profilbilder jetzt selbst bearbeiten.
- Zuletzt besuchte Konferenzen werden im "Conference"-Modul jetzt vorgeschlagen. Das führt hoffentlich zu mehr Konsistenz.
- Dem Profil von Berichterstattern wurden neue Elemente hinzugefügt
- Einige Interfaces wurden angepasst, z.B. ist die Übersichtsseite einer Aktivität jetzt noch nutzerfreundlicher.
- OSIRIS-Seiten sollten sich jetzt sehr viel besser ausdrucken lassen.
- Der Style von Buttons und Badges wurde angepasst.
- Es gibt jetzt eine neue 404 Seite. Die ist schön, schaut sie euch gern mal an.

<i class="ph ph-code text-osiris"></i>
**Hintergrundverbesserungen**
- Es gibt außerdem ein paar Verbesserungen hinter den Szenen. Das wird in Zukunft zu noch schnellerer und konsistenterer Entwicklung führen. 
- Eine neue Datenbank-Klasse war längst überfällig.
- Nutzerdaten wurden in Personen und Accounts geteilt. Dadurch können auch Personen angelegt werden, die keine Nutzer sind und Accountdaten sind von Personendaten getrennt. Das war ein wichtiger Schritt für die Gästeformulare. 



## 10.07.23
<i class="ph ph-briefcase text-osiris"></i>
**Altdaten-Import**

Es können jetzt auch Altdaten importiert werden. Wie genau das funktioniert, liest du am besten in der [Anleitung](https://osiris-app.de/install#import). 

## 06.07.23

<i class="ph ph-swap text-osiris"></i>
**IDA-Integration**

Mit einer IDA-Integration wurde begonnen. Um das Feature zu aktivieren, muss in der CONFIG-Datei der folgende Wert auf true gesetzt werden:

```php
// activate IDA integration here
define("IDA_INTEGRATION", true);
```

Zurzeit wird nur Formular 18.3 als Proof of Concept unterstützt. An weiteren Formularen wird gearbeitet, sobald der Fragenkatalog für das folgende Berichtsjahr feststeht. 

Außerdem wurden einige neue Module hinzugefügt, die für die IDA-Abfrage relevant sind, z.B. gender, nationality, open access status. 

<i class="ph ph-tree-structure text-osiris"></i>
**Schema.org Integration**

OSIRIS-Aktivitäten sind jetzt auch als Schema.org hinterlegt und exportierbar. Falls ihr nicht wisst, was das ist, könnt ihr es auf der [offiziellen Seite von Schema.org](https://schema.org/) nachlesen. Und falls ihr wissen wollt, wie so ein maschinen-lesbares Format aussieht, habe ich es euch hier mal für eine meiner Publikationen dargestellt:

```json
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@id": "#issue",
            "@type": "PublicationIssue",
            "issueNumber": 5,
            "isPartOf": {
                "@id": "#volume"
            }
        },
        {
            "@id": "#volume",
            "@type": "PublicationVolume",
            "volumeNumber": 19,
            "datePublished": "2023-05"
        },
        {
            "@type": "Periodical",
            "@id": "#journal",
            "name": "Nature Chemical Biology",
            "issn": [
                "1552-4469",
                "1552-4450"
            ],
            "publisher": "Nature Pub. Group",
            "hasPart": {
                "@id": "#volume"
            }
        },
        {
            "@id": "#record",
            "@type": "ScholarlyArticle",
            "name": "Metabolism from the magic angle",
            "author": {
                "@type": "Person",
                "givenName": "Koblitz",
                "familyName": "Julia"
            },
            "datePublished": "2023-05",
            "identifier": [
                {
                    "@type": "PropertyValue",
                    "name": "DOI",
                    "value": "10.1038/s41589-023-01317-2"
                }
            ],
            "pagination": "538-539",
            "isPartOf": [
                {
                    "@id": "#issue"
                },
                {
                    "@id": "#volume"
                },
                {
                    "@id": "#journal"
                }
            ]
        }
    ]
}
```


## 05.06.23

<i class="ph ph-queue text-osiris"></i>
**Neues Feature: Warteschlange**

OSIRIS prüft jetzt selbstständig einmal in der Woche (sonntags um 2 Uhr), ob es neue Publikationen für die Institution gibt. Wenn für dich neue Publikationen gefunden wurden, erhältst du einen Hinweis auf deiner Startseite.


## 12.05.23

`Version 1.1.0`
<a id="version-1.1.0"></a>

Ich habe mehrere kleinere Releases in einem großen zusammengeführt.

<i class="ph ph-cake text-osiris"></i>
**Neue Icons wurden eingeführt**

Aufgrund von Lizenzproblemen wurden die Icons auf der Seite vollständig durch [Phosphor-Icons](https://phosphoricons.com/) ersetzt. Die sind (genau wie OSIRIS) unter der Open Source-Lizenz MIT veröffentlicht.

Dadurch sieht die Seite auf den ersten Blick vielleicht etwas ungewohnt aus, ihr werdet euch aber sicher schnell daran gewöhnen.

<i class="ph ph-plus-circle text-osiris"></i>
**Verbesserungen beim Hinzufügen der Aktivitäten**
- Viele Module wurden umstrukturiert und verbessert
- Ein neuer Date-Picker für Zeiträume erleichtert (hoffentlich) die Bedienung
- Journale und Lehrveranstaltungen haben ein komplett neues UI/UX und lassen sich jetzt (hoffentlich) besser bedienen
- Wenn eine DOI oder Pubmed-ID eingegeben wird, wird nun sofort überprüft, ob diese bereits in der Datenbank vorhanden ist (danke an Markus für das Feedback)
- Bearbeitungsnotizen sind nur noch verfügbar, wenn mehr als ein Autor beteiligt ist
- Preprints brauchen jetzt kein Journal mehr (was quatsch war). Stattdessen kann der Veröffentlichungsort (z.B. BioRxiv) in ein Freitextfeld eingetragen werden.
- Ein Fehler wurde behoben, durch den sich das Interface aufhängen konnte, wenn die DOI nicht gefunden wurde
- Ein Fehler wurde behoben, durch den OSIRIS sich 'verschluckt' hat, wenn Autoren-Vornamen mit einem Sonderzeichen beginnen

<i class="ph ph-chalkboard-simple text-osiris"></i>
**Umstrukturierung der Lehre**
- Lehrveranstaltungen sind jetzt standardisiert
- Jedes Modul hat eine einzigartige Modulnummer, über die es leicht gefunden werden kann
- Es kann für jede Person einzeln der Anteil der SWS angegeben werden (nur bei affilierten Personen notwendig)
- Ein SWS-Rechner wurde hinzugefügt
- Einem Modul können Lehrveranstaltungen (z.B. Praktika, Vorlesungen, Seminare) hinzugefügt werden
- Gäste, die wegen einer Lehrveranstaltung hier sind (i.e. Studenten), können ebenfalls mit dem Modul verknüpft werden


<i class="ph ph-book-open-text text-osiris"></i>
**Neue Pubmed-Suche**
- Pubmed kann nun nach Autor(en), Titel, Jahr und Affiliation [durchsucht werden](activities/pubmed-search?authors=Koblitz&year=2023)
- Mittels der Levenshtein-Distanz wird die Wahrscheinlichkeit berechnet, ob es sich um ein Duplikat handelt oder nicht (nur basierend auf dem Titel)
- Die neue Suche kann z.B. bei Publikationen in [Mein Jahr](scientist) gefunden werden


<i class="ph ph-newspaper-clipping text-osiris"></i>
**Verbesserungen bei Journalen**
- Das UI/UX-Design der Journale wurde verbessert
- Es wird nun eine bessere API verwendet, um Journale abzufragen. [OpenAlex](https://docs.openalex.org/api-entities/venues) ist nicht nur deutlich schneller als NLM, es enthält auch mehr Journale und mehr Datenfelder. So muss OpenAccess jetzt nicht mehr zusätzlich abgefragt werden.

<i class="ph ph-sparkle text-osiris"></i>
**Verbesserungen bei den Aktivitäten**
- Als Beta-Feature wurden Awards eingeführt. Falls ihr dort Datenfelder vermisst, meldet euch bitte bei mir.
- Die Filter-Funktionen in Alle Aktivitäten wurden verbessert
  - Laufende Aktivitäten werden jetzt auch bei der Zeitraum-Suche berücksichtigt
  - Autoren, die sich hinter et al. verstecken, werden jetzt auch bei der Suche berücksichtigt
  - Der mittlerweile sehr viel Platz fressende Aktivitätenfilter wurde in ein Dropdown gepackt
  - Es wurde die Möglichkeit hinzugefügt, nach Abteilung zu filtern und Epubs (Online ahead of print) auszuschließen

<i class="ph ph-shapes text-osiris"></i>
**Sonstiges**
- Nicht aktive Nutzer werden in der Expertise-Suche nicht mehr berücksichtigt
- Widgets auf der Profilseite sind nur noch sichtbar, wenn sie Daten enthalten
- Coins müssen jetzt aktiv eingeschaltet werden (drei Zustände: nicht sichtbar, für mich sichtbar, für alle sichtbar)
- Die Seitennavigation wurde überarbeitet, sodass die wachsende Menge an Inhalten besser strukturiert ist.
- Viele Verbesserungen am Report (geschützte Leerzeichen, Bindestrich-Abk., Software und versch. Reviews berücksichtigt, Alphabetische Sortierung)
- Es wurden weitere Filter und Links bei der Abteilungsvisualisierung hinzugefügt
- Man erhält nur noch Erfolge für Aktivitäten, die man bereits bestätigt hat


<i class="ph ph-hammer text-osiris"></i>
**Maximale Flexibilität**
- durch einige umfangreiche Umstellungen ist es nun kinderleicht, neue Aktivitätsarten hinzuzufügen, zu konfigurieren, formatieren und zu bearbeiten. Dafür sind jetzt nicht mal mehr Programmierkenntnisse erforderlich.
- Die Konfiguration bei anderen Instituten wird auch bei neuen Updates nicht überschrieben, wodurch sie die maximale Flexibilität haben, OSIRIS nach ihren Wünschen zu gestalten.


<div class="alert alert-danger">
  <h3 class="title">
    <i class="ph ph-warning"></i>
    Achtung für alle anderen Institute! Breaking Changes!!!
  </h3>
  <ul class="list">
    <li>Die Struktur der Einstellungen wurde verändert!</li>
    <li>Für alle Einstellungen, die zuvor an <code class="code">settings.json</code> vorgenommen wurden, gibt es jetzt ein Admin-Dashboard.</li>
    <li>Da die Datei <code class="code">settings.json</code> aus technischen Gründen jetzt extern sichtbar ist, wurden sicherheitsrelevante Einstellungen in die <code class="code">CONFIG.php</code> transferiert. Bitte schau dir die Datei <code class="code">CONFIG.default.php</code> an, um zu sehen, wie die neue Datei auszusehen hat. </li>
    <li>Dafür sind Aktivitäten jetzt komplett konfigurierbar. Tutorials und Beispiele folgen demnächst auf <a href="https://osiris-app.de" target="_blank">der offiziellen Webseite</a>.</li>
  </ul>
</div>


## 31.01.23

- Die Datenbank wurde aufgeräumt: nicht mehr benötigte/gepflegte/gezeigte Datenfelder wurden entfernt. 
- Beim Hinzufügen von Publikationen via DOI oder PM-ID wird nun das Journal anhand der ISSN gesucht. Dadurch wird eine falsche Journalzuordnung durch unterschiedliche Namen vermieden.
- Ich habe alle Journale, die mindestens ein verknüpftes Paper hatten, mit JCR verknüpft
  - Über die API von Web of Science wurden MetaInformationen hinzugefügt.
  - Auf der Übersichtsseite eines Journals findet sich bei betroffenen Journalen jetzt ein Link zum Journal Citation Report.
  - Mittels eines selbstgeschriebenen Web Scrapers wurden Impact Factoren von JCR gezogen und gespei

## 25.01.23

- Feedback zum Hinzufügen von Editorenschaften wurde eingepflegt (Danke an Andrey)
- Auf der Profilseite gibt es (wenn vorh.) eine Übersicht mit allen laufenden Mitgliedschaften (Gremien & Editorial Boards)
- Es gibt jetzt eine Liste mit Namen, die für das Autoren-Matching verwendet werden. Diese enthält standardmäßig den vollen Namen und den abgekürzten Namen. Letzterer kann jedoch auch entfernt werden (falls er zu viele Treffer verursacht). Außerdem können weitere Namen (Mädchenname, Pseudonyme, optionale Vornamen) hinzugefügt werden, die für das Matching relevant sind. Bearbeiten kann man die Liste im Profil.
- Ein Bug wurde gefixt, bei dem eine Publikation ohne Impact Factor keine Coins gab.
- Ein Bug wurde gefixt, wegen dem Autoren, deren Vornamen mit einem Sonderzeichen beginnen, das System gebrochen haben.

## 23.01.23
- Der Autoreneditor auf der Seite "Aktivität hinzufügen" wurde verbessert
- Wenn ein Journal nicht in OSIRIS gefunden wurde, wird automatisch eine Suche in NLM durchgeführt.


## 11.01.23
- Es wurde eine Möglichkeit hinzugefügt, den Typ einer Aktivität nachträglich zu bearbeiten
- In die neue Web-Ansicht wurde das Datum der Aktivität integriert
- Es wurde ein Bug gefixt, durch den beim Filtern nach Vorträgen in allen Aktivitäten auch bestimmte Studierende gezeigt wurden

## 02.01.23

`Version 1.0`

Über die Feiertage habe ich noch ein paar Features hinzugefügt und (nicht aufgeführt) ein paar Bugs entfernt:

**Download von Aktivitäten**

  - Einzelne Aktivitäten können jetzt auf der Übersichtsseite heruntergeladen werden. Dafür gehst du rechts oben auf Download und kannst dann auswählen, ob und wer fett hervorgehoben wird und in welchem Format du herunterladen möchtest
  - Der "Einkaufswagen" wurde hinzugefügt. Damit können einzelne Aktivitäten gesammelt werden (sowohl auf der Übersichtsseite als auch bei Alle Aktivitäten). Alle ausgewählten Aktivitäten können dann gesammelt heruntergeladen werden.

**Import von Aktivitäten**

  - Der Import von Publikationen aus Google Scholar wurde hinzugefügt
  - Dazu muss im Nutzerprofil der Google Scholar-Account hinterlegt sein
  - Um Duplikate zu vermeiden, wird sowohl nach Titelübereinstimmung (Levenshtein-Distanz) als auch nach DOIs gesucht
  - Bitte beachtet, dass oftmals eine Überprüfung der Publikation notwendig ist, da Google Scholar-Infos leider oft inkorrekt oder unvollständig sind

**Achievements:**

  - Errungenschaften wurden eingeführt
  - für den Anfang gibt es 16 Errungenschaften mit eigenen Icons, verschiedenen Leveln und Beschreibungen
  - Errungenschaften werden auf der Profilseite angezeigt
  - Es gibt eine eigene Seite, um detailliert die Errungenschaften einer Person anzuschauen

**Profileinstellungen**

  - Im Menü "Profil bearbeiten" können jetzt Präferenzen festgelegt werden
  - Beispielsweise können Coins und Errungenschaften ausgeblendet werden. Sie werden dann weder für dich selbst noch für andere Nutzer auf deinem Profil gezeigt.

**Neue Aktivitätsansicht**

  - Es gibt jetzt eine neue Ansicht für Aktivitäten, die besser fürs Web optimiert ist
  - Der Titel wird dabei größer dargestellt, Autoren und weitere Infos sind je in einer eigenen Zeile
  - Die neue Ansicht ist jetzt der Standard bei allen Tabellen und auf Profil- und Jahresseiten
  - Falls euch die alte Ansicht besser gefallen hat, könnt ihr in euren Profileinstellungen im Punkt "Aktivitäten-Anzeige" auf Print stellen.

**Journale**

  - Ein Journal kann nun als Open Access markiert werden 
  - Dazu kann entweder angegeben werden, dass das Journal ausschließlich open access ist, oder ab welchem Jahr
  - Neu hinzugefügte Publikationen, die nach dem angegebenen Jahr publiziert wurden, werden automatisch als Open Access markiert
  - Wenn das Open Access Jahr eines Journals neu gesetzt wird, werden alle Publikationen die *nach* dem Jahr publiziert wurden, automatisch als Open Access markiert. Publikationen *im* angegebenen Jahr müssen manuell überprüft werden
  - Journale können jetzt manuell hinzugefügt werden (für Admins)

**Dokumentation**

  - Eine erste Dokumentation wurde bereitgestellt
  - Es werden zurzeit bereits Themen wie zum Beispiel das Hinzufügen von Aktivitäten, mein Jahr und Warnungen abgebildet
  - Weitere Themen sind in Arbeit und werden sukzessiv hinzugefügt

OSIRIS geht damit in die Version 1.0 über und verlässt die Betaphase. Wir werden natürlich trotzdem weiterhin Feedback einsammeln und an dem Tool weiterentwickeln. Danke an alle, die an der Betaphase beteiligt waren!


## 18.12.22
- Neues "Experten-Tool": mit der [erweiterten Suche](search/activities) können jetzt alle Aktivitäten detailliert durchsucht werden. 45 Datenfelder sind mit unterschieldichen Optionen durchsuchbar. Ein Anleitungsvideo folgt in Kürze.
- In der Übersicht einer Aktivität sind nun alle Autoren aufgeführt
- Bei Autoren ohne Vornamen (z.B. "The Microbiome Consortium") wird nun kein Komma mehr angezeigt
- Bug Fixes im Report:
  - Impact Faktoren werden korrekt angezeigt
  - Hoch- und tiefgestellte Zeichen werden jetzt korrekt übernommen
  - Leerzeile zwischen der Publikation und der Bemerkung "Shared authors" wurde entfernt
  - Wenn in einer Aktivität kein Autor mit DSMZ-Affiliation gefunden wurde, wird sie nicht aufgeführt

## 13.12.22
- Aktivitäten:
  - Zu allen Aktivitäten können nun optional Kommentare hinzugefügt werden
  - Kommentare sind nur für Autoren der Aktivität (und Admins, a.k.a. Julia und das Controlling) sichtbar
  - Dadurch können einerseits "private" Notizen zu den eigenen Aktivitäten hinzugefügt werden, aber auch Bemerkungen für das Controlling können hinterlassen werden.
- Mein Jahr:
  - Die Seite "Mein Jahr" wurde etwas überarbeitet: unwichtige Sachen wurden entfernt und wichtige bekommen zentralere Positionen.
  - Eine Timeline wurde hinzugefügt, um eine visuelle Übersicht über das Jahr zu geben.
  - Der Prozess, mit dem ein Quartal für das Controlling freigegeben werden kann, wurde verbessert.
  - Wenn das letzte Quartal noch nicht freigegeben wurde, erscheint eine Nachricht im persönlichen Profil.
- Journale:
  - Die Tabelle mit allen Journalen wurde verbessert und enthält nun eine Anzahl von Aktivitäten in OSIRIS, nach der auch standardmäßig sortiert wird.
  - Auf der Übersicht eines Journals werden jetzt alle Publikationen, sowie Reviewer- und Editortätigkeiten in dem jeweiligen Journal gezeigt.

## 02.12.22
- Beim Hinzufügen von Publikationen werden Journale jetzt standardisiert und verknüpft
- Neue Journale können anhand des NLM-Katalogs hinzugefügt werden
- Journale können bearbeitet und um neue Impact Factoren erweitert werden

## 29.11.22
- Neue Visualisierungen wurden hinzugefügt (Abteilungs-Netzwerk und -Übersicht) und die vorhandene (Coautoren-Netzwerk) wurde verbessert

## 24.11.22
- Das Menü wurde umstrukturiert und farblich etwas einfacher gehalten
- Neue Icons für Aktivitäten wurden eingeführt
- Der Knopf zum Hinzufügen von Aktivitäten war anscheinend zu fancy, um ihn wahrzunehmen. Er wurde vereinfacht.
- Die neue Primärfarbe ist "Osiris"-Orange
- Die Profilseite wurde überarbeitet:
  - Es wurde eine Grafik zur Rolle in Publikationen hinzugefügt
  - Publikationen und andere Aktivitäten werden jetzt getrennt voneinander aufgeführt
  - Viele Graphen haben nun Achsenbeschriftungen bekommen
  - Der Polar-Chart zu Impact Faktoren wurde durch ein Histogramm ersetzt
  - Die neuen Aktivitätstypen (Software & Teaching) wurden der Grafik über alle Aktivitäten hinzugefügt
- Bei den zu überprüfenden Autorenschaften wurde die Information hinzugefügt, wer die Aktivität hinzugefügt bzw. zuletzt bearbeitet hat
- Das Löschen von Aktivitäten ist nach ausdrücklichem Wunsch des Controllings für vergangene Quartale nicht mehr möglich. Fehlerhafte oder doppelte Aktivitäten können vom Controlling gelöscht werden, ein Knopf für schnellen Kontakt wurde hinzugefügt.
- "Open Access" ist jetzt ein Radio Button statt einer Checkbox
- Ein Icon für "Closed Access" wurde eingeführt
- Die Übersichtsseite der Aktivitäten wurde grundsätzlich überarbeitet und ist jetzt deutlich übersichtlicher gestaltet
- Der Upload von Dokumenten zu Aktivitäten wurde überarbeitet
  - Jetzt getrennt vom Erstellen einer Publikation
  - Es können mehrere Dokumente für eine Aktivität hochgeladen werden
  - Es können jetzt auch andere Dokumente als PDF hochgeladen werden (z.B. PPTX, Word, usw.)
  - Dokumente können jetzt wieder gelöscht werden
  - Ein kleines Datei-Icon mit Link zum Dokument erscheint in der formatierten Aktivität.
- Bug Fixes:
  - Anzahl der Erst- und Letztautoren werden beim Bearbeiten einer Publikation nicht mehr versehentlich überschrieben
  - Eine Aktivität zu kopieren war nicht möglich
  - Viele kleinere Bug Fixes

## 18.11.22
- Ich habe zusätzlich zu Crossref auch DataCite hinzugefügt. Dadurch können jetzt auch **DOIs von anderen Aktivitäten** als nur Publikationen hinzugefügt werden, beispielsweise Software, Datensätze, Poster, Präsentationen, usw. Um eine DOI für eine solche Aktivität zu bekommen, empfehle ich die Aktivität auf einem Datenarchiv wie beispielsweise [Zenodo](https://zenodo.org/) hochzuladen.
- Die Seite "Mein Jahr" wurde für die neuen Aktivitäten aktualisiert
- Die Einstellungen im Downloadbereich wurden erweitert:
  - Es kann nun eine Abteilung ausgewählt werden
  - Ein genauer Zeitraum (Monat/Jahr) kann ausgewählt werden
  - Man kann nun einstellen, ob und was fett markiert werden soll
  - BibTex-Export funktioniert jetzt

## 17.11.22
Ich habe sehr viel User-Feedback aus der Beta-Phase eingearbeitet, u.a. folgendes:

- Neue Aktivitäten wurden hinzugefügt:
  - Software
  - Lehre (Vorlesungen und Praktika)
  - Reviews von Grant Proposals und Abschlussarbeiten

- Das Hinzufügen von Aktivitäten wurde verbessert:
  - Die Aktivitäten Publikation, Reviews, Misc und Studierende haben jetzt Unterkategorien, die ausgewählt werden können, nachdem eine Kategorie ausgewählt wurde. Dadurch sollten viele offene Fragen geklärt werden, da die Datenfelder nun etwas flexibler reagieren.
  - Beispiele wurden hinzugefügt. Wenn man nun auf Beispiele klickt, werden ausgewählte Aktivitäten oberhalb des Formulars angezeigt. Dabei sind Datenfelder farblich gekennzeichnet und beim Hovern wird der Name des Datenfelds angezeigt. Die Datenfelder im Formular sind in der gleichen Farbe gekennzeichnet.
  - Eine Hilfe-Funktion erklärt nun, wie der Autoreneditor funktioniert.
  - Lectures: Es kann jetzt angegeben werden, ob es sich um eine *Invited lecture* handelt

## 15.11.22
- Das Controlling-Dashboard wurde komplett überarbeitet. Neue Metriken werden in verbesserten Graphen dargestellt.

## 07.11.22
- Ich habe auf Anfrage eine Nutzerprofilseite hinzugefügt. Die Seite "Mein Jahr", auf die bisher von der Nutzertabelle verwiesen wurde, hat offenbar zu einiger Verwirrung geführt. "Mein Jahr" soll ausschließlich der Übersicht des aktuellen Jahrs/Quartals dienen und die bekannten Excel-Listen ablösen.
- Das neue Profil wurde mit Metriken und Graphen ausgestattet. Außerdem findet sich hier eine kurze Übersicht über die neuesten Aktivitäten sowie ein paar Nutzerinfos.
- Da das Quartal-Auswahlfeld (vorher zu finden oben rechts im Header) nur noch auf der Seite "mein Jahr" verwendet wurde, wurde es dorthin verschoben, um Verwirrung zu vermeiden.
- Das Dashboard wurde durch das neue Profil abgelöst

## 06.11.22
- Man kann sich nun ein Koautoren-Netzwerk anzeigen lassen. Dort sind alle DSMZ-Wissenschaftler dargestellt, mit denen man zusammen publiziert hat. Die Verknüpfungen sind dabei nach Abteilung gefärbt.
- Achievements wurden hinzugefügt (im Moment noch low level, sprich nur für erstellte/bearbeitete Datensätze)


## 31.10.22
- Journale wurden um alle NLM Journale ergänzt
- Falls vorhanden wurde der Impact Factor hinzugefügt
- Der Journal- und Nutzerbrowser wurde optimiert und ist nun komplett durchsuch- und sortierbar
- Quartale können nicht mehr bestätigt werden, wenn sie noch nicht zu Ende sind
- Es wird nun am Ende eines Quartals auf weitere mgl. Probleme hingewiesen (z.B. noch laufende Aktivitäten ohne Enddatum)

## 07.10.22
- Nutzer können jetzt bearbeitet werden

## 03.10.22
Der erste Report wurde eingeführt (Research report)
- Die Zeitspanne kann genau angepasst werden
- Der Export richtet sich ungefähr nach dem bisherigen Report, Header, Footer und Tabellen müssen manuell angepasst werden



## 25.09.22
- Die Übersichtsseite für Aktivitäten wurde verbessert (trotzdem noch im Alpha-Status).
- Man kann nun PDF-Dokumente an Aktivitäten anhängen. Diese können auf der Übersichtsseite heruntergeladen werden.  
- Eine Übersichtsseite mit allen gefundenen Problemen ermöglicht Wissenschaftlern ihre Aktivitäten zu bereinigen. Folgende "Probleme" gibt es:
  - Eine Autorenschaft wurde noch nicht bestätigt
  - Eine Publikation ist noch als Epub hinterlegt
  - Eine Abschlussarbeit ist noch "in Progress", aber das Abschlussdatum liegt in der Vergangenheit
- Im Titel-Editor können jetzt auch ein paar Sonderzeichen hinzugefügt werden.
- Autoren können nun im Detail-Editor bearbeitet werden. So ist es möglich, z.B. Nutzernamen anzupassen, damit die Aktivitäten auch den richtigen Autoren zugeordnet werden können.

## 24.09.22
- Dashboard hinzugefügt: Das Dashboard ist die neue Startseite. Sie wurde sowohl für Wissenschaftler als auch für das Controlling verbessert und zeigt jetzt eine Übersicht über die Aktivitäten (beim Wissenschaftler nur über eigene). 

## 23.09.22
- Die verschiedenen Aktivitäten wurden alle in einer Tabelle zusammengefasst, die übersichtlicher gestaltet ist
- Die Aktivitäten lassen sich nach Art der Aktivität und Datum filtern und durchsuchen
- Die Aktivitäten lassen sich anschauen (über die Lupe) und bearbeiten (über den Stift). Bitte beachten, dass die Ansicht sehr rudimentär ist (die Rohdaten werden gezeigt) und noch verbessert wird.
- Die Aktivitäten lassen sich jetzt alle über ein gemeinsames Formular hinzufügen
- Die Aktivitäten lassen sich bearbeiten und löschen
- Geteilte Autorenschaften werden dargestellt
- Für Open-Access-Publikationen wurde ein Icon hinzugefügt

## 21.09.22
- Eine Schnittstelle wurde eingeführt, über die Nutzer aus LDAP bezogen werden können. Die Nutzer wurden aktualisiert und in Abteilungen unterteilt

## 11.09.22
- Titel von Aktivitäten können nun formatiert werden. Dabei ist es möglich, fett, kursiv und unterstrichen zu formatieren. Die Formatierungen sind auch bei den Bearbeitungen möglich und können mit exportiert werden. 
- Bilder der Nutzer werden jetzt von Gravatar importiert
- Ich habe angefangen, das Confirmation-System einzuführen. Funktioniert soweit ganz gut.
- Bei den Publikationen funktionieren jetzt die Knöpfe "nicht Autor" und Affiliation.
- Bei dem LOM-System werden nun keine Punkte vergeben, wenn der Autor als Affiliation nicht die DSMZ hat.
- Wenn bei dem Hinzufügen einer Aktivität keiner der Autoren der DSMZ zugehörig ist, wird eine Warnmeldung gezeigt.

## 02.09.22
Ich habe ein ganz simples Punkte-System aufgesetzt. 
- Die Punkte werden in einer von mir definierten Matrix gespeichert, die leicht anzupassen ist. 
- Über ein Punkte-Portal kann das Controlling einfach die Punkte für die einzelnen Bereiche anpassen. Siehe [hier](lom).
- Auf der Übersichtsseite eines Wissenschaftlers werden oben die Punkte aufsummiert. Hinter jeder Aktivität stehen die dafür erhaltenen Punkte. Die Punkte für refereed journals errechnen sich mit dem Impact Factor (falls vorhanden)

## 01.09.22
Dieses Update bezieht sich ausschließlich auf die Ansicht des Controllings
- Auf der Startseite wird nun eine Übersicht aller Wissenschaftler gezeigt, die das aktuelle Quartal "approved" haben 
- Die Inhaltsseiten (Publikationen, Poster, Vorträge, Reviewsm Misc, Students) zeigen nun alle Aktivitäten des ausgewählten Jahres
- Inhaltsseiten sind jetzt filterbar (Achtung! Groß- und Kleinschreibung beachten.)

## 31.07.22
- Publikationen können jetzt hinzugefügt werden. Sowohl Journal-Artikel als auch Bücher funktionieren. 
- Auf das Löschen von Publikationen wurde bewusst verzichtet. Vielleicht fügt man die Funktion ein, ermöglicht es aber nur begrenzten User-Gruppen (z.B. Admin und Controlling)
- Export von Publikationen in Word und BibTex wurde hinzugefügt.

## 29.07.22

Ich habe mal wieder alles auf den Kopf gestellt. Die Datenbank zum Beispiel. Dort läuft jetzt MongoDB anstatt MySQL. Warum? Weil das für mehr Flexibilität, schnellere Ladezeiten und geringere Entwicklungszeit führt. Key-Value for the win :)

Dadurch war jetzt in kürzester Zeit folgender Fortschritt möglich:
- Alle CRUD-Funktionen wurden hinzugefügt, was bedeutet, dass sich jetzt alle dargestellten Aktivitäten erstellen, anzeigen, bearbeiten und löschen lassen. Einzige Ausnahme ist das Hinzufügen/Bearbeiten von Publikationen.
- Bei vielen Aktivitätstypen gibt es Sonderfunktionen:
  - Abschlussarbeiten können einfach verlängert werden.
  - Bereits gehaltenen Vorträgen kann einfach eine Wiederholung hinzugefügt werden
  - Reviews können weitere Reviewaktivitäten hinzugefügt werden
  - Die Zeitspanne von Editorials kann angepasst werden
  - Sonstige Forschungsaktivitäten (Misc) können weitere Termine hinzugefügt werden (wenn einmalig) bzw. die Zeitspanne angepasst werden (wenn annual)
- Auf der Übersichtsseite eines Wissenschaftlers können neue Aktivitäten über Popups hinzugefügt werden
- Man kann bei allen Aktivitäten den Nutzer auswählen, sodass z.B. auch das Controlling für andere Nutzer Aktivitäten hinzufügen kann.
- Die Übersicht der Nutzer ist nun durchsuchbar (Vorname, Nachname, Kürzel) und kann nach Wissenschaftler gefiltert werden.
- Die Übersicht der Journale ist nun nach Name des Journals und ISSN durchsuchbar.

## 24.07.22

- Dieses Changelog wurde hinzugefügt, um den Überblick über die Entwicklung zu behalten
- Neues Auswahlmenü zur Navigation hinzugefügt: jetzt können Jahr und Quartal präziser ausgewählt werden.
- Auf der Übersichtsseite werden jetzt alle Aktivitäten des aktuell gewählten **Jahres** gezeigt. Aktivitäten, die nicht im ausgewählten **Quartal** stattfanden werden ausgegraut
- Es wurden mit Students & Guests Doktoranden, Studenten und Gäste hinzugefügt.
  - Es gibt ein Interface, um neue Datensätze hinzuzufügen
  - Verantwortliche Wissenschaftler haben in diesem Jahr gelaufene Betreuungen in ihrer Übersicht.
  - Der verantw. Wissenschaftler bekommt einen Hinweis, wenn die Zeit eines Gast/Student/Doktorand abgelaufen ist und kann das Ende bestätigen oder verschieben
  - Abschlussarbeiten können auch abgebrochen werden
- Auf der Übersichtsseite werden jetzt erste Fehlermeldungen angezeigt.
