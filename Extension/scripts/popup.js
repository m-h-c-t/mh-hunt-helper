function openPopupLink(website) {
    chrome.tabs.query({active: true, currentWindow: true}, function (tabs) {
        var needle = new RegExp('mousehuntgame\.com|apps\.facebook\.com\/mousehunt', 'i');
        if (tabs[0].url.search(needle) == -1) {
            alert("Please navigate to MouseHunt page first.");
            return;
        }
        chrome.tabs.sendMessage(tabs[0].id, {link: website}, function (response) {});
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
