/**
 * Helping scroll function - scrolls to div inside other scrollable div
 */
jQuery.fn.scrollTo = function(elem) {
  $(this).scrollTop($(this).scrollTop() - $(this).offset().top + $(elem).offset().top);
  return this;
};

/**
 * array.remove(anything)
 */
Array.prototype.remove = function() {
  var what, a = arguments, L = a.length, ax;
  while (L && this.length) {
    what = a[--L];
    while ((ax = this.indexOf(what)) !== -1) {
      this.splice(ax, 1);
    }
  }
  return this;
};

// Nieuwere versie van hide- & showScreen().
function showBounceBall(display = true, text = "") {
  var screen = $(".white-screen");
  screen.find(".text").html(text);
  screen.css("display", display ? "block" : "none");
}

 /**
 * Shows update button and enables prompt
 */
function toggleUpdate(show) {
  if (show) {
    $("#universal-update").show(600);
    window.onbeforeunload = () => true;
  } else {
    $("#universal-update").hide(300);
    window.onbeforeunload = undefined;
  }
}

function getChanged(selector, allowAll = false) {
  var changed = {};
  $(selector).each(function(index, element) {
    if (allowAll || $(this).data('changed')) {
      changed[$(this).prop('id')] = $(this).val();
    }
  });
  return changed;
}

function filterSearch(value, links, counterSpan = null, isDiv = false) {
  var occ = 0;
  $.each(links, function() {
    var name = isDiv ? $(this).data('name') : $(this).prop('name');
    var match = name.toLowerCase().includes(value.toLowerCase())
    $(this).css('display', match ? 'block' : 'none');
    occ += match ? 1 : 0;
  });

  if (counterSpan != null) {
    var startValue = parseInt(counterSpan.html());
    var milliseconds = (Math.abs(startValue - occ) + 100) * 2;

    $({ Counter: startValue }).stop(true, false).animate({ Counter: occ += (startValue < occ) }, {
      duration: milliseconds, step: function() {
        counterSpan.html(parseInt(this.Counter));
      }
    });
  }
}

function toggleSelected(element, selectedList, triggerButton = null) {
  if (element.attr('class').endsWith('selected-dashboards')) {
    element.removeClass('selected-dashboards');
    selectedList.splice(selectedList.indexOf(element.data('id')), 1);

  } else {
    element.addClass('selected-dashboards');
    selectedList = [...selectedList, element.data('id')];
  }

  if (triggerButton) {
    if (selectedList.length == 0) {
      triggerButton.hide(1000);
    } else {
      triggerButton.show(1000);
    }
  }
  return selectedList;
}

function generateChart(canvas, datalist, labels = null, axes = [false, false]) {
  if (!$(`#${canvas}`).is('canvas'))
    return;

  const backgroundColors = ["rgba(72, 125, 215, 0.1)", "rgba(238, 82, 83, 0.1)"];
  const borderColors = ["#487dd7", "#ee5253"];

  var sets = new Array();
  for (var i = 0; i < datalist.length; i++) {
    sets.push({
      radius: 0,
      backgroundColor: backgroundColors[i],
      borderColor: borderColors[i],
      data: datalist[i]
    });
  }
  $.getScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js", function () {
    new Chart(canvas, {
      type: 'line',
      data: {
        labels: labels != null ? labels : new Array(datalist[0].length),
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

// Parse Client Info for client setup, audit setup and report setup.
function changeClientInputFields(field) {
  var unparsed = $(field).val();

  if (unparsed) {
    var parsed = parseClientInput($(field).data("type"), unparsed);
    parsed = grabPageId(parsed);

    if (parsed) {
      $(field).val(parsed);
      return;
    }
  }
}

function grabPageId(found) {
  var fbPageID = '(?:[A-Za-z0-9_.]+)(?:\-)([0-9]{14,17})$';
  var pageID = found.match(fbPageID);
  return (pageID && pageID.length > 1) ? pageID[1] : found;
}

function parseClientInput(type, input) {
  var patterns = {
    'facebook': /(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:[A-Za-z0-9_])*#!\/)?(?:(?:pages|pg)?\/)?([\w_.\-]+)?/g,
    'instagram': /(?:(?:(?:http|https):\/\/)?(?:www.)?instagram.com\/|\@)?([A-Za-z0-9_.\-]{0,30})?/g,
    'website': /(.*)/g,
  }
  var found = patterns[type].exec(input);
  return (found && found.length > 1) ? found[1] : input;
}

/**
 * Deze functie word zowel in client dashboard als in report setup gebruikt
 */
function getAdAccounts(ad_id) {
  // Don't make the same request a second time
  if (Instance.adAccounts.length == 0) {
    console.log(getAdAccountsQuery());
    FB.api(getAdAccountsQuery(), function (response) {
      if (response && !response.error && response.data.length != 0) {

        response.data.forEach(function(ad_account) {
          const {name, id} = ad_account;

          var selected = (ad_id == id) ? 'selected' : '';
          var str = `<option onclick="connect()" class="row-ad-accounts click-option" value="${id}" ${selected}>${name} ${id}</option>`;

          $('#ad-account-list').append(str);
        });

        Instance.adAccounts = response.data;
      } else if (response.data.length == 0) {
        $('#ad-account-list').html('<option class="row-ad-accounts">No ad accounts found.</option>');
      } else {
        logResponse(response);
      }
    });
  } else {
    $('#ad-account-list').empty();
    Instance.adAccounts.forEach(function(account) {
      const {name, id} = account;
      var selected = (ad_id == id) ? 'selected' : '';
      var str = `<option class="row-ad-accounts" onclick="connect()" value="${id}" ${selected}>${name} ${id}</option>`;
      $('#ad-account-list').append(str);
    });
  }
}

/**
 * Deze functie word zowel in client dashboard als report setup gebruikt
 */
function connectAccount(adId, clientId) {
  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      'action': 'update_ad_account',
      'ad_id': adId,
      'client_id': clientId,
    },
    success: logResponse,
    error: logResponse,
  });
}


// Find 'selected' class in list of elements
function getSelectedAdAccount(optionList) {
  var selected = optionList.find('option:selected');
  if (selected.length == 0) {
    optionList.fadeOut(50).fadeIn(400);
    return false;
  }
  return selected;
}

function logResponse(response) {
  console.log({response});
}

function error_func(xhr, textStatus, errorThrown, data) {
  return `ERRORTHROWN: ${JSON.stringify(errorThrown)} XHR: ${JSON.stringify(xhr)} TEXTSTATUS: ${JSON.stringify(textStatus)} DATA: ${JSON.stringify(data)}`;
}

function logError(message, file = '', func = '') {
  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      'action': 'log_error',
      'message': message,
      'stacktrace': (func == '') ? file : `${file} in ${func}()`
    },
    success: logResponse,
    error: logResponse,
  });
}
