<?php

namespace BEP\LOL;

class Player
{

    const FIELD_NAME = 0;
    const FIELD_NICK = 1;
    const FIELD_TEAM = 2;
    const FIELD_WINNER = 3;
    const FIELD_POSITION = 4;
    const FIELD_KILLS = 5;
    const FIELD_DEATHS = 6;
    const FIELD_ASSISTS = 7;
    const FIELD_DAMAGE = 8;
    const FIELD_HEAL = 9;

    const ERROR_INVALID_POSITION = 'ERROR_INVALID_POSITION';

    const POSITION_RATINGS = [
        'T' => [
            'damage' => 0.03,
            'heal' => 0.01,
        ],
        'B' => [
            'damage' => 0.03,
            'heal' => 0.01,
        ],
        'M' => [
            'damage' => 0.03,
            'heal' => 0.01,
        ],
        'J' => [
            'damage' => 0.02,
            'heal' => 0.02,
        ],
        'S' => [
            'damage' => 0.01,
            'heal' => 0.03,
        ],
    ];

    const TOTAL_PLAYER_FIELDS = 10;

    var $name;
    var $nick;
    var $team;
    var $position;
    var $kills;
    var $deaths;
    var $assists;
    var $winner;
    var $damage;
    var $heal;
    var $valid;

    public function __construct(array $playerInfo)
    {
        if (count($playerInfo) != self::TOTAL_PLAYER_FIELDS) {
            $this->valid = false;
            return;
        }

        $this->valid = true;

        $this->name = $playerInfo[self::FIELD_NAME];
        $this->nick = $playerInfo[self::FIELD_NICK];
        $this->team = $playerInfo[self::FIELD_TEAM];
        $this->position = $playerInfo[self::FIELD_POSITION];
        $this->kills = $playerInfo[self::FIELD_KILLS];
        $this->deaths = $playerInfo[self::FIELD_DEATHS];
        $this->assists = $playerInfo[self::FIELD_ASSISTS];
        $this->damage = $playerInfo[self::FIELD_DAMAGE];
        $this->heal = $playerInfo[self::FIELD_HEAL];
        $this->winner = $playerInfo[self::FIELD_WINNER];
    }

    public function calculateKDA():float
    {
        return ($this->kills + $this->assists) / $this->deaths;
    }

    public function calculateRate():float
    {
        if (!isset(self::POSITION_RATINGS[$this->position])) {
            throw new Exception(self::ERROR_INVALID_POSITION);
        }

        $rate = $this->calculateKDA();
        $rate += $this->damage * self::POSITION_RATINGS[$this->position]['damage'];
        $rate += $this->heal * self::POSITION_RATINGS[$this->position]['heal'];
        return $rate;
    }

    public function isValid():bool
    {
        return $this->valid;
    }

    public function isWinner():bool
    {
        return $this->winner == 'true';
    }

}