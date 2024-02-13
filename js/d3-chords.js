// https://www.visualcinnamon.com/2016/06/orientation-gradient-d3-chord-diagram/
customChordLayout = function () {
    var ε = 1e-6,
        ε2 = ε * ε,
        π = Math.PI,
        τ = 2 * π,
        τε = τ - ε,
        halfπ = π / 2,
        d3_radians = π / 180,
        d3_degrees = 180 / π;
    var chord = {},
        chords, groups, matrix, n, padding = 0,
        sortGroups, sortSubgroups, sortChords;

    function relayout() {
        var subgroups = {},
            groupSums = [],
            groupIndex = d3.range(n),
            subgroupIndex = [],
            k, x, x0, i, j;
        var numSeq;
        chords = [];
        groups = [];
        k = 0, i = -1;

        while (++i < n) {
            x = 0, j = -1, numSeq = [];
            while (++j < n) {
                x += matrix[i][j];
            }
            groupSums.push(x);
            for (var m = 0; m < n; m++) {
                numSeq[m] = (n + (i - 1) - m) % n;
            }
            subgroupIndex.push(numSeq);
            k += x;
        } //while

        k = (τ - padding * n) / k;
        x = 0, i = -1;
        while (++i < n) {
            x0 = x, j = -1;
            while (++j < n) {
                var di = groupIndex[i],
                    dj = subgroupIndex[di][j],
                    v = matrix[di][dj],
                    a0 = x,
                    a1 = x += v * k;
                subgroups[di + "-" + dj] = {
                    index: di,
                    subindex: dj,
                    startAngle: a0,
                    endAngle: a1,
                    value: v
                };
            } //while

            groups[di] = {
                index: di,
                startAngle: x0,
                endAngle: x,
                value: (x - x0) / k
            };
            x += padding;
        } //while

        i = -1;
        while (++i < n) {
            j = i - 1;
            while (++j < n) {
                var source = subgroups[i + "-" + j],
                    target = subgroups[j + "-" + i];
                if (source.value || target.value) {
                    chords.push(source.value < target.value ? {
                        source: target,
                        target: source
                    } : {
                        source: source,
                        target: target
                    });
                } //if
            } //while
        } //while
        if (sortChords) resort();
    } //function relayout

    function resort() {
        chords.sort(function (a, b) {
            return sortChords((a.source.value + a.target.value) / 2, (b.source.value + b.target.value) / 2);
        });
    }
    chord.matrix = function (x) {
        if (!arguments.length) return matrix;
        n = (matrix = x) && matrix.length;
        chords = groups = null;
        return chord;
    };
    chord.padding = function (x) {
        if (!arguments.length) return padding;
        padding = x;
        chords = groups = null;
        return chord;
    };
    chord.sortGroups = function (x) {
        if (!arguments.length) return sortGroups;
        sortGroups = x;
        chords = groups = null;
        return chord;
    };
    chord.sortSubgroups = function (x) {
        if (!arguments.length) return sortSubgroups;
        sortSubgroups = x;
        chords = null;
        return chord;
    };
    chord.sortChords = function (x) {
        if (!arguments.length) return sortChords;
        sortChords = x;
        if (chords) resort();
        return chord;
    };
    chord.chords = function () {
        if (!chords) relayout();
        return chords;
    };
    chord.groups = function () {
        if (!groups) relayout();
        return groups;
    };
    return chord;
};

function Chords(selector, matrix, labels, colors, data, links, useGradient, labelBold, type = 'publication') {
    var margin = {
        left: 80,
        top: 80,
        right: 80,
        bottom: 80
    },
        width = Math.min(window.innerWidth, 1000) - margin.left - margin.right,
        height = Math.min(window.innerWidth, 1000) - margin.top - margin.bottom,
        innerRadius = Math.min(width, height) * .39,
        outerRadius = innerRadius * 1.08;

    var opacityDefault = 0.8;

    /// Create scale and layout functions ///

    var colors = d3.scaleOrdinal()
        .domain(d3.range(labels.length))
        .range(colors);

    //A "custom" d3 chord function that automatically sorts the order of the chords in such a manner to reduce overlap	
    var chord = customChordLayout()
        .padding(.07)
        .sortChords(d3.descending) //which chord should be shown on top when chords cross. Now the biggest chord is at the bottom
        .matrix(matrix);

    var arc = d3.arc()
        .innerRadius(innerRadius * 1.01)
        .outerRadius(outerRadius);

    var path = d3.ribbon()
        .radius(innerRadius);

    /// Create SVG ///

    var svg = d3.select(selector).append("svg")
        .attr("viewBox", [0, 0, width + margin.left + margin.right, height + margin.top + margin.bottom])
        // .attr("width", width + margin.left + margin.right)
        // .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + (width / 2 + margin.left) + "," + (height / 2 + margin.top) + ")");

    /// Create the gradient fills ///

    //Function to create the id for each chord gradient
    function getGradID(d) {
        return "linkGrad-" + d.source.index + "-" + d.target.index;
    }

    if (useGradient) {
        //Create the gradients definitions for each chord
        var grads = svg.append("defs").selectAll("linearGradient")
            .data(chord.chords())
            .enter().append("linearGradient")
            .attr("id", getGradID)
            .attr("gradientUnits", "userSpaceOnUse")
            .attr("x1", function (d, i) {
                return innerRadius * Math.cos((d.source.endAngle - d.source.startAngle) / 2 + d.source.startAngle - Math.PI / 2);
            })
            .attr("y1", function (d, i) {
                return innerRadius * Math.sin((d.source.endAngle - d.source.startAngle) / 2 + d.source.startAngle - Math.PI / 2);
            })
            .attr("x2", function (d, i) {
                return innerRadius * Math.cos((d.target.endAngle - d.target.startAngle) / 2 + d.target.startAngle - Math.PI / 2);
            })
            .attr("y2", function (d, i) {
                return innerRadius * Math.sin((d.target.endAngle - d.target.startAngle) / 2 + d.target.startAngle - Math.PI / 2);
            })

        //Set the starting color (at 0%)
        grads.append("stop")
            .attr("offset", "0%")
            .attr("stop-color", function (d) {
                return colors(d.source.index);
            });

        //Set the ending color (at 100%)
        grads.append("stop")
            .attr("offset", "100%")
            .attr("stop-color", function (d) {
                return colors(d.target.index);
            });
    }

    /// Draw outer Arcs ///

    var outerArcs = svg.selectAll("g.group")
        .data(chord.groups)
        .enter().append("g")
        .attr("class", "group")
        .on("mouseover", fade(.1))
        .on("mouseout", fade(opacityDefault));

    if (links) {
        function chord_click() {
            return function (g, i) {
                if (links[i]) {
                    window.location.href = links[i];
                }
            };
        }

        outerArcs
            .on('click', chord_click())
            .style('cursor', (d, i) => links[i] ? 'pointer' : null)
    }


    outerArcs.append("path")
        .style("fill", function (d) {
            return colors(d.index);
        })
        .attr("d", arc)
    // .each(function (d, i) {
    //     //Search pattern for everything between the start and the first capital L
    //     var firstArcSection = /(^.+?)L/;

    //     //Grab everything up to the first Line statement
    //     var newArc = firstArcSection.exec(d3.select(this).attr("d"))[1];
    //     //Replace all the comma's so that IE can handle it
    //     newArc = newArc.replace(/,/g, " ");

    //     //If the end angle lies beyond a quarter of a circle (90 degrees or pi/2) 
    //     //flip the end and start position
    //     if (d.endAngle > 90 * Math.PI / 180 & d.startAngle < 270 * Math.PI / 180) {
    //         var startLoc = /M(.*?)A/, //Everything between the first capital M and first capital A
    //             middleLoc = /A(.*?)0 0 1/, //Everything between the first capital A and 0 0 1
    //             endLoc = /0 0 1 (.*?)$/; //Everything between the first 0 0 1 and the end of the string (denoted by $)
    //         //Flip the direction of the arc by switching the start en end point (and sweep flag)
    //         //of those elements that are below the horizontal line
    //         var newStart = endLoc.exec(newArc)[1];
    //         var newEnd = startLoc.exec(newArc)[1];
    //         var middleSec = middleLoc.exec(newArc)[1];

    //         //Build up the new arc notation, set the sweep-flag to 0
    //         newArc = "M" + newStart + "A" + middleSec + "0 0 0 " + newEnd;
    //     } //if

    //     // Create a new invisible arc that the text can flow along
    //     svg.append("path")
    //         .attr("class", "hiddenArcs")
    //         .attr("id", "arc" + i)
    //         .attr("d", newArc)
    //         .style("fill", "none");
    // });

    /// Append labels ///

    //Append the label labels on the outside
    var outerLabels = outerArcs.append("text")
        .attr("class", "titles")
        // .attr("dy", function (d, i) {
        //     return (d.endAngle > 90 * Math.PI / 180 & d.startAngle < 270 * Math.PI / 180 ? 25 : -16);
        // })
        // .append("textPath")
        // .attr("startOffset", "50%")
        // .style("text-anchor", "middle")
        // .attr("xlink:href", function (d, i) {
        //     return "#arc" + i;
        // })
        .style('font-size', '0.9em')
        .text(function (d, i) {
            return labels[i];
        })
        .attr("dy", ".35em")
        .attr("text-anchor", function (d) {
            return ((d.startAngle + d.endAngle) / 2) > Math.PI ? "end" : null;
        })
        .attr("transform", function (d) {
            return "rotate(" + (((d.startAngle + d.endAngle) / 2) * 180 / Math.PI - 90) + ")" +
                "translate(" + (height * .40 + 30) + ")" +
                (((d.startAngle + d.endAngle) / 2) > Math.PI ? "rotate(180)" : "");
        })

    if (labelBold) {
        outerLabels.style("font-weight", function (d) {
            return d.index == labelBold ? "bold" : null;
        })
    }


    /// Draw inner chords ///

    var chord = svg.selectAll("path.chord")
        .data(chord.chords)
        .enter().append("path")
        .attr("class", "chord")
        .style("opacity", opacityDefault)
        .attr("d", path)
        .on("mouseover", mouseoverChord)
        .on("mouseout", mouseoutChord);

    if (useGradient) {
        console.log(useGradient);
        chord.style("fill", function (d) {
            return "url(#" + getGradID(d) + ")";
        })
    } else {
        chord.style("fill", function (d) {
            return colors(d.target.index);
        })
    }



    /// Extra Functions ///
    //Returns an event handler for fading a given chord group.
    function fade(opacity) {
        return function (d, i) {
            svg.selectAll("path.chord")
                .filter(function (d) {
                    return d.source.index !== i && d.target.index !== i;
                })
                .transition()
                .style("opacity", opacity);

            $(this).popover({
                placement: 'auto top',
                container: selector,
                mouseOffset: 10,
                followMouse: true,
                trigger: 'hover',
                html: true,
                content: function () {
                    return `<span style='font-weight:900'>
                        ${data[d.index]['count']}
                    </span> ${type}s
                    `
                }
            });
            $(this).popover('show');
        };
    } //fade

    //Highlight hovered over chord
    function mouseoverChord(d, i) {

        //Decrease opacity to all
        svg.selectAll("path.chord")
            .transition()
            .style("opacity", 0.1);
        //Show hovered over chord with full opacity
        d3.select(this)
            .transition()
            .style("opacity", 1);

        //Define and show the tooltip over the mouse location
        $(this).popover({
            placement: 'auto top',
            container: selector,
            mouseOffset: 10,
            followMouse: true,
            trigger: 'hover',
            html: true,
            content: function () {
                return `<span style='font-weight:900'>
                    ${d.source.value}
                </span> ${type}s
                `
            }
        });
        $(this).popover('show');
    } //mouseoverChord

    //Bring all chords back to default opacity
    function mouseoutChord(d) {
        //Hide the tooltip
        $('.popover').each(function () {
            $(this).remove();
        });
        //Set opacity back to default for all
        svg.selectAll("path.chord")
            .transition()
            .style("opacity", opacityDefault);
    }
}
