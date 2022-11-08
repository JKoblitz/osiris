# To-Do-Liste

## Aus Beta-Feedback
-[ ] In Formularen: es wäre cool, wenn ein "*-Feld" noch nicht ausgefüllt wurde, dieses rot zu markieren, wenn man auf Speichern klickt
-[ ] Ein detailliertes Profil (vielleicht auch mit einem BarChart über ALLE Aktivitäten?)

## Publikationen:
-[x] Publikationen können noch nicht bearbeitet werden
-[x] Magazine-Article: funktioniert hinzufügen über eine DOI?
-[x] der Pub-Type muss standardisiert werden
-[x] Formatierungen für unterschiedliche Pub-Typen hinzufügen (zurzeit nur Journal-Artikel)
-[x] Corrections? Wie funktioniert das überhaupt? Reichen ein Boolean und eine Checkbox aus?
-[x] Vermeidung von Datendoppelung: Suche nach DOI/PM-ID
-[x] Knöpfe funktionieren noch nicht: nicht Autor und nicht Affiliation.
-[x] Knöpfe funktionieren nicht bei Editorenschaften: diese vielleicht generell extra aufführen?
-[x] Es sollen Publikationen als PDF hinterlegt werden: dafür soll beim Hinzufügen ein Upload möglich sein und auf der Übersichtsseite ein Link zum PDF
-[x] Warnmeldung wenn keine Autoren mit DSMZ-Affiliation angezeigt werden.
-[x] Eine weitere Kategorie sind Preprints. Diese sollten ebenfalls hinzufügbar sein.
-[x] beim Hinzufügen von Publikationen sollte angezeigt werden, zu welcher Abteilung die Publikation zugeordnet wird. Dies sollte auch als Datenfeld in die Datenbank geschrieben werden. Die Zugehörigkeit sollte nachträglich bearbeitet werden können.
-[ ] Import: Nutzer können Daten aus bspw. BibTeX importieren

## Confirmation:
Außerdem will ich einen Bestätigungsmechanismus zu allen Mehr-Autor-Aktivitäten hinzufügen:
-[x] Boolean für jeden Autor/Editor: `approved`:
-[x] Beim Nutzer, der den Datensatz hinzufügt, ist der Wert automatisch true
-[x] bei allen anderen wird auf der Startseite ein Hinweis gezeigt: können Approven oder Ablehnen (z.B. wenn nicht Autor der Publikation oder Affiliation nicht DSMZ)

## Approve:
-[x] Beim Akzeptieren des Quartals sollten Fehlermeldungen angezeigt werden
-[x] Außerdem: neue, noch unbestätigte Confirmations
-[x] Nur bereits vergangene Quartale können bestätigt werden
-[ ] Das Controlling kann eine Abfrage starten, woraufhin Mails an die Wissenschaftler geschickt werden

## Reports:
-[x] hier werden genau definierte Zeiträume gebraucht (Start und Ende)
-[x] Support für verschiedene Zitationsstile
-[x] Für Wissenschaftler: nur eigener Name fett
-[ ] Filtern nach Abteilungen usw.

## Statistik:
-[x] Co-Autoren-Netzwerk
-[ ] Kollaboration-Network

## Nutzermanagement:
-[ ] Neue Nutzer müssen angelegt werden können
-[x] Vorhandene Nutzer sollten bearbeitet werden können (z.B. Abteilungen zuordnen, Namen ändern usw.; die Frage ist hier, wie viel wir über LDAP lösen können...)
-[x] Vielleicht wäre ein Rechtevergabesystem sinnvoll?


-[x] Dashboard mit Statistiken und wichtigen Links
<!-- -[ ] Berechne Lom für jeden Autor und schreibe es in die Tabelle???? -->
-[ ] Schreibe Impact Factor in die Pub-Tabelle
-[ ] Speichere ISSN in den Publikationen (Journalname kann variieren)
-[ ] Warnung wenn IF nicht bekannt
-[ ] Files: Error wenn Datei größer als 15 MB!
<!-- 
<div class="csl-entry">Feynman, R. (2000). Probability Theory. In <i>Reliability, Maintenance and Logistic Support</i> (pp. 13–49). Springer US. https://doi.org/10.1007/978-1-4615-4655-9_2</div> -->