# Fragen

1. wenn ich ein Poster präsentiere auf einer Konferenz, die vom 30.06. bis zum 01.07. stattfindet, liegt das in Quartal 2 oder 3? Oder anders gefragt: entscheidet das Anfangs- oder das Enddatum über das Quartal?


# Changelog

## 31.07.22
- Publikationen können jetzt hinzugefügt werden. Sowohl Journal-Artikel als auch Bücher funktionieren. 
- Folgendes ist noch zu tun: 
  - Magazine-Article: funktioniert hinzufügen über eine DOI?
  - der Pub-Type muss standardisiert werden
  - Formatierungen für unterschiedliche Pub-Typen (zurzeit nur Journal-Artikel)
  - Corrections? Wie funktioniert das überhaupt?
  - Vermeidung von Datendoppelung: Suche nach DOI/PM-ID
  - Boolean für jeden Autor/Editor: `approved`:
    - Beim Nutzer, der hinzufügt automatisch true
    - bei allen anderen wird auf der Startseite ein Hinweis gezeigt: können Approven oder Ablehnen (z.B. wenn nicht Autor der Publikation oder Affiliation nicht DSMZ)
  - Knöpfe funktionieren noch nicht: nicht Autor und nicht Affiliation.

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