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


// Find map mice
// If user click on popup link, this will receive a message
// Gets mice and opens appropriate new tab prepopulated
var userClickedLink = "";
var openedMapAlready = false;
chrome.runtime.onMessage.addListener( function(request, sender, sendResponse) {
    var mice = "";
    if (request.link === "mhmh" || request.link === "tsitu") {
        openedMapAlready = false;
        userClickedLink = request.link;
        console.log('inject ran');
        if (null !== document.querySelector("div.treasureMapPopupContainer.hasMap")) {
            mice = getMapMice();
            openMapToolWindow(request.link, mice);
            userClickedLink = "";
            return;
        }

        var map_button = document.getElementsByClassName("mousehuntHud-userStat treasureMap");
        if (!map_button.length) {
            alert('Please navigate to mousehunt page and make sure you are on a map.');
            return;
        }
        map_button["0"].children["0"].click();
        setTimeout(function() {
            if (null !== document.querySelector("div.treasureMapPopupContainer.hasMap") && !openedMapAlready) {
                openedMapAlready = true;
                mice = getMapMice();
                openMapToolWindow(request.link, mice);
            }
        }, 600);
    } else if (request.mapOpened && userClickedLink.length > 0 && !openedMapAlready) {
        openedMapAlready = true;
        mice = getMapMice();
        openMapToolWindow(userClickedLink, mice);
        userClickedLink = "";
    }
});

// Parses open map for mice
// Thanks to Tran for coming up with this awesome parser
// License: https://choosealicense.com/licenses/apache-2.0/
// Modified to return mice and open different pages
function getMapMice() {
    var mice = [];
    var currLoc = document.getElementsByClassName("treasureMapPopup-mice-groups")[0].className;
    if (currLoc.indexOf("inotherenvironments") < 0) {
        //Locations with periods and apostrophes
        //Extra escapes are workaround for copy link address/JS string handling
        currLoc = currLoc.replace(/\\./g, "\\\\.");
        currLoc = currLoc.replace(/\\'/g, "\\\\'");
        currLoc = currLoc.replace(" ", ".");
        var uncaughtLoc = document.querySelectorAll("." + currLoc + " .treasureMapPopup-mice-group-mouse-name span");
        for (var i=0; i<uncaughtLoc.length; i++) {
            mice.push(uncaughtLoc[i].textContent);
        }
    }

    var uncaughtOther = document.querySelectorAll(".treasureMapPopup-mice-groups.uncaughtmiceinotherenvironments .treasureMapPopup-mice-group-mouse-name span");
    if (uncaughtOther != null) {
        for (var i=0; i<uncaughtOther.length; i++) {
            mice.push(uncaughtOther[i].textContent);
        }
    }
    return mice;
}

// Opens new tab with prepopulated mice
function openMapToolWindow(website, mice) {
    var url, divider, pretext;

    if (website === "mhmh") {
        url = "https://mhmaphelper.agiletravels.com/mice/";
        divider = "+";
        pretext = "";
    } else if (website === "tsitu") {
        url = "https://tsitu.github.io/MH-Tools/map.html";
        divider = "/";
        pretext = "?mice=";
    } else {
        return;
    }
    url = url + pretext + encodeURI(mice.join(divider));
    window.open(url);
}
