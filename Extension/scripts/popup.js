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
    document.getElementById('mhmh').addEventListener('click', function() {
        openPopupLink('mhmh');
    });

    document.getElementById('tsitu').addEventListener('click', function() {
        openPopupLink('tsitu');
    });
});
