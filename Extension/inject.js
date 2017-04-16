var extension_version = document.createElement("input");
extension_version.setAttribute("id", "mhhh_version");
extension_version.setAttribute("type", "hidden");
extension_version.setAttribute("value", chrome.runtime.getManifest().version);
(document.head || document.documentElement).appendChild(extension_version);

var s = document.createElement('script');
s.src = chrome.extension.getURL('main.js');
s.onload = function() {
    this.remove();
};
(document.head || document.documentElement).appendChild(s);
