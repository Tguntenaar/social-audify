<?php
/**
 * Template Name: Report Setup
 */
?>
<!--
  TODO:
  1. In de lijst met klanten -> laat geen klanten zien die al geen facebook hebben.

 -->
<!DOCTYPE html>
<html lang='en'>
<head>
  <title>Create Report</title>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/fbcalls.js" charset="utf-8" defer></script>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/multistep.js" charset="utf-8" defer></script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/multistep.css" type="text/css" />
</head>
  <?php
    include(dirname(__FILE__)."/../header/dashboard_header.php");

    $user = $user_control->get($user_id);
    /**
     * TODO: if statement of die wel bestaat
     * op de config page moet je je iba id kunnen veranderen
     */

    // $iba_id = $user->instagram_business_account_id;
    $clients = $client_control->get_all();
    $reports = $report_control->get_all();
  ?>

  <!-- back button -->
  <div class="content-title col-xs-9 col-sm-9 col-md-9 col-lg-9">
    <span class="back" onclick="showIntro(true)">
    <i class="fas fa-chevron-left"></i> Back</span>
  </div>

  <!-- Initial block -->
  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9 responsive-padding-report" style="padding-bottom: 100px;">
  <div class="create-block-box col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <h3>Existing or New contact?</h3>
    <p>
      Here you can choose to select a contact you made previously or simply make a completely new contact.
      Selecting an existing contact will retrieve your contact list, which stores all contacts from previous audits and reports.
      When creating a new contact you start from scratch and need to fill in a contact form, after which this new contact
      will be added to your contact list automatically for future use.
    </p>
    <div class="audit-option-center">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 audit-option border-rightt audit-option-left" onclick="showIntro(false)">
        <div class="vertical-align">
          <span class="option-title">Existing contact</span>
          <span class="option-text">Choose a contact from your contact list.</span>
        </div>
      </div>
      <a href="../client-setup/?from=report" class="ol-xs-12 col-sm-6 col-md-6 col-lg-6 audit-option audit-option-right">
        <div class="vertical-align">
          <span class="option-title">Create new contact</span>
          <span class="option-text">Make a contact and add it to your contact list.</span>
        </div>
      </a>
    </div>
  </div>
  <!-- Containers for multistep form -->
  <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 left responsive-height" style="height: 605px;">
      <div class="inner height-auto">
        <span class="title title-audit-page">Create a report</span>

        <!-- One "tab" for each step in the form: -->
        <form id='regForm' style="margin-bottom: 20px;">

          <!-- Facebook login tab -->
          <div class="tab">
            <span class="login-title">Login to retrieve the data of Facebook that is needed to create a Report.</span>
            <div class="fb-login-button login-center"
                data-scope="manage_pages,instagram_basic,instagram_manage_insights,ads_read"
                auth_type="rerequest"
                data-width="100"
                data-max-rows="1"
                data-size="large"
                data-button-type="continue_with"
                data-show-faces="false"
                data-auto-logout-link="true"
                data-use-continue-as="false"
                onlogin="checkLoginState();">
            </div>
          </div>

          <!-- Choose a client -->
          <div class="tab">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title no-padding">
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">Contact</div>
            </div>
            <input type="text" name="search" id="search-input" placeholder="Search..." valid/>
            <div class="inner-scroll" style="height: 335px;" id="client-list"><?php

              foreach($clients as $client) {
                $data = ["id"=> $client->id, "name"=>$client->name,"facebook"=> $client->facebook, "instagram"=> $client->instagram, "website"=> $client->website, "ad_id"=>$client->ad_id];?>
                <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 height-repsonsive-auto audit-row campaign-row campaign-<?php echo $client->id; ?>" name="<?php echo $client->name; ?>"
                   data-client='<?php echo htmlentities(json_encode($data)); ?>' id="client-<?php echo $client->id; ?>">

                  <span class="name-client"><?php echo $client->name;?></span><?php

                  $ad_overlay = ($client->ad_id == NULL) ? "connect" : "change"; ?>
                  <div class="overlay-ad-account <?php echo $ad_overlay;?>-ad-account">
                    <p><?php echo ucfirst($ad_overlay); ?> ad account</p>
                  </div>
                </a><?php
              } ?>
            </div>
          </div>

          <!-- Compare report tab -->
          <div class="tab">
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">
                <span class="selected-client">Selected client = <strong class="show-client"></strong></span>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title no-padding">
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style no-padding title-green">Compare Reports<span style="color:#000; font-size: 12px; margin-left: 5px;">(Optional)</span></div>
            </div>
            <input type="text" name="search" id="search-input-compare" placeholder="Search..." valid/>
            <div class="inner-scroll" style="height: 335px;" id="compare-list">
              <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row no-compare selected" name="-">No comparable report...</a><?php

              foreach($reports as $report) {
                $data = ["id"=> $report->id, "name"=> $report->name]; ?>
                <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row report-compare campaign-row" name="<?php echo $report->name; ?>"
                  data-compare='<?php echo htmlentities(json_encode($data)); ?>'><?php echo $report->name; ?>
                </a><?php
              } ?>
            </div>
          </div>

          <!-- Report name and options -->
          <div class="tab">
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">
                <span class="selected-client">Selected client = <strong class="show-client"></strong></span>
            </div>
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">
            <span class="selected-client">Selected report to compare = <strong class="show-compare"></strong></span>
            </div>
            <label class="custom-label">
              <span class="name-label" style="margin-left: 20px;">Report Name</span>
              <input type="text" name="report_name" class="name-input" title="Only letters and numbers are allowed." placeholder="Report name.." maxlength="25" required>
            </label>
            <label class="custom-label">
              <span class="name-label" style="margin-left: 20px;">Report options:</span>
              <span class="notice"><p>All the options are selected to include the data of the following platforms, you can deselect it by clicking on the icons.</p></span>
              <label class="c_container">
                <input type="checkbox" name="facebook_checkbox" id="facebook_checkbox" value="facebook_checkbox" checked><br />
                <span class="checkmark"><i class="fab fa-facebook-f"></i></span>
              </label>
              <label class="c_container">
                <input type="checkbox" name="instagram_checkbox" id="instagram_checkbox" value="instagram_checkbox" checked><br />
                <span class="checkmark"><i class="fab fa-instagram"></i></span>
              </label>
            </label>
          </div>

          <!-- Choose campaign/ad level tab -->
          <div class="tab custom-radio">
            <div style="overflow-y:scroll;">
              <span class="name-label">On which level do you want a report?</span>
              <input type="radio" name="level" value="ads" data-response="" checked/>Ads<br />
              <input type="radio" name="level" value="adsets" data-response="" />Ad sets<br />
              <input type="radio" name="level" value="campaigns" data-response="" />Campaigns
            </div>
          </div>

          <!-- Select campaign tab -->
          <div class="tab">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title">
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style name-label" style="margin-left: -27px;">Select Ads or Campaigns</div>
            </div>
            <input type="text" name="search" id="search-input-campaign" placeholder="Search..." valid/>
            <div id="campaign-list" class="inner-scroll" style="height:335px;"></div>
          </div>

          <div>
            <div class="nav-buttons">
              <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
              <button type="button" id="nextBtn">Next</button>
            </div>
          </div>

          <!-- Circles that indicate form steps: -->
          <div class="dot-nav" style="text-align:center;">
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>
  <script charset="utf-8">
    // Report Instance - filled in multistep.
    var Instance = {
      page : { type: 'report' },
      iba_id : <?php echo (isset($iba_id) && $iba_id) ? json_encode($iba_id) : 'null'; ?>,
      adAccounts : [],
    };

    // Selectable list - TODO : kan sws naar dashboard-header
    $('#client-list .audit-row, #compare-list .audit-row, .row-ad-accounts').on('click', function() {
      $(this).parent().find('.audit-row').removeClass('selected');
      $(this).addClass('selected');
    });

    $('.report-compare').on('click', function() {
      nextPrev(1);
    });

    function toggleSelectedAds(that) {
      if (that.hasClass('selected')) {
        that.removeClass('selected');
      } else {
        if ($('#campaign-list .selected').length < 5) {
          that.addClass('selected');
        } else {
          var edge = $('[name=level]:checked').val();
          showModal(initiateModal('errorModal', 'error', {
            'text': `Reached the maximum number of ${edge} selected`,
            'subtext': `You can\'t select more than 5 ${edge}`,
          }));
        }
      }
    }

    function escapeHtml(text) {
      var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
      };

      return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function showActiveCampaigns() {
      valid = true;
      // Show loading ring
      $('#campaign-list').html("<div class='lds-dual-ring'></div>");

      // Ads or campaign level
      var radioBtn= $('[name=level]:checked');
      var edge = radioBtn.val();
      var campaignPromise = makeAdPromise(radioBtn);

      campaignPromise.then(function(response) {
        Instance.currency = response.currency;

        console.log({response});

        if (!response.hasOwnProperty(edge) || response[edge].data.length == 0) {
          $('#campaign-list').html('No data found.');
          return;
        } else {
          // Set data-edge attribute of radio buttons.
          radioBtn.data('response', response);
          $('#campaign-list').empty();
          placeAdsInList($('#campaign-list'), edge, filterActiveAds(response, edge));
        }

        initSearchBar('campaign');

        return valid;
      }).catch(function (reason) {
        console.log({reason});

        showModal(initiateModal('errorModal', 'error', {
          'text': "Couldn't gather campaigns",
          'subtext': `Choose a candidate with a valid ad account.`,
        }));

        nextPrev(-4);

        return false;
      });
    }

    function filterActiveAds(response, edge) {
      var active_ads = [];
      response[edge].data.forEach(function(campaigns) {
        const {id, name, ...insights} = campaigns;

        // Als insights niet bestaat zorgt object destruction ervoor dat insights == {}
        if (!$.isEmptyObject(insights)) {
          active_ads.push(campaigns);
        }
      });
      return active_ads;
    }

    function placeAdsInList(list, edge, ads) {
      if (ads.length == 0) {
        list.html(`No active ${edge} running.`);
      } else {
        ads.forEach(function(ad) {
          var str = `<a class="audit-row competitors" data-id=${ad.id} name="${ad.name}" onclick="toggleSelectedAds($(this))">Name: ${ad.name}</a>`;
          list.append(str);
        });
      }
    }

    function makeAdPromise(radioBtn) {
      // Api won't be called twice for the same edge.
      if (!$.isEmptyObject(radioBtn.data('response')))
        return Promise.resolve(radioBtn.data('response'));

      // call facebook api.
      return new Promise(function (resolve, reject) {
        console.log(getCampaignsQuery(Instance.client.ad_id, radioBtn.val()));
        FB.api(getCampaignsQuery(Instance.client.ad_id, radioBtn.val()), function (response) {
          if (response && !response.error) {
            resolve(response);
          }
          reject(response);
        });
      });
    }

    function check_nan(value) {
        return (Number.isNaN(value) || value === undefined) ? 0 : parseFloat(value);
    }

    /**
     * uses the global <selectedAds> variable.
     * data = [{name: <name>, insights: {cpp: <cpp>, cpm: <cpm>}}, etc]
     */
    function transformResponseData(response) {
      var data = [], insight;
      var edge = $('[name=level]:checked').val();
      var selectedAds = [];

      if (!response.hasOwnProperty(edge)) {
        return data;
      }

      $('#campaign-list .selected').each(function(i, ad) {
        selectedAds = [...selectedAds, $(ad).data('id')];
      });


      response[edge].data.forEach(function(campaign) {
        const {id, name, ...rest} = campaign;

        if ((selectedAds.includes(id) || selectedAds.includes(Number(id))) && !$.isEmptyObject(rest)) { // Als the campaign is geselecteerd en hij insights heeft.
          data = [...data, {name: name, insights: rest.insights.data[0]}];
        }
      });

      var avg = calculateAverageObject(data);

      data = [...data, {name:'average', insights: avg}];
      return data;
    }

    function calculateAverageObject(data) {
      var avg = {};
      // dit kan dus een stuk dynamischer
      var sum = data.reduce(function(acc, cur) {
        return {
          reach: acc.reach + check_nan(cur.insights.reach),
          impressions: acc.impressions + check_nan(cur.insights.impressions),
          cpc: acc.cpc + check_nan(cur.insights.cpc),
          cpm: acc.cpm + check_nan(cur.insights.cpm),
          cpp: acc.cpp + check_nan(cur.insights.cpp),
          ctr: acc.ctr + check_nan(cur.insights.ctr),
          frequency: acc.frequency + check_nan(cur.insights.frequency),
          spend: acc.spend + check_nan(cur.insights.spend),
          unique_inline_link_clicks: acc.unique_inline_link_clicks + check_nan(cur.insights.unique_inline_link_clicks),
          website_purchase_roas: acc.website_purchase_roas + check_nan(cur.insights.website_purchase_roas)
        };
      }, {reach: 0, impressions: 0, cpc: 0, cpm: 0, cpp: 0, ctr: 0, frequency: 0, spend: 0, unique_inline_link_clicks: 0, website_purchase_roas: 0});
      // divides sum into avg

      for (var insight in sum) {
        avg[insight] = sum[insight] / data.length;
      }
      return avg;
    }

    $(function() {
      <?php
      if (isset($_GET['cid'])) { ?>
        showIntro(false);
        var id = "<?php echo $_GET['cid']; ?>";
        var selected = $(`#client-list a[id=client-${id}]`);
        // TODO: open ad account modal hier ook; && remove??
        selected.parent().find('.audit-row').removeClass('selected');
        selected.addClass('selected');<?php
      } ?>

      // Connect Ad Account Modal
      var modalData = {
        text: 'Select the right ad account for the right campaigns',
        html: `<select size="2" id="ad-account-list" class="ad-account-list"></select>
                <input type="hidden"  id="client_id" name="client_id" value="0">
                <input type="hidden" id="ad_id" name="ad_id" value="">`,
        confirm: 'adAccountConfirm'
      }

      var adAccountModal = initiateModal('adAccountModal', 'confirm', modalData);

      // Connect Ad Account
      $('.connect-ad-account, .change-ad-account').on('click', function() {
        openAdAccountDialog($(this), adAccountModal);
      });

      // TODO: deze moet openen  als newClient is geset maar scope problemen
      function openAdAccountDialog(link, adAccountModal) {
        // 1. SET THE CLIENT
        var client =  link.parent().data('client');
        $('#client_id').val(client.id);

        // 2. SET CURRENT AD ID
        $('#ad_id').val(client.ad_id);

        getAdAccounts(client.ad_id);
        showModal(adAccountModal);
        $('#ad-account-list').focus();
      }

      // 3. Update Ad id
      $('#adAccountConfirm').click(function() {
        if (selectedOption = getSelectedAdAccount($('#ad-account-list'))) {

          var clientId = $('#client_id').val();
          var adId = selectedOption.val();
          var clickedClient = $(`.campaign-${clientId}`);

          // Change the data-client attribute
          var clientDataAttribute = clickedClient.data('client');

          clientDataAttribute.ad_id = adId;
          clickedClient.data("client", clientDataAttribute);

          // Change the class of the overlay
          var overlay = clickedClient.find('div');
          overlay.addClass('change-ad-account').removeClass('connect-ad-account');
          // Change the link
          var link = overlay.find('p');
          link.text('Change ad account');

          // Connect the account
          connectAccount(selectedOption.val(), clientId);
        }
      });

      // Searchable lists
      ['client', 'compare'].forEach(function(name) {
        initSearchBar(name);
      });
    });

    function initSearchBar(name) {
      var elems = $(`#${name}-list .audit-row`);
      var search = name == 'client' ? '' : `-${name}`;

      $(document).on('keyup', `input#search-input${search}`, function() {
        console.log(elems);
        filterSearch($(this).val(), elems);
      });
    }

    function submitForm() {
      showBounceBall(true, 'Give us a few seconds as we create your awesome report');

      // validate number of ads selected
      if ($('#campaign-list .selected').length < 1 || $('#campaign-list .selected').length > 5) {
        var edge = $('[name=level]:checked').val();
        var hiLo = ($('#campaign-list .selected').length > 5) ? 'high' : 'low';
        showModal(initiateModal('errorModal', 'error', {
          'text': `Number of ${edge} selected is too ${hiLo}`,
          'subtext': `Please select between 1 and 5 ${edge}`,
        }));
        return showBounceBall(false);
      }

      var loggedInPromise = new Promise((resolve, reject) => {
        FB.getLoginStatus(function(response) {
          if (response.status === 'connected') {
            FB.AppEvents.logEvent("reportSubmitted");
            resolve(response);
          } else {
            reject(response);
          }
        });
      });

      loggedInPromise.then((value) => {
        // last check what edge the user selected
        var response = $('[name=level]:checked').data('response');

        Instance.client.chart_data = transformResponseData(response);

        // console.log(Instance);
        makeApiCalls(Instance);

        return false;
      }).catch((reason) => {
        showBounceBall(false);
        // var msg = (typeof reason == 'string') ? reason : reason.error.message;
        // showModal(initiateModal('errorModal', 'error', {
        //   'text': `${msg}`,
        //   'subtext': `Please try again to login to Facebook`,
        // })); // TODO: welke is beter? doe hetzelfde in audit setup
        showModal(initiateModal('errorModal', 'error', {
          'text': "Problem with Login Status",
          'subtext': "Please try again later or notify an admin if the issue persists"
        }));
      });
    }
  </script>
</body>
</html>
