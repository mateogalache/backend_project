<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\Riddle;

interface RiddleRepository
{
    public function createRiddle(Riddle $riddle): void;

    public function countRiddles(): int;

    public function selectRiddleGame(int $numberStop);

    public function getRiddleById(int $riddle_id);

    public function createRiddleAPI(Riddle $riddle): void;

    public function getAllRiddles();

    public function deleteRiddle(int $id);

    public function updateRiddle(Riddle $riddle);
}
