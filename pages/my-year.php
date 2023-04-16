<?php

$currentuser = $user == $_SESSION['username'];

$YEAR = intval($_GET['year'] ?? CURRENTYEAR);
$QUARTER = intval($_GET['quarter'] ?? CURRENTQUARTER);

$q = $YEAR . "Q" . $QUARTER;

include_once BASEPATH . "/php/_lom.php";
$LOM = new LOM($user, $osiris);

$_lom = 0;

// gravatar
// $email = $scientist['mail']; #. "@dsmz.de";
// $default = ROOTPATH . "/img/person.jpg";
// $size = 140;

// $gravatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $size;

$groups = [
    'publication' => [],
    'poster' => [],
    'lecture' => [],
    'review' => [],
    "teaching" => [],
    "students" => [],
    "software" => [],
    "misc" => [],
];

$timeline = [];
$timelineGroups = [];

$filter = ['authors.user' => $user];
$filter['$or'] =   array(
    [
        "start.year" => array('$lte' => $YEAR),
        '$or' => array(
            ['end.year' => array('$gte' => $YEAR)],
            [
                'end' => null,
                '$or' => array(
                    ['type' => 'misc', 'iteration' => 'annual'],
                    ['type' => 'review', 'role' =>  ['$in' => ['Editor', 'editorial']]],
                )
            ]
        )
        // 'type' => ['$in' => array()]
    ],
    ['year' => $YEAR]
);

$options = [
    'sort' => ["year" => -1, "month" => -1],
    // 'projection' => ['file' => -1]
];
$cursor = $osiris->activities->find($filter, $options);

// dump($cursor->toArray(), true);


$endOfYear = new DateTime("$YEAR-12-31");
$startOfYear = new DateTime("$YEAR-01-01");
foreach ($cursor as $doc) {
    if (!array_key_exists($doc['type'], $groups)) continue;

    // $doc['format'] = $format;
    $groups[$doc['type']][] = $doc;
    $icon = activity_icon($doc, false);

    $date = getDateTime($doc['start'] ?? $doc);

    // make sure date lies in range
    if ($date < $startOfYear) $date = $startOfYear;

    $starttime = $date->getTimestamp();

    $event = [
        'starting_time' => $starttime,
        'type' => $doc['type'],
        'id' => strval($doc['_id']),
        'title' => htmlspecialchars(strip_tags(trim($doc['title'] ?? $doc['journal']))),
        // 'icon' => $icon
    ];
    if (isset($doc['end'])) {
        if (empty($doc['end'])) {
            $date = $endOfYear;
        } else {
            $date = getDateTime($doc['end']);

            // make sure date lies in range
            if ($date > $endOfYear) $date = $endOfYear;
        }
        $endtime = $date->getTimestamp();
        if ($endtime - $starttime > 2595625) {
            // etwa ein monat
            $event['ending_time'] = $endtime;
        }
    } else {
        // $event["display"] = "circle";
    }
    // $timeline[$doc['type']]['times'][] = $event;
    $timeline[] = $event;
    if (!in_array($doc['type'], $timelineGroups)) $timelineGroups[] = $doc['type'];
}

// dump($timeline, true);
// $showcoins = (!($scientist['hide_coins'] ?? true)  && !($USER['hide_coins'] ?? false));
$showcoins = ($scientist['show_coins'] ?? 'no');
if ($showcoins == 'all'){
    $showcoins = true;
} elseif($showcoins == 'myself' && $currentuser){
    $showcoins = true;
} else {
    $showcoins = false;
}
?>



<div class="modal modal-lg" id="coins" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content w-600 mw-full">
            <a href="#" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
           <?php
            include BASEPATH . "/components/what-are-coins.php";
           ?>

        </div>
    </div>
</div>

<div class="">

    <div class="row align-items-center">

        <div class="col">
            <h1 class="mb-0">
                Das Jahr von
                <a href="<?= ROOTPATH ?>/profile/<?= $user ?>" class="link colorless">
                    <?= $name ?>
                </a>
            </h1>

            <h3 class="m-0 text-<?= $scientist['dept'] ?>">
                <?php
                echo $Settings->getDepartments($scientist['dept'])['name'];
                ?>
            </h3>
            <?php if ($showcoins) { ?>
                <p class="lead mt-0">
                    <i class="ph ph-regular ph-lg ph-coin text-signal"></i>
                    <b id="lom-points"></b>
                    Coins in <?= $YEAR ?>
                    <a href='#coins' class="text-muted">
                        <i class="ph ph-regular ph-question-circle text-muted"></i>
                    </a>
                </p>
            <?php } ?>
        </div>
        <div class="col">

            <form id="" action="" method="get" class="w-400 mw-full ml-auto">
                <div class="form-group">
                    <label for="year">
                        <?= lang('Change year and quarter', 'Ändere Jahr und Quartal') ?>:
                    </label>

                    <div class="input-group">

                        <div class="input-group-prepend">
                            <div class="input-group-text" data-toggle="tooltip" data-title="<?= lang('Select quarter', 'Wähle ein Quartal aus') ?>">
                                <i class="ph ph-regular ph-calendar-check"></i>
                            </div>
                        </div>
                        <select name="year" id="year" class="form-control">
                            <?php foreach (range($Settings->startyear, CURRENTYEAR) as $year) { ?>
                                <option value="<?= $year ?>" <?= $YEAR == $year ? 'selected' : '' ?>><?= $year ?></option>
                            <?php } ?>
                        </select>
                        <select name="quarter" id="quarter" class="form-control">
                            <option value="1" <?= $QUARTER == '1' ? 'selected' : '' ?>>Q1</option>
                            <option value="2" <?= $QUARTER == '2' ? 'selected' : '' ?>>Q2</option>
                            <option value="3" <?= $QUARTER == '3' ? 'selected' : '' ?>>Q3</option>
                            <option value="4" <?= $QUARTER == '4' ? 'selected' : '' ?>>Q4</option>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-primary"><i class="ph ph-regular ph-check"></i></button>
                        </div>
                    </div>

                    <p class="text-muted font-size-12 mt-0">
                        <?= lang('The entire year is shown here. Activities outside the selected quarter are grayed out. ', 'Das gesamte Jahr ist hier gezeigt. Aktivitäten außerhalb des gewählten Quartals sind ausgegraut.') ?>
                    </p>
                </div>
            </form>

        </div>
    </div>
    <div class="d-flex">
        <?php
        if ($currentuser) {
            $approved = isset($USER['approved']) && in_array($q, $USER['approved']->bsonSerialize());
            $approval_needed = array();

            $q_end = new DateTime($YEAR . '-' . (3 * $QUARTER) . '-' . ($QUARTER == 1 || $QUARTER == 4 ? 31 : 30) . ' 23:59:59');
            $quarter_in_past = new DateTime() > $q_end;
        ?>

            <?php if (!$quarter_in_past) { ?>
                <a href="#" class="btn disabled">
                    <i class="ph ph-regular ph-check mr-5"></i>
                    <?= lang('Selected quarter is not over yet.', 'Gewähltes Quartal ist noch nicht zu Ende.') ?>
                </a>
            <?php

            } elseif ($approved) { ?>
                <a href="#" class="btn disabled">
                    <i class="ph ph-regular ph-check mr-5"></i>
                    <?= lang('You have already approved the currently selected quarter.', 'Du hast das aktuelle Quartal bereits bestätigt.') ?>
                </a>
            <?php } else { ?>
                <a class="btn btn-lg btn-success" href="#approve">
                    <i class="ph-fill ph-question mr-5"></i>
                    <?= lang('Approve current quarter', 'Aktuelles Quartal freigeben') ?>
                </a>
            <?php } ?>

        <?php } ?>
        <a target="_blank" href="<?= ROOTPATH ?>/docs/my-year" class="btn btn-tour ml-auto" id="tour">
            <i class="ph ph-regular ph-lg ph-question mr-5"></i>
            <?= lang('Read the Docs', 'Zur Hilfeseite') ?>
        </a>

    </div>



    <style>
        .table tbody tr:target,
        .table tbody tr.target {
            -moz-box-shadow: 0 0 0 0.3rem var(--signal-box-shadow-color);
            -webkit-box-shadow: 0 0 0 0.3rem var(--signal-box-shadow-color);
            box-shadow: 0 0 0 0.3rem var(--signal-box-shadow-color);
            z-index: 2;
            position: relative;
        }

        svg .axes line,
        svg .axes path {
            stroke: var(--text-color);
        }

        svg .axes text {
            fill: var(--text-color);
        }
    </style>

    <div id="timeline" class="box">
        <div class="content mb-0">

            <h1>
                <?php
                echo lang('Research activities in ', 'Forschungsaktivitäten in ') . $YEAR;
                ?>
            </h1>


        </div>
    </div>

    <script src="<?= ROOTPATH ?>/js/d3.v4.min.js"></script>
    <script src="<?= ROOTPATH ?>/js/popover.js"></script>
    <!-- <script src="<?= ROOTPATH ?>/js/d3-timeline.js"></script> -->

    <script>
        let typeInfo = JSON.parse('<?= json_encode($Settings->getActivities(null)) ?>');
        let events = JSON.parse('<?= json_encode(array_values($timeline)) ?>');
        console.log(events);
        var types = JSON.parse('<?= json_encode($timelineGroups) ?>');

        // set the dimensions and margins of the graph
        var radius = 3,
            distance = 8,
            divSelector = '#timeline'

        var margin = {
                top: 8,
                right: 25,
                bottom: 30,
                left: 60
            },
            width = 600,
            height = (distance * types.length) + margin.top + margin.bottom;


        var svg = d3.select(divSelector).append('svg')
            .attr("viewBox", `0 0 ${width} ${height}`)

        width = width - margin.left - margin.right
        height = height - margin.top - margin.bottom;

        var timescale = d3.scaleTime()
            .domain([new Date(<?= $YEAR ?>, 0, 1), new Date(<?= $YEAR ?>, 12, 1)])
            .range([0, width]);

        // var types = Object.keys(typeInfo)
        let ordinalScale = d3.scaleOrdinal()
            .domain(types.reverse())
            .range(Array.from({
                length: types.length
            }, (x, i) => i * (height / (types.length - 1))));


        let axisLeft = d3.axisLeft(ordinalScale);
        svg.append('g').attr('class', 'axes')
            .attr('transform', `translate(${margin.left}, ${margin.top})`)
            .call(axisLeft);

        var axisBottom = d3.axisBottom(timescale)
            .ticks(12)
        // .tickPadding(5).tickSize(20);
        svg.append('g').attr('class', 'axes')
            .attr('transform', `translate(${margin.left}, ${height+margin.top+radius*2})`)
            .call(axisBottom);

        d3.selectAll("g>.tick>text")
            .each(function(d, i) {
                d3.select(this).style("font-size", "8px");
            });

        var Tooltip = d3.select(divSelector)
            .append("div")
            .style("opacity", 0)
            .attr("class", "tooltip")
            .style("background-color", "white")
            .style("border", "solid")
            .style("border-width", "2px")
            .style("border-radius", "5px")
            .style("padding", "5px")


        function mouseover(d, i) {

            d3.select(this)
                .select('circle,rect')
                .transition()
                .duration(300)
                // .attr('cy', -2)
                // .attr('y', -radius-2)
                // .attr("r", radius * 1.5)
                .style('opacity', 1)

            //Define and show the tooltip over the mouse location
            $(this).popover({
                placement: 'auto top',
                container: divSelector,
                mouseOffset: 10,
                followMouse: true,
                trigger: 'hover',
                html: true,
                content: function() {
                    var icon = '';
                    if (typeInfo[d.type]) {
                        icon = `<i class="ph-fill ph-${typeInfo[d.type].icon}" style="color:${typeInfo[d.type].color}"></i>`
                    }
                    return `${icon} ${d.title ?? 'No title available'}`
                }
            });
            $(this).popover('show');
        } //mouseoverChord

        //Bring all chords back to default opacity
        function mouseout(event, d) {
            d3.select(this).select('circle,rect')
                .transition()
                .duration(300)
                // .attr("r", radius)
                // .attr('dy', 4)
                // .attr('cy', 0)
                // .attr('y', -radius)
                .style('opacity', .6)
            //Hide the tooltip
            $('.popover').each(function() {
                $(this).remove();
            });
        }

        var dots = svg.append('g')
            .attr('transform', `translate(${margin.left}, ${margin.top})`)
            .selectAll("g")
            .data(events)
            .enter().append("g")
            .attr('transform', function(d, i) {
                var date = new Date(d.starting_time * 1000)
                var x = timescale(date)
                var y = ordinalScale(d.type) //(typeInfo[d.type]['index'] * -(radius * 2)) + radius
                return `translate(${x}, ${y})`
            })

        dots.on("mouseover", mouseover)
            // .on("mousemove", mousemove)
            .on("mouseout", mouseout)
            .on("click", (d) => {
                // $('tr.active').removeClass('active')
                var element = document.getElementById("tr-" + d.id);
                // element.className="active"
                var headerOffset = 60;
                var elementPosition = element.getBoundingClientRect().top;
                var offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            });
        // .style("stroke", "gray")

        var circle = dots.append('circle')
            .style("fill", function(d, i) {
                if (d.ending_time !== undefined) return 'transparent'
                return typeInfo[d.type]['color']
            })
            .attr("r", radius)
            .attr('cy', (d) => Math.random() * distance - distance / 2)
            .style('opacity', .6)

        var lines = dots.append('rect')
            .style("fill", function(d, i) {
                if (d.ending_time === undefined) return 'transparent'
                return typeInfo[d.type]['color']
            })
            .attr('height', radius * 2)
            .attr('width', function(d, i) {
                if (d.ending_time === undefined) return 0

                var date = new Date(d.starting_time * 1000)
                var x1 = timescale(date)
                var date = new Date(d.ending_time * 1000)
                var x2 = timescale(date)
                return x2 - x1
            })
            .style('opacity', .6)
            .attr('rx', 3)
            .attr('y', -radius)
        // ending_time
        // var labels = dots.append('text')
        // .attr("cx", function(d, i) {
        //     var date = new Date(d.starting_time * 1000)
        //     return timescale(date)
        // })
        // .attr("cy", (d)=>((typeInfo[d.type]['index'])*-8)+4)

        //     .on('mouseover', function(d){
        //     d3.select(this).style({opacity:'0.8'})
        //     d3.select("text").style({opacity:'1.0'});
        //             })
        // .on('mouseout', function(d){
        //   d3.select(this).style({opacity:'0.0',})
        //   d3.select("text").style({opacity:'0.0'});
        // })
    </script>





    <?php
    foreach ($groups as $col => $data) {
    ?>

        <div class="box box-<?= $col ?>" id="<?= $col ?>">
            <div class="content">
                <h4 class="title text-<?= $col ?>">
                    <i class="ph ph-regular ph-fw ph-<?= $Settings->getActivities($col)['icon'] ?> mr-5"></i>
                    <?= $Settings->getActivities($col)[lang('name', 'name_de')] ?>
                </h4>
            </div>
            <table class="table table-simple">
                <tbody>
                    <?php
                    // $filter['type'] = $col;
                    // $cursor = $collection->find($filter, $options);
                    // dump($cursor);
                    foreach ($data as $doc) {
                        $id = $doc['_id'];
                        $l = $LOM->lom($doc);
                        $_lom += $l['lom'];

                        if ($doc['year'] == $YEAR) {
                            $q = getQuarter($doc);
                            $in_quarter = $q == $QUARTER;
                            $q = "Q$q";
                        } else {
                            $q = getQuarter($doc);
                            $in_quarter = false;
                            $q = $doc['year'] . "Q$q";
                        }


                        echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "' id='tr-$id'>";
                        // echo "<td class='w-25'>";
                        // echo activity_icon($doc);
                        // echo "</td>";
                        echo "<td class='quarter'>";
                        if (!empty($q)) echo "$q";
                        echo "</td>";
                        echo "<td>";
                        // echo $doc['format'];
                        if ($USER['display_activities'] == 'web') {
                            echo $Format->formatShort($doc);
                        } else {
                            echo $Format->format($doc);
                        }

                        // show error messages, warnings and todos
                        $has_issues = has_issues($doc);
                        if ($currentuser && !empty($has_issues)) {
                            $approval_needed[] = array(
                                'type' => $col,
                                'id' => $id,
                                'title' => $Format->title,
                                'badge' => activity_badge($doc),
                                'tags' => $has_issues
                            );
                    ?>
                            <br>
                            <b class="text-danger">
                                <?= lang('This activity has unresolved warnings.', 'Diese Aktivität hat ungelöste Warnungen.') ?>
                                <a href="<?= ROOTPATH ?>/issues#tr-<?= $id ?>" class="link">Review</a>
                            </b>
                        <?php
                        }

                        ?>

                        </td>

                        <td class="unbreakable w-50">
                            <a class="btn btn-link btn-square" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                <i class="ph ph-regular ph-arrow-fat-line-right"></i>
                            </a>
                            <button class="btn btn-link btn-square" onclick="addToCart(this, '<?= $id ?>')">
                                <i class="<?= (in_array($id, $cart)) ? 'ph-fill ph-shopping-cart ph-shopping-cart-plus text-success' : 'ph ph-regular ph-shopping-cart ph-shopping-cart-plus' ?>"></i>
                            </button>
                            <?php if ($currentuser) { ?>
                                <a class="btn btn-link btn-square" href="<?= ROOTPATH . "/activities/edit/" . $id ?>">
                                    <i class="ph ph-regular ph-pencil-simple-line"></i>
                                </a>
                            <?php } ?>
                        </td>
                        <?php if ($showcoins) { ?>
                            <td class='lom w-50'><span data-toggle='tooltip' data-title='<?= $l['points'] ?>'><?= $l["lom"] ?></span></td>
                        <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="content mt-0">
                <?php if ($currentuser) {
                    $t = $col;
                    if ($col == "publication") $t = "article";
                ?>
                    <a href="<?= ROOTPATH ?>/my-activities?type=<?= $col ?>" class="btn text-<?= $Settings->getActivities($col)['color'] ?>">
                        <i class="ph ph-regular ph-<?= $Settings->getActivities($col)['icon'] ?> mr-5"></i> <?= lang('My ', 'Meine ') ?><?= $Settings->getActivities($col)[lang('name', 'name_de')] ?>
                    </a>
                    <a href="<?= ROOTPATH . "/activities/new?type=" . $t ?>" class="btn"><i class="ph ph-regular ph-plus"></i></a>
                    <?php if ($col == 'publication') { ?>
                        <a class="btn mr-20" href="<?= ROOTPATH ?>/activities/pubmed-search?authors=<?= $scientist['last'] ?>&year=<?= $YEAR ?>">
                            <i class="ph ph-regular ph-magnifying-glass-plus mr-5"></i>
                            <?= lang('Search in Pubmed', 'Suche in Pubmed') ?>
                        </a>
                    <?php } ?>

                <?php } ?>

            </div>

        </div>

    <?php } ?>




    <?php if ($currentuser) { ?>


        <div class="modal modal-lg" id="approve" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content w-600 mw-full" style="border: 2px solid var(--success-color);">
                    <a href="#" class="btn float-right" role="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                    <h5 class="title text-success"><?= lang("Approve quarter $QUARTER", "Quartal $QUARTER freigeben") ?></h5>

                    <?php
                    if (!$quarter_in_past) {
                        echo "<p>" . lang('Quarter is not over yet.', 'Das gewählte Quartal ist noch nicht zu Ende.') . "</p>";
                    } else  if ($approved) {
                        echo "<p>" . lang('You have already approved the currently selected quarter.', 'Du hast das aktuelle Quartal bereits bestätigt.') . "</p>";
                    } else if (!empty($approval_needed)) {

                        $tagnames = [
                            'approval' => lang('Approval needed', 'Überprüfung nötig'),
                            'epub' => 'Online ahead of print',
                            'students' => lang('Student\' graduation', "Studenten-Abschluss"),
                            'openend' => lang('Open-end'),
                            'journal_id' => lang('Non-standardized journal', 'Nicht-standardisiertes Journal')
                        ];

                        echo "<p>" . lang(
                            "The following activities have unresolved warnings. Please <a href='" . ROOTPATH . "/issues' class='link'>review all issues</a> before approving the current quarter.",
                            "Die folgenden Aktivitäten haben ungelöste Warnungen. Bitte <a href='" . ROOTPATH . "/issues' class='link'>kläre alle Probleme</a> bevor du das aktuelle Quartal freigeben kannst."
                        ) . "</p>";
                        echo "<table class='table table-simple'><tbody>";
                        foreach ($approval_needed as $item) {
                            // $type = ucfirst($item['type']);
                            echo "<tr><td class='px-0'>
                                $item[title]
                                <br>
                                $item[badge]";
                            foreach ($item['tags'] as $tag) {
                                $tag = $tagnames[$tag] ?? $tag;
                                echo "<a class='badge badge-danger filled ml-5' href='" . ROOTPATH . "/issues#tr-$item[id]'>$tag</a>";
                            }

                            echo "</td></tr>";
                        }
                        echo "</tbody></table>";
                    } else { ?>

                        <p>
                            <?= lang('
                            You are about to approve the current quarter. Your data will be sent to the Controlling and you hereby confirm that you have entered or checked all reportable activities for this year and that all data is correct. This process cannot be reversed and any changes to the quarter after this must be reported to Controlling.
                            ', '
                            Du bist dabei, das aktuelle Quartal freizugeben. Deine Daten werden an das Controlling übermittelt und du bestätigst hiermit, dass du alle meldungspflichtigen Aktivitäten für dieses Jahr eingetragen bzw. überprüft hast und alle Daten korrekt sind. Dieser Vorgang kann nicht rückgängig gemacht werden und alle Änderungen am Quartal im Nachhinein müssen dem Controlling gemeldet werden.
                            ') ?>
                        </p>

                        <form action="<?= ROOTPATH ?>/approve" method="post">
                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                            <input type="hidden" name="quarter" class="hidden" value="<?= $YEAR . "Q" . $QUARTER ?>">
                            <button class="btn btn-success"><?= lang('Approve', 'Freigeben') ?></button>
                        </form>
                    <?php } ?>

                </div>
            </div>
        </div>
    <?php } ?>

</div>
<script>
    $('#lom-points').html('<?= round($_lom) ?>');
</script>