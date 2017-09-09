// Pass version # from manifest to injected script
var extension_version = document.createElement("input");
extension_version.setAttribute("id", "mhhh_version");
extension_version.setAttribute("type", "hidden");
extension_version.setAttribute("value", chrome.runtime.getManifest().version);
document.body.appendChild(extension_version);

// Add flash message div
var mhhh_flash_message_div = document.createElement("div");
mhhh_flash_message_div.setAttribute("id", "mhhh_flash_message_div");
mhhh_flash_message_div.setAttribute(
    "style",
    "display:none;" +
    "z-index:100;" +
    "position:absolute;" +
    "top:20%;" +
    "background-color: white;" +
    "padding: 10px;" +
    "border-radius: 5px;" +
    "box-shadow: 0 0 10px 1px black;");
document.body.appendChild(mhhh_flash_message_div);

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

window.addEventListener("message",
    function(event) {
        if (event.data.jacks_settings_request !== 1) {
            return;
        }

        if (event.data.get_options === "messages") {
            chrome.storage.sync.get({
                success_messages: true, // defaults
                error_messages: true // defaults
            }, function (items) {
                event.source.postMessage(
                    {
                        jacks_settings_response: 1,
                        get_options: "messages",
                        settings: items
                    },
                    event.origin);
            });
        }
    },
    false
);
