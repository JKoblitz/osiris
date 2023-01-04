# Changelog

## 02.01.23
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
  - für den Anfang gibt es 10 Errungenschaften mit eigenen Icons, verschiedenen Leveln und Beschreibungen
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

**Dokumentation**

  - Eine erste Dokumentation wurde bereitgestellt, diese deckt zurzeit erstmal nur das Hinzufügen von Aktivitäten ab
  - In den nächsten Tagen und Wochen werde ich sie um weitere Themen erweitern

OSIRIS geht damit in die Version 1.0 über und verlässt die Betaphase. Wir werden natürlich trotzdem weiterhin Feedback einsammeln und an dem Tool weiterentwickeln. Danke an alle, die an der Betaphase beteiligt waren!


## 18.12.22
- Neues "Experten-Tool": mit der [erweiterten Suche](activities/search) können jetzt alle Aktivitäten detailliert durchsucht werden. 45 Datenfelder sind mit unterschieldichen Optionen durchsuchbar. Ein Anleitungsvideo folgt in Kürze.
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

<!-- 
# Fragen @ Controlling

1. Wenn ich ein Poster präsentiere auf einer Konferenz, die vom 30.06. bis zum 01.07. stattfindet, liegt das in Quartal 2 oder 3? Oder anders gefragt: entscheidet das Anfangs- oder das Enddatum über das Quartal?
   - **Antwort**: Das Startdatum zählt.
2. Welche Berichte werden gebraucht? Was kommt dort rein und welche Zeiträume beinhaltet es? Gibt es Vorlagen, an denen ich mich orientieren kann?
   - **Antwort**: Bericht-Templates wurden zur Verfügung gestellt. Genaue Zeiträume müssen definierbar sein. 
3. Wäre es möglich, eine Alpha-Testphase mit ausgewählten Wissenschaftlern zu machen? Vielleicht mit Frau Fischer abklären?
4. Nutzerdaten wurden zurzeit nur aus der Telefonliste importiert. Wer wird die Nutzer in OSIRIS in Zukunft pflegen? Wie werden Personaldaten im Moment gepflegt? In SAP? Gibt es dort eine Schnittstelle, die man abgreifen kann oder gibt es ein gängiges Export-Format, das importiert werden kann? Oder sollen die Daten händisch gepflegt werden?
5. In diesem Zusammenhang: Müssen Datenschutzbeauftragter und Betriebsrat ebenfalls involviert werden, da wir mit Personaldaten arbeiten?
6. Wir müssen anscheinend noch über die Autor-"Anmerkungen" sprechen. Im Moment können nur Geteilte Autorenschaften eingefügt werden und die werden standardisiert dargestellt. [Beispiel](activities/view/632da4672199cd3df8dbc166) -->
