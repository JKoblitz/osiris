

const tour = new Shepherd.Tour({
    useModalOverlay: true,
    defaultStepOptions: {
        classes: 'shadow-lg alert primary p-5 filled-dm',
        useModalOverlay: true,
        scrollTo: { behavior: 'smooth', block: 'center' },
        popperOptions: {
            modifiers: [{ name: 'offset', options: { offset: [0, 12] } }]
        },
        when: {
            show() {
                const currentStepElement = tour.currentStep.el;
                const header = currentStepElement.querySelector('.shepherd-footer');
                const progress = document.createElement('div');
                const innerBar = document.createElement('span');
                const progressPercentage = ((tour.steps.indexOf(tour.currentStep) + 1) / tour.steps.length) * 100 + '%';

                // progress.innerText = `${tour.steps.indexOf(tour.currentStep) + 1}/${tour.steps.length}`;

                progress.className = 'shepherd-progress-bar';
                // progress.className='shepherd-progress-bar';
                innerBar.style.width = progressPercentage;
                // if only one button
                if (document.getElementsByClassName('shepherd-button').length == 1) {
                    progress.style.minWidth = '260px';
                }
                progress.appendChild(innerBar);
                // progress.appendChild(text);
                header.insertBefore(progress, currentStepElement.querySelector('.shepherd-button'));
            }
        },
        //   cancelIcon: {
        //     enabled: true
        //   },

    }
});
const nextBtn = {
    text: lang('Next', "Weiter"),
    action: tour.next,
    classes: 'btn primary'
}

const cancelBtn = {
    text: '<i class="ph-bold fa-xmark"></i>',
    action: tour.cancel,
    classes: 'btn danger'
}
const lastBtn = {
    text: lang('Finish', "Ende"),
    action: tour.cancel,
    classes: 'btn primary'
}

var counter = 0;

tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('You can click on one of these activities to add one manually ...', 'Du kannst auf eine dieser Aktivitäten klicken, um sie manuell hinzuzufügen ...'),
    attachTo: {
        element: '#select-btns',
        on: 'bottom'
    },
    buttons: [
        cancelBtn, nextBtn
    ]
});


tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('... or you can insert a DOI or a Pubmed-ID to look up the bibliographic data automatically. <b>Enter the following DOI (or any other) to advance:</b> <code class="code">10.1093/nar/gkab961</code>',
        '... oder du gibst eine DOI oder eine Pubmed-ID ein, um bibliographische Daten automatisch hnzuzufügen. <b>Gib folgende (oder eine andere) DOI ein, um fortzufahren:</b> <code class="code">10.1093/nar/gkab961</code>.'),
    attachTo: {
        element: '#search-doi',
        on: 'bottom'
    },
    buttons: [
        cancelBtn,
        {
            text: lang('Next', "Weiter"),
            action: null,
            classes: 'btn primary disabled'
        }
    ],
    advanceOn: {
        selector: '#search-doi',
        event: 'submit'
    }
});

tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('Here you can see the form for adding the activity. The DOI will automatically determine what type of activity you have added. If you have used the sample DOI, you can see the Bac<em>Dive</em> publication here.',
        'Hier siehst du das Formular zum Hinzufügen der Aktivität. Anhand der DOI wird automatisch fertgelegt, welche Art der Aktivität du hinzugefügt hast. Solltest du die Beispiel-DOI benutzt haben, siehst du hier die Bac<em>Dive</em>-Publikation.'),
    attachTo: {
        element: '#publication-form',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});

tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('You should check the title carefully. Both formatting and special characters should be correct. Use the format icons above the editor to format the text.',
        'Du solltest den Titel sorgsam überprüfen. Sowohl Formatierung als auch Sonderzeichen sollten korrekt sein. Benutz die Formatsymbole oberhalb des Editors, um den Text zu formatieren.'),
    attachTo: {
        element: '.title-editor',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});


tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('You should also check the authors for correct spelling. If the system did not detect it automatically, you can click on authors to mark them as ' + AFFILIATION + '-authors. You can add new authors via the text field and change the order by drag & drop.',
        'Die Autoren solltest du ebenfalls auf korrekte Schreibweise überprüfen. Falls das System es nicht automatisch erkannt hat, kannst du Autoren anklicken, um sie als ' + AFFILIATION + '-Autoren zu markieren. Du kannst neue Autoren über das Textfeld hinzufügen und die Reihenfolge durch Drag &amp; Drop verändern.'),
    attachTo: {
        element: '.author-list',
        on: 'bottom'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});

tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('If there should be more than one first or last author, you can enter the number here. This is important for the correct allocation of points and reporting.',
        'Falls es mehr als einen Erst- bzw. Letztautor geben soll, kannst du die Anzahl hier eintragen. Dies ist für die korrekte Vergabe der Punkte und die Berichterstattung wichtig.'),
    attachTo: {
        element: '#author-numbers',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});


tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('Unfortunately, OSIRIS does not recognize whether a publication is Open Access or not. Therefore, this checkmark has to be set manually.',
        'OSIRIS erkennt leider nicht, ob eine Publikation Open-Access ist oder nicht. Deshalb muss dieser Haken manuell gesetzt werden.'),
    attachTo: {
        element: '#open_access-div',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});

tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('Publications are often already available online before they are officially published. An example is the NAR-Database issue, which always appears in January, but whose publications are sometimes already visible online in September of the previous year. In this case, a check mark must be set here.<hr> This is important because these publications only show up in reports when they are actually published.',
        'Oftmals sind Publikationen bereits online verfügbar, bevor sie offiziell veröffentlich werden. Ein Beispiel ist das NAR-Database issue, das immer im Januar erscheint, dessen Publikationen aber teilweise schon im September des Vorjahres online sichtbar sind. In diesem Fall muss hier ein Haken gesetzt werden.<hr> Das ist wichtig, da diese Publikationen erst in Berichten auftauchen, wenn sie tatsächlich veröffentlicht werden.'),
    attachTo: {
        element: '#epub-div',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});

tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('A check mark is set here if the publication is a correction. This is included, but does not give any points, for example.',
        'Hier wird ein Haken gesetzt, falls es sich bei der Publikation um eine Correction handelt. Diese wird mit aufgenommen, gibt aber beispielsweise keine Punkte.'),
    attachTo: {
        element: '#correction-div',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});
tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('It is good scientific practice to file publications. OSIRIS allows you to attach a PDF document to each activity. If available, you can file the published document. It is also possible to upload a proof. Slides of lectures or posters can also be filed. The documents can only be viewed internally and are not made available to the public.',
        'Es gehört zur guten wissenschaftlichen Praxis, Publikationen abzulegen. Bei OSIRIS kannst du zu jeder Aktivität ein PDF-Dokument anhängen. Falls verfügbar kannst du also das veröffentlichte Dokument ablegen. Es ist aber auch möglich, einen Proof hochzuladen. Auch Folien von Vorträgen oder Poster können abgelegt werden. Die Dokumente sind nur intern einsehbar und werden nicht der Öffentlichkeit zugänglich gemacht.'),
    attachTo: {
        element: '#file-input-div',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        nextBtn
    ]
});
tour.addStep({
    id: 'guide-' + (counter++),
    text: lang('This button confirms that all data is correct. The entry will be added and you will be redirected to the overview page of the entry. There you can view the entry again and edit it if necessary. A detailed authoring editor is also available. <hr>  If you have used the sample DOI, you can still click Confirm. Since the DOI already exists in our database, you will be redirected to the entry. It is not possible to add two entries with the same DOI or Pubmed ID.',
        'Mit diesem Knopf wird bestätigt, dass alle Daten korrekt sind. Der Eintrag wird hinzugefügt und du wirst zur Übersichtsseite des Eintrages weitergeleitet. Dort kannst du dir den Eintrag noch einmal ansehen und ihn ggf. bearbeiten. Auch ein detaillierter Autoren-Editor steht dir zur Verfügung.  <hr> Wenn du die Beispiel-DOI verwendet hast, kannst du trotzdem auf Bestätigen klicken. Da die DOI bereits in unserer Datenbank existiert, wirst du zu dem Eintrag weitergeleitet. Es ist nämlich nicht möglich, zwei Einträge mit der gleichen DOI oder Pubmed-ID hinzuzufügen.'),
    attachTo: {
        element: '#submit-btn',
        on: 'top'
    },
    buttons: [
        cancelBtn,
        lastBtn
    ]
});

$('#tour').on("click", tour.start)