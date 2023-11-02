<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Model;

use DateTime;
use JsonSerializable;

class Game implements JsonSerializable
{

    private int $game_id;
    private int $riddle_id1;

    private int $riddle_id2;

    private int $riddle_id3;
    private int $riddleGame_id;

    private int $team_id;

    private int $current_points;

//    public function __construct(
//        string   $email,
//        string   $password,
//        Datetime $createdAt,
//        Datetime $updatedAt
//    )
//    {
//        $this->email = $email;
//        $this->password = $password;
//        $this->createdAt = $createdAt;
//        $this->updatedAt = $updatedAt;
//    }

    /**
     * Static constructor / factory
     */
    public static function create(): Game
    {
        return new self();
    }

    /**
     * Function called when encoded with json_encode
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getGameId()
    {
        return $this->game_id;
    }

    public function getRiddleId1()
    {
        return $this->riddle_id1;
    }

    public function getRiddleId2()
    {
        return $this->riddle_id2;
    }

    public function getRiddleId3()
    {
        return $this->riddle_id3;
    }

    public function getRiddleGameId()
    {
        return $this->riddleGame_id;
    }

    public function getTeamId()
    {
        return $this->team_id;
    }

    public function getCurrentPoints()
    {
        return $this->current_points;
    }

    public function setGameId(int $game_id)
    {
        $this->game_id = $game_id;
        return $this;
    }


    public function setRiddleId1(int $riddle_id1)
    {
        $this->riddle_id1 = $riddle_id1;
        return $this;
    }

    public function setRiddleId2(int $riddle_id2)
    {
        $this->riddle_id2 = $riddle_id2;
        return $this;
    }

    public function setRiddleId3(int $riddle_id3)
    {
        $this->riddle_id3 = $riddle_id3;
        return $this;
    }

    public function setRiddleGameId(int $riddleGame_id)
    {
        $this->riddleGame_id = $riddleGame_id;
        return $this;
    }

    public function setTeamId(int $team_id)
    {
        $this->team_id = $team_id;
        return $this;
    }

    public function setCurrentPoints(int $current_points)
    {
        $this->current_points = $current_points;
        return $this;
    }
}
