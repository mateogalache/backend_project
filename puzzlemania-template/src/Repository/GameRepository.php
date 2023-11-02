<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\Game;

interface GameRepository
{
    public function createGame(Game $game);

    public function getGameById(int $game_id);

    public function setRiddleGameId(int $riddleGameId,int $game_id);

    public function addCurrentPoints(Game $game);
}
