
function partition(data) {
  const root = d3.hierarchy(data)
    .sum(d => d.value)
    .sort((a, b) => b.value - a.value);
  return d3.partition()
    .size([2 * Math.PI, root.height + 1])
    (root);
}

function chart(selector, data) {


  const format = d3.format(",d")
  const width = 800
  const radius = width / 8
  const color = d3.scaleOrdinal(d3.quantize(d3.interpolateRainbow, data.children.length + 1))
  const arc = d3.arc()
    .startAngle(d => d.x0)
    .endAngle(d => d.x1)
    .padAngle(d => Math.min((d.x1 - d.x0) / 2, 0.005))
    .padRadius(radius * 1.5)
    .innerRadius(d => d.y0 * radius)
    .outerRadius(d => Math.max(d.y0 * radius, d.y1 * radius - 1))


  const root = partition(data);
  root.each(d => d.current = d);

  const svg = d3.create("svg")
    .attr("viewBox", [0, 0, width, width])
    .style("font", "10px sans-serif");

  const g = svg.append("g")
    .attr("transform", `translate(${width / 2},${width / 2})`);

  const path = g.append("g")
    .selectAll("path")
    .data(root.descendants().slice(1))
    .join("path")
    .attr("fill", (d) => {
      while (d.data.color === undefined) d = d.parent;
      return d.data.color
    })
    .attr("fill-opacity", d => arcVisible(d.current) ? (d.children ? 0.8 : 0.5) : 0)
    .attr("pointer-events", d => arcVisible(d.current) ? "auto" : "none")

    .attr("d", d => arc(d.current))
    .style("cursor", "pointer")
    .on('click', function (e, d) {
      if (d.data.link !== undefined) {
        window.location.href = d.data.link;
      }
    });

  path.filter(d => d.children)
    .on("click", clicked);

  // path.append("title")
  //     .text(d => `${d.ancestors().map(d => d.data.name).reverse().join("/")}\n${format(d.value)}`);


  path.on("mouseover", mouseover)
    .on("mouseout", mouseout);

  //Highlight hovered over chord
  function mouseover(event, d, i) {
    //Define and show the tooltip over the mouse location
    $(this).popover({
      placement: 'auto top',
      container: selector,
      mouseOffset: 10,
      followMouse: true,
      trigger: 'hover',
      html: true,
      content: function () {
        return `${d.ancestors().map(d => d.data.id).reverse().join("/")}<br>${d.data.name}<br>${format(d.value)}`
      }
    });
    $(this).popover('show');
  } //mouseoverChord

  //Bring all chords back to default opacity
  function mouseout(event, d) {
    //Hide the tooltip
    $('.popover').each(function () {
      $(this).remove();
    });
  }

  const label = g.append("g")
    // .attr("pointer-events", "none")
    .attr("text-anchor", "middle")
    .style("user-select", "none")
    .selectAll("text")
    .data(root.descendants().slice(1))
    .join("text")
    .attr("dy", "0.35em")
    .attr("fill-opacity", d => +labelVisible(d.current))
    .attr("transform", d => labelTransform(d.current))
    .text(d => {
      if (d.children) return d.data.id;
      return d.data.name
    })


  const parent = g.append("circle")
    .datum(root)
    .attr("r", radius)
    .attr("fill", "none")
    .attr("pointer-events", "all")
    .on("click", clicked);

  function clicked(event, p) {
    parent.datum(p.parent || root);

    root.each(d => d.target = {
      x0: Math.max(0, Math.min(1, (d.x0 - p.x0) / (p.x1 - p.x0))) * 2 * Math.PI,
      x1: Math.max(0, Math.min(1, (d.x1 - p.x0) / (p.x1 - p.x0))) * 2 * Math.PI,
      y0: Math.max(0, d.y0 - p.depth),
      y1: Math.max(0, d.y1 - p.depth)
    });

    const t = g.transition().duration(750);

    // Transition the data on all arcs, even the ones that aren’t visible,
    // so that if this transition is interrupted, entering arcs will start
    // the next transition from the desired position.
    path.transition(t)
      .tween("data", d => {
        const i = d3.interpolate(d.current, d.target);
        return t => d.current = i(t);
      })
      .filter(function (d) {
        return +this.getAttribute("fill-opacity") || arcVisible(d.target);
      })
      .attr("fill-opacity", d => arcVisible(d.target) ? (d.children ? 0.8 : 0.5) : .8)
      .attr("pointer-events", d => arcVisible(d.target) ? "auto" : "none")
      .attrTween("d", d => () => arc(d.current));

    // label.filter(function(d) {
    //         return +this.getAttribute("fill-opacity") || labelVisible(d.target);
    //     }).transition(t)
    //     .attr("fill-opacity", d => +labelVisible(d.target))
    //     .attrTween("transform", d => () => labelTransform(d.current));

    label.style("visibility", (e) => isParentOf(p, e) ? null : 'hidden')
      .transition()
      .duration(750)
      .attrTween("transform", d => () => {
        if (d.data.name == p.data.name) return;
        return labelTransform(d.current)
      })
      .attr("fill-opacity", d => {
        if (d.data.name == p.data.name) return true;
        return +labelVisible(d.target)
      })
  }

  function isParentOf(p, c) {
    if (p === c) return true;
    if (p.children) {
      return p.children.some(function (d) {
        return isParentOf(d, c);
      });
    }
    return false;
  }

  function arcVisible(d) {
    return d.y1 <= 4 && d.y0 >= 1 && d.x1 > d.x0;
  }

  function labelVisible(d) {
    return d.y1 <= 4 && d.y0 >= 1 && (d.y1 - d.y0) * (d.x1 - d.x0) > 0.03;
  }

  function labelTransform(d) {
    const x = (d.x0 + d.x1) / 2 * 180 / Math.PI;
    const y = (d.y0 + d.y1) / 2 * radius;
    return `rotate(${x - 90}) translate(${y},0) rotate(${x < 180 ? 0 : 180})`;
  }

  $('#flare').html(svg.node())

  return svg.node();
}