<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Model\User;

interface TeamRepository
{
    public function createTeam(Team $team): void;

    public function getTeamByName(string $name);
    public function getAllTeams();

    public function getTeamById(int $team_id);
    public function getTeamByMember1(int $member1);
    public function getTeamByMember2(int $member2);

    public function joinTeam(Team $team);

    public function addPoints(Team $team);
}
