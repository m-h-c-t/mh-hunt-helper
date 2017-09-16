function findOpenMHTab(button_pressed, callback, silent) {
    chrome.tabs.query({'url': ['*://www.mousehuntgame.com/*', '*://apps.facebook.com/mousehunt/*']}, function(tabs) {
        if ( tabs.length > 0 ) {
            callback(tabs[0].id, button_pressed);
        }
        else if (!silent) {
            displayErrorPopup("Please navigate to MouseHunt page first.");
        }
    });
}

function sendMessageToScript(tab, button_pressed) {
    if (button_pressed === "horn") {
        chrome.tabs.update(tab, {'active': true});
        chrome.tabs.sendMessage(tab, {jacks_link: button_pressed}, function (response) {});
    } else {
        chrome.tabs.sendMessage(tab, {jacks_link: button_pressed}, function (response) {});
    }
}

document.addEventListener('DOMContentLoaded', function() {
    findOpenMHTab("huntTimer", updateHuntTimer, true);

    var buttons = ['mhmh', 'tsitu', 'userhistory', 'ryonn', 'horn'];
    buttons.forEach(function(id) {
        var button_element = document.getElementById(id);
        if (!button_element) {
            return;
        }
        button_element.addEventListener('click', function() {
            findOpenMHTab(id, sendMessageToScript);
        });
    });
});

function updateHuntTimerField(tab, huntTimerField) {
    chrome.tabs.sendMessage(tab, {jacks_link: "huntTimer"}, function (response) {

        if (response === "Ready!") {
            huntTimerField.innerHTML = '<img src="images/horn.png" class="horn">';
        } else {
            huntTimerField.textContent = response;
        }
    });
}

function updateHuntTimer(tab, button_pressed) {
    var huntTimerField = document.getElementById("huntTimer");
    updateHuntTimerField(tab, huntTimerField); // Fire now
    setInterval(updateHuntTimerField, 1000, tab, huntTimerField); // Continue firing each second
}

function displayErrorPopup(message) {
    var error_popup = document.getElementById('error_popup');
    error_popup.innerText = message;
    error_popup.style.display = 'block';
    setTimeout( function(){
        error_popup.style.display = 'none';
    }, 2000);
}

document.getElementById('options').addEventListener('click', function() {
    if (chrome.runtime.openOptionsPage) {
        // New way to open options pages, if supported (Chrome 42+).
        chrome.runtime.openOptionsPage();
    } else {
        // Reasonable fallback.
        window.open(chrome.runtime.getURL('options.html'));
    }
});
