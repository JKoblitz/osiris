<?php
$users = $osiris->users->find(['is_scientist' => true], ['sort' => ["last" => 1]]);

$scientist = $_GET['scientist'] ?? $_SESSION['username'];
$selectedUser = $osiris->users->findone(['_id' => $scientist]);

?>


<h1>
    <i class="far fa-chart-network" aria-hidden="true"></i>
    <?= lang('Coauthor network', 'Koautoren-Netzwerk') ?>
</h1>


<form action="" method="get">
    <div class="input-group">
        <select name="scientist" id="scientist-select" class="form-control">
            <?php foreach ($users as $u) { ?>
                <option value="<?= $u['_id'] ?>" <?= $u['_id'] == $scientist ? 'selected' : '' ?>><?= $u['formalname'] ?></option>
            <?php } ?>
        </select>
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">Select</button>
        </div>
    </div>
</form>

<a class="link" href="<?= ROOTPATH ?>/scientist/<?= $scientist ?>"><i class="fas fa-user-graduate"></i>
    <?= lang('View scientist page of ', 'Zeige die Übersichtsseite von ') ?>
    <?= $selectedUser['displayname'] ?>
</a>

<div id="chord" class="d-flex"></div>
<svg id="legend" height=300 width=450></svg>
<div id="chord-info-div" class="tile h-auto" style="display: none;"></div>

<small class="text-muted">
    I know the tooltips are all over the place... I am still working on this.
</small>

<?php
function combinations($array)
{
    $results = array();
    foreach ($array as $a)
        foreach ($array as $b) {
            $t = [$a, $b];
            sort($t);
            if ($a == $b || in_array($results, $t)) continue;
            $results[] = $t;
        }
    return $results;
}

// generate graph json
$all_authors = [];

$labels = [];
$combinations = [];
$marix = [];

$activities = $osiris->activities->find(['authors.user' => "$scientist", 'type' => 'publication']);
$activities = $activities->toArray();
$N = count($activities);

$index = 0;

foreach ($activities as $doc) {
    // dump($doc['authors']);
    $authors = [];
    foreach ($doc['authors'] as $aut) {
        if (!($aut['aoi'] ?? false) || empty($aut['user'])) continue;

        $id = $aut['user'];
        if (array_key_exists($id, $labels)) {
            $name = $labels[$id]['name'];
            $labels[$id]['count']++;
        } else {
            $name = $osiris->users->findone(['_id' => $aut['user']]);
            if (empty($name)) continue;

            $labels[$id] = [
                'name' => $name['formalname'] ?? $name['last'],
                'id' => $id,
                'user' => $aut['user'],
                'dept' => $name['dept'],
                'count' => 1
            ];
        }
        $authors[] = $id;
    }

    $combinations = array_merge($combinations, combinations($authors));
}

uasort($labels, function ($a, $b) use ($scientist) {
    if ($a['user'] == $scientist) return -1;
    return $a['dept'] > $b['dept'];
});
$i = 0;
foreach ($labels as $key => $val) {
    $labels[$key]['index'] = $i++;
}

$matrix = array_fill(0, count($labels), 0);
$matrix = array_fill(0, count($labels), $matrix);

foreach ($combinations as $c) {
    $a = $labels[$c[0]]['index'];
    $b = $labels[$c[1]]['index'];

    $matrix[$a][$b] += 1;
    $matrix[$b][$a] += 1;
}


?>
<!-- https://github.com/vivo-project/VIVO/blob/1dd2851e45f1fc10ecb9cadeab4d0db8eb83ca00/webapp/src/main/webapp/templates/freemarker/visualization/personlevel/coAuthorPersonLevelD3.ftl -->
<script src="<?=ROOTPATH?>/js/d3.v4.min.js"></script>
<script>
    const DEPTS = JSON.parse('<?=json_encode(deptInfo())?>');

    var matrix = '<?= json_encode($matrix) ?>'
    matrix = JSON.parse(matrix);

    var labels = '<?= json_encode(array_column($labels, 'name')) ?>'
    labels = JSON.parse(labels);

    var ids = '<?= json_encode(array_column($labels, 'id')) ?>'
    ids = JSON.parse(ids);

    var users = '<?= json_encode(array_values($labels)) ?>'
    users = JSON.parse(users);

    var chord = d3.chord()
        .padAngle(0.05)
        .sortSubgroups(d3.descending);
    var width = 725;
    var height = 725;
    var padding = 175;
    var inner_radius = Math.min(width, height) * 0.37;
    var outer_radius = Math.min(width, height) * 0.39;
    // var fill = d3.scaleOrdinal()
    //     .domain(d3.range(20))
    //     .range(["#000000", "#1f77b4", "#aec7e8", "#ff7f0e", "#ffbb78",
    //         "#2ca02c", "#98df8a", "#d62728", "#ff9896", "#9467bd",
    //         "#c5b0d5", "#8c564b", "#c49c94", "#e377c2", "#f7b6d2",
    //         "#7f7f7f", "#c7c7c7", "#bcbd22", "#dbdb8d", "#17becf"
    //     ]);
    // #9edae5
    var SVG = d3.select('#chord').append('svg')
        .attr('width', width + padding)
        .attr('height', height + padding)
    var svg = SVG.append('g').attr('transform', 'translate(' + (width + padding) / 2 + ',' + (height + padding) / 2 + ')')
        .datum(chord(matrix));
    svg.append('g').selectAll('path').data(function(chords) {
            return chords.groups;
        }).enter()
        .append('path').style('fill', function(val) {
            var d = DEPTS[users[val.index]['dept']]
            if (d===undefined) return '#ccc'
            return d['color'] 
            // return DEPTS[users[val.index]['dept']]['color'] ?? '#ccc'
        })
        .style('stroke', function(val) {
            var d = DEPTS[users[val.index]['dept']]
            if (d===undefined) return '#ccc'
            return d['color'] 
            // return DEPTS[users[val.index]['dept']]['color'] ?? '#ccc'
        })
        .attr('d', d3.arc().innerRadius(inner_radius).outerRadius(outer_radius))
        .on('click', chord_click())
        .on("mouseover", chord_hover(.05))
        .on("mouseout", chord_hover(.8));
    var group_ticks = function(d) {
        var k = (d.endAngle - d.startAngle) / d.value;
        return d3.range(d.value / 2, d.value, d.value / 2).map(function(v) {
            return {
                angle: v * k + d.startAngle,
                label: Math.round(d.value)
            };
        });
    };
    var chord_ticks = svg.append('g')
        .selectAll('g')
        .data(function(chords) {
            return chords.groups;
        })
        .enter().append('g')
        .selectAll('g')
        .data(group_ticks)
        .enter().append('g')
        .attr('transform', function(d) {
            return 'rotate(' + (d.angle * 180 / Math.PI - 90) + ') translate(' + outer_radius + ',0)';
        });
    svg.append('g')
        .attr('class', 'chord')
        .selectAll('path')
        .data(function(chords) {
            return chords;
        })
        .enter().append('path')
        .style('fill', function(d) {
            var d = DEPTS[users[d.target.index]['dept']]
            if (d===undefined) return '#ccc'
            return d['color'] 
            // return fill(d.target.index);
        })
        .attr('d', d3.ribbon().radius(inner_radius))
        .style('opacity', .8);
    svg.append("g").selectAll(".arc")
        .data(function(chords) {
            return chords.groups;
        })
        .enter().append("svg:text")
        .attr("dy", ".35em")
        .attr("style", function(d) {
            return d.index == 0 ? "font-size: .75em; font-weight: bold;" : "font-size: .70em;";
        })
        .attr("text-anchor", function(d) {
            return ((d.startAngle + d.endAngle) / 2) > Math.PI ? "end" : null;
        })
        .attr("transform", function(d) {
            return "rotate(" + (((d.startAngle + d.endAngle) / 2) * 180 / Math.PI - 90) + ")" +
                "translate(" + (height * .40) + ")" +
                (((d.startAngle + d.endAngle) / 2) > Math.PI ? "rotate(180)" : "");
        })
        .text(function(d) {
            var u = users[d.index];
            return u['name'];
        })
        .on('click', chord_click())
        .on("mouseover", chord_hover(.05))
        .on("mouseout", chord_hover(.8));

    function chord_hover(opacity) {
        return function(g, i) {
            if (opacity > .5) {
                var chordInfoDiv = d3.select('#chord-info-div');
                chordInfoDiv.style('display', 'none');
                $('#chord').css('cursor', 'default');
            } else {
                var chord = d3.select('#chord').node();

                var wrapper = document.getElementsByClassName('content-wrapper')
                var coords = d3.mouse(chord);
                var hoverEvent = d3.event;
                var topPos = coords[0] + (100);
                var leftPos = coords[1] + wrapper.pageX;

                $('#chord').css('cursor', 'pointer');
                var chordInfoDiv = d3.select('#chord-info-div');
                var hoverMsg = labels[i] + "<br/>";
                hoverMsg += users[i]['dept'] + "<br/>"
                if (i > 0) {
                    hoverMsg += users[i]['count'] + " Joint Publications<br/>";
                } else {
                    hoverMsg += "<?= $N ?> Publications<br/>";
                }
                chordInfoDiv.html(hoverMsg);
                chordInfoDiv.style('display', 'block');
                chordInfoDiv.style('position', 'absolute');
                if (d3.mouse(chord)[1] > height / 2) {
                    topPos += 80;
                }
                chordInfoDiv.style('top', topPos + 'px');
                if (hoverEvent.pageX > wrapper.clientWidth / 2) {
                    leftPos = hoverEvent.pageX + 10;
                } else {
                    leftPos = hoverEvent.pageX - (10 + chordInfoDiv.node().getBoundingClientRect().width);
                }
                chordInfoDiv.style('left', leftPos + 'px');
            }
            svg.selectAll(".chord path")
                .filter(function(d) {
                    return d.source.index != i && d.target.index != i;
                })
                .transition()
                .style("opacity", opacity);
        }
    }

    function chord_click() {
        return function(g, i) {
            if (i >= 0) {
                window.location.href = "?scientist=" + ids[i];
            }
        };
    }

    // var SVG = d3.select("#legend")
    var depts_in_use = [];
    for (var i = 0; i < users.length; i++) {
        var d = users[i]['dept']

        if (d && DEPTS[d]!==undefined && !depts_in_use.includes(d))
            depts_in_use.push(d);
    }

    var legend = d3.select('#chord')
    // .append('div').attr('class', 'box w-auto')
    .append('div').attr('class', 'content')

    legend.append('div')
    .style('font-weight', 'bold')
    .attr('class', 'mb-5')
    .text('<?=lang("Departments","Abteilungen")?>')

    depts_in_use.forEach(d => {
        var row = legend.append('div')
        .attr('class', 'd-flex mb-5 text-'+d)

        row.append('div')
        .style('background-color',DEPTS[d]['color'])
        .style("width", "2rem")
        .style("height", "2rem")
        .style("display", "inline-block")
        .style("margin-right", "1rem")
        row.append('span').text(DEPTS[d]['name'])
    });
    

    // var size = 20
    // var SVG = d3.select('#chord').append('svg')
    //     .attr('width', 300)
    //     .attr('height', 300)
        
    // SVG.append('text')
    // .attr('x', 0)
    // .attr('y', 30)
    // .style('font-weight', 'bold')
    // .text('<?=lang("Departments","Abteilungen")?>')


    // var legend = SVG.append('g')
    //     .attr('id', 'legends')
    //     // .attr('transform','translate('+(width+padding/2)+' 10)')
    //     .selectAll("mydots")
    //     .data(depts_in_use)
    //     .enter()
    //     .append('g')

    // legend
    //     .append("rect")
    //     .attr("x", 0)
    //     .attr("y", function(d, i) {
    //         return 40 + i * (size + 5)
    //     }) // 30 is where the first dot appears. 25 is the distance between dots
    //     .attr("width", size)
    //     .attr("height", size)
    //     .style("fill", function(d) {
    //         return DEPTS[d]['color']
    //     })

    // // Add one dot in the legend for each name.
    // legend //.selectAll("mylabels")
    //     .append("text")
    //     .attr("x", 10 + size * 1.2)
    //     .attr("y", function(d, i) {
    //         return 40 + i * (size + 5) + (size / 1.3)
    //     }) // 100 is where the first dot appears. 25 is the distance between dots
    //     .style("fill", function(d) {
    //         return DEPTS[d]['color']
    //     })
    //     .text(function(d) {
    //         return DEPTS[d]['name']
    //     })
    //     .attr("text-anchor", "left")
    //     .style("alignment-baseline", "middle")
</script>