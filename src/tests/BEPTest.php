<?php

use PHPUnit\Framework\TestCase;

use BEP\App as App;
use BEP\Reader as Reader;
use BEP\LOL\LOL as LOL;

class BEPTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();
        $this->reader = new Reader;
        $this->app = new App($this->reader);
    }

    public function test_Reader_whenFileIsEmpty_shouldReturnException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Reader::ERROR_INVALID_FILEPATH);
        $this->reader->readCSV('');
    }

    public function test_Reader_whenFileDoesNotExist_shouldReturnException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Reader::ERROR_FILE_DOES_NOT_EXIST);
        $this->reader->readCSV('fakepath');
    }

    public function test_Reader_whenFileExist_shouldReturnArrayWithLines()
    {
        $result = $this->reader->readCSV('files/lol.csv');
        $this->assertEquals(is_array($result), true);
        $this->assertEquals($result[0][0], LOL::FILE_HEADER);
    }

    public function test_App_whenAnalyzingLOLWithFieldWithInvalidFieldsForPlayers_shouldReturnDefinedError()
    {
        $file = 'files/lol.csv';
        $result = $this->app->analyzeLOL($file);
        $this->assertEquals($result->error, LOL::INVALID_FIELDS_FOR_PLAYERS);
    }

    public function test_App_whenAnalyzingLOLAndStatsFileIsForAnotherGame_shouldReturnDefinedError()
    {
        $file = 'files/valorant.csv';
        $result = $this->app->analyzeLOL($file);
        $this->assertEquals($result->error, LOL::INVALID_GAME);
    }

    public function test_App_whenAnalyzingLOLAndStatsFileHasInvalidNumberOfTeams_shouldReturnDefinedError()
    {
        $file = 'files/lol_invalid_number_of_teams.csv';
        $result = $this->app->analyzeLOL($file);
        $this->assertEquals($result->error, LOL::INVALID_NUMBER_OF_TEAMS);
    }

    public function test_App_whenAnalyzingLOLAndStatsFileHasInvalidNumberOfKillsAndDeath_shouldReturnDefinedError()
    {
        $file = 'files/lol_invalid_number_kills_and_death.csv';
        $result = $this->app->analyzeLOL($file);
        $this->assertEquals($result->error, LOL::INVALID_GAME_KILLS_AND_DEATHS);
    }

    public function test_App_whenAnalyzingLOLAndStatsFileAndBothTeamsWin_shouldReturnDefinedError()
    {
        $file = 'files/lol_both_team_wins.csv';
        $result = $this->app->analyzeLOL($file);
        $this->assertEquals($result->error, LOL::INVALID_WINNERS);
    }

    public function test_App_whenAnalyzingLOLAndStatsFileAreOK_shouldReturnErrorOK()
    {
        $file = 'files/lol_fixed.csv';
        $result = $this->app->analyzeLOL($file);
        $this->assertEquals($result->error, APP::ERROR_OK);
    }

}