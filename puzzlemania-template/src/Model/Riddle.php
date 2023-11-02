<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Model;

use DateTime;
use JsonSerializable;

class Riddle implements JsonSerializable
{

    private int $riddle_id;
    private int $user_id;

    private string $riddle;

    private string $answer;

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
    public static function create(): Riddle
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

    public function getRiddleId()
    {
        return $this->riddle_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getRiddle()
    {
        return $this->riddle;
    }


    public function getAnswer()
    {
        return $this->answer;
    }

    public function setRiddleId(int $riddle_id)
    {
        $this->riddle_id = $riddle_id;
        return $this;
    }


    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function setRiddle(string $riddle)
    {
        $this->riddle = $riddle;
        return $this;
    }

    public function setAnswer(string $answer)
    {
        $this->answer = $answer;
        return $this;
    }
}
