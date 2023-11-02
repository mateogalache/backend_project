<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use PDO;
use Salle\PuzzleMania\Model\Game;
use Salle\PuzzleMania\Model\Team;

final class MySQLGameRepository implements GameRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createGame(Game $game): int
    {
        $query = <<<'QUERY'
        INSERT INTO game(riddle_id1,riddle_id2,riddle_id3,riddleGame_id,team_id,current_points)
        VALUES(:riddle_id1, :riddle_id2,:riddle_id3,:riddleGame_id,:team_id,:current_points)
        QUERY;

        $riddle_id1 = $game->getRiddleId1();
        $riddle_id2 = $game->getRiddleId2();
        $riddle_id3 = $game->getRiddleId3();
        $riddleGame_id = $game->getRiddleGameId();
        $team_id = $game->getTeamId();
        $current_points = $game->getCurrentPoints();

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('riddle_id1', $riddle_id1, PDO::PARAM_INT);
        $statement->bindParam('riddle_id2', $riddle_id2, PDO::PARAM_INT);
        $statement->bindParam('riddle_id3', $riddle_id3, PDO::PARAM_INT);
        $statement->bindParam('riddleGame_id', $riddleGame_id, PDO::PARAM_INT);
        $statement->bindParam('team_id', $team_id, PDO::PARAM_INT);
        $statement->bindParam('current_points', $current_points, PDO::PARAM_INT);


        $statement->execute();

        return (int)$this->databaseConnection->lastInsertId();
    }

    public function getGameById(int $game_id)
    {
        $query = <<<'QUERY'
        SELECT * FROM game WHERE game_id = :game_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function setRiddleGameId(int $riddleGameId,int $game_id): void
    {
        $query = <<<'QUERY'
        UPDATE game SET riddleGame_id = :riddleGameId WHERE game_id = :game_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('riddleGameId', $riddleGameId, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function addCurrentPoints(Game $game): void
    {
        $query = <<<'QUERY'
        UPDATE game SET current_points = :current_points WHERE game_id = :game_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $game_id = $game->getGameId();
        $current_points = $game->getCurrentPoints();

        $statement->bindParam('current_points', $current_points, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);

        $statement->execute();
    }

}
