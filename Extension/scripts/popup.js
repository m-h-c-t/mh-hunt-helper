function openPopupLink(website) {
    chrome.tabs.query({'url': ['*://www.mousehuntgame.com/*', '*://apps.facebook.com/mousehunt/*']}, function(tabs) {
        if ( tabs.length > 0 ) {
            chrome.tabs.update(tabs[0].id, {'active': true});
            chrome.tabs.sendMessage(tabs[0].id, {link: website}, function (response) {});
        }
        else {
            displayErrorPopup("Please navigate to MouseHunt page first.");
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var buttons = ['mhmh', 'tsitu', 'userhistory', 'ryonn'];
    buttons.forEach(function(id) {
        var button_element = document.getElementById(id);
        if (!button_element) {
            return;
        }
        button_element.addEventListener('click', function() {
            openPopupLink(id);
        });
    });
});

function displayErrorPopup(message) {
    var error_popup = document.getElementById('error_popup');
    error_popup.innerText = message;
    error_popup.style.display = 'block';
    setTimeout( function(){
        error_popup.style.display = 'none';
    }, 2000);
}
