
# <i class="ph ph-magnifying-glass-plus text-secondary"></i> Die erweiterte Aktivitäten-Suche

Die Seite **Erweiterte Suche** ermöglicht es dir, Aktivitäten mithilfe erweiterter Filter zu suchen, Daten nach bestimmten Feldern zu aggregieren, deine Abfragen zu speichern und die Ergebnisse in einer detaillierten Tabelle anzusehen.


## Filter anwenden

Um deine Suche zu starten, kannst du verschiedene Filter anwenden.

1. **Baukasten-Modus**:
    - Die Standardansicht zeigt den "Builder", wo du Regeln hinzufügen kannst, um Aktivitäten zu filtern.
    - Klicke auf "Regel hinzufügen", um einen neuen Filter zu erstellen.
    - Wähle eine Kategorie (z.B. "Kategorie", "Typ") und setze die Bedingung (z.B. "gleich", "enthält").

    **Beispiel**:
    - Füge eine Regel hinzu: "Kategorie gleich publication", um alle Aktivitäten zu finden, die Publikationen sind.

2. **Experten-Modus**:
    - Wechsle in den Experten-Modus, indem du auf "Experten-Modus" klickst.
    - In diesem Modus kannst du manuell komplexe Abfragen an MongoDB eingeben.
    - Schreibe deine Abfrage in das bereitgestellte Textfeld.

    **Beispiel**:
    - Gib ein: `{"type":"publication"}`, um dasselbe wie oben zu erreichen.



## Ergebnisse aggregieren

Du kannst deine Suchergebnisse aggregieren, um zusammengefasste Daten zu sehen.

1. Wähle eine Aggregationsoption aus dem Dropdown-Menü.
2. Klicke auf "Anwenden", um die aggregierten Ergebnisse zu sehen.

**Beispiel**:
- Wähle "Jahr", um die Ergebnisse nach dem Jahr zu aggregieren. Du erhältst eine Liste der Jahre mit der Anzahl aller Publikationen.



## Ergebnisse ansehen

Die Ergebnisse deiner Suche werden in einer Tabelle angezeigt.

- Die Spalten umfassen "Typ", "Ergebnis", "Anzahl", "Link".
- Typ und Link sind nur ohne Aggregation relevant, Anzahl nur mit.
- Verwende die Schaltflächen über der Tabelle, um die Daten zu kopieren, nach Excel zu exportieren oder als CSV herunterzuladen.
- In der Excel-Tabelle hast du zudem noch die Spalten "Jahr", "Print", "Subtyp", "Titel" und "Autoren".



## Abfragen speichern und verwalten

Du kannst deine Abfragen für die zukünftige Nutzung speichern.

1. **Eine Abfrage speichern**:
    - Gib einen Namen für deine Abfrage im Bereich "Abfrage speichern" ein.
    - Klicke auf "Abfrage speichern".

    **Beispiel**:
    - Nenne deine Abfrage "Meine Publikations-Suche" und speichere sie.

2. **Eine gespeicherte Abfrage laden**:
    - Klicke auf den Namen einer gespeicherten Abfrage im Bereich "Meine Abfragen".
    - Die Filter und Regeln werden automatisch angewendet.

3. **Eine gespeicherte Abfrage löschen**:
    - Klicke auf das rote "X" neben der gespeicherten Abfrage, die du löschen möchtest.



## Zusätzliche Tipps

- **Filter löschen**: Um eine neue Suche zu starten, lösche alle Filter, indem du die Seite aktualisierst oder jede Regel manuell entfernst.
- **Umgang mit Fehlern**: Wenn ein Fehler auftritt, überprüfe das Format deiner Regeln oder versuche, die Seite neu zu laden.
- **Mehrsprachige Unterstützung**: Die Benutzeroberfläche unterstützt sowohl Englisch als auch Deutsch.

Wir hoffen, dass dir diese Anleitung hilft, die erweiterte Aktivitäten-Suche optimal zu nutzen!

Viel Erfolg bei der Suche!

