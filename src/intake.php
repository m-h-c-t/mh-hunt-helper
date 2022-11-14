<?php
define('not_direct_access', TRUE);

require_once "send_response.php";

require_once "check-ban.php";

require_once "check-cors.php";

require_once "config.php";

require_once "check-version.php";

require_once "uuid.php";

require_once "check-userid.php";

require_once "check-time.php";

require_once "db-connect.php";

require_once "rh_intake.php";

require_once "check-15mins.php";

require_once "temp-fixes.php";

require_once "hunt-intake.php";

require_once "giveaway-intake.php";

sendResponse('success', "Thanks for the hunt info!");

