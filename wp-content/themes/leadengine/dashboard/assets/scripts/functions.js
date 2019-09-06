/**
 * Helping scroll function - scrolls to div inside other scrollable div
 */
jQuery.fn.scrollTo = function(elem) {
  $(this).scrollTop($(this).scrollTop() - $(this).offset().top + $(elem).offset().top);
  return this;
};

/**
 *  Getloginstatus maakt gebruik van de cache de tweede parameter true forceert
 *  een roundtrip naar de facebook servers.
 *  Gets called when the user is finished with the facebook login button.
 *  !!!Keep outside document ready scope!!
 * */
function checkLoginState() {
  console.log('check login state');
  FB.getLoginStatus(function(response) {
    if (response.status === 'connected') {
      nextPrev(1);
    } // else?
  }, true);
}

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

function filterSearch(value, links, counterSpan = null) {
  var occ = 0;
  $.each(links, function() {
    var match = $(this).prop('name').toLowerCase().includes(value.toLowerCase())
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

function generateChart(canvas, datalist, labels = null, axes = [false, false]) {
  // More can be added..?
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

function generateBarChart(canvas, dataList, labelList, axes = [false, false]) {
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
function parsePageInput(field) {
  if (!$(field).val()) {
    return;
  }

  var unparsed, parsed, pattern, matchedArray;
  const fbPageID = '(?:[A-Za-z0-9_]+)(?:\-)([0-9]{16})$';

  if ($(field).attr('id').includes('instagram')) {
    pattern = '(?:(?:(?:http|https):\/\/)?(?:www.)?instagram.com\/|\@)?([A-Za-z0-9_\-]{0,28})?';
  } else if ($(field).attr('id').includes('facebook')) {
    pattern = '(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:[A-Za-z0-9_])*#!\/)?(?:pages\/)?(?:pg\/)?([A-Za-z0-9_\-]*)?';
  } else {
    pattern = '(.*)'; // FIXME:
  }

  unparsed = $(field).val();
  matchedArray = unparsed.match(pattern);

  if (matchedArray !== null && matchedArray[1] !== 'undefined') {
    parsed = matchedArray[1];

    $(field).val(parsed);
    if ($(field).attr('id').includes('facebook')) {
      const pageID = parsed.match(fbPageID);

      if (pageID) {
        $(field).val(pageID[1]);
      }
    }
  }
  return;
}

/**
 * Deze functie word zowel in client dashboard als in report setup gebruikt
 */
function getAdAccounts() {
  // TODO: maak dit request maar 1 keer naar de facebook servers.
  FB.api(getAdAccountsQuerie(), function (response) {
    if (response && !response.error && response.data.length != 0) {

      response.data.forEach(function(ad_account) {
        const {name, id} = ad_account;

        var ad_id = $('#ad_id').val();
        var selected = (ad_id == id) ? 'selected' : '';

        var str = `<option class="row-ad-accounts" value="${id}" ${selected}>${name}</option>`;

        $('#ad-account-list').append(str);
      });
    } else if (response.data.length == 0) {
        $('#ad-account-list').html('<option class="row-ad-accounts">No ad accounts found.</option>');
    } else {
      logResponse(response);
    }
  });
}

/**
 * Deze functie word zowel in client dashboard als report setup gebruikt
 */
function connectAccount(adId, clientId) {
  var clientId = parseInt(clientId);

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

  $('#adAccountModal').css({'display': 'none'});
  return;
}


// Find 'selected' class in list of elements
function findSelected(optionList) {
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
