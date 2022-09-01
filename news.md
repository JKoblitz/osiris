# Fragen @ Controlling

1. Wenn ich ein Poster präsentiere auf einer Konferenz, die vom 30.06. bis zum 01.07. stattfindet, liegt das in Quartal 2 oder 3? Oder anders gefragt: entscheidet das Anfangs- oder das Enddatum über das Quartal?
2. Welche Berichte werden gebraucht? Was kommt dort rein und welche Zeiträume beinhaltet es? Gibt es Vorlagen, an denen ich mich orientieren kann?
3. Wäre es möglich, eine Alpha-Testphase mit ausgewählten Wissenschaftlern zu machen? Vielleicht mit Frau Fischer abklären?
4. Nutzerdaten wurden zurzeit nur aus der Telefonliste importiert. Wer wird die Nutzer in OSIRIS in Zukunft pflegen? Wie werden Personaldaten im Moment gepflegt? In SAP? Gibt es dort eine Schnittstelle, die man abgreifen kann oder gibt es ein gängiges Export-Format, das importiert werden kann? Oder sollen die Daten händisch gepflegt werden?
5. In diesem Zusammenhang: Müssen Datenschutzbeauftragter und Betriebsrat ebenfalls involviert werden, da wir mit Personaldaten arbeiten?


# Nächste Schritte
Ich muss erstmal weiter an den Publikationen arbeiten. In diesem Bereich ist noch einiges zu tun:
- Publikationen können noch nicht bearbeitet werden
- Magazine-Article: funktioniert hinzufügen über eine DOI?
- der Pub-Type muss standardisiert werden
- Formatierungen für unterschiedliche Pub-Typen hinzufügen (zurzeit nur Journal-Artikel)
- Corrections? Wie funktioniert das überhaupt? Reichen ein Boolean und eine Checkbox aus?
- Vermeidung von Datendoppelung: Suche nach DOI/PM-ID
- Knöpfe funktionieren noch nicht: nicht Autor und nicht Affiliation.

Außerdem will ich einen Bestätigungsmechanismus zu allen Mehr-Autor-Aktivitäten hinzufügen:
- Boolean für jeden Autor/Editor: `approved`:
- Beim Nutzer, der den Datensatz hinzufügt, ist der Wert automatisch true
- bei allen anderen wird auf der Startseite ein Hinweis gezeigt: können Approven oder Ablehnen (z.B. wenn nicht Autor der Publikation oder Affiliation nicht DSMZ)

Reports:
- hier werden genau definierte Zeiträume gebraucht (Start und Ende)
- Support für verschiedene Zitationsstile
- Für Wissenschaftler: nur eigener Name fett

# Changelog

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
- Es wurden mit Teaching & Guests Doktoranden, Studenten und Gäste hinzugefügt.
  - Es gibt ein Interface, um neue Datensätze hinzuzufügen
  - Verantwortliche Wissenschaftler haben in diesem Jahr gelaufene Betreuungen in ihrer Übersicht.
  - Der verantw. Wissenschaftler bekommt einen Hinweis, wenn die Zeit eines Gast/Student/Doktorand abgelaufen ist und kann das Ende bestätigen oder verschieben
  - Abschlussarbeiten können auch abgebrochen werden
- Auf der Übersichtsseite werden jetzt erste Fehlermeldungen angezeigt.