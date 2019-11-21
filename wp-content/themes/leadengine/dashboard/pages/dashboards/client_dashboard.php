<?php
/**
 * Template Name: Client dashboard
 */
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php
  // Header
  include(dirname(__FILE__)."/../header/dashboard_header.php");

  // Get all the clients
  $clients = $client_control->get_all();
?>

<head>
  <meta charset="utf-8">
  <title>Contact Dashboard</title>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/fbcalls.js" charset="utf-8" defer></script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/client_dashboard.css<?php echo $cache_version; ?>" type="text/css" />
</head>
<body>
  <div id="adAccountModal" class="modal"></div>
  <!-- <div id="confirmAddAdAccountModal" class="modal"></div> -->
  <div id="confirmDeleteModal" class="modal"></div>

  <!-- Edit client Modal TODO: dit moet in een modal-->
  <div id="edit-client-modal" class="modal client_modal" style="display:none">
    <div class="modal-content">
      <span id="close_model" class='close'>&times;</span>
      <span><i class="fas fa-trash delete-this-audit" id="delete_button_client" style="left:20px; right:500px; width:16px;"></i></span>
      <span class="modal-title">Edit Contact:</span>
      <form id="edit-form" action="/process-client/?edit=true" method="post">
        <div class="" style="align:center">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 edit-client-left">
            <span class="input-tag">Name</span>
            <span class="input-tag">Facebook url</span>
            <span class="input-tag">Instagram url</span>
            <span class="input-tag">Website url</span>
            <span class="input-tag">E-mail</span>
            <span class="input-tag">Ad Account</span>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 edit-client-right">
            <input maxlength="25" type="text" id="client_name" name="client_name" placeholder="Name" pattern="<?php echo $Regex->name ?>" title="Only letters and numbers are allowed"><br />
            <input type="text" id="facebook_url" name="facebook_url" data-type="facebook" placeholder="Facebook page url, page id or page username" ><br />
            <input type="text" id="instagram_url" name="instagram_url" data-type="instagram" placeholder="Instagram username or url"><br />
            <input type="text" id="website_url" name="website_url" data-type="website" placeholder="www.website.com" pattern="<?php echo $Regex->wb;?>"><br />
            <input type="email" id="mail_adress" name="client_mail" placeholder="mail@example.com"><br />
            <div id="ad-account-bttn-wrapper" style="display:none">
              <span class="responsive-label-ad">Ad account</span><button type="button" class="create-audit-button client-button" id="connect-ad-account">Connect</button><br>
            </div>
            <div id="fb-login-wrapper" class="custom-fb-button">
              <div class="fb-login-button login-center"
                  data-scope="manage_pages,instagram_basic,instagram_manage_insights,ads_read"
                  auth_type="rerequest"
                  data-width="100"
                  data-max-rows="1"
                  data-size="medium"
                  data-button-type="continue_with"
                  data-show-faces="false"
                  data-auto-logout-link="false"
                  data-use-continue-as="false"
                  onlogin="showConnectAdAccount()">
              </div>
            </div>
          </div>
          <div style="clear:both"></div>
          <input type="hidden" id="client_id" name="client_id" value="0">
          <input type="hidden" id="ad_id" name="ad_id" value="">
        </div>
        <input type="submit" class="advice-button" name="" value="Make changes">
      </form>
    </div>
  </div>

  <div class="client-dashboard content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="padding-bottom: 50px;">
    <div class="sub-nav-client">
          <h1 class="create-report-h1-client">Create and manage contacts.</h1>
        <div class="center-buttons">
          <a href='/client-setup/' class="create-button-client" style="margin-left: 15px;">Create client</a>
          <a href='/client-import/' class="create-button-client">Mass import client</a>
        </div>
    </div>
    <input type="text" name="search" id="search-input" placeholder="Search..."/>
    <div class="client-overview" id="client-results"><?php
      foreach($clients as $client) { 
        $data = ["id"=> $client->id, "name"=>$client->name, "fb"=> $client->facebook, "ig"=> $client->instagram,
          "wb"=> $client->website, "ml" => $client->mail, "ad_id" => $client->ad_id]; ?>

        <div class="client-overview-row" data-name="<?php echo $client->name; ?>">
          <div class="client-overview-row-inner" data-id="<?php echo $client->id; ?>" data-client="<?php echo htmlentities(json_encode($data)); ?>"><?php
            echo $client->audit_count > 0 ? 
              "<div class='client-status converted'>{$client->audit_count} Audit sent".($client->audit_count > 1 ? 's' : '')."</div>" :
              "<div class='client-status no_reply'>Not sent</div>"; ?>

            <div class="details">
              <span class="client-name-n"><?php echo $client->name; ?></span><?php

              if($client->facebook != NULL) { ?>
                <a class="social-icon" target="_blank" rel="norefferer" href="https://www.facebook.com/<?php echo $client->facebook; ?>">
                  <i class="fab fa-facebook-f"></i>
                </a><?php
              }
              if($client->instagram != NULL) { ?>
                <a class="social-icon instagram-social mail-social" target="_blank" rel="norefferer" href="https://www.instagram.com/<?php echo $client->instagram; ?>">
                  <i class="fab fa-instagram"></i>
                </a><?php
              } 
              if($client->website != NULL) { ?>
                <a class="social-icon web-social" target="_blank" rel="norefferer" href="<?php echo $client->website; ?>">
                  <i class="fas fa-globe"></i>
                </a><?php
              } 
              if($client->mail != NULL) { ?>
                <a class="social-icon instagram-social mail-social" href="mailto: <?php echo $client->mail; ?>">
                  <i class="far fa-envelope"></i>
                </a><?php
              } ?>
            </div>
            <i class="fas fa-ellipsis-v edit-client" style="cursor:pointer;" onclick="editClient(this)"></i>
          </div>
        </div><?php
      } ?>
    </div>
  </div>

	<script charset="utf-8">
    var Instance = {
      adAccounts : [],
    };

    function editClient(rowIcon) {
      const {id, name, fb, ig, wb, ml, ad_id} = $(rowIcon).parent().data('client');

      $('#client_id').val(id);
      $('#client_name').val(name);
      $('#facebook_url').val(fb);
      $('#instagram_url').val(ig);
      $('#website_url').val(wb);
      $('#mail_adress').val(ml);
      $('#ad_id').val(ad_id);

      // Change the button
      $('.connect-ad-account').text((ad_id == null) ? 'Connect' : 'Change');
      $('#edit-client-modal').css({'display': 'block'});
    }

    function showConnectAdAccount() {
      $('#fb-login-wrapper').css({display: 'none'});
      $('#ad-account-bttn-wrapper').css({display: 'block'});
    }

    $(function() {
      var elems = $("#client-results .client-overview-row");
      var selectedList = [];

      elems.find('.audit-row-style').on('click', function() {
        selectedList = toggleSelected($(this).parent(), selectedList, $(".selectDelete"), 1);
      });

      $('#delete_button_client').click(function() { deleteClients([$('#client_id').val()]); });
      $('.selectDelete').click(function() { deleteClients(selectedList); });

      function deleteClients(selectedList) {
        console.log(selectedList);
        showModal(initiateModal('confirmModal', 'confirm', {
          'text': `Delete Clients`,
          'subtext': `Would you like to delete the selected Client${(selectedList.length == 1 ? '' : 's')}?`,
          'confirm': 'delete_confirmed'
        }));

        $("#delete_confirmed").click(function() {
          $('#edit-client-modal').css({'display': 'none'});
          showBounceBall(true, 'Deleting Clients...');
          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'delete_multiple', ids: selectedList, type: 'client'},
            success: function(response) { location.reload(); },
            error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, selectedList);
              logError(send_error, 'setups/delete_clients.php', 'submit');
              location.reload();
            }
          });
        });
      }

      // Close the pop up form
      $('#close_model').click(function() {
        $('#edit-client-modal').css({'display': 'none'});
      });

      // Connect Ad Account Modal FIXME:
      var adAccountModal = initiateModal('adAccountModal', 'confirm', {
        text: 'Select the right ad account for the right campaigns',
        html: `<select size="2" id="ad-account-list" class="ad-account-list"></select>`,
        confirm: 'adAccountConfirm'
      });

      $('#connect-ad-account').on('click', function() {
        // 1. SET CLIENT ID
        // 2. SET CURRENT AD ID
        // in client dashboard worden beide al geset in het eerste modal

        getAdAccounts($('#ad_id').val());
        showModal(adAccountModal);
        $('#ad-account-list').focus();
      });

      $('#adAccountConfirm').click(function() {
        if (selectedOption = getSelectedAdAccount($('#ad-account-list'))) {
          // Change the button
          $('#connect-ad-account').text('Change');
          $('#ad_id').val(selectedOption.val());
          connectAccount(selectedOption.val(), $("#client_id").val());

          $('#adAccountModal').css({'display': 'none'});
        }
      });


      // Search function
      var counterSpan = $('#counterSpan');
      $(document).on('keyup', 'input#search-input', function() {
        filterSearch($(this).val(), elems, counterSpan, true);
      });

      $('#facebook_url, #instagram_url, #website_url').focusout(function() {
        changeClientInputFields(this);
      });
    });
	</script>
</body>
</html>
