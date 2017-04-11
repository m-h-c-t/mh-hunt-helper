var s = document.createElement('script');

s.src = chrome.extension.getURL('main.js');
s.onload = function() {
    this.remove();
};
(document.head || document.documentElement).appendChild(s);
