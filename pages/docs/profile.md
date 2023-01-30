# Profilpflege

## Alternative Namen hinzufügen

TODO

## Expertise angeben

TODO

## Profilpflege übertragen

Du kannst in OSIRIS die Pflege deines Profils auf eine:n anderen Nutzer:in übertragen.
Bitte beachte aber, dass diese:r Nutzer **volle Kontrolle** über deinen Account bekommt. Diese Person kann alles tun, was du ebenfalls tun kannst, wie zum Beispiel:

- Aktivitäten hinzufügen, bearbeiten und entfernen
- Quartale bestätigen
- Warnungen beseitigen
- Profilinformationen ändern

Du solltest nur vertrauenswürdigen Personen Zugang zu deinem Profil geben.

Um jemandem Zugriff zu gewähren, logge dich in OSIRIS ein und gehe auf Profil bearbeiten:

<div class="demo">
    <div class="btn-group btn-group-lg">
        <span class="btn" data-toggle="tooltip" data-title="Aktivität hinzufügen">
            <i class="icon-activity-plus text-osiris fa-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Mein Jahr">
            <i class="far fa-calendar text-success fa-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Meine Aktivitäten ">
            <i class="icon-activity-user text-primary fa-fw"></i>
        </span>
        <span class="btn active" data-toggle="tooltip" data-title="Bearbeite Profil">
            <i class="far fa-user-pen text-muted fa-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Meine Errungenschaften">
            <i class="far fa-trophy text-signal fa-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Mein Koautoren-Netzwerk">
            <i class="far fa-chart-network text-osiris fa-fw"></i>
        </span>
    </div>
</div>


Gib in folgendem Feld den Benutzernamen des Nutzers ein, der deinen Nutzer pflegen soll (eine
Autocompletion hilft dir dabei):
<div class="demo">
    <div class="alert alert-danger">
        <div class="title">
            Übertrage die Pflege deines Profils an jemand anderes:
        </div>
        <div class="form-group form-inline mb-0">
            <label for="maintenance">Username:</label>
            <input type="text" list="user-list" name="values[maintenance]" id="maintenance"
                class="form-control" value="juk20">
        </div>
    </div>
</div>

Mit Klick auf "Update" bestätigst du die Änderungen.


> Der *Maintainer* kann nun oberhalb seiner Menüleiste zwischen seinem eigenen und deinem Profil hin und her schalten:

<div class="w-300 demo">
    <div class="sidebar-menu position-static">
        <div action="" class="content">
            <select class="form-control">
                <option value="juk20">Julia Koblitz</option>
                <option value="dok21">Dominic Koblitz</option>
            </select>
        </div>
        <div class="sidebar-title">
            Julia Koblitz
        </div>
        <div class="cta">
            <a class="btn btn-osiris " style="border-radius:2rem">
                <i class="icon-activity-plus mr-10" aria-hidden="true"></i>
                Aktivität hinzuf. 
            </a>
        </div>
        <a class="sidebar-link sidebar-link-osiris with-icon ">
            <i class="far fa-user-graduate" aria-hidden="true"></i>
            Julia Koblitz </a>
        <a class="sidebar-link sidebar-link-osiris with-icon ">
            <i class="far fa-calendar" aria-hidden="true"></i>
            Mein Jahr </a>
        <a class="sidebar-link sidebar-link-osiris with-icon ">
            <i class="icon-activity-user" aria-hidden="true"></i>
            Meine Aktivitäten </a>
        <a class="sidebar-link sidebar-link-osiris with-icon ">
            <i class="far fa-chart-network" aria-hidden="true"></i>
            Visualisierung </a>
        <a class="sidebar-link with-icon mt-10">
            <i class="far fa-right-from-bracket" aria-hidden="true"></i>
            Logout
        </a>
        <div class="sidebar-title">
            Weiteres </div>
        <div class="content">
            ...
        </div>
    </div>
</div>