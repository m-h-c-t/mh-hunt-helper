// Pass version # from manifest to injected script
var extension_version = document.createElement("input");
extension_version.setAttribute("id", "mhhh_version");
extension_version.setAttribute("type", "hidden");
extension_version.setAttribute("value", chrome.runtime.getManifest().version);
(document.head || document.documentElement).appendChild(extension_version);

// Inject main script
var s = document.createElement('script');
s.src = chrome.extension.getURL('scripts/main.js');
s.onload = function() {
    this.remove();
};
(document.head || document.documentElement).appendChild(s);


// Forwards messages from popup to main script
chrome.runtime.onMessage.addListener( function(request, sender, sendResponse) {
    if (request.link === "userhistory" ||
        request.link === "mhmh" ||
        request.link === "tsitu" ||
        request.link === "ryonn") {
        window.postMessage({ jacksmessage: request.link }, "*");
    }
});
