<?php
/**
 * Template Name: Audit setup
 */
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <!-- TODO: kan niet naar beneden dan overschrijft ie dashboard css maar hier kan geen cache version achter -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/multistep.css" type="text/css" />
  </head>
  <?php
    // Header
    include(dirname(__FILE__)."/../header/dashboard_header.php");

    $client_id = $_GET['cid'];

    // Redirect to dashboard without client.
    if (!isset($client_id)) {
        header("Location: https://".getenv('HTTP_HOST')."/dashboard");
    }

    $user = $user_control->get($user_id);
    $clients = $client_control->get_all();

    $audits_made = $audit_control->get_amount(date('Y-m-d'));
    $audits = $audit_control->get_all($user_id);
  ?>
  <head>
    <title>Create Audit</title>
    <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/fbcalls.js<?php echo $cache_version; ?>" charset="utf-8" defer></script>
    <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/multistep.js<?php echo $cache_version; ?>" charset="utf-8" defer></script>
  </head>

  <div id="competitorModal" class="modal"></div>
  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-12 responsive-padding" style="padding-bottom: 25px;">

  <!-- Containers for multistep form -->
  <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 left responsive-height" style="height: 605px;">
      <div class="inner">
        <span class="title title-audit-page">Create an Audit</span>
        <div class="audit_count"><?php echo $audits_made.' created Audits today'; ?></div>

          <!-- Choose a client -->
          <div class="tab tab-setup">
            <div style="border: 0;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title no-padding">
              <div style="font-weight: 100;" class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">Select a contact</div>
            </div>
            <input type="text" name="search" class="setup-input" id="search-input" placeholder="Search..." valid/>
            <div class="inner-scroll" style="height: 335px;" id="client-list"><?php 
              foreach($clients as $client) {

                $selected = $client->id == $client_id ? "selected" : "";
                $data = ["id"=> $client->id, "name"=>$client->name, "facebook"=> $client->facebook, "instagram"=> $client->instagram, "website"=> $client->website]; ?>

                <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row client campaign-row <?php echo $selected; ?>" 
                   name="<?php echo $client->name; ?>" id="client-<?php echo $client->id;?>" data-client='<?php echo htmlentities(json_encode($data)); ?>'><?php

                  echo "$client->name".($client->audit_count > 0 ? 
                    "<div class='client-status converted'>Audit sent</div>" :
                    "<div class='client-status no_reply'>New</div>"); ?>
                </a><?php
              } ?>
            </div>
          </div>

          <!-- Chosing an competitor -->
          <div class="tab tab-setup" >
            <span style="display: none;" class="temp-show-comp"></span>
            <div style="border: 0;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title no-padding">
              <div style="font-weight:100;" class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">
                Want to compare <span style="font-weight: 100; color: #000;" class="show-client"></span> to a Competitor?<span style="color:#000; font-size: 12px; margin-left: 5px;">(Optional)</span>
              </div>
            </div>
            <input type="text" name="search" class="setup-input" id="search-input-compare" placeholder="Search..." valid/>
            <div class="inner-scroll" style="height: 335px;" id="compare-list">
              <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row client selected" name="-" data-compare=''>No competitor...</a>
              <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row new-compare" name="+"
                data-compare='<?php echo htmlentities(json_encode([ 'facebook' => '', 'instagram' => '', 'website' => ''])); ?>'>New competitor
              </a><?php
              foreach($clients as $client) {
                $data = ["id"=> $client->id, "name"=> $client->name, "facebook"=> $client->facebook, "instagram"=> $client->instagram, "website"=> $client->website];?>
                <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row client campaign-row" name="<?php echo $client->name; ?>"
                  data-compare='<?php echo htmlentities(json_encode($data)); ?>'><?php echo $client->name; ?>
                </a><?php
              } ?>
            </div>
          </div>

          <!-- Audit name and options -->
          <div class="tab tab-setup">
            <div class="custom-label">
              <span style="font-weight: 100;" class="name-label">Fill in your audit name</span>
              <input type="text" id="name-input" name="audit_name" class="setup-input name-input" placeholder="Audit name.." title="Only letters and numbers are allowed." maxlength="25" required>
            </div>
            <div class="custom-label">
              <span style="font-weight: 100;" class="name-label" style="margin-top: -15px;">Audit options:</span>
              <span class="notice"><p>All the options are selected to include the data of the following platforms, you can deselect it by clicking on the icons.</p></span>
              <label class="c_container">
                <input type="checkbox" name="facebook_checkbox" id="facebook_checkbox" value="facebook_checkbox" checked><br />
                <span class="checkmark"><i class="fab fa-facebook-f"></i></span>
              </label>
              <label class="c_container">
                <input type="checkbox" name="instagram_checkbox" id="instagram_checkbox" value="instagram_checkbox" checked><br />
                <span class="checkmark"><i class="fab fa-instagram"></i></span>
              </label>
              <label class="c_container">
                <input type="checkbox" name="website_checkbox"  id="website_checkbox" value="website_checkbox" checked><br />
                <span class="checkmark"><i class="fas fa-globe"></i></span>
              </label>
            </div>
            <div style="clear: both;"></div>
            <span style="font-weight: 100;" class="name-label" style="margin-top: -20px; float: left;">Selected contact:</span>
            <div style="font-size: 14px !important; position: absolute; left: 20px; margin-top: 5px; float: left;">
              <span style="font-weight: 600;" class="show-client"></span>
              <span class="has_comp"></span><span style="font-weight: 600;" class="show-compare"></span>
            </div>
            <br/>
          </div>
          <div>
            <div class="nav-buttons">
              <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
              <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
            </div>
          </div>

          <!-- Circles that indicate form steps: -->
          <div class="dot-nav" style="text-align:center;">
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    // Audit Instance - filled in multistep.
    var Instance = {
      page : {
        type: 'audit',
        manual: 0,
        competitor_manual: 0,
      },
      iba_id : <?php 
        echo (isset($user->instagram_business_account_id) && $user->instagram_business_account_id) ?
          json_encode($user->instagram_business_account_id) : 'null'; 
      ?>,
    }

    // Selectable list - TODO : kan wss naar dashboard-header
    $('.audit-row.client').on('click', function() {
      $(this).parent().find('.audit-row').removeClass('selected');
      $(this).addClass('selected');
      nextPrev(1);
    });

    // Custom 'Selected contact'-message when competitor is selected.
    $("body").on('DOMSubtreeModified', ".show-compare", function() {
        $('.has_comp').html(" is going to be compared to ");
    });

    $(function() {
      // New Competitor Modal:
      var modalData = {
        text: 'New Competitor:',
        html: `<div class="new-competitor" style="align:center">
            <input type="text" id="competitor-name" placeholder="Name" pattern="<?php echo $Regex->name; ?>" title="Only letters are allowed">
            <input type="text" id="facebook_url" data-type="facebook" placeholder="Facebook page url, page id or page username">
            <input type="text" id="instagram_url" data-type="instagram" placeholder="Instagram username or url">
            <input type="text" id="website_url" data-type="website" placeholder="https://www.example.com" pattern="<?php echo $Regex->wb; ?>">
          </div>`,
        subtext: 'Create a new temporary client for just this audit',
        confirm: 'competitor_confirmed'
      }

      var competitorModal = initiateModal('competitorModal', 'confirm', modalData);

      $('#compare-list .new-compare').on('click', function() {
        var data = $(this).data('compare');
        $(competitorModal).find('#competitor-name').val(data.name);
        $(competitorModal).find('#facebook_url').val(data.facebook);
        $(competitorModal).find('#instagram_url').val(data.instagram);
        $(competitorModal).find('#website_url').val(data.website);
        showModal(competitorModal);
      });

      $('#facebook_url, #instagram_url, #website_url').focusout(function() {
        changeClientInputFields(this);
      });

      $("#competitor_confirmed").click(function() {
        var newCompare = $('#compare-list .new-compare');
        newCompare.data('compare', {
          name : $('#competitor-name').val(),
          facebook : $('#facebook_url').val(),
          instagram : $('#instagram_url').val(),
          website : $('#website_url').val().replace("https://", "").replace("http://", "")
        });
        var name = (newCompare.data('compare').name !== "") ? newCompare.data('compare').name: 'empty';
        newCompare.html(`New Competitor <span style="color:grey;">(${name})</span>`);

        newCompare.parent().find('.audit-row').removeClass('selected');
        newCompare.addClass('selected');
        nextPrev(1);
      });

      // Searchable lists
      ['client', 'compare'].forEach(function(name) {
        var elems = $(`#${name}-list .audit-row`);
        var search = name == 'client' ? '' : `-compare`;

        $(document).on('keyup', `input#search-input${search}`, function() {
          filterSearch($(this).val(), elems);
        });
      });
    });

    /**
     * TODO:  de variablen die zoals clientInfo competitor options etc moeten
     * direct na het valideren worden gemaakt niet pas wanneer je submit.
     * Dan wordt de code lokaler
     */
    function submitForm() {
      showBounceBall(true, "Give us a few seconds as we create your awesome audit");

      var loggedInPromise = new Promise((resolve, reject) => {
        FB.getLoginStatus(function(response) {
          if (response.status === 'connected') {
            FB.AppEvents.logEvent("auditSubmitted");
            resolve(response);
          } else {
            reject(response);
          }
        });
      });

      loggedInPromise.then((value) => {
        makeApiCalls(Instance);

        // If makeApiCalls doesn't redirect...
        return false;
      }).catch((reason) => {
        showModal(initiateModal('errorModal', 'error', {
          'text': "Problem with Login Status",
          'subtext': "Please try again later or notify an admin if the issue persists"
        }));
        showBounceBall(false);
      });
    }
  </script>
</body>
</html>
