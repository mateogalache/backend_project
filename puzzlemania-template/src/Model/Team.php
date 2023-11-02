<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Model;

use DateTime;
use JsonSerializable;

class Team implements JsonSerializable
{

    private int $team_id;
    private string $name;
    private int $num_members;
    private int $member1;
    private int $member2;

    private int $total_points;

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
    public static function create(): Team
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

    public function getTeamId()
    {
        return $this->team_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNum_members()
    {
        return $this->num_members;
    }

    public function getMember1()
    {
        return $this->member1;
    }

    public function getMember2()
    {
        return $this->member2;
    }

    public function getTotalPoints()
    {
        return $this->total_points;
    }


    public function setTeamId(int $team_id)
    {
        $this->team_id = $team_id;
        return $this;
    }


    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }


    public function setNum_members(int $num_members)
    {
        $this->num_members = $num_members;
        return $this;
    }


    public function setMember1(int $member1)
    {
        $this->member1 = $member1;
        return $this;
    }


    public function setMember2(int $member2)
    {
        $this->member2 = $member2;
        return $this;
    }

    public function setTotalPoints(int $total_points)
    {
        $this->total_points = $total_points;
        return $this;
    }



}
