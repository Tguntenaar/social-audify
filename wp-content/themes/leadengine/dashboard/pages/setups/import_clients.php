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
  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="padding-bottom: 50px;">
  <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 screen-height mobile-margin" style="height: 350px; text-align: center;">
      <div class="center-center">
        <h1 class="create-report-h1" style="width: 65%; margin: 0 auto; margin-top: 20px;">Mass import all your contacts</h1>
        <div class="file-upload">
          <div class="file-upload-button">
            <label class="create-audit-button client-button">
              <input type="file" name="File Upload" id="update-data-from-file" accept=".csv" />
              Chose csv file
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
        <div class="inner-scroll client-dashboard" id="client-results">

            <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="CLIENT_NAME">
              <input type="text" style="overflow:hidden" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style" value="CLIENT_NAME">
              <input type="text" style="overflow:hidden" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style" value="CLIENT_FB">
              <input type="text" style="overflow:hidden" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style" value="CLIENT_IG">
              <input type="text" style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" value="CLIENT_WB">
              <input type="text" style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" value="CLIENT_EMAIL">
            </a>

        </div>
      </div>
    </div>
  </div>
  <button id="universal-update" class="advice-button floating-update"> Submit Clients </button>

	<script charset="utf-8">
    function createClientRow({name, facebook, instagram, website, email}) {
      return `<a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="${name}">
        <input type="text" style="" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style" value="${name}">
        <input type="text" style="overflow: hidden; text-overflow:ellipsis;" class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style" value="${facebook}">
        <input type="text" style="overflow: hidden; text-overflow:ellipsis;" class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style" value="${instagram}">
        <input type="text" style="overflow: hidden; text-overflow:ellipsis;" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" value="${website}">
        <input type="text" style="overflow: hidden; text-overflow:ellipsis;" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style" value="${email}"></a>`;
    }

    $(function() {
      // Search function
      $(document).on('keyup', 'input#search-input', function() {
        filterSearch($(this).val(), $("#client-results .audit-row"), $("#counterSpan"));
      });

      $('#facebook_url, #instagram_url, #website_url').focusout(function() {
        changeClientInputFields(this);
      });
    });

    var uploadedClients = [];

    $("#update-data-from-field").click(function() {
      changeDataFromField(function(data) {
        console.log(data);
      });
    });

    // Parse pasted CSV
    function changeDataFromField(cb) {
      var arr = [];
      $('#enter-data-field').val().replace( /\n/g, "^^^xyz" ).split( "^^^xyz" ).forEach(function(d) {
        arr.push(d.replace( /\t/g, "^^^xyz" ).split( "^^^xyz" ))
      });
      cb(csvToJson(arr));
    }

    // Event handlers
    $("#update-data-from-file").change(function(e) {
      changeDataFromUpload(e, function(data) {
        uploadedClients = data;

        $('#client-results').html('');
        data.forEach(function(client) {
          var { name, facebook = '', instagram = '', website = '', email } = client || {};
          console.log({client});

          facebook = grabPageId(parseClientInput('facebook', facebook));
          instagram = parseClientInput('instagram', instagram);
          // TODO: goedkeuren
          if (isValid(client)) {
            $('#client-results').append(createClientRow({name, facebook, instagram, website, email}));
          }
        });

        $("#counterSpan").text($('#client-results a').length);

        console.log(uploadedClients);
        toggleUpdate(true);
      });
    });

    function isValid(client) {
      return (client.name != "") && (client.email != "")
    }

    // Use the HTML5 File API to read the CSV
    function changeDataFromUpload(evt, cb) {
      if (!browserSupportFileUpload()) {
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

    // Method that checks that the browser supports the HTML5 File API
    function browserSupportFileUpload() {
      return (window.File && window.FileReader && window.FileList && window.Blob);
    }

    // Parse the CSV input into JSON
    function csvToJson(data) {
      var output = [];
      // check if 5 colums;
      for (var i = 1; i < data.length; i++) {
        var obj = { name:'', facebook:'', instagram:'', website:'', email:'', };
        data[0].forEach(function(col, index) {
          var newcol = translateCsvTitles(col);
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
      return getKeyByValue(obj, value);
    }

    function getKeyByValue(object, value) {
      return Object.keys(object).find(key => object[key].includes(value));
    }

    // Submit parsed clients to functions.php
    $('#universal-update').on('click', function() {
      var data = {clients: uploadedClients};

      console.log(data);
      if (!$.isEmptyObject(data)) {
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {action: 'import_clients', ...data},
          success: function(response) {
            toggleUpdate(false);
            console.log(response);
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
  </script>
</body>
</html>
