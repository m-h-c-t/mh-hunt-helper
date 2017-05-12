function openPopupLink(website) {
    chrome.tabs.query({'url': ['*://www.mousehuntgame.com/*', '*://apps.facebook.com/mousehunt/*']}, function(tabs) {
        if ( tabs.length > 0 ) {
            chrome.tabs.update(tabs[0].id, {'active': true});
            chrome.tabs.sendMessage(tabs[0].id, {link: website}, function (response) {});
        }
        else {
            alert("Please navigate to MouseHunt page first.");
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var buttons = ['mhmh', 'tsitu', 'userhistory'];
    buttons.forEach(function(id) {
        document.getElementById(id).addEventListener('click', function() {
            openPopupLink(id);
        });
    });
});
