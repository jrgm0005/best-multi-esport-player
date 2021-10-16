<?php

namespace BEP\LOL;

use \stdClass;
use BEP\LOL\Player as Player;

class LOL
{

    const FILE_HEADER = 'LEAGUE OF LEGENDS';
    const INVALID_FIELDS_FOR_PLAYERS = 'INVALID_FIELDS_FOR_PLAYERS';
    const INVALID_GAME = 'INVALID_GAME';
    const INVALID_NUMBER_OF_TEAMS = 'INVALID_NUMBER_OF_TEAMS';
    const INVALID_GAME_KILLS_AND_DEATHS = 'INVALID_GAME_KILLS_AND_DEATHS';
    const INVALID_WINNERS = 'INVALID_WINNERS';

    var $data;
    var $header;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->header = $data[0][0];
        unset($this->data[0]);
    }

    public function getGameInfo():stdClass
    {
        $game = new stdClass;
        $game->bestPlayer  = null;
        $game->highestRate = 0.0;
        $game->winnerTeam = null;
        $teams = [];

        // At this level, we already should have validated the info. We could add extra checks to ensure it is as expected. Skipped for the test.
        foreach ($this->data as $key => $playerStr) {

            $player = new Player(explode(';', $playerStr[0]));
            if (!isset($teams[$player->team])) {
                $teams[$player->team] = [];
            }
            $teams[$player->team][] = $player;

            if (!isset($game->winnerTeam) && $player->isWinner()){
                $game->winnerTeam = $player->team;
            }

            // Calculate if this player is the best player in the game until now.
            $playerRate = $player->calculateRate();
            if ($playerRate > $game->highestRate) {
                $game->highestRate = $playerRate;
                $game->bestPlayer = $player->nick;
            }

        }

        $game->teams = $teams;
        return $game;
    }

    public function validate():stdClass
    {
        $response = new stdClass;
        $response->validated = true;

        if (!$this->validateHeader()) {
            $response->validated = false;
            $response->error = self::INVALID_GAME;
            return $response;
        }


        $killsDeath = [];
        $teams = [];
        $teamsNumber = 0;
        foreach ($this->data as $key => $playerStr) {

            // Get player.
            $player = new Player(explode(';', $playerStr[0]));
            if (!$player->isValid()) {
                $response->validated = false;
                $response->error = self::INVALID_FIELDS_FOR_PLAYERS;
                return $response;
            }

            if (!isset($killsDeath[$player->team])) {
                $killsDeath[$player->team]['kills'] = 0;
                $killsDeath[$player->team]['deaths'] = 0;
                $killsDeath[$player->team]['win'] = 0;
                $teams[$teamsNumber] = $player->team;
                $teamsNumber++;
            }

            $killsDeath[$player->team]['kills'] += $player->kills;
            $killsDeath[$player->team]['deaths'] += $player->deaths;
            if ($player->isWinner()) {
                $killsDeath[$player->team]['win'] = 1;
            }
        }

        if ($teamsNumber != 2) {
            $response->validated = false;
            $response->error = self::INVALID_NUMBER_OF_TEAMS;
            return $response;
        }

        // Based on task desc. All team player should win/lose. So, a team should win and another lose, if both teams win/lose data is invalid.
        if ($killsDeath[$teams[0]]['win'] == $killsDeath[$teams[1]]['win']) {
            $response->validated = false;
            $response->error = self::INVALID_WINNERS;
            return $response;
        }

        // Kills from team A should be the same than death from team B.
        if ($killsDeath[$teams[0]]['kills'] != $killsDeath[$teams[1]]['deaths'] || $killsDeath[$teams[1]]['kills'] != $killsDeath[$teams[0]]['deaths']) {
            $response->validated = false;
            $response->error = self::INVALID_GAME_KILLS_AND_DEATHS;
            return $response;
        }

        return $response;

    }

    protected function validateHeader()
    {
        return $this->header == self::FILE_HEADER;
    }

}