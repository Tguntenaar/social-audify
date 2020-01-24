/**
 * Helping scroll function - scrolls to div inside other scrollable div
 */
jQuery.fn.scrollTo = function (elem) {
  $(this).scrollTop($(this).scrollTop() - $(this).offset().top + $(elem).offset().top);
  return this;
};

/**
 * array.remove(anything)
 */
Array.prototype.remove = function () {
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
    $("#universal-update").slideDown(300);
    window.onbeforeunload = () => true;
  } else {
    $("#universal-update").slideUp(300);
    window.onbeforeunload = undefined;
  }
}

function getChanged(selector, allowAll = false) {
  var changed = {};
  $(selector).each(function (index, element) {
    if (allowAll || $(this).data('changed')) {
      changed[$(this).prop('id')] = $(this).val();
    }
  });
  return changed;
}

function filterSearch(value, links, counterSpan = null, isDiv = false) {
  var occ = 0;
  $.each(links, function () {
    var name = isDiv ? $(this).data('name') : $(this).prop('name');
    var match = name.toString().toLowerCase().includes(value.toLowerCase())
    $(this).css('display', match ? 'block' : 'none');
    occ += match ? 1 : 0;
  });

  countAnimationFromTo(counterSpan, parseInt(counterSpan.html()), occ);
}

function countAnimation(span, countTo) {
  if (span != null) {
    var startValue = parseInt(span.html());
    var milliseconds = (Math.abs(startValue - countTo) + 100) * 2;

    $({ Counter: startValue }).stop(true, false).animate({ Counter: countTo += (startValue < countTo) }, {
      duration: milliseconds, step: function () {
        span.html(parseInt(this.Counter));
      }
    });
  }
}

function countAnimationFromTo(span, from, to, duration = null) {
  if (span != null) {
    var startValue = from;
    var countTo = to;
    var milliseconds = duration || (Math.abs(startValue - countTo) + 100) * 2;

    $({ Counter: startValue }).stop(true, false).animate({ Counter: countTo += (startValue < countTo) }, {
      duration: milliseconds, step: function () {
        span.html(parseInt(this.Counter));
      }
    });
  }
}

function toggleSelected(element, selectedList, triggerButton = null, postIds = null) {
  if (element.attr('class').endsWith(`selected`)) {
    element.removeClass(`selected`);
    selectedList.splice(selectedList.indexOf(element.data('id')), 1);
    if (postIds) {
      postIds.splice(postIds.indexOf(element.data('post')), 1);
    }

  } else {
    element.addClass(`selected`);
    selectedList = [...selectedList, element.data('id')];
    if (postIds) {
      postIds = [...postIds, element.data('post')];
    }
  }

  if (triggerButton) {
    if (selectedList.length == 0) {
      triggerButton.slideUp(500);
    } else {
      triggerButton.slideDown(500);
    }
  }
  return postIds ? { selectedList, postIds } : selectedList;
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

        response.data.forEach(function (ad_account) {
          const { name, id } = ad_account;

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
    Instance.adAccounts.forEach(function (account) {
      const { name, id } = account;
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
  console.log({ response });
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



// Hex color shader
function shadeColor(color, percent = -10) {
  var r = parseInt(color.substring(1, 3), 16);
  var g = parseInt(color.substring(3, 5), 16);
  var b = parseInt(color.substring(5, 7), 16);

  r = Math.min(parseInt(r * (100 + percent) / 100), 255);
  g = Math.min(parseInt(g * (100 + percent) / 100), 255);
  b = Math.min(parseInt(b * (100 + percent) / 100), 255);

  var rr = (r.toString(16).length == 1 ? "0" + r.toString(16) : r.toString(16));
  var gg = (g.toString(16).length == 1 ? "0" + g.toString(16) : g.toString(16));
  var bb = (b.toString(16).length == 1 ? "0" + b.toString(16) : b.toString(16));

  return "#" + rr + gg + bb;
}

// Find complement of hex color - (maybe unnecessary)
function complementColor(color) {
  var r = parseInt(color.substring(1, 3), 16) / 255.0;
  var g = parseInt(color.substring(3, 5), 16) / 255.0;
  var b = parseInt(color.substring(5, 7), 16) / 255.0;

  var max = Math.max(r, g, b);
  var min = Math.min(r, g, b);
  var h, s, l = (max + min) / 2.0;

  if (max == min) {
    h = s = 0;  //achromatic
  } else {
    var d = max - min;
    s = (l > 0.5 ? d / (2.0 - max - min) : d / (max + min));

    if (max == r && g >= b) {
      h = 1.0472 * (g - b) / d;
    } else if (max == r && g < b) {
      h = 1.0472 * (g - b) / d + 6.2832;
    } else if (max == g) {
      h = 1.0472 * (b - r) / d + 2.0944;
    } else if (max == b) {
      h = 1.0472 * (r - g) / d + 4.1888;
    }
  }

  // Shift hue to opposite side of wheel and convert to [0-1] value
  h = h / 6.2832 * 360.0 + 180;
  if (h > 360) { h -= 360; }
  h /= 360;

  if (s === 0) {
    r = g = b = l; // achromatic
  } else {
    var hue2rgb = function hue2rgb(p, q, t) {
      if (t < 0) t += 1;
      if (t > 1) t -= 1;
      if (t < 1 / 6) return p + (q - p) * 6 * t;
      if (t < 1 / 2) return q;
      if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
      return p;
    };

    var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
    var p = 2 * l - q;

    r = hue2rgb(p, q, h + 1 / 3);
    g = hue2rgb(p, q, h);
    b = hue2rgb(p, q, h - 1 / 3);
  }

  r = Math.round(r * 255);
  g = Math.round(g * 255);
  b = Math.round(b * 255);

  // Convert r b and g values to hex
  rgb = b | (g << 8) | (r << 16);
  return "#" + (0x1000000 | rgb).toString(16).substring(1);
}