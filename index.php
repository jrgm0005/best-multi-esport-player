<?php

require("vendor/autoload.php");

use stdClass;
use DateTime;

use BEP\App as App;
use BEP\Reader as Reader;

// This simple index app. Shows LOL game info from a CSV with stats data.
// Task required the best player, but I added some extra info like winner team, best player nickname and best player rate.
// Also, added a field with teams info.

try {
    $reader = new Reader;
    $app = new App($reader);
    $output = $app->analyzeLOL(App::FILE_LOL_FIXED_EXAMPLE);
} catch (\Throwable $e) {
    $error = $e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine();
    $output->error = $error;
}

print "<pre>";
print_r($output);
print "</pre>";