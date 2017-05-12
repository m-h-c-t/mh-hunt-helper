// Update check
chrome.runtime.onUpdateAvailable.addListener(function(details) {
    console.log("MHHH: updating to version " + details.version);
    chrome.runtime.reload();
});

var time_interval = 7200 * 1000; // In seconds
window.setInterval(function() {
    chrome.runtime.requestUpdateCheck(function(status) {
        if (status == "update_available") {
            console.log("MHHH: update pending...");
        } else if (status == "no_update") {
            console.log("MHHH: no update found");
        } else if (status == "throttled") {
            console.log("MHHH: Oops, update check failed.");
        }
    });
}, time_interval);

// Send out a message when map overlay has been opened
chrome.webRequest.onCompleted.addListener(
    function(details){
        if (details.url.search('relichunter.php') !== -1) {
            chrome.tabs.query({active: true, currentWindow: true}, function (tabs) {
                chrome.tabs.sendMessage(tabs[0].id, {mapOpened: true}, function (response) {});
            });
        }
    },
    { urls: ["*://www.mousehuntgame.com/*"] },
    ["responseHeaders"]
);
