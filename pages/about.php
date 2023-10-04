<div class="container">
    <h1>OSIRIS</h1>
    <h4 class="text-osiris font-weight-normal mt-0">The Open, Simple and Integrated Research Information System</h4>

    <p>
        <?= lang(
            'OSIRIS is our attempt to create a light-weight and open-source research information system; made-to-measure the needs of smaller institutes such as the DSMZ. Thereby, usability is my key concern.',
            'OSIRIS ist unser Ansatz, ein leichtgewichtiges Open-Source Forschungsinformationssystem zu schaffen; maßgeschneidert auf die Bedürfnisse kleinerer Institute wie der DSMZ. Dabei ist die Nutzerfreundlichkeit der Seite mein größtes Anliegen.'
        ) ?>
    </p>

    <blockquote class="mb-20 font-size-16" style="border-left: 5px solid var(--osiris-color);">
        <p>
            A user interface is like a joke. If you have to explain it, it’s not that good”.
        </p>
        <em>— Martin Leblanc</em>
    </blockquote>

    <div class="alert primary">
        <p>
            <?= lang('Read more on OSIRIS on the official webpage (only in German):', 'Lies mehr über OSIRIS auf der offiziellen Webseite:') ?>
            <a href="https://osiris-app.de/" target="_blank" rel="noopener noreferrer">osiris-app.de</a>.
        </p>
    </div>

    <h3>
        <?= lang('Get in touch', 'Das Team hinter OSIRIS ') ?>
    </h3>

    <div class="row row-eq-spacing">
        <div class="col-md-6">
            <div class="content">
                <img src="<?= ROOTPATH ?>/img/julia.jpeg" class="img-fluid mw-full w-300">

                <h5 class="title">
                    Dr. Julia Koblitz
                </h5>

                <p>
                    <?= lang(
                        '
                        Julia is a scientist at the DSMZ. She leads the Data Integration group in the Department of Bioinformatics and has developed several databases and interactive web applications, for example MediaDive, CellDive and MetaboMAPS. Her research focuses on microorganisms and artificial intelligence.',
                        'Julia ist Wissenschaftlerin an der DSMZ. Sie leitet die Arbeitsgruppe Datenintegration in der Abteilung Bioinformatik und hat schon mehrere Datenbanken und interaktive Web-Applikationen entwickelt, beispielsweise MediaDive, CellDive und MetaboMAPS. Ihre Forschungsschwerpunkte liegen bei Mikroorganismen und künstlicher Intelligenz.'
                    ) ?>
                </p>

                <p>
                    <a href="mailto:julia.koblitz@dsmz.de"><?= lang('Contact Julia', 'Kontaktiere Julia') ?></a>, <?= lang('if you have any technical questions', 'wenn du Fragen zur technischen Umsetzung hast') ?>.
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="content">
                <img src="<?= ROOTPATH ?>/img/dominic.jpeg" class="img-fluid mw-full w-300">

                <h5 class="title">
                    Dominic Koblitz
                </h5>

                <p>
                    <?= lang(
                        'Dominic is a business economist and works in project controlling at the DSMZ. Among other things, he is responsible for reporting and needs the data collected in OSIRIS for this purpose.',
                        'Dominic ist Betriebswirt und arbeitet im Projektcontrolling an der DSMZ. Er ist u.a. zuständig für die Berichterstattung und benötigt dazu die Daten, die in OSIRIS erhoben werden.'
                    ) ?>
                </p>
                <p>
                    <a href="mailto:dominic.koblitz@dsmz.de"><?= lang('Contact Dominic', 'Kontaktiere Dominic') ?></a>, <?= lang('if you have questions about the reporting', 'wenn du Fragen zur Berichterstattung hast') ?>.
                </p>
            </div>
        </div>
    </div>
</div>