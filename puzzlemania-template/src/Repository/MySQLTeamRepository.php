<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use PDO;
use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Model\User;

final class MySQLTeamRepository implements TeamRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createTeam(Team $team): void
    {
        $query = <<<'QUERY'
        INSERT INTO team(name,member1,total_points)
        VALUES(:name, :member1,:total_points)
        QUERY;

        $name = $team->getName();
        $member1 = $team->getMember1();
        $total_points = $team->getTotalPoints();

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('name', $name, PDO::PARAM_STR);
        $statement->bindParam('member1', $member1, PDO::PARAM_INT);
        $statement->bindParam('total_points', $total_points, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getAllTeams()
    {

        $query = <<<'QUERY'
        SELECT * FROM team
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();

        $teams = [];


        $count = $statement->rowCount();
        if ($count > 0) {

            $rows = $statement->fetchAll();
            for ($i = 0; $i < $count; $i++) {
                $num_members = 0;
                if (intval($rows[$i]['member1'])){

                    $num_members = $num_members + 1;
                }
                if (intval($rows[$i]['member2'])){

                    $num_members = $num_members + 1;
                }
                $team = Team::create()
                    ->setTeamId(intval($rows[$i]['team_id']))
                    ->setName($rows[$i]['name'])
                    ->setMember1(intval($rows[$i]['member1']))
                    ->setMember2(intval($rows[$i]['member2']))
                    ->setNum_members($num_members);

                $teams[] = $team;

            }
        }
        return $teams;
    }

    public function joinTeam(Team $team): void
    {
        $query = <<<'QUERY'
        UPDATE team SET member2 = :member2 WHERE name = :name
        QUERY;

        $member2 = $team->getMember2();

        $name = $team->getName();


        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('name', $name, PDO::PARAM_STR);
        $statement->bindParam('member2', $member2, PDO::PARAM_INT);


        $statement->execute();
    }

    public function joinTeamId(Team $team): void
    {
        $query = <<<'QUERY'
        UPDATE team SET member2 = :member2 WHERE team_id = :team_id
        QUERY;

        $member2 = $team->getMember2();

        $team_id = $team->getTeamId();


        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('team_id', $team_id, PDO::PARAM_INT);
        $statement->bindParam('member2', $member2, PDO::PARAM_INT);


        $statement->execute();
    }



    public function getTeamByName(string $name)
    {
        $query = <<<'QUERY'
        SELECT * FROM team WHERE name = :name
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('name', $name, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getTeamById(int $team_id)
    {
        $query = <<<'QUERY'
        SELECT * FROM team WHERE team_id = :team_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('team_id', $team_id, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }


    public function getTeamByMember1(int $member1)
    {
        $query = <<<'QUERY'
        SELECT * FROM team WHERE member1 = :member1
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('member1', $member1, PDO::PARAM_INT);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getTeamByMember2(int $member2)
    {
        $query = <<<'QUERY'
        SELECT * FROM team WHERE member2 = :member2
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('member2', $member2, PDO::PARAM_INT);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function addPoints(Team $team): void
    {
        $query = <<<'QUERY'
        UPDATE team SET total_points = :total_points WHERE team_id = :team_id
        QUERY;

        $total_points = $team->getTotalPoints();
        $team_id = $team->getTeamId();

        $statement = $this->databaseConnection->prepare($query);


        $statement->bindParam('total_points', $total_points, PDO::PARAM_INT);
        $statement->bindParam('team_id', $team_id, PDO::PARAM_INT);

        $statement->execute();
    }

}
