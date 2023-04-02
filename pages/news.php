
        <div class='container'>
            <h1>OSIRIS</h1>
            <h4 class="text-osiris font-weight-normal mt-0">The Open, Simple and Integrated Research Information System</h4>

            <p>
                <?= lang(
                    'OSIRIS is my attempt to create a light-weight and open-source research information system; made-to-measure the needs of smaller institutes such as the DSMZ. Thereby, usability is my key concern.',
                    'OSIRIS ist mein Ansatz, ein leichtgewichtiges Open-Source Forschungsinformationssystem zu schaffen; maßgeschneidert auf die Bedürfnisse kleinerer Institute wie der DSMZ. Dabei ist die Nutzerfreundlichkeit der Seite mein größtes Anliegen.'
                ) ?>
            </p>

            <blockquote class="mb-20 alert font-size-16" style="border-left: 5px solid var(--primary-color);">
                <p>
                    A user interface is like a joke. If you have to explain it, it’s not that good”.
                </p>
                <em>
                    — Martin Leblanc</em>
            </blockquote>

            <!-- <hr> -->
            <?php
            // include BASEPATH . "/pages/roadmap.php";
            ?>
            <!-- <hr> -->
            <?php
            // $text = file_get_contents(BASEPATH . "/todo.md");
            // $parsedown = new Parsedown;
            // echo $parsedown->text($text);
            ?>
            <hr>
            <?php
            $text = file_get_contents(BASEPATH . "/news.md");
            $parsedown = new Parsedown;
            echo $parsedown->text($text);
            ?>
        <!-- </div>
    </div>
    <div class="col-lg-3 d-none d-lg-block">
        <nav class="on-this-page-nav">
            <div class="content">
                <div class="title">Auf dieser Seite</div>
                <a href="#roadmap">Roadmap</a>
                <a href="#to-do-liste">To-Do-Liste</a>
                <a href="#changelog">Changelog</a>
            </div>
        </nav>
    </div> -->
</div>