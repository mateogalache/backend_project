<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use PDO;
use Salle\PuzzleMania\Model\Game;
use Salle\PuzzleMania\Model\Riddle;
use Salle\PuzzleMania\Model\Team;

final class MySQLRiddleRepository implements RiddleRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createRiddle(Riddle $riddle2): void
    {
        $query = <<<'QUERY'
        INSERT INTO riddles(riddle,answer)
        VALUES(:riddle,:answer)
        QUERY;

        $riddle1 = $riddle2->getRiddle();
        $answer = $riddle2->getAnswer();

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('riddle', $riddle1, PDO::PARAM_STR);
        $statement->bindParam('answer', $answer, PDO::PARAM_STR);

        $statement->execute();
    }

    public function countRiddles(): int
    {
        $query = <<<'QUERY'
        SELECT * FROM riddles
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();

        return $statement->rowCount();
    }

    public function selectRiddleGame(int $numberStop)
    {
        $numberMinus = $numberStop - 1;
        $query = <<<'QUERY'
        SELECT * FROM riddles LIMIT 1 OFFSET :numberMinus
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('numberMinus', $numberMinus, PDO::PARAM_INT);


        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getRiddleById(int $riddle_id)
    {
        $query = <<<'QUERY'
        SELECT * FROM riddles WHERE riddle_id = :riddle_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('riddle_id', $riddle_id, PDO::PARAM_INT);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function createRiddleAPI(Riddle $riddle): void
    {
        $query = <<<'QUERY'
        INSERT INTO riddles(riddle_id,user_id,riddle,answer)
        VALUES(:riddle_id,:user_id,:riddle,:answer)
        QUERY;

        $riddle_id = $riddle->getRiddleId();
        $riddle1 = $riddle->getRiddle();
        $answer = $riddle->getAnswer();
        $user_id = $riddle->getUserId();

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('riddle', $riddle1, PDO::PARAM_STR);
        $statement->bindParam('answer', $answer, PDO::PARAM_STR);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam('riddle_id', $riddle_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getAllRiddles()
    {
        $query = <<<'QUERY'
        SELECT * FROM riddles
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();

        $riddles = [];

        $count = $statement->rowCount();
        if ($count > 0) {
            $rows = $statement->fetchAll();

            for ($i = 0; $i < $count; $i++) {
                $user = Riddle::create()
                    ->setRiddleId(intval($rows[$i]['riddle_id']))
                    ->setUserId(intval($rows[$i]['user_id']))
                    ->setRiddle($rows[$i]['riddle'])
                    ->setAnswer($rows[$i]['answer']);
                $users[] = $user;
            }
        }
        return $users;
    }

    public function deleteRiddle(int $riddle_id) {
        $query = <<<'QUERY'
        DELETE FROM riddles WHERE riddle_id = :riddle_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('riddle_id', $riddle_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function updateRiddle(Riddle $riddle): void
    {
        $query = <<<'QUERY'
        UPDATE riddles SET riddle_id = :riddle_id, answer = :answer, user_id = :user_id WHERE riddle_id = :riddle_id
        QUERY;

        $riddle1 = $riddle->getRiddle();
        $answer = $riddle->getAnswer();
        $user_id = $riddle->getUserId();
        $riddle_id = $riddle->getRiddleId();

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('riddle', $riddle1, PDO::PARAM_STR);
        $statement->bindParam('answer', $answer, PDO::PARAM_STR);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam('riddle_id', $riddle_id, PDO::PARAM_INT);


        $statement->execute();
    }
}
