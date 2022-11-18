# To-Do-Liste

## Aus Beta-Feedback
- [ ] Ich wollte bei einem Vortrag die pptx-Datei anhängen, da kam Error 405 und es ging nicht weiter (war vielleicht zu groß?)
- [ ] bei den nicht-begutachteten Publikationen habe ich versucht eine PDF hochzuladen. Die war danach nicht sichtbar (selbst, wenn wäre das die falsche gewesen und da war mir aufgefallen, dass hier keine Möglichkeit gab, den Anhang zu löschen ;-))
- [ ] In Formularen: es wäre cool, wenn ein "*-Feld" noch nicht ausgefüllt wurde, dieses rot zu markieren, wenn man auf Speichern klickt
- [ ] Für Autoren sollte die ORCID zusätzlich gespeichert werden.
- [ ] Funding sollte gespeichert werden
- [x] Ein detailliertes Profil (vielleicht auch mit einem BarChart über ALLE Aktivitäten?)
- [x] Eine Legende für das Koautorennetzwerk
- [x] Invited Lectures bei Vorträgen
- [x] Beispiele, was in den einzelnen Kategorien eingefügt werden kann/soll
- [x] Dashboard: wenn es möglich wäre, wäre es cool, wenn man die Balken klicken könnte und damit auf die einzelnen Einträge kommt. Also bei Publikationen 2022 auf die entsprechenden Einträge verweisen/listen. 
- [x] Coauthor network: merkwürdigerweise taucht Stefan Nagel nicht als Coauthor auf
- [x] Coauthor network: bei mir sind blaue, graue und grüne Balken zu sehen - welche Relevanz hat das? => vielleicht hilft eine Legende
- [x] Add activity: in englischer Version für "Students & Guests" bei "Category" deutsche Beschreibungen und Textfeld "Details (Stipendium etc.)" mit deutsch
- [x] Add activity: "Title" => "Bitte Rechtschreibung ..." => in englisch?
- [x] Bei Reviews gibt es nur die Kategorie Journal - ich hatte ein Grant propsal zum Review. Habe es jetzt auch dort eingetragen, aber vielleicht wäre es schön, diese Rubrik extra zu haben? Oder Journal/Grant proposal gemeinsam?
- [x] bei den "Postern" kann man aktuell nur 2 Autoren auflisten. Ich habe nicht gefunden wie man hier zusätzliche Autorenfelder anlegen kann
- [x] generell - wenn man etwas eingetragen hat und auf "weitere Aktivitäten hinzufügen" geht, kommt "Error"
- [x] bei "alle/meine Aktivitäten" wäre es gut, wenn es auf der rechten Seite noch einen Button für Löschen (Papierkorb) gibt, damit man doppelte oder falsch eingetragene Positionen direkt wieder entfernen kann
- [x] es fehlt noch eine Oberfläche für Lehre/Vorlesungen. Es gibt zwar "Studierende/Gäste", aber hier erfordert die Oberfläche, dass überall Namen eingetragen werden. Das mach bei Vorlesungen und Praktika mit mehreren Teilnehmern keinen Sinn.
- [x] mir ist noch nicht ganz klar, was über "Misc" eingetragen werden soll bzw. machne Aktivitäten lassen sich hier nicht gut darstellen; z.B. Mitgliedschaften in Gremien, Mentoring-Aktivitäten,... 
- [x] misc: Titel/Beschreibung
- [x] bei Review-Aktivitäten sollte man auch di Möglichkeit haben die Begutachtung von Forschungsanträgen einzutragen, oder Begutachtung von wissenschaftlichen Abschlussarbeiten, etc.
- [x] Plausibilitätscheck für Jahreszahlen! sodass 72020 nicht als Jahreszahl eingetragen werden kann. Keine Jahre aus der Zukunft (+1???) und vielleicht auch nicht vor 1900?
- [x] Controlling Dashboard

## Publikationen:
- [x] Publikationen können bearbeitet werden
- [x] Magazine-Article: funktioniert hinzufügen über eine DOI?
- [x] der Pub-Type muss standardisiert werden
- [x] Formatierungen für unterschiedliche Pub-Typen hinzufügen (zurzeit nur Journal-Artikel)
- [x] Corrections? Wie funktioniert das überhaupt? Reichen ein Boolean und eine Checkbox aus?
- [x] Vermeidung von Datendoppelung: Suche nach DOI/PM-ID
- [x] Knöpfe funktionieren noch nicht: nicht Autor und nicht Affiliation.
- [x] Knöpfe funktionieren nicht bei Editorenschaften: diese vielleicht generell extra aufführen?
- [x] Es sollen Publikationen als PDF hinterlegt werden: dafür soll beim Hinzufügen ein Upload möglich sein und auf der Übersichtsseite ein Link zum PDF
- [x] Warnmeldung wenn keine Autoren mit DSMZ-Affiliation angezeigt werden.
- [x] Eine weitere Kategorie sind Preprints. Diese sollten ebenfalls hinzufügbar sein.
- [x] beim Hinzufügen von Publikationen sollte angezeigt werden, zu welcher Abteilung die Publikation zugeordnet wird. Dies sollte auch als Datenfeld in die Datenbank geschrieben werden. Die Zugehörigkeit sollte nachträglich bearbeitet werden können.
- [ ] Import: Nutzer können Daten aus bspw. BibTeX importieren

## Confirmation:
Außerdem will ich einen Bestätigungsmechanismus zu allen Mehr-Autor-Aktivitäten hinzufügen:

- [x] Boolean für jeden Autor/Editor: `approved`:
- [x] Beim Nutzer, der den Datensatz hinzufügt, ist der Wert automatisch true
- [x] bei allen anderen wird auf der Startseite ein Hinweis gezeigt: können Approven oder Ablehnen (z.B. wenn nicht Autor der Publikation oder Affiliation nicht DSMZ)

## Approve:
- [x] Beim Akzeptieren des Quartals sollten Fehlermeldungen angezeigt werden
- [x] Außerdem: neue, noch unbestätigte Confirmations
- [x] Nur bereits vergangene Quartale können bestätigt werden
- [ ] Das Controlling kann eine Abfrage starten, woraufhin Mails an die Wissenschaftler geschickt werden

## Reports:
- [x] hier werden genau definierte Zeiträume gebraucht (Start und Ende)
- [x] Support für verschiedene Zitationsstile
- [x] Für Wissenschaftler: nur eigener Name fett
- [ ] Filtern nach Abteilungen usw.

## Statistik:
- [x] Co-Autoren-Netzwerk
- [ ] Kollaboration-Network

## Nutzermanagement:
- [ ] Neue Nutzer müssen angelegt werden können
- [x] Vorhandene Nutzer sollten bearbeitet werden können (z.B. Abteilungen zuordnen, Namen ändern usw.; die Frage ist hier, wie viel wir über LDAP lösen können...)
- [x] Vielleicht wäre ein Rechtevergabesystem sinnvoll?


- [x] Dashboard mit Statistiken und wichtigen Links
- [ ] Schreibe Impact Factor in die Pub-Tabelle
- [ ] Speichere ISSN in den Publikationen (Journalname kann variieren)
- [ ] Warnung wenn IF nicht bekannt
- [ ] Files: Error wenn Datei größer als 15 MB!


<!-- 
<div class="csl-entry">Feynman, R. (2000). Probability Theory. In <i>Reliability, Maintenance and Logistic Support</i> (pp. 13–49). Springer US. https://doi.org/10.1007/978-1-4615-4655-9_2</div> -->