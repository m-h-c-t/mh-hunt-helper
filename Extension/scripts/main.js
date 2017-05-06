/*jslint browser:true */

(function () {
    'use strict';

    var db_url = "https://mhhunthelper.agiletravels.com/intake.php";

    if (!window.jQuery) {
        console.log("MHHH: Can't find jQuery, exiting.");
        return;
    }
    var mhhh_version = $("#mhhh_version").val();

    $(document).ajaxSuccess(function (event, xhr, ajaxOptions) {
     //   /* Method        */ ajaxOptions.type
     //   /* URL           */ ajaxOptions.url
     //   /* Response body */ xhr.responseText
     //   /* Request body  */ ajaxOptions.data

        if (ajaxOptions.url.search("mousehuntgame.com/managers/ajax/turns/activeturn.php") != -1) {
            var response = JSON.parse(xhr.responseText);
            var message = {};
            var journal = {};

            if (response.active_turn && response.success && response.journal_markup != null && response.journal_markup.length > 0) {
                for (var i=0; i < response.journal_markup.length; i++) {
                    if (response.journal_markup[i].render_data.css_class.match(/(catchfailure|catchsuccess|attractionfailure)/) &&
                        response.journal_markup[i].render_data.css_class.match(/active/)) {
                        journal = response.journal_markup[i];
                        break;
                    }
                }

                if (!journal) {
                    window.console.log("MHHH: Missing Info (trap check or friend hunt).");
                    return;
                }

                message = getMainHuntInfo(message, response, journal);
                message = fixTransitionMice(message, response, journal);
                message = getStage(message, response, journal);

                if (!message || !message.location || !message.location.name) {
                    window.console.log("MHHH: Missing Info (will try better next hunt).");
                    return;
                }

                message.extension_version = mhhh_version;

                // Send to database
                $.post(db_url, message)
                    .done(function (data) {
                        if (data) {
                            window.console.log(data);
                        }
                    });
            }
            else {
                window.console.log("MHHH: Missing Info (trap check or friend hunt).");
            }
        }
    });

    function getMainHuntInfo(message, response, journal) {

        // Entry ID
        message.entry_id = journal.render_data.entry_id;

        // Entry Timestamp
        message.entry_timestamp = journal.render_data.entry_timestamp;

        // User ID
        message.user_id = response.user.user_id;

        // Location
        message.location = {};
        message.location.name = response.user.location;
        message.location.id = response.user.environment_id;

        // Trap
        message.trap = {};
        message.trap.name = response.user.weapon_name.replace(/\ trap/i, '');
        message.trap.id = response.user.weapon_item_id;

        // Base
        message.base = {};
        message.base.name = response.user.base_name.replace(/\ base/i, '');
        message.base.id = response.user.base_item_id;

        // Charm
        message.charm = {};
        if (response.user.trinket_name) {
            message.charm.name = response.user.trinket_name.replace(/\ charm/i, '');
            message.charm.id = response.user.trinket_item_id;
        }

        // Cheese
        message.cheese = {};
        if (response.user.bait_name) {
            message.cheese.name = response.user.bait_name.replace(/\ cheese/i, '');
            message.cheese.id = response.user.bait_item_id;
        }

        // Shield (true / false)
        message.shield = response.user.has_shield;

        // Caught / Attracted / Mouse
        var outcome = journal.publish_data.attachment.name;
        var action = journal.render_data.css_class;
        if (action.match(/catchsuccess/)) {
            message.caught = 1;
            message.attracted = 1;
            message.mouse = outcome.replace(/i\ caught\ an?\ /i, '');
            message.mouse = message.mouse.replace(/(\ mouse)?\!/i, '');
        } else if (action.match(/catchfailure/)) {
            message.caught = 0;
            message.attracted = 1;
            message.mouse = outcome.replace(/i\ failed\ to\ catch\ an?\ /i, '');
            message.mouse = message.mouse.replace(/(\ mouse)?\./i, '');
        } else if (action.match(/attractionfailure/)) {
            message.caught = 0;
            message.attracted = 0;
        }

        return message;
    }

    function fixTransitionMice(message, response, journal) {
        if (message.mouse === "Realm Ripper" && message.location.name === "Acolyte Realm") {
            message.location.name = "Forbidden Grove";
            message.location.id = 11;
            message.stage = "Closed";
        } else if (message.mouse === "Riptide" && message.location.name === "Jungle of Dread") {
            // Can't determine Balack's Cove stage
            message = "";
        } else if (message.mouse === "Icewing" && message.location.name === "Slushy Shoreline") {
            message.location.name = "Iceberg";
            message.location.id = 40;
            message.stage = "Iceberg";
        }
        return message;
    }

    function getStage(message, response, journal) {
        switch (response.user.location) {
            case "Labyrinth":
                message = getLabyrinthStage(message, response, journal);
                break;
            case "Fiery Warpath":
                message = getFieryWarpathStage(message, response, journal);
                break;
            case "Balack's Cove":
                message = getBalacksCoveStage(message, response, journal);
                break;
            case "Living Garden":
                message = getLivingGardenStage(message, response, journal);
                break;
            case "Sand Dunes":
                message = getSandDunesStage(message, response, journal);
                break;
            case "Lost City":
            case "Cursed City":
                message = getLostCityStage(message, response, journal);
                break;
            case "Iceberg":
                message = getIcebergStage(message, response, journal);
                break;
            case "Sunken City":
                message = getSunkenCityStage(message, response, journal);
                break;
            case "Zokor":
                message = getZokorStage(message, response, journal);
                break;
            case "Seasonal Garden":
                message = getSeasonalGardenStage(message, response, journal);
                break;
            case "Furoma Rift":
                message = getFuromaRiftStage(message, response, journal);
                break;
            case "Toxic Spill":
                message = getToxicSpillStage(message, response, journal);
                break;
            case "Burroughs Rift":
                message = getBurroughsRiftStage(message, response, journal);
                break;
            case "Twisted Garden":
                message = getTwistedGardenStage(message, response, journal);
                break;
            // case "Sand Crypts":
                // message = get---Stage(message, response, journal);
                // break;
            case "Fort Rox":
                message = getFortRoxStage(message, response, journal);
                break;
            case "Gnawnian Express Station":
                message = getTrainStage(message, response, journal);
                break;
            // case "Whisker Woods Rift":
                // message = get---Stage(message, response, journal);
                // break;
            case "Forbidden Grove":
                message = getFobiddenGroveStage(message, response, journal);
                break;
        }

        return message;
    }

    function getLabyrinthStage(message, response, journal) {
        if (response.user.quests.QuestLabyrinth.status === "hallway") {
            message.stage = response.user.quests.QuestLabyrinth.hallway_name;
            // Remove first word (like Short)
            message.stage = message.stage.substr(message.stage.indexOf(" ") + 1);
            message.stage = message.stage.replace(/\ hallway/i, '');
        } else {
            // Not recording last hunt of a hallway and intersections at this time
            return;
        }
        return message;
    }

    function getFieryWarpathStage(message, response, journal) {
        if (message.mouse === "Warmonger") {
            message.stage = "Wave 4";
        } else {
            message.stage = "Wave " + response.user.viewing_atts.desert_warpath.wave;
        }

        return message;
    }

    function getBalacksCoveStage(message, response, journal) {
        if (response.user.viewing_atts.tide) {
            var tide = response.user.viewing_atts.tide;
            message.stage = tide.substr(0, 1).toUpperCase() + tide.substr(1);
            if (message.stage === "Med") {
                message.stage = "Medium";
            }
            message += " Tide";
        }
        return message;
    }

    function getSeasonalGardenStage(message, response, journal) {
        switch (response.user.viewing_atts.season) {
            case "sr":
                message.stage = "Summer";
                break;
            case "fl":
                message.stage = "Fall";
                break;
            case "wr":
                message.stage = "Winter";
                break;
            default:
                message.stage = "Spring";
                break;
        }
        return message;
    }

    function getLivingGardenStage(message, response, journal) {
        if (response.user.quests.QuestLivingGarden.minigame.bucket_state) {
            var bucket = response.user.quests.QuestLivingGarden.minigame.bucket_state;
            if (bucket === "filling") {
                message.stage = "Not Pouring";
            } else {
                message.stage = "Pouring";
            }
        }
        return message;
    }

    function getSandDunesStage(message, response, journal) {
        if (response.user.quests.QuestSandDunes.minigame.has_stampede) {
            message.stage = "Stampede";
        } else {
            message.stage = "No Stampede";
        }
        return message;
    }

    function getLostCityStage(message, response, journal) {
        if (response.user.quests.QuestLostCity.minigame.is_cursed) {
            message.stage = "Cursed";
        } else {
            message.stage = "Not Cursed";
        }
        return message;
    }

    function getTwistedGardenStage(message, response, journal) {
        if (response.user.quests.QuestLivingGarden.minigame.vials_state === "dumped") {
            message.stage = "Pouring";
        } else {
            message.stage = "Not Pouring";
        }
        return message;
    }

    function getIcebergStage(message, response, journal) {
        if (!response.user.quests.QuestIceberg.current_phase) {
            return '';
        }

        //switch on current depth after checking what phase has for generals
        switch (response.user.quests.QuestIceberg.current_phase) {
            case "Treacherous Tunnels":
                message.stage = "0-300ft";
                break;
            case "Brutal Bulwark":
                message.stage = "301-600ft";
                break;
            case "Bombing Run":
                message.stage = "601-1600ft";
                break;
            case "The Mad Depths":
                message.stage = "1601-1800ft";
                break;
            case "Icewing's Lair":
                message.stage = "1800ft";
                break;
            case "Hidden Depths":
                message.stage = "1801-2000ft";
                break;
            case "The Deep Lair":
                message.stage = "2000ft";
                break;
            case "General":
                message.stage = "Generals";
                break;
        }
        return message;
    }

    function getSunkenCityStage(message, response, journal) {
        if (!response.user.quests.QuestSunkenCity.is_diving) {
            message.stage = "Docked";
            return message;
        }

        // "if else" faster than "switch" calculations
        depth = response.user.quests.QuestSunkenCity.distance;
        if (depth < 2000) {
            message.stage = "0-2km";
        } else if (depth < 10000) {
            message.stage = "2-10km";
        } else if (depth < 15000) {
            message.stage = "10-15km";
        } else if (depth < 25000) {
            message.stage = "15-25km";
        } else if (depth >= 25000) {
            message.stage = "25+km";
        }

        return message;
    }

    function getZokorStage(message, response, journal) {
        if (!response.user.quests.QuestAncientCity.district_name) {
            return message;
        }

        var zokor_stages = {
            "Garden":     "Farming 0+",
            "Study":      "Scholar 15+",
            "Shrine":     "Fealty 15+",
            "Outskirts":  "Tech 15+",
            "Room":       "Treasure 15+",
            "Minotaur":   "Lair - Each 30+",
            "Temple":     "Fealty 50+",
            "Auditorium": "Scholar 50+",
            "Farmhouse":  "Farming 50+",
            "Centre":     "Tech 50+",
            "Vault":      "Treasure 50+",
            "Library":    "Scholar 80+",
            "Manaforge":  "Tech 80+",
            "Sanctum":    "Fealty 80+"
        };

        var zokor_district = response.user.quests.QuestAncientCity.district_name;

        var search_string;
        $.each(zokor_stages, function(key, value) {
            search_string = new RegExp(key, "i");
            if (zokor_district.match(search_string)) {
                message.stage = value;
                return false;
            }
        });

        return message;
    }

    function getFuromaRiftStage(message, response, journal) {
        switch (response.user.quests.QuestRiftFuroma.droid.charge_level) {
            case "":
                message.stage = "Outside";
                break;
            case "charge_level_one":
                message.stage = "Battery 1";
                break;
            case "charge_level_two":
                message.stage = "Battery 2";
                break;
            case "charge_level_three":
                message.stage = "Battery 3";
                break;
            case "charge_level_four":
                message.stage = "Battery 4";
                break;
            case "charge_level_five":
                message.stage = "Battery 5";
                break;
            case "charge_level_six":
                message.stage = "Battery 6";
                break;
            case "charge_level_seven":
                message.stage = "Battery 7";
                break;
            case "charge_level_eight":
                message.stage = "Battery 8";
                break;
            case "charge_level_nine":
                message.stage = "Battery 9";
                break;
            case "charge_level_ten":
                message.stage = "Battery 10";
                break;
        }
        return message;
    }

    function getToxicSpillStage(message, response, journal) {
        var titles = response.user.quests.MiniEventPollutionOutbreak.titles;
        for (var i=0; i < titles.length; i++) {
            if (titles.active) {
                message.stage = titles.name;
                break;
            }
        }
        return message;
    }

    function getBurroughsRiftStage(message, response, journal) {
        switch (response.user.quests.QuestRiftBurroughs.mist_tier) {
            case "tier_0":
                message.stage = "Mist 0";
                break;
            case "tier_1":
                message.stage = "Mist 1-5";
                break;
            case "tier_2":
                message.stage = "Mist 6-18";
                break;
            case "tier_3":
                message.stage = "Mist 19-20";
                break;
        }
        return message;
    }

    window.console.log("MH Hunt Helper v" + mhhh_version + " loaded! Good luck!");

    function getTrainStage(message, response, journal) {
        if (response.user.quests.QuestTrainStation.on_train) {
            switch (response.user.quests.QuestTrainStation.phase_name) {
                case "Supply Depot":
                    message.stage = "1st Phase";
                    break;
                case "Raider River":
                    message.stage = "2nd Phase";
                    break;
                case "Daredevil Canyon":
                    message.stage = "3rd Phase";
                    break;
            }
        } else {
            message.stage = "Station";
        }

        return message;
    }

    function getFortRoxStage(message, response, journal) {
        if (message.mouse === "Heart of the Meteor" || response.user.quests.QuestFortRox.is_lair) {
            message.stage = "Heart of the Meteor";
        } else if (response.user.quests.QuestFortRox.is_dawn) {
            message.stage = "Dawn";
        } else if (response.user.quests.QuestFortRox.is_day) {
            message.stage = "Day";
        } else if (response.user.quests.QuestFortRox.is_night) {
            switch (user.quests.QuestFortRox.current_stage) {
                case "stage_one":
                    message.stage = "Twilight";
                    break;
                case "stage_two":
                    message.stage = "Midnight";
                    break;
                case "stage_three":
                    message.stage = "Pitch";
                    break;
                case "stage_four":
                    message.stage = "Utter Darkness";
                    break;
                case "stage_five":
                    message.stage = "First Light";
                    break;
            }
        }
        return message;
    }

    function getFobiddenGroveStage(message, response, journal) {
        if (message.mouse === "Realm Ripper") {
            message.stage = "Closed";
        } else {
            message.stage = "Open";
        }

        return message;
    }

}());
