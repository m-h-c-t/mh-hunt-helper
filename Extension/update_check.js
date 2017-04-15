chrome.runtime.onUpdateAvailable.addListener(function(details) {
  console.log("MHHH: updating to version " + details.version);
  chrome.runtime.reload();
});

var time_interval = 3600 * 1000; // In seconds
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
