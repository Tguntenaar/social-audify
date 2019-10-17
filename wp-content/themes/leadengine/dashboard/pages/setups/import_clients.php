<?php
/**
 * Template Name: import client
 */
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php
  // Header
  include(dirname(__FILE__)."/../header/dashboard_header.php");
?>

<head>
  <meta charset="utf-8">
  <title>Contact Dashboard</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.1.2/papaparse.min.js" defer></script>
</head>
<body>
  <!-- modal? -->
  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="padding-bottom: 50px;">
  <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 screen-height mobile-margin" style="height: 350px; text-align: center;">
      <div class="center-center">
        <h1 class="create-report-h1" style="width: 65%; margin: 0 auto; margin-top: 20px;">Mass import all your contacts</h1>
        <div class="file-upload">
          <div class="file-upload-button">
            <label class="create-audit-button client-button">
              <input type="file" name="File Upload" id="update-data-from-file" accept=".csv" />
              Choose csv file
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  right float-right no-margin" style="margin-top: 150px;">
      <div class="inner no-scroll client-dashboard">
        <span class="title"><span class="title-background" style="width:220px;">Contacts to be added</span>
          <span class="count" id="counterSpan">0</span>
        </span>
        <input type="text" name="search" id="search-input" placeholder="Search..."/>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title">
          <div class="col-12 col-sm-2 col-md-2 col-lg-2 row-title-style" style="padding:0;">Name</div>
          <div class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 row-title-style" style="padding:0;">Facebook</div>
          <div class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 row-title-style" style="padding:0;">Instagram</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Website</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Email</div>
        </div>
        <div class="inner-scroll client-dashboard" id="client-results"></div>
      </div>
    </div>
  </div>
  <button id="universal-update" class="advice-button floating-update"> Submit Clients </button>

	<script charset="utf-8">
    var resultLocation = $('#client-results');

    
    function createClientRow({name, facebook, instagram, website, email}) {
      var nameValid = (name == '') ? 'invalid' : 'valid';
      var mailValid = (email == '') ? 'invalid' : 'valid';
      return `<a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="${name}">
        <input type="text" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style" data-type="name" value="${name}" ${nameValid}>
        <input type="text" class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style" data-type="facebook" value="${facebook}">
        <input type="text" class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style" data-type="instagram" value="${instagram}">
        <input type="text" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" data-type="website" value="${website}">
        <input type="text" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" data-type="email" value="${email}" ${mailValid}></a>`;
    }

    // Event handlers
    $("#update-data-from-file").change(function(e) {
      changeDataFromUpload(e, function(data) {
        resultLocation.html('');
        var invalidClients = [];

        data.forEach(function(client, index) {
          var { name= '', facebook = '', instagram = '', website = '', email = ''} = client || {};
          console.log({client});

          client.facebook = grabPageId(parseClientInput('facebook', facebook));
          client.instagram = parseClientInput('instagram', instagram);

          if (!isValid(client)) {
            invalidClients = [...invalidClients, index + 1];
          }
          var newRow = $.parseHTML(createClientRow(client));
          $(newRow).data("client", client);
          resultLocation.append(newRow);

        });

        if (invalidClients.length > 1) {
          showModal(initiateModal('errorModal', 'error', {
            'text': `Clients number ${invalidClients.slice(0, -1).join(',')+' and '+invalidClients.slice(-1)} seem to be invalid`,
            'subtext': `Please provide at least their name and email.`,
          }));
        } else if (invalidClients.length == 1) {
          showModal(initiateModal('errorModal', 'error', {
            'text': `Client number ${invalidClients[0]} seems to be invalid`,
            'subtext': `Please provide a name and email.`,
          }));
        }


        $('.audit-row-style').focusout(function() {
          if (/^(facebook|instagram|website)$/.test($(this).data("type"))) {
            changeClientInputFields(this);
          }
          // update data-client attribute na het editen.
          var temp = $(this).parent().data("client");
          temp[$(this).data("type")] = $(this).val();
          $(this).parent().data("client", temp);
        });

        $("#counterSpan").text(resultLocation.find('a').length);
        toggleUpdate(true);
      });
    });

    function isValid(client) {
      return (client.name != "") && (client.email != "") && (Object.keys(client).length == 5);
    }

    // Use the HTML5 File API to read the CSV
    function changeDataFromUpload(evt, cb) {

      // Check that the browser supports the HTML5 File API
      if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
        console.error("The File APIs are not fully supported in this browser!");

      } else if ((file = evt.target.files[0]) !== "") {
        var reader = new FileReader();
        $("#filename").html(file.name);

        reader.onload = function(event) {
          var parsed = Papa.parse(event.target.result);
          cb(csvToJson(parsed.data));
        };
        reader.onerror = function() {
          console.error("Unable to read " + file.name);
        };
      }

      reader.readAsText(file);
      $("#update-data-from-file")[0].value = "";
    }

    // Parse the CSV input into JSON
    function csvToJson(data) {
      var output = [];
      // check if 5 colums;
      for (var i = 1; i < data.length; i++) {
        var obj = { name:'', facebook:'', instagram:'', website:'', email:'', };
        data[0].forEach(function(col, index) {
          var newcol = translateCsvTitles(col);
          if (newcol == 'undefined') {
            // TODO: there is an invalid column in your csv file.
          }
          obj[newcol] = data[i][index];
        });
        output.push(obj);
      }
      return output;
    }

    function translateCsvTitles(title) {
      // remove "/", "\", "-" and whitespace
      var value = title.toLowerCase().replace(/[\s\/\\-]+/, "");
      var obj = {
        name: ['client', 'name'],
        facebook: ['fb', 'facebook'],
        instagram: ['ig','insta', 'instagram'],
        website: ['website', 'web', 'url', 'site'],
        email: ['email', 'mail', 'gmail', 'hotmail'],
      }
      return Object.keys(obj).find(key => obj[key].includes(value));
    }

    // Submit parsed clients to functions.php
    $('#universal-update').on('click', function() {
      var retrievedClients = [];

      resultLocation.find('.audit-row').each(function() {
        retrievedClients.push($(this).data('client'));
      });

      console.log(retrievedClients);
      if (!$.isEmptyObject(retrievedClients)) {
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {action: 'import_clients', clients: retrievedClients},
          success: function(response) {
            toggleUpdate(false);
            console.log(response);
            // TODO redirect!
          },
          error: function (xhr, textStatus, errorThrown) {
            var send_error = error_func(xhr, textStatus, errorThrown, data);
            logError(send_error, 'setups/import_clients.php', 'submit');
          }
        });
      } else {
        toggleUpdate(false);
      }
    });

    $(function() {
      // Search function
      $(document).on('keyup', 'input#search-input', function() {
        filterSearch($(this).val(), resultLocation.find(".audit-row"), $("#counterSpan"));
      });
    });
  </script>
</body>
</html>
