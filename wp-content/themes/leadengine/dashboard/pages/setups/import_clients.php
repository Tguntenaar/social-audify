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
    <div class="file-upload">
      <div class="file-upload-button">
        <input type="file" name="File Upload" id="update-data-from-file" accept=".csv" />
        <label class="button" id="fileLabel" for="update-data-from-file">Upload CSV</label>
      </div>
      <div class="file-upload-name" id="filename">No file chosen</div>
    </div>

    Or, paste your data from a spreadsheet:
    <textarea id="enter-data-field"></textarea>
    <button id="update-data-from-field">Update data</button>

    <!-- clients -->
    <ul id="new-clients"> </ul>
    <button id="universal-update" class="advice-button floating-update"> Submit Clients </button>
  </div>

  <script>
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

        $('#new-clients').html("");
        data.forEach(function(client) {
          var { Name, Facebook, Instagram, Website, Email } = client;

          var parsedfb = parseClientInput('facebook', Facebook);
          parsedfb = grabPageId(parsedfb);

          var parsedig = parseClientInput('instagram', Instagram);

          $('#new-clients').append(`<li>${Name}/${parsedfb}/${parsedig}/${Website}/${Email}</li>`);
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
      for (var i = 1; i < data.length; i++) {
        var obj = {};
        data[0].forEach(function(col, index) {
          obj[col] = data[i][index];
        });
        output.push(obj);
      }
      return output;
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
