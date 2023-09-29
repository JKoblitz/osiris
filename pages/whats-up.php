<h1>
    <?= lang('What\'s up?', 'Was ist los?') ?>
</h1>



<div class="row row-eq-spacing">
    <div class="profile-widget col-lg-6">
        <h2 class="title"><?= lang('Latest conferences', 'Letzte Konferenzen') ?></h2>
        <?php

        $query = $osiris->activities->find(
            [
                'conference' => ['$ne' => null],
                'year' => ['$lte' => CURRENTYEAR],
                'month' => ['$lte' => CURRENTMONTH]
            ],
            [
                'sort' => [
                    'year' => -1, 'month' => -1, 'date' => -1
                ],
                'limit' => 10,
                'projection' => ['conference' => 1, 'year' => 1]
            ]
        );

        $conferences = [];
        foreach ($query as $c) {
            if (count($conferences) >= 5) break;
            // TODO: Levenshtein?
            if (array_key_exists($c['conference'], $conferences)) continue;
            $conferences[$c['conference']] = $c['year'];
        }

        foreach ($conferences as $conf => $year) {
            $docs = $osiris->activities->find(['conference' => $conf, 'year' => $year]);
            $docs = $DB::doc2Arr($docs->toArray());
        ?>
            <div class="alert mb-10">
                <h3 class="title">
                    <?= $conf ?>
                </h3>

                <a onclick="$(this).hide().next().removeClass('hidden');">
                    <?php if (count($docs) == 1) { 
                        echo lang('1 Contribution', '1 Beitrag');
                     } else {
                        echo count($docs). lang(' Contributions', ' Beiträge');
                     }?>
                </a>
                <div class="hidden">
                    <table class="table simple small border-top">
                        <?php foreach ($docs as $doc) :
                            if (!isset($doc['rendered'])) $DB->renderActivities(['_id' => $doc['_id']]);
                        ?>
                            <tr>
                                <td>

                                    <?= $doc['rendered']['icon'] ?>
                                </td>
                                <td class="pb-5">
                                    <?= $doc['rendered']['web'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>


            </div>
        <?php } ?>

    </div>
</div>