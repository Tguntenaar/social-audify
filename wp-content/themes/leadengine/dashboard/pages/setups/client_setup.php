<?php
/**
 * Template Name: Create client
 */
?>

<?php
  if (isset($_GET['from'])) {
    $from = $_GET['from'];
    if ($from == 'audit') {
      $to = "/process-client/?redirect=audit-setup/";
    } elseif ($from == 'report') {
      $to = "/process-client/?redirect=report-setup/";
    }
  } else {
    $from = 'client';
    $to = '/process-client/?redirect=client-dashboard';
  }
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <title>Create Contact</title>
</head>
  <?php
    include(dirname(__FILE__)."/../header/dashboard_header.php");
  ?>

  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="padding-bottom: 50px;">
  <div class="content-title col-lg-12"> </div>
  <div class="col-lg-12 client_register_box">
    <div class="col-lg-6 client_left client_none">
      <div class="col-lg-12">
        <div class="content-title col-lg-12">
          <span class="title">How to create a contact?</span>
          <ul>
            <li><span class="number">1.</span> Fill in the name of the contact in the form.</li>
            <li><span class="number">2.</span> Search for the URL's.</li>
            <li><span class="number">3.</span> Copy the URL's into the form.</li>
            <li><span class="number">4.</span> Click the submit button.</li>
            <li><span class="number">5.</span> Done. You created a contact.</li>
            <li><span class="number">Or</span> Import clients with a csv file <a href="/client-import/">here.</a></li>

          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-6 client_right client-form">
      <div class="col-lg-12">
        <div class="content-title col-lg-9">
          <span class="title">Fill in the form.</span>
        </div>
        <form class="col-lg-10 create_client_form" method="post" action="<?php echo $to; ?>">
          Name:<br>
          <input maxlength="25" type="text" id="client_name" name="client_name" placeholder="Name" pattern="<?php echo $Regex->name ?>" title="Only letters are allowed" required>

          Facebook Page:<br>
          <input type="text" id="facebook_url" data-type="facebook" name="facebook_url" placeholder="Facebook page url, page id or page username" required>
          <div class="toggleClient" id="toggle_facebook" onclick="toggle_client_options('facebook')"><i class="fas fa-minus-circle"></i></div>

          Instagram:<br>
          <input type="text" id="instagram_url" data-type="instagram" name="instagram_url" placeholder="Instagram username or url" maxlength="50" required>
          <div class="toggleClient" id="toggle_instagram" onclick="toggle_client_options('instagram')"><i class="fas fa-minus-circle"></i></div>

          Website:<br>
          <input type="text" id="website_url" data-type="website" name="website_url" placeholder="http://www.example.com" required>
          <div class="toggleClient" id="toggle_website" onclick="toggle_client_options('website')"><i class="fas fa-minus-circle"></i></div>

          E-mail:<br>
          <input type="email" id="client_mail" name="client_mail" title="" placeholder="mail@example.com" required>

          <button type="submit">Submit</button>
        </form>
      </div>
    </div>
  </div>
  </div>
</body>

<script>
  function toggle_client_options(option) {
    var inpt = $(`#${option}_url`);
    var icon = $(`#toggle_${option}`);

    if (inpt.prop('required')) {
      icon.html("<i class='fas fa-plus'></i>");
      inpt.prop('required', false);

      icon.attr('style', 'background: #6e9d9c !important;');
      inpt.css('opacity', '0.4');
    } else {
      icon.html("<i class='fas fa-minus-circle'></i>");
      inpt.prop('required', true);

      icon.attr('style', 'background: #c0392b !important;');
      inpt.css('opacity', '1');
    }
  }

  function toggleClientOption(type) {
    $(`#${type}_checkbox`).click(function() {
      $(`#${type}_url`).prop(`disabled`, function(i, v) { return !v; });
      $(`#${type}_url`).prop(`value`, ``);
      $(`#${type}_url`).fadeToggle();
    });
  }

  $(function() {
    toggleClientOption('instagram');
    toggleClientOption('facebook');
    toggleClientOption('website');

    $('#facebook_url, #instagram_url, #website_url').focusout(function() {
      changeClientInputFields(this);
    });
  });
</script>
</html>
