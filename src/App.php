<?php

namespace BEP;

use \stdClass;

use BEP\Reader as Reader;
use BEP\LOL\LOL as LOL;

class App
{
    const ERROR_INVALID_FILE = 'ERROR_INVALID_FILE';
    const ERROR_OK = 'OK';
    const FILE_LOL_EXAMPLE = 'files/lol.csv';
    const FILE_LOL_FIXED_EXAMPLE = 'files/lol_fixed.csv';
    const FILE_VALORANT_EXAMPLE = 'files/valorant.csv';
    const INVALID_CSV_DATA = 'INVALID_CSV_DATA';

    var $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function analyzeLOL(string $file):stdClass
    {
        $response = new stdClass;
        $response->error = self::ERROR_OK;

        if (empty($file)) {
            $response->error = self::ERROR_INVALID_FILE;
            return $response;
        }

        $lol = new LOL($this->reader->readCSV($file));
        if (empty($lol)) {
            $response->error = self::INVALID_CSV_DATA;
            return $response;
        }

        $validatedResponse = $lol->validate();
        if ($validatedResponse->validated) {
            $response->info = $lol->getGameInfo();
        } else {
            $response->error = $validatedResponse->error;
        }

        return $response;
    }

}