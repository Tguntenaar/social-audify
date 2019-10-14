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
        <label class="button" id="fileLabel" for="update-data-from-file">
          Upload CSV
        </label>
      </div>
      <div class="file-upload-name" id="filename">No file chosen</div>
    </div>

    Or, paste your data from a spreadsheet:
    <textarea id="enter-data-field"></textarea>
    <button id="update-data-from-field">
      Update data
    </button>
    <!-- clients -->
    <li id="new-clients">

    </li>
    <button id="universal-update" class="advice-button floating-update"> Submit Clients </button>
</body>
    <script>
      var clients = [];
      // Event handlers
      $("#update-data-from-file").change(function(e){
        changeDataFromUpload(e, function(data){
          console.log(data);
          clients = data;
          data.forEach(function(client) {
            var {Name, Facebook, Instagram, Website, Email} = client;
            // TODO: parse clients
            $('#new-clients').append(`<ul>${Name}/${Facebook}/${Instagram}/${Website}/${Email}</ul>`);
          });
          toggleUpdate(true);
        });
      });

      $("#update-data-from-field").click(function(){
        changeDataFromField(function(data){
          console.log(data);
        });
      });

      // Submit parsed clients to functions.php
      $('#universal-update').on('click', function() {
        var data = {clients: clients};
        
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
      
      // Parse pasted CSV
      function changeDataFromField(cb){
        var arr = [];
        $('#enter-data-field').val().replace( /\n/g, "^^^xyz" ).split( "^^^xyz" ).forEach(function(d){
          arr.push(d.replace( /\t/g, "^^^xyz" ).split( "^^^xyz" ))
        });
        cb(csvToJson(arr));
      }

      // Use the HTML5 File API to read the CSV
      function changeDataFromUpload(evt, cb){
        if (!browserSupportFileUpload()) {
          console.error("The File APIs are not fully supported in this browser!");
        } else {
          var data = null;
          var file = evt.target.files[0];
          var fileName = file.name;
          $("#filename").html(fileName);

          if (file !== "") {
            var reader = new FileReader();

            reader.onload = function(event) {
              var csvData = event.target.result;
              var parsed = Papa.parse(csvData);
              cb(csvToJson(parsed.data));
            };
            reader.onerror = function() {
              console.error("Unable to read " + file.fileName);
            };
          }

          reader.readAsText(file);
          $("#update-data-from-file")[0].value = "";
        }
      }

      // Method that checks that the browser supports the HTML5 File API
      function browserSupportFileUpload() {
        var isCompatible = false;
        if (window.File && window.FileReader && window.FileList && window.Blob) {
          isCompatible = true;
        }
        return isCompatible;
      }

      // Parse the CSV input into JSON
      function csvToJson(data) {
        var cols = data[0];
        var out = [];
        for (var i = 1; i < data.length; i++){
          var obj = {};
          var row = data[i];
          cols.forEach(function(col, index){
            obj[col] = row[index];
          });
          out.push(obj);
        }
        return out;
      }
    </script>
</html>
