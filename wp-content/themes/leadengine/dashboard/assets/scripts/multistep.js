var currentTab = 0; // Current tab is set to be the first tab (0)
var pathName = window.location.pathname;

showTab(currentTab);

function showIntro(display) {
  types = display ? ['block', 'none'] : ['none', 'block'];
  $('.create-block-box').css({'display': types[0]});
  $('.back').css({'display': types[1]});
  $('.overview-audit-report .left').css({'display': types[1]});
}

function showTab(index) {
  // This function will display the specified tab of the form ...
  var tab = $('.tab');
  tab.eq(index).css({'display': 'block'});
  // ... and fix the previous button:
  $('#prevBtn').css({'display': index == 0 ? 'none' : 'inline'});

  // Fix the next button
  if (index == (tab.length - 1)) {
    // fix the create button
    var name = Instance.page.type.charAt(0).toUpperCase() + Instance.page.type.slice(1);
    $('#nextBtn').html(`Create ${name}`);

    tab.eq(index).find('input[type=text]').focus();

    // change on click functionality.
    $('#nextBtn').on('click', function() {
      if ((Instance.page.type == 'report' && validateSelectedAds()) ||
          (Instance.page.type == 'audit' && validateName())) {
        submitForm();
      }
    });

  } else {
    if ($('#nextBtn').html() !== 'Next') {
      $('#nextBtn').off('click');
      $('#nextBtn').html('Next');
    }
    $('#nextBtn').css({'display':'inline'});
  }

  // Remove all active, and set active to current
  var steps = $('.step').removeClass('active');
  steps.eq(index).addClass('active');
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var tab = $('.tab');

  // Exit the function if any field in the current tab is invalid:
  console.log('validating step..');

  // validate this step
  if (n == 1 && !validateStep()) return false;

  // request campaigns or ads from facebook servers.
  if (n === 1 && currentTab === 4 && Instance.page.type == 'report') showActiveCampaigns(); // FIXME: dit moet niet hier gebeuren.

  // Hide the current tab:
  tab.eq(currentTab).css({'display':'none'});

  // Increase or decrease the current tab by 1:
  currentTab += n;

  // if you have reached the end of the form... :
  if (currentTab >= tab.length) return false;

  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateStep() {
  switch (currentTab) {
    case 0:
      return validateFacebookLogin();
    case 1:
      return validateClient();
    case 2:
      return validateCompetitorTab();
    case 3:
      return validateName();
    case 4:
      return true; // showActiveCampaigns
  }
  return false;
}

function validateSelectedAds() {
  return selectedAds.length !== 0;
}

// TODO:
function validateFacebookLogin() {
  valid = true;

  if (valid) {
    $('.step').eq(currentTab).addClass('finish');
  }

  return valid
}

function validateClient() {
  if (!(selected = findSelected($('#client-list'))))
    return false;

  Instance.client = JSON.parse(selected.attr("data-client"));
  // console.log(JSON.paselected.attr("data-client"));

  // console.log(Instance.client.ad_id);
  // Stop progress if client has no ad_id
  if (Instance.page.type == 'report' && !Instance.client.ad_id) {
    $('#client-list').fadeOut(50).fadeIn(400);
    showModal(initiateModal('errorModal', 'error', {
      'text': "Client doesn't have an ad account connected.",
      'subtext': "In order to create a report for a client you have to connect an ad account."
    }));
    return false;
  }

  // Disable options die een client niet heeft.
  enableOption('facebook', Instance.client.facebook);
  enableOption('instagram', Instance.client.instagram);
  enableOption('website', Instance.client.website);

  $('.step').eq(currentTab).addClass('finish');
  return true;
}

function validateCompetitorTab() {
  Instance.competitor = false;

  // Compare data fetch
  if (!(selected = findSelected($('#compare-list'))))
    return false;

  Instance.competitor = selected.data('compare');

  if (Instance.page.type == 'audit' && Instance.competitor) {
    // Disable options die een client & competitor niet hebben.
    enableOption('facebook', Boolean(Instance.competitor.facebook) && Boolean(Instance.client.facebook));
    enableOption('instagram', Boolean(Instance.competitor.instagram) &&  Boolean(Instance.client.instagram));
    enableOption('website', Boolean(Instance.competitor.website) && Boolean(Instance.client.website));
  }

  $('.step').eq(currentTab).addClass('finish');
  $('.name-input').focus();

  return true;
}

function validateName() {
  var nameInput = $('.name-input');

  if (nameInput.val().length < 1 || nameInput.val().length > 25) {
    nameInput.addClass('invalid');
    nameInput.focus();
    return false;
  }

  // check if at least one options selected
  if ($(".c_container > input[type=checkbox]:checked").length === 0) {
    $('.c_container').css('color', 'red');
    return false;
  };

  nameInput.removeClass('invalid');
  $('.step').eq(currentTab).addClass('finish');

  var options = {"facebook_checkbox": 0, "website_checkbox": 0, "instagram_checkbox": 0};
  $.each($(".c_container input[type=checkbox]:checked"), function () {
    options[$(this).attr("name")] = 1;
  });

  Instance.options = options;
  Instance.page.name = nameInput.val();

  return true;
}

// Find selected option in list 
function findSelected(optionList) {
  var selected = $(optionList).find('.selected');
  console.log({selected});
  if (selected.length == 0) {
    optionList.fadeOut(50).fadeIn(400);
    return false;
  }
  return selected;
}

// Check box enabler
function enableOption(type, v) {
  var bool = Boolean(v);
  $(`#${type}_checkbox`).prop("disabled", !bool);
  $(`#${type}_checkbox`).prop("checked", bool);
}
