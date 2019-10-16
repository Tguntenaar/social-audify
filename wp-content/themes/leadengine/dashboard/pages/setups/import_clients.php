<?php
/**
 * Template Name: import client
 */
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <title>Import Clients</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.1.2/papaparse.min.js" defer></script>
</head>
  <?php
    include(dirname(__FILE__)."/../header/dashboard_header.php");
  ?>

  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="padding-bottom: 50px;">
    <span class="back-icon"><i class="fas fa-chevron-left"></i> Back</span>
    <h1 class="create-report-h1" style="width: 80%; text-align:center; margin: 0 auto; margin-bottom: 40px; margin-top: 20px;">Mass import all your contacts.</h1>
    <!-- <span style="width: 100%; text-align: center; font-size: 17px; margin-top: -25px;" class="option-text">Create many contacts in a few steps.</span> -->
    <div class="audit-option-center">
      <div class="upload-container">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 audit-option border-rightt audit-option-left">
            <div class="vertical-align">
              <span class="option-title">Upload your contacts</span>
              <span class="option-text">Upload a csv or ... file with your contacts in it.</span>
            </div>
          </div>
          <div class="ol-xs-12 col-sm-6 col-md-6 col-lg-6 audit-option audit-option-right">
            <div class="vertical-align">
              <span class="option-title">Paste your contacts</span>
              <span class="option-text">Paste your contacts in a text field...........</span>
            </div>
        </div>
        </div>
    </div>
    <div class="file-upload-block">
        <div class="file-upload">
          <div class="file-upload-button">
              <label class="button" id="fileLabel" for="update-data-from-file">Upload CSV, click the button below.</label>

              <label class="custom-file-upload">
                 <input type="file" name="File Upload" id="update-data-from-file" accept=".csv" />
                 Custom Upload
             </label>
            <!-- <div class="file-upload-name" id="filename">No file chosen</div> -->

            </div>
        </div>
    </div>
    <div class="file-paste-block">
         Paste your data from a spreadsheet:
        <textarea id="enter-data-field" placeholder="Name...   Facebook...   Instagram...   Website...   Email...&#10;Name...   Facebook...   Instagram...   Website...   Email... &#10;Name...   Facebook...   Instagram...   Website...   Email...&#10;Name...   Facebook...   Instagram...   Website...   Email..."></textarea>
        <button id="update-data-from-field">Confirm data<i class="fas fa-download"></i></button>
    </div>
    <!-- clients -->
    <span style="font-size: 16px; margin-bottom: 3px;" id="title-import">Imported clients</span>
    <ul id="new-clients">
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
        <li>Name / Facebook / Instagram / Website / Email</li>
    </ul>
  </div>
  <button id="universal-update" class="advice-button floating-update"> Submit Clients </button>
  <script>
    $( document ).ready(function() {
        $( ".audit-option-left" ).click(function() {
            $('.file-upload-block').css("display", "block");
            $('.audit-option-center').css("display", "none");
            $('.back-icon').css("display", "block");
            $('#new-clients').css("display", "block");
            $('#title-import').css("display", "block");
        });

        $( ".audit-option-right" ).click(function() {
            $('.file-paste-block').css("display", "block");
            $('.audit-option-center').css("display", "none");
            $('.back-icon').css("display", "block");
            $('#new-clients').css("display", "block");
            $('#title-import').css("display", "block");
        });

        $( ".back-icon" ).click(function() {
            $('.file-upload-block').css("display", "none");
            $('.file-paste-block').css("display", "none");
            $('.audit-option-center').css("display", "block");
            $('.back-icon').css("display", "none");
            $('#new-clients').css("display", "none");
            $('#title-import').css("display", "none");
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

        $('#new-clients').html("<li>Name / Facebook / Instagram / Website / Email</li>");
        data.forEach(function(client) {
          var { name, facebook = '', instagram = '', website = '', email } = client || {};
          console.log({client});

          var parsedfb = parseClientInput('facebook', facebook);
          parsedfb = grabPageId(parsedfb);

          var parsedig = parseClientInput('instagram', instagram);

          $('#new-clients').append(`<li>${name} / ${parsedfb} / ${parsedig} / ${website} / ${email}</li>`);
        });

        console.log(uploadedClients);
        toggleUpdate(true);
      });
    });

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
