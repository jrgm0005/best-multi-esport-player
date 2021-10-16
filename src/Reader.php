<?php

namespace BEP;

use \Exception;

class Reader
{

    const ERROR_FILE_DOES_NOT_EXIST = 'ERROR_FILE_DOES_NOT_EXIST';

    const ERROR_INVALID_FILEPATH = 'ERROR_INVALID_FILEPATH';
    const ERROR_UNABLE_TO_READ_FILE = 'ERROR_UNABLE_TO_READ_FILE';

    public function readCSV(string $path):array
    {
        if (empty($path)) {
            throw new \Exception(self::ERROR_INVALID_FILEPATH);
        }

        if(!file_exists($path)) {
            throw new \Exception(self::ERROR_FILE_DOES_NOT_EXIST);
        }

        $response = [];
        if (($fileHandler = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($fileHandler, 1000, ",")) !== FALSE) {
                $response[] = $data;
            }
            fclose($fileHandler);
            return $response;
        } else {
            throw new \Exception(self::ERROR_UNABLE_TO_READ_FILE);
        }
    }
}