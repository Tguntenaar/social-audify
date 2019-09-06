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
  include(dirname(__FILE__)."/../../assets/php/client_regex.php");

  class clientMedia {
    public $name, $id, $fb, $ig, $wb, $ml;

    public function __construct($client) {
      $this->id = $client->id;
      $this->name = $client->name;
      $this->fb = $client->facebook;
      $this->ig = $client->instagram;
      $this->wb = $client->website;
      $this->ml = $client->mail;
      $this->ad_id = $client->ad_id;
    }
  }

  // Get all the clients
  $clients = $client_control->get_all();
  $jsClients = array();

  foreach ($clients as $c) {
    array_push($jsClients, new clientMedia($c));
  }
?>

<head>
  <meta charset="utf-8">
  <title>Client Dashboard</title>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/fbcalls.js" charset="utf-8" defer></script>

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
          <div class="col-lg-6 edit-client-left">
            <span class="input-tag">Name</span>
            <span class="input-tag">Facebook url</span>
            <span class="input-tag">Instagram url</span>
            <span class="input-tag">Website url</span>
            <span class="input-tag">E-mail</span>
            <span class="input-tag">Ad Account</span>
          </div>
          <div class="col-lg-6 edit-client-right">
            <input type="text" id="client_name" name="client_name" placeholder="Name" pattern="<?php echo $name_regex ?>" title="Only letters are allowed"><br />
            <input type="text" id="fb_url" name="facebook_url" min="5" placeholder="pageusername or url" name="facebook_url"><br />
            <input type="text" id="ig_url" name="instagram_url" min="5" max="30" placeholder="username or url" name="instagram_url"><br />
            <input type="text" id="wb_url" name="website_url" placeholder="www.website.com" pattern="<?php echo $website_regex;?>"><br />
            <input type="email" id="mail_adress" name="client_mail" placeholder="mail@example.com"><br />
            <div id="ad-account-bttn-wrapper" style="display:none">
              <button type="button" class="create-audit-button client-button connect-ad-account">Connect</button><br>
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

  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="padding-bottom: 50px;">
  <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 screen-height mobile-margin" style="height: 350px; text-align: center;">
      <div class="center-center">
        <h1 class="create-report-h1" style="width: 65%; margin: 0 auto; margin-bottom: 40px; margin-top: 20px;">Create a contact in a few steps.</h1>
        <a class="create-audit-button client-button" href="/client-setup/">Create Contact</a>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  right float-right no-margin" style="margin-top: 150px;">
      <div class="inner no-scroll client-dashboard">
        <span class="title"><span class="title-background">Contacts</span>
          <span class="count" id="counterSpan"><?php echo $number_of_clients; ?></span>
        </span>
        <input type="text" name="search" id="search-input" placeholder="Search..."/>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title">
          <div class="col-12 col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Name</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Facebook</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Instagram</div>
          <div class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 row-title-style" style="padding:0;">Website</div>
        </div>
        <div class="inner-scroll client-dashboard" id="client-results"><?php
          foreach ($clients as $client) {
            $name = strlen($client->name) <= 18 ? $client->name : substr($client->name, 0, 18).'...';
            $facebook = strlen($client->facebook) <= 18 ? $client->facebook : substr($client->facebook, 0, 18).'...';
            $instagram = strlen($client->instagram) <= 18 ? $client->instagram : substr($client->instagram, 0, 18).'...';
            $website = strlen($client->website) <= 18 ? $client->website : substr($client->website, 0, 18).'...'; ?>

            <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="<?php echo $client->name; ?>">
              <div style="overflow:hidden" class="col-12 col-sm-3 col-md-3 col-lg-3 audit-row-style"><?php echo $name; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style"><?php echo $facebook; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style"><?php echo $instagram; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style"><?php echo $website; ?></div>
              <i class="fas fa-ellipsis-v delete-this-audit client_<?php echo $client->id; ?>-edit" style="color:grey; cursor:pointer;"></i>
            </a><?php
          } ?>
        </div>
      </div>
    </div>
  </div>

	<script charset="utf-8">
    function showConnectAdAccount() {
      $('#fb-login-wrapper').css({display: 'none'});
      $('#ad-account-bttn-wrapper').css({display: 'block'});
    }

    $(function() {
      // Close the pop up form
      $('#close_model').click(function() {
        $('#edit-client-modal').css({'display': 'none'});
      });

      // Delete Client Modal
      var modalData = {
        'text': 'Sure you want to delete this Client?',
        'subtext': 'This action is irreversible',
        'confirm': 'delete_confirmed',
      }

      var deleteModal = initiateModal('confirmDeleteModal', 'confirm', modalData);

      $('#delete_button_client').click(function() {
        showModal(deleteModal);
      });

      $('#delete_confirmed').click(function() {
        var data = {
          'action': 'delete_client',
          'client': $('#client_id').val(),
          'auth': '<?php echo hash('sha256', 'auth'.$user_id.'salted'.date('Y-m-d H:i').'randomstuff'); ?>',
          'client_name': $('#client_name').val(),
        };

        $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (response) { location.reload(); },
          error: function (errorThrown) {
            var modalData = {
            'text': "Can't delete this client",
            'subtext': "Please try again later or notify an admin if the issue persists"
            }
            showModal(initiateModal('errorModal', 'error', modalData));
            console.log({errorThrown});
          }
        });
      });

      // Connect Ad Account Modal FIXME:
      var modalData = {
        text: 'Select the right ad account for the right campaigns',
        html: `<select size="2" id="ad-account-list" class="ad-account-list"></select>`,
        confirm: 'adAccountConfirm'
      }

      var adAccountModal = initiateModal('adAccountModal', 'confirm', modalData);

      $('.connect-ad-account').on('click', function() {
        // 1. SET CLIENT ID
        // 2. SET CURRENT AD ID
        // in client dashboard worden beide al geset in het eerste modal

        getAdAccounts();
        showModal(adAccountModal);
        $('#ad-account-list').focus();
      });

      $('#adAccountConfirm').click(function() {
        if (selectedOption = findSelected($('#ad-account-list'))) {
          // Change the button
          $('.connect-ad-account').text('Change')

          connectAccount(selectedOption.val(), $("#client_id").val());
        }
      });

      // Create for every client an on click event listener
      var clientList = <?php echo json_encode($jsClients); ?>;
      for (var i = 0; i < clientList.length; i++) {
        const {name, id, fb, ig, wb, ml, ad_id} = clientList[i];

        $(`.client_${id}-edit`).on('click', function() {

          $('#edit-client-modal').css({'display': 'block'});
          $('#client_name').val(`${name}`);
          $('#client_id').val(`${id}`);
          $('#mail_adress').val(`${ml}`);
          $('#fb_url').val(`${fb}`);
          $('#ig_url').val(`${ig}`);
          $('#wb_url').val(`${wb}`);
          $('#ad_id').val(`${ad_id}`);

          // Change the button
          $('.connect-ad-account').text((ad_id == null) ? 'Connect' : 'Change');
        });
      }

      // Search function
      $(document).on('keyup', 'input#search-input', function() {
        filterSearch($(this).val(), $("#client-results .audit-row"), $("#counterSpan"));
      });
    });
	</script>
</body>
</html>
