
function generateChart(canvas, dataList, labels = null, axes = [false, false]) {
  if (!$(`#${canvas}`).is('canvas'))
    return;

  const backgroundColors = ["rgba(72, 125, 215, 0.1)", "rgba(238, 82, 83, 0.1)"];
  const borderColors = ["#487dd7", "#ee5253"];

  var sets = new Array();
  for (var i = 0; i < dataList.length; i++) {
    sets.push({
      radius: 0,
      backgroundColor: backgroundColors[i],
      borderColor: borderColors[i],
      data: dataList[i]
    });
  }
  $.getScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js", function () {
    new Chart(canvas, {
      type: 'line',
      data: {
        labels: labels != null ? labels : new Array(dataList[0].length),
        datasets: sets,
        fillOpacity: .3
      },
      options: {
        spanGaps: false,
        maintainAspectRatio: false,
        elements: { line: { tension: 0.000001 } },
        legend: { display: false },
        scales: { xAxes: [{ display: axes[0] }], yAxes: [{ display: axes[1] }] }
      }
    });
  }, true);
}


function generateAreaChart(canvas, data, labels) {
  if (!$(`#${canvas}`).is('canvas'))
    return;

  backgroundColors = [];
  for (var i = 0; i < data[0].length; i++) {
    backgroundColors.push(`rgba(72, 125, 215, ${0.2 + (i * 0.15)})`)
  }

  if (data.length > 1) {
    for (var i = 0; i < data[0].length; i++) {
      backgroundColors.push(`rgba(238, 82, 83, ${0.2 + (i * 0.15)})`)
    }
  }
  $.getScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js", function () {
    new Chart(canvas, {
      type: 'polarArea',
      data: {
        datasets: [{
          data: [].concat.apply([], data),
          backgroundColor: backgroundColors,
        }],
        labels: [].concat.apply([], labels)
      },
      options: { legend: { position: 'right' }, title: { display: true } }
    });
  }, true);
}


// TODO: maak dit error bestendig.
function generateBarChart(canvas, dataList, labelList, axes = [false, false]) {
  if (!$(`#${canvas}`).is('canvas'))
    return;

  // Not dynamic, only works with comparing 2 values...
  var barData = new Array(), barLabels = new Array(),
      backgroundColors = new Array(), borderColors = new Array();

  for (var i = 0; i < dataList[0].length; i++) {
    barData.push(dataList[0][i]);
    barLabels.push(labelList[0][i]);
    backgroundColors.push("rgba(72, 125, 215, 0.1)");
    borderColors.push("#487dd7");

    if (dataList.length > 1 && typeof labelList[1][i] !== 'undefined') {
      barData.push(dataList[1][i]);
      barLabels.push(labelList[1][i]);
      backgroundColors.push("rgba(238, 82, 83, 0.1)");
      borderColors.push("#ee5253");
    }
  }

  $.getScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js", function () {
    new Chart(canvas, {
      type: 'horizontalBar',
      data: {
        labels: barLabels,
        datasets: [{ data: barData, backgroundColor: backgroundColors,
            borderColor: borderColors, borderWidth: 3
          }
        ]
      },
      options: {
        legend: { display: false },
        scales: {
          yAxes: [{ display: axes[0] }],
          xAxes: [{ display: axes[1], ticks: { beginAtZero: true }}]
        }
      }
    });
  }, true);
}


function generateLineChart(canvas, dataList, labelList, axes = [false, false], colors = ["#4da1ff", "#e36364"]) {
  if (!$(`#${canvas}`).is('canvas'))
    return;

  var sets = new Array();
  for (var i = 0; i < dataList.length; i++) {
    sets.push({
      fill: false,
      borderWidth: 8,
      pointRadius: 0,
      borderColor: colors[i],
      data: dataList[i]
    });
  }

  $.getScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js", function () {
    new Chart(canvas, {
      type: 'line',
      data: {
        labels: labelList != null ? labelList : new Array(dataList[0].length),
        datasets: sets
      },
  
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { display: false },
        tooltips: { mode: 'index', intersect: false, },
        scales: {
          xAxes: [{ gridLines: { display: axes[0] }, ticks: { fontColor: "#b7b7b7" } }],
          yAxes: [{ gridlines: { display: axes[1] }, ticks: { fontColor: "#b7b7b7" } }]
        }
      }
    });
  }, true);
}