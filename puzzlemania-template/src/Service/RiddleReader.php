<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Service;

use Salle\PuzzleMania\Model\Riddle;
use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Repository\RiddleRepository;

class RiddleReader
{
    public function __construct
    (
        private RiddleRepository $riddleRepository,
    )
    {

    }

    public function createRiddle(): void
    {
        $json = file_get_contents('/app/resources/riddles/riddles.json');
        $riddles = json_decode($json);

        foreach ($riddles as $riddle) {
            $riddle = Riddle::create()
                ->setRiddle($riddle->riddle)
                ->setAnswer($riddle->answer);
            $this->riddleRepository->createRiddle($riddle);
        }

    }



}

