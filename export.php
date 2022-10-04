<?php
Route::get('/export/?', function () {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => lang("Export")]
    ];

    include BASEPATH . "/header.php";
    echo "TODO";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/export/publications', function () {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => 'Export', 'path' => "/export"],
        ['name' => lang("Publications", "Publikationen")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/export/publications.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/export/reports', function () {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => 'Export', 'path' => "/export"],
        ['name' => lang("Reports", "Berichte")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/export/reports.php";
    include BASEPATH . "/footer.php";
}, 'login');


function getTypeTitle($type)
{
    $types = [
        'teaching' => 'Lehre und Gäste',
        'poster' => 'Poster',
        'lecture' => 'Vorträge',
        'publication' => 'Publikationen',
        'misc' => 'Weitere Aktivitäten',
        'review' => 'Reviews &amp; Editorial Boards'
    ];
    return $types[$type] ?? '';
}


Route::post('/export/publications', function () {
    require_once BASEPATH . '/php/_db.php';
    require_once BASEPATH . '/php/format.php';
    $Format = new Format($_SESSION['username'], 'word');

    $order = array(
        'publication',
        'lecture',
        'poster',
        'review',
        'misc',
        'teaching',
    );
    // select data
    $collection = $osiris->activities;
    $options = ['sort' => ["type" => 1, "year" => 1, "month" => 1]];

    $filter = [];
    if (isset($_POST['filter']['type']) && !empty($_POST['filter']['type'])) {
        $filter['type'] = trim($_POST['filter']['type']);
    }
    if (isset($_POST['filter']['user']) && !empty($_POST['filter']['user'])) {
        $filter['$or'] = [['authors.user' => $_POST['filter']['user']], ['editors.user' => $_POST['filter']['user']]];
    }
    if (isset($_POST['filter']['year']) && !empty($_POST['filter']['year'])) {
        $filter['year'] = intval($_POST['filter']['year']);
    }
    $cursor = $collection->find($filter, $options);

    $headers = [];

    if ($_POST['format'] == "word") {
        // Creating the new document...
        \PhpOffice\PhpWord\Settings::setZipClass(\PhpOffice\PhpWord\Settings::PCLZIP);
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);

        $phpWord->addTitleStyle(1, ["bold" => true, "size" => 16], ["spaceBefore" => 8]);
        // $phpWord->setOutputEscapingEnabled(true);
        /* Note: any element you append to a document must reside inside of a Section. */

        // Adding an empty Section to the document...
        $section = $phpWord->addSection();

        // sort the elements
        $cursor = $cursor->toArray();
        usort($cursor, function ($a, $b) use ($order) {
            $pos_a = array_search($a['type'], $order);
            $pos_b = array_search($b['type'], $order);
            return $pos_a - $pos_b;
        });

        foreach ($cursor as $doc) {
            if (!in_array($doc['type'], $headers)) {
                $headers[] = $doc['type'];
                $section->addTitle(getTypeTitle($doc['type']), 1);
            }
            $paragraph = $section->addTextRun();

            $line = $Format->format($doc['type'], $doc);
            
            \PhpOffice\PhpWord\Shared\Html::addHtml($paragraph, $line);

            // foreach ($doc['authors'] as $i => $a) {
            //     $author = abbreviateAuthor($a['last'], $a['first']);
            //     if (($a['aoi'] ?? 1) == 1) {
            //         $author = $paragraph->addText($author, ['bold' => true]);
            //     } else {
            //         $author = $paragraph->addText($author);
            //     }
            //     if ($i == count($doc['authors']) - 2) {
            //         $paragraph->addText(" and ");
            //     } elseif ($i < count($doc['authors']) - 1) {
            //         $paragraph->addText(", ");
            //     }
            // }

            // // $result = formatAuthors($doc['authors']);
            // if (!empty($doc['year'])) {
            //     $paragraph->addText(" ($doc[year])");
            // }
            // if (!empty($doc['title'])) {
            //     // $paragraph->addText(" $doc[title].");
            //     //preserve formtting of title:
            //     \PhpOffice\PhpWord\Shared\Html::addHtml($paragraph, " $doc[title].");
            // }
            // if (!empty($doc['journal'])) {
            //     //str_replace("&", "&amp;", " $doc[journal]")
            //     $paragraph->addText(" " . $doc['journal'], ["italic" => true]);

            //     if (!empty($doc['volume'])) {
            //         $paragraph->addText(" $doc[volume]");
            //     }
            //     if (!empty($doc['pages'])) {
            //         $paragraph->addText(":$doc[pages].");
            //     }
            // }
            // if (!empty($doc['doi'])) {
            //     // $result .= " DOI: <a target='_blank' href='http://dx.doi.org/$doc[doi]'>http://dx.doi.org/$doc[doi]</a>";
            // }
            // if (!empty($doc['epub'])) {
            //     $paragraph->addText(" [Epub ahead of print]", ["color" => "#B61F29"]);
            // }
            // $section->addText($result);
        }

        // Download file
        $file = 'Publications.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $xmlWriter->save("php://output");

        // Saving the document as OOXML file...
        // $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        // $objWriter->save('helloWorld.docx');
        // // Saving the document as HTML file...
        // $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        // $objWriter->save('helloWorld.html');



    } else if ($_POST['format'] == "bibtex") {
        header("Content-Type: text/plain; charset=utf-8");

        $bibentries = [
            'journal-article' => "article",
            'article' => "article",
            'Journal Article' => "article",
            'book' => "book",
            'chapter' => "inbook",
            "misc" => "misc"
        ];
        foreach ($cursor as $doc) {

            echo '@' . ($bibentries[trim($doc['pubtype'] ?? 'misc')] ?? 'misc') . '{' . $doc['_id'] . ',' . PHP_EOL;

            if (isset($doc['title']) and ($doc['title'] != '')) echo '  Title = {' . $doc['title'] . '},' . PHP_EOL;
            if (isset($doc['authors']) and ($doc['authors'] != '')) echo '  Author = {' . formatAuthors($doc['authors'], ', ') . '},' . PHP_EOL;
            if (isset($doc['editor']) and ($doc['editor'] != '')) echo '  Editor = {' . $doc['editor'] . '},' . PHP_EOL;
            if (isset($doc['journal']) and ($doc['journal'] != '')) echo '  Journal = {' . $doc['journal'] . '},' . PHP_EOL;
            if (isset($doc['year']) and ($doc['year'] != '')) echo '  Year = {' . $doc['year'] . '},' . PHP_EOL;
            if (isset($doc['number']) and ($doc['number'] != '')) echo '  Number = {' . $doc['number'] . '},' . PHP_EOL;
            if (isset($doc['pages']) and ($doc['pages'] != '')) echo '  Pages = {' . $doc['pages'] . '},' . PHP_EOL;
            if (isset($doc['volume']) and ($doc['volume'] != '')) echo '  Volume = {' . $doc['volume'] . '},' . PHP_EOL;
            if (isset($doc['doi']) and ($doc['doi'] != '')) echo '  Doi = {' . $doc['doi'] . '},' . PHP_EOL;
            if (isset($doc['isbn']) and ($doc['isbn'] != '')) echo '  Isbn = {' . $doc['isbn'] . '},' . PHP_EOL;
            if (isset($doc['publisher']) and ($doc['publisher'] != '')) echo '  Publisher = {' . $doc['publisher'] . '},' . PHP_EOL;
            if (isset($doc['book']) and ($doc['book'] != '')) echo '  Booktitle = {' . $doc['book'] . '},' . PHP_EOL;
            if (isset($doc['chapter']) and ($doc['chapter'] != '')) echo '  Chapter = {' . $doc['chapter'] . '},' . PHP_EOL;
            if (isset($doc['abstract']) and ($doc['abstract'] != '')) echo '  Abstract = {' . $doc['abstract'] . '},' . PHP_EOL;
            if (isset($doc['keywords']) and ($doc['keywords'] != '')) {
                echo '  Keywords = {';
                foreach ($doc['keywords'] as $keyword) echo $keyword . PHP_EOL;
                echo '},' . PHP_EOL;
            }

            echo '}' . PHP_EOL . PHP_EOL;
        }
    }
}, 'login');
