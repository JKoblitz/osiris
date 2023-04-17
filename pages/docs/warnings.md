# <i class="ph ph-exclamation-triangle text-osiris"></i> Warnungen

Um bekannte Fehlerquellen und Probleme zu vermeiden, führt OSIRIS im Hintergrund eine ganze Reihe von Logik-Checks durch. Sollten dabei Probleme gefunden werden, werden diese entweder automatisch behoben (sofern möglich) oder an die Nutzer:innen zurückgespielt. 

# Die Seite "Warnungen"

Die Seite "Warnungen" ist nur über die eigene Profilseite oder die Seite "Mein Jahr erreichbar" und auch nur, wenn Warnungen vorhanden sind.

In deinem persönlichen Profil gibt es eine Infobox, die neben nützlichen Links und Mitteilungen auch Warnungen enthalten kann. Diese sind recht deutlich als rote Box dargestellt:

<div class="demo">
    <h5 class="title font-size-16 mt-0">Dies ist deine persönliche Profilseite.</h5>
    <div class="btn-group btn-group-lg">
        <span class="btn" data-toggle="tooltip" data-title="Aktivität hinzufügen">
            <i class="icon-activity-plus text-osiris ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Mein Jahr">
            <i class="ph ph-calendar text-success ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Meine Aktivitäten ">
            <i class="icon-activity-user text-primary ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Bearbeite Profil">
            <i class="ph ph-user-list text-muted ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Meine Errungenschaften">
            <i class="ph ph-trophy text-signal ph-fw"></i>
        </span>
    </div>
    <div class="alert alert-danger mt-20">
        <a class="link text-danger" href="#test">
            Du hast 6 ungelöste Probleme mit deinen Aktivitäten.
        </a>
    </div>
</div>


Auch auf der Seite [Mein Jahr](my-year) werden Warnungen dargestellt. Sie stehen als Hinweise direkt unterhalb der angezeigten Aktivitäten:

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

Durch einen Klick auf die oben gezeigten Links kommt man zur Seite "Warnungen". Diese Seite ist in Sektionen eingeteilt, die sich jeweils mit einem Typ von Problem beschäftigen. Jede Sektion wird nur dargestellt, wenn entsprechende Warnungen gefunden wurden. 


## Arten von Warnungen

Im Folgenden werde ich kurz auf die Warnungen eingehen, die OSIRIS zurzeit darstellt. Dabei werde ich jeweils kurz erklären, warum die Warnung angezeigt wird, warum es nötig ist, sie zu lösen, und wie man sie löst.

### Überprüfung der Autorenschaft nötig
Manchmal fügen andere Wissenschaftler:innen oder Mitglieder des Institutes wissenschaftliche Aktivitäten hinzu,
an denen du ebenfalls beteiligt warst. Das System versucht, diese automatisch zuzuordnen, weshalb sie hier 
in dieser Liste auftauchen. Allerdings kann dabei sehr viel schief gehen. Für die Berichterstattung ist es z.B. 
nicht nur wichtig, dass die bibliographischen Daten korrekt sind, die Nutzer müssen auch korrekt zugeordnet sein. 
Deshalb ist es wichtig, **ob du das überhaupt bist** (oder vielleicht jemand mit einem ähnlichen Namen), 
dass **dein Name korrekt geschrieben** ist und du außerdem **der DSMZ zugehörig** bist. 

<q>**Ich habe diese Aktivität doch aber schon einmal bestätigt.**</q><br>
Das kann sehr gut sein. Denn sobald eine Aktivität bearbeitet wird, und sei es nur, dass ein Dokument hinterlegt oder ein Rechtschreibfehler im Titel korrigiert wurde, wird die Bestätigung aller Autoren zurückgesetzt. Dadurch soll vermieden werden, dass ohne dein Wissen bereits bestätigte Aktivitäten bearbeitet werden. 
Du kannst übrigens beim Bearbeiten einer Aktivität einen [Editor-Kommentar](add-activities#Editorkommentare) hinterlassen, damit deine Koautoren wissen, was genau geändert wurde. Das erleichtert für alle diesen Prozess.


Die Warnmeldung sieht folgendermaßen aus:

<div class="demo">
    <p class="m-0">
        Lorenz Christian Reimer hat die folgende Aktivität bearbeitet.<br><b>Editor-Kommentar:</b> <em>Als Open Access markiert.</em> 
    </p>
    <p>
        <span class="mr-20"><span data-toggle="tooltip" data-title="Journal article">
            <i class="ph ph-lg text-publication ph-file-lines"></i>
        </span></span>
        <b>Koblitz, J.</b>, Halama, P., Spring, S., Thiel, V., Baschien, C., Hahnke, R.L., Pester, M., Overmann, J. and Reimer, L.C. (2022) Media<i>Dive</i>: the expert-curated cultivation media database.  <i>Nucleic Acids Research</i> DOI: <a target="_blank" href="https://doi.org/10.1093/nar/gkac803">https://doi.org/10.1093/nar/gkac803</a> <span style="color:#B61F29;">[Online ahead of print]</span> <i class="icon-open-access text-success" title="Open Access"></i> 
    </p>
    <div class="" id="approve-632da4672199cd3df8dbc166">
                                    Bitte bestätige (evtl. erneut), dass du Autor:in bist:                                                <br>
        <span class="btn btn-sm text-success" data-toggle="tooltip" data-title="Ja, und ich war der DSMZ angehörig">
            <i class="ph ph-check ph-fw"></i>
        </span>
        <span class="btn btn-sm text-signal" data-toggle="tooltip" data-title="Ja, aber ich war nicht der DSMZ angehörig">
            <i class="ph ph-push-pin-slash ph-fw"></i>
        </span>
        <span class="btn btn-sm text-danger" data-toggle="tooltip" data-title="Nein, das bin ich nicht">
            <i class="ph ph-x ph-fw"></i>
        </span>
        <span class="btn btn-sm text-primary" data-toggle="tooltip" data-title="Aktivität bearbeiten">
            <i class="ph ph-regular ph-pencil-simple-line"></i>
        </span>
        <span class="btn btn-sm text-primary" data-toggle="tooltip" data-title="Aktivität ansehen">
            <i class="ph ph-regular ph-arrow-fat-line-right"></i>
        </span>
    </div>
</div>

Wie man sehen kann, befinden sich unter der Aktivität fünf Knöpfe: die ersten drei führen eine Aktion aus, die anderen beiden sind Links zu anderen Seiten. Bei allen Aktionen wird die Warnung gelöst und verschwindet somit von der Warnungs-Seite. Die Knöpfe tun im Detail folgendes:

<i class="ph ph-check ph-fw text-success mr-10"></i> Hier kannst du die Aktivität bestätigen, wenn das tatsächlich deine Aktivität ist und du dabei Autor der DSMZ warst. Du erhältst Coins für die Aktivität und wirst im Reporting als DSMZ-Autor berichtet.

<i class="ph ph-push-pin-slash ph-fw text-signal mr-10"></i> Hiermit bestätigst du, dass es sich zwar um deine Aktivität handelt, du aber nicht Autor der DSMZ warst. Die Aktivität taucht in deinem Profil auf, du bekommst aber keine Coins dafür. 

<i class="ph ph-x ph-fw text-danger mr-10"></i> Das bist du nicht. Hiermit entfernst du die Aktivität aus deinem Profil. 

<i class="ph-fw ph ph-regular ph-pencil-simple-line text-primary mr-10"></i> Hier kannst du die Aktivität direkt bearbeiten, falls dir Unstimmigkeiten auffallen. Du musst sie nicht zusätzlich bestätigen, wenn du sie bearbeitest, wirst du selbst direkt abgehakt.

<i class="ph-fw ph ph-regular ph-arrow-fat-line-right text-primary mr-10"></i> Zu guter Letzt kannst du dir hier die Aktivität ansehen. Dort findest du auch jede Menge weitere Informationen, sowie hilfreiche Links.


Solltest du sehr viele Überprüfungen haben, kannst du auch alle auf einmal bestätigen. Dafür gibt es am Anfang der Liste dem Knopf <button class="btn btn-sm text-success"><i class="ph ph-check"></i>Alle bestätigen</button>. Tu dies aber bitte nur, wenn du sicher bist, dass sie korrekt hinzugefügt wurden und alle auch wirklich **deine Aktivitäten** sind. Du solltest wenigstens einmal über die Liste scrollen und schauen, ob sie dir bekannt vorkommen.


### Online ahead of print
[Online ahead of print] bedeutet, dass die Publikation bereits online verfügbar ist, die eigentliche Publikation in einem Issue jedoch noch aussteht. Als Beispiel kann man das NAR database issue nennen, bei dem Publikationen bereits im September oder Oktober online verfügbar sind, obwohl das Issue erst im darauffolgenden Januar erscheint.  

**Diese Publikationen können in den Berichterstattungen nicht berücksichtigt werden.** Sie werden in OSIRIS aufgenommen, um sie nicht aus den Augen zu verlieren und weil sie bereits erbrachte Leistungen darstellen. Doch aus diesem Grund wird regelmäßig abgefragt, ob die Publikation nun veröffentlicht wurde. Denn erst dann kann sie in der Berichterstattung berücksichtigt werden und erst dann bekommt ihr Coins für diese Leistung.

**Die bibliographischen Daten müssen dazu erneut überprüft werden.** Dabei muss der Haken bei "Epub" entfernt werden und i.d.R. wird auch das Veröffentlichungsdatum angepasst. Des Weiteren passiert es auch, dass sich an den bibliographischen Daten selbst noch etwas ändert. Deshalb überprüft bitte sorgfältig, ob alle Daten stimmen.


### Studenten-Abschluss
Um sicherzustellen, dass bei Abschlussarbeiten immer der korrekte Status und das korrekte Abschlussdatum angegeben ist, gibt OSIRIS eine Warnung aus, sollten sich diese Arbeiten noch immer "in Progress" befinden, obwohl das Abschlussdatum in der Vergangenheit befindet. **Bitte überprüft, ob die Arbeit bereits abgeschlossen wurde.** In diesem Fall tragt bitte ein, ob die Arbeit erfolgreich beendet wurde oder nicht und gebt das korrekte Abschlussdatum an. Sollte sich die Abschlussarbeit noch immer "in Progress" befinden, verlängert bitte den Zeitraum, indem ihr ein neues voraussichtliches Abschlussdatum angebt. OSIRIS wird euch dann zu gegebener Zeit erneut fragen, ob die Arbeit erfolgreich abgeschlossen wurde.


### Open-end
Diese Warnung wird einmal im Quartal auftauchen, solltet ihr Mitglied in Gremien, Editorial Boards oder dergleichen sein. Der Hintergrund ist, dass solche Aktivitäten ohne Enddatum angegeben werden. Das bedeutet, sie werden euch jedes Jahr wieder in die Timeline geschrieben, solange bis ihr selbst ein Enddatum festlegt. Einmal im Quartal werdet ihr hier daran erinnert, dass diese Aktivität noch immer unbeendet ist. Im folgenden seht ihr, wie so eine Warnmeldung aussieht. 

<div class="demo">
    <b>Koblitz, J.</b> Mitglied in einem Gremium, von 18.01.2023 bis heute.                    
    <div class="alert alert-signal">
        <span class="btn btn-sm text-success">
            <i class="ph ph-check"></i>
            Ja                  
        </span>
        <span class="btn btn-sm text-danger">
            <i class="ph ph-x"></i>
            Nein (Bearbeiten)
        </span>
    </div>
</div>

Der <span class="btn btn-sm text-success"><i class="ph ph-check"></i> Ja</span>-Knopf wirkt dabei wie ein Snooze-Button, mit dem die Anzeige der Warnmeldung bis zum nächsten Quartal unterdrückt wird. Wenn ihr jedoch <span class="btn btn-sm text-danger"><i class="ph ph-x"></i> Nein</span> auswählt, werdet ihr auf die "Aktivität bearbeiten"-Seite weitergeleitet, wo ihr das Enddatum der Aktivität festlegen könnt.


### Nicht-standardisiertes Journal
Diese Warnmeldung kann durch zwei Umstände entstehen. 

Einerseits ist sie ein Überbleibsel von der OSIRIS-Beta, als Journale noch nicht standardisiert wurden. Lass mich einen Moment ausholen (oder lies einfach den nächsten Absatz): Seit Dezember 2022 werden alle Aktivitäten, die mit Journalen assoziert sind (Journal-Artikel, Reviews, usw.) auch immer mit der ID des Journals aus der OSIRIS-Datenbank verknüpft. Dadurch kann leichter der Impact Factor oder die Abkürzung des Journals abgerufen werden. Außerdem wird dadurch sichergestellt, dass die Journale in der OSIRIS-Datenbank existieren. Zu diesem Zweck wurde ein spezieller Mechanismus eingebaut, mit dem neue Journale aus der NLM-Datenbank abgefragt werden können. Dazu könnt ihr mehr [hier](add-activities#das-journal-bearbeiten) lesen. Bei einigen älteren Einträgen hat das nachträgliche Matching der Journale jedoch nicht funktioniert. Deshalb tauchen sie hier noch auf. Sorry.

Andererseits entstehen solche Fehler, wenn über die **Import-Funktion** Publikationen hinzugefügt werden, deren Journale entweder noch nicht in OSIRIS sind, oder aufgrund von unterschiedlicher Schreibweise des Titels nicht richtig zugeordnet werden konnten. Das ist sehr schade, da dadurch jetzt leider etwas Aufwand auf euch zukommt. Doch damit OSIRIS und all seine Features gut funktionieren, ist es leider nicht abdingbar.

<div class="demo">
    <b>Koblitz, J.</b> Reviewer für  <i>Artificial Intelligence Review</i>. September 2022.                    <div class="alert alert-signal" id="approve-632da4672199cd3df8dbc168">
        <span class="btn btn-sm text-primary">
            <i class="ph ph-edit"></i>
            Aktivität bearbeiten
        </span>
    </div>
</div>

Tatsächlich gibt es nur einen Weg, dieses Problem zu lösen: ihr klickt auf  <span class="btn btn-sm text-primary"> <i class="ph ph-edit"></i> Aktivität bearbeiten</span> und editiert das Journal. Klickt dazu auf das Bearbeiten-Icon <i class="ph ph-edit"></i> neben dem Datenfald mit dem Journal und wählt ein Journal aus der Liste aus. Wie das im Detail funktioniert findet ihr  [hier](add-activities#das-journal-bearbeiten).

