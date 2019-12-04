function initiateModal(id, type, data) {
  var element = document.getElementById(id);

  var text = typeof data.text == 'undefined' ? '' : data.text;
  var html = typeof data.html == 'undefined' ? '' : data.html;
  var subtext = typeof data.subtext == 'undefined' ? '' : data.subtext;
  var confirmName = typeof data.confirm == 'undefined' ? 'modalConfirm' : data.confirm;
  var cancelName = typeof data.cancel == 'undefined' ? 'modalCancel' : data.cancel;
  var confirmText = typeof data.confirmtext == 'undefined' ? 'Confirm' : data.confirmtext;
  var link = typeof data.link == 'undefined' ? '' : data.link;

  var zIndex = type == "error" ? '1250' : type == "confirm" ? '1150' : '1050';

  var buttons = '';
  switch (type) {
    case "confirm":
      buttons = `<button class='agree' id='${confirmName}'>${confirmText}</button>\
        <button class='deny' id='${cancelName}'>Cancel</button>`;
      break;
    case "link":
      buttons = `<a href="${link}"><button class='agree' id="${confirmName}">${confirmText}</button></a>`;
      break;
    case "select":
      buttons = `<a class="agree" style="display:none"></a>`;
      break;
    default:
      buttons = `<button class='agree' id='${confirmName}'>Understood</button>`;
      break;
  }
  
  element.setAttribute('style', 'z-index: ' + zIndex);
  element.innerHTML =
    `<div class="modal-content ${type}">\
       <span class="close">&times;</span>\

        <p>${text}</p>\

        ${html}\
        <p class="subtext">${subtext}</p>\

        ${buttons}\
     </div>`;

  var close = element.getElementsByClassName('close')[0];
  close.onclick = function() { removeModal(element) };

  var confirm = element.getElementsByClassName('agree')[0];
  confirm.onclick = function(e) { removeModal(element) };

  if (type == "confirm") {
    var closeButton = element.getElementsByClassName('deny')[0];
    closeButton.onclick = function() { removeModal(element) };
  }

  window.addEventListener('click', function(e) {
    if (element.style.display == "block" && e.target == element) {
      removeModal(element);
    }
  });

  window.addEventListener('keyup', function(e) {
    if (element.style.display == "block") {
      var key = e.keyCode ? e.keyCode : e.which;
      if (key == 13) { confirm.click(); }
        else if (key == 27) { close.click(); }
    }
  });

  return element;
}

function removeModal(element) {
  element.style.display = "none";
}

function showModal(element) {
  element.style.display = "block";
}
