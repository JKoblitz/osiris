# To-Do-Liste

-[x] Generell alle Titel: Formatierungen über Knöpfe bearbeiten

## Publikationen:
-[ ] Publikationen können noch nicht bearbeitet werden
-[ ] Magazine-Article: funktioniert hinzufügen über eine DOI?
-[ ] der Pub-Type muss standardisiert werden
-[ ] Formatierungen für unterschiedliche Pub-Typen hinzufügen (zurzeit nur Journal-Artikel)
-[ ] Corrections? Wie funktioniert das überhaupt? Reichen ein Boolean und eine Checkbox aus?
-[ ] Vermeidung von Datendoppelung: Suche nach DOI/PM-ID
-[x] Knöpfe funktionieren noch nicht: nicht Autor und nicht Affiliation.
-[ ] Knöpfe funktionieren nicht bei Editorenschaften: diese vielleicht generell extra aufführen?
-[ ] Es sollen Publikationen als PDF hinterlegt werden: dafür soll beim Hinzufügen ein Upload möglich sein und auf der Übersichtsseite ein Link zum PDF
-[ ] Warnmeldung wenn keine Autoren mit DSMZ-Affiliation angezeigt werden.

## Confirmation:
Außerdem will ich einen Bestätigungsmechanismus zu allen Mehr-Autor-Aktivitäten hinzufügen:
-[x] Boolean für jeden Autor/Editor: `approved`:
-[x] Beim Nutzer, der den Datensatz hinzufügt, ist der Wert automatisch true
-[x] bei allen anderen wird auf der Startseite ein Hinweis gezeigt: können Approven oder Ablehnen (z.B. wenn nicht Autor der Publikation oder Affiliation nicht DSMZ)

## Approve:
-[ ] Beim Akzeptieren des Quartals sollten Fehlermeldungen angezeigt werden
-[ ] Außerdem: neue, noch unbestätigte Confirmations
-[ ] Nur bereits vergangene Quartale können bestätigt werden
-[ ] Das Controlling kann eine Abfrage starten, woraufhin Mails an die Wissenschaftler geschickt werden

## Reports:
-[ ] hier werden genau definierte Zeiträume gebraucht (Start und Ende)
-[ ] Support für verschiedene Zitationsstile
-[ ] Für Wissenschaftler: nur eigener Name fett
-[ ] Filtern nach Abteilungen usw.

## Statistik:
-[ ] Co-Autoren-Netzwerk
-[ ] Kollaboration-Network

## Nutzermanagement:
-[ ] Neue Nutzer müssen angelegt werden können
-[ ] Vorhandene Nutzer sollten bearbeitet werden können (z.B. Abteilungen zuordnen, Namen ändern usw.; die Frage ist hier, wie viel wir über LDAP lösen können...)
<!-- 
<div class="csl-entry">Feynman, R. (2000). Probability Theory. In <i>Reliability, Maintenance and Logistic Support</i> (pp. 13–49). Springer US. https://doi.org/10.1007/978-1-4615-4655-9_2</div> -->