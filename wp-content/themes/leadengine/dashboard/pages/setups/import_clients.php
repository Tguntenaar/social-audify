<?php

/**
 * Template Name: import client
 */
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php
// Header
include(dirname(__FILE__) . "/../header/dashboard_header.php");
$clients = $client_control->get_all();
$jsclients = array();
foreach ($clients as $c) {
  $c = array($c->name, $c->facebook, $c->instagram, $c->website, $c->mail);
  array_push($jsclients, $c);
}
?>

<head>
  <meta charset="utf-8">
  <title>Contact Dashboard</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.1.2/papaparse.min.js" defer></script>
</head>

<body>
  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="padding-bottom: 50px;">
    <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12" style="top: auto !important; transform: none !important;">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 screen-height mobile-margin" style="height: 350px; text-align: center;">

        <h1 class="create-report-h1" style="width: 65%; margin: 0 auto; margin-bottom: 40px; margin-top: 100px;">Mass import all your contacts.</h1>
        <div class="file-upload" style="margin-bottom: 30px;">
          <div class="file-upload-button">
            <label class="create-audit-button client-button" style="border: 1px solid #487dd7; cursor: pointer;font-size: 16px; margin-bottom: 0px !important; margin-top: 0px !important;">
              <input type="file" name="File Upload" id="update-data-from-file" accept=".csv" />
              Choose csv file
            </label>
          </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  right float-right no-margin" style="margin-top: 150px;">
          <div class="inner no-scroll client-dashboard">
            <span class="title"><span class="title-background" style="width:220px; float:left;">Contacts to be added</span>
              <span class="count" id="counterSpan">0</span>
            </span>
            <input type="text" name="search" id="search-input" placeholder="Search..." />
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
    </div>
  </div>

  <button id="universal-update" class="advice-button floating-update"> Submit Clients </button>

  <script charset="utf-8">
    var resultLocation = $('#client-results');

    function createClientRow({
      name,
      facebook,
      instagram,
      website,
      email
    }, valid) {
      return `<a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row ${(valid ? "" : "invalid")}" name="${name}">
    <input type="text" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style" data-type="name" value="${name}">
    <input type="text" class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style" data-type="facebook" value="${facebook}">
    <input type="text" class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style" data-type="instagram" value="${instagram}">
    <input type="text" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" data-type="website" value="${website}">
    <input type="text" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" data-type="email" value="${email}"></a>`;
    }

    // Event handlers
    $("#update-data-from-file").change(function(e) {
      changeDataFromUpload(e, function(data) {
        resultLocation.html('');
        data.forEach(function(client, index) {
          var {
            name = '', facebook = '', instagram = '', website = '', email = ''
          } = client || {};

          client.facebook = grabPageId(parseClientInput('facebook', facebook));
          client.instagram = parseClientInput('instagram', instagram);

          var newRow = $.parseHTML(createClientRow(client, isValid(client)));
          $(newRow).data("client", client);
          resultLocation.append(newRow);
        });

        $('.audit-row-style').focusout(function() {
          if (/^(facebook|instagram|website)$/.test($(this).data("type"))) {
            changeClientInputFields(this);
          }

          // update data-client attribute after edit.
          var tempClient = $(this).parent().data("client");
          tempClient[$(this).data("type")] = $(this).val();

          if (isValid(tempClient)) {
            $(this).parent().removeClass('invalid');
          } else {
            $(this).parent().addClass('invalid');
          }

          $(this).parent().data("client", tempClient);
        });

        $("#counterSpan").text(resultLocation.find('a').length);
        toggleUpdate(true);
      });
    });


    // Use the HTML5 File API to read the CSV
    function changeDataFromUpload(evt, cb) {

      // Check that the browser supports the HTML5 File API
      if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
        console.error("The File APIs are not fully supported in this browser!");

      } else if ((file = evt.target.files[0]) !== "") {
        $("#filename").html(file.name);

        var reader = new FileReader();
        reader.onload = function(event) {
          var parsed = Papa.parse(event.target.result, {
            skipEmptyLines: true
          });
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
      var columns = {
        name: ['client', 'name', 'company'],
        facebook: ['fb', 'facebook'],
        instagram: ['ig', 'insta', 'instagram'],
        website: ['website', 'web', 'url', 'site'],
        email: ['email', 'mail', 'gmail', 'hotmail'],
      }

      var output = [];
      for (var i = 1; i < data.length; i++) {
        var obj = {
          name: '',
          facebook: '',
          instagram: '',
          website: '',
          email: '',
        };

        data[0].forEach(function(column, index) {
          var value = column.toLowerCase().replace(/[\s\/\\-]+/, "");
          var columnName = Object.keys(columns).find(key => columns[key].includes(value));
          obj[columnName] = (data[i][index] !== undefined) ? data[i][index].trim(): data[i][index];
        });
        output.push(obj);
      }
      return output;
    }


    // Submit parsed clients to functions.php
    $('#universal-update').on('click', function() {
      var retrievedClients = [];
      var invalidClients = [];

      resultLocation.find('.audit-row').each(function(index) {
        if (isValid($(this).data('client'))) {
          retrievedClients = [...retrievedClients, $(this).data('client')];
        } else {
          invalidClients = [...invalidClients, (index + 1)];
        }
      });

      if (invalidClients.length == 0) {
        importClients(retrievedClients);
      } else {
        var text = invalidClients.length == 1 ? ` number ${invalidClients[0]} seems` :
          `s number ${invalidClients.slice(0, -1).join(',')} and ${invalidClients.slice(-1)} seem`;
        var subtext = invalidClients.length == 1 ? 'this client' : 'these clients';

        showModal(initiateModal('confirmModal', 'confirm', {
          'text': `Client${text} to be invalid`,
          'subtext': `Please make sure you provide at least their name and email.</br>
        Would you like to skip ${subtext} and continue anyway?`,
          'confirm': 'continue_confirmed'
        }));

        $("#continue_confirmed").click(function() {
          importClients(retrievedClients);
        });
      }
    });

    function importClients(retrievedClients) {
      console.log(retrievedClients);

      if (!$.isEmptyObject(retrievedClients) && retrievedClients.length > 0) {
        toggleUpdate(false);
        showBounceBall(true, 'Give us a few seconds as we import your clients');
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {action: 'import_clients', clients: retrievedClients},
          success: function(response) {
            console.log(response);
            window.location.replace('https://<?php echo getenv('HTTP_HOST'); ?>/client-dashboard');
          },
          error: function (xhr, textStatus, errorThrown) {
            showBounceBall(false);
            var send_error = error_func(xhr, textStatus, errorThrown, retrievedClients);
            logError(send_error, 'setups/import_clients.php', 'submit');
          }
        });
      } else {
        showModal(initiateModal('errorModal', 'error', {
          'text': `No valid clients found`,
          'subtext': `Please make sure you provide at least their name and email.`,
        }));
      }
    }

    function exportClients() {
      var clientList = <?php echo json_encode($jsclients); ?>;
      let csvContent = "data:text/csv;charset=utf-8," +
        "Name,Facebook,Instagram,Website,Email\n" +
        clientList.map(e => e.join(",")).join("\n");

      var encodedUri = encodeURI(csvContent);
      window.open(encodedUri);
    }

    function exportClients() {
      var clientList = <?php echo json_encode($jsclients); ?>;
      let csvContent = "data:text/csv;charset=utf-8," +
        "Name,Facebook,Instagram,Website,Email\n" +
        clientList.map(e => e.join(",")).join("\n");

      var encodedUri = encodeURI(csvContent);
      // window.open(encodedUri);
      link = document.createElement('a');
      link.setAttribute('href', csvContent);
      link.setAttribute('download', "filename");
      link.click();
    }

    // Valid client check
    function isValid(client) {
      return (client.name != "") && (client.email != "") && (Object.keys(client).length == 5);
    }

    // Search function
    $(function() {
      $(document).on('keyup', 'input#search-input', function() {
        filterSearch($(this).val(), resultLocation.find(".audit-row"), $("#counterSpan"));
      });
    });
  </script>
</body>

</html>