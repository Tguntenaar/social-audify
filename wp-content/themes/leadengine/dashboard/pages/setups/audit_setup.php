<?php
/**
 * Template Name: Audit setup
 */
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <title>Create Audit</title>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/fbcalls.js" charset="utf-8" defer></script>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/multistep.js" charset="utf-8" defer></script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/multistep.css" type="text/css" />
</head>
  <?php
    // Header
    include(dirname(__FILE__)."/../header/dashboard_header.php");

    $user = $user_control->get($user_id);
    $iba_id = $user->instagram_business_account_id;
    $clients = $client_control->get_all();
  ?>

  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9 responsive-padding" style="padding-bottom: 25px;">

  <!-- back button -->
  <div class="content-title col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <span class="back" onclick="showIntro(true)">
      <i class="fas fa-chevron-left"></i> Back</span>
    <div class="audit_count"><?php
      $max_audits = 1000;
      $audits_made = $audit_control->get_amount($date = date('Y-m-d'));
      echo $audits_made.'/'.$max_audits.' audits today'; ?>
    </div>
  </div>

  <!-- Initial block -->
  <div class="create-block-box col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <?php
    if ($audits_made > $max_audits) { ?>
      <div class="max-audits"> Max of <?php echo $max_audits; ?> audits reached.
        <span class="description">You can upgrade your account to increase your daily audit count.</span>
        <a href="../dashboard/"><i class="fas fa-th-large"></i>&nbsp; Dashboard</a>
      </div><?php
    } else { ?>
      <h3>Existing or New contact?</h3>
      <p>
        Here you can choose to select a contact you made previously or simply make a completely new contact.
        Selecting an existing contact will retrieve your contact list, which stores all contacts from previous audits and reports.
        When creating a new contact you start from scratch and need to fill in a contact form, after which this new contact
        will be added to your client list automatically for future use.
      </p>
      <div class="audit-option-center">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 audit-option border-rightt audit-option-left" onclick="showIntro(false)">
          <div class="vertical-align">
            <span class="option-title">Existing contact</span>
            <span class="option-text">Choose a client from your client list.</span>
          </div>
        </div>
        <a href="../client-setup/?from=audit" class="ol-xs-12 col-sm-6 col-md-6 col-lg-6 audit-option audit-option-right">
          <div class="vertical-align">
            <span class="option-title">Create new contact</span>
            <span class="option-text">Make a client and add it to your client list.</span>
          </div>
        </a>
      </div>
    </div>
    <!-- Containers for multistep form -->
    <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 left responsive-height" style="height: 605px;">
        <div class="inner height-auto">
          <span class="title title-audit-page">Create an Audit</span>

          <!-- One "tab" for each step in the form: -->
          <form id="regForm" style="margin-bottom: 20px;" class="submit-audit">

            <!-- Facebook tab -->
            <div class="tab">
              <span class="login-title">Login to retrieve the data of Facebook that is needed to create an Audit.</span>
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
                  $data = ["id"=> $client->id, "name"=>$client->name, "facebook"=> $client->facebook, "instagram"=> $client->instagram, "website"=> $client->website]; ?>
                  <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row client campaign-row" name="<?php echo $client->name; ?>" id="client-<?php echo $client->id;?>"
                    data-client='<?php echo htmlentities(json_encode($data)); ?>'><?php echo $client->name; ?>
                  </a><?php
                } ?>
              </div>
            </div>

            <!-- Chosing an competitor -->
            <div class="tab" >
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">
                <span class="selected-client">Selected client = <strong class="show-client"></strong></span>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title no-padding">
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">Competitor</div>
              </div>
              <input type="text" name="search" id="search-input-compare" placeholder="Search..." valid/>
              <div class="inner-scroll" style="height: 335px;" id="compare-list">
                <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row no-compare selected" name="-" data-compare=''>No competitor...</a>
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
            <div class="tab">
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">
                <span class="selected-client">Selected client = <strong class="show-client"></strong></span>
              </div>
              <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 row-title-style title-green no-padding">
                <span class="selected-client">Selected competitor = <strong class="show-compare"></strong></span>
              </div>
              <div class="custom-label">
                <span class="name-label">Audit Name</span>
                <input type="text" name="audit_name" class="name-input" placeholder="Audit name.." title="Only letters and numbers are allowed." maxlength="25" required>
              </div>
              <div class="custom-label">
                <span class="name-label">Audit options:</span>
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
            </div>
            <div>
              <div class="nav-buttons">
                <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                <button type="button" id="nextBtn">Next</button>
              </div>
            </div>

            <!-- Circles that indicate form steps: -->
            <div class="steps dot-nav" style="text-align:center;">
              <span class="step"></span>
              <span class="step"></span>
              <span class="step"></span>
              <span class="step"></span>
            </div>
          </form>
        </div>
      </div><?php
    } ?>
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
      iba_id : <?php echo (isset($iba_id) && $iba_id) ? json_encode($iba_id) : 'null'; ?>,
    }

    // Selectable list - TODO : kan wss naar dashboard-header
    $('.audit-row').on('click', function() {
      $(this).parent().find('.audit-row').removeClass('selected');
      $(this).addClass('selected');
    });

    $('.audit-row.client').on('click', function() {
      nextPrev(1);
    });

    $(function() {
      <?php
      if (isset($_GET['cid'])) { ?>
        showIntro(false);
        var id = "<?php echo $_GET['cid']; ?>";
        var selected = $(`#client-list a[id=client-${id}]`);
        // wrm zou iets geselecteerd zijn?
        selected.parent().find('.audit-row').removeClass('selected');
        selected.addClass('selected');<?php
      } ?>

      // New Competitor Modal:
      var modalData = {
        text: 'New Competitor:',
        html: `<div class="new-competitor" style="align:center">
            <input type="text" id="competitor-name" placeholder="Name" pattern="<?php echo $name_regex; ?>" title="Only letters are allowed">
            <input type="text" id="facebook_url" placeholder="Facebook page url, page id or page username">
            <input type="text" id="instagram_url" placeholder="Instagram username or url">
            <input type="text" id="website_url" placeholder="https://www.example.com" pattern="<?php echo $website_regex; ?>">
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
        parseClientInputFields(this);
      });

      $("#competitor_confirmed").click(function() {
        var newCompare = $('#compare-list .new-compare');
        newCompare.data('compare', {
          name : $('#competitor-name').val(),
          facebook : $('#facebook_url').val(),
          instagram : $('#instagram_url').val(),
          website : $('#website_url').val().replace("https://", "").replace("http://", ""),
        });
        var name = (newCompare.data('compare').name !== "") ? newCompare.data('compare').name: 'empty';
        newCompare.html(`New Competitor <span style="color:grey;">(${name})</span>`);
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
      showBounceBall(true, "Preparing audit, please hold a few seconds");
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
