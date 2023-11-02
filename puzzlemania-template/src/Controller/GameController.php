<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Salle\PuzzleMania\Model\Game;
use Salle\PuzzleMania\Model\User;
use Salle\PuzzleMania\Repository\GameRepository;
use Salle\PuzzleMania\Repository\RiddleRepository;
use Salle\PuzzleMania\Repository\TeamRepository;
use Salle\PuzzleMania\Service\ValidatorService;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class GameController
{

    public function __construct(
        private Twig    $twig,
        private UserRepository $userRepository,
        private TeamRepository $teamRepository,
        private Messages $flash,
        private GameRepository $gameRepository,
        private RiddleRepository $riddleRepository,
    )
    {

    }

    public function showGame(Request $request, Response $response): Response
    {
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        if(!$this->teamRepository->getTeamByMember1($user->id) && !$this->teamRepository->getTeamByMember2($user->id)) {
            $this->flash->addMessage(
                'notifications',
                'You need a team to play the game!'
            );

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $response
                ->withHeader('Location', $routeParser->urlFor("teams"))
                ->withStatus(302);
        }
        else{

            if ($this->teamRepository->getTeamByMember1($user->id)){
                $teamStats = $this->teamRepository->getTeamByMember1($user->id);

            }
            else{
                $teamStats = $this->teamRepository->getTeamByMember2($user->id);
            }

            return $this->twig->render(
                $response,
                'game.twig',
                [
                    "team" => $teamStats,
                ]
            );
        }

    }

    public function startGame(Request $request,Response $response): Response
    {
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);
        if ($this->teamRepository->getTeamByMember1($user->id)){
            $teamStats = $this->teamRepository->getTeamByMember1($user->id);

        }
        else{
            $teamStats = $this->teamRepository->getTeamByMember2($user->id);
        }

        $randomRiddle1 = rand(1,$this->riddleRepository->countRiddles());
        do{
            $randomRiddle2 = rand(1,$this->riddleRepository->countRiddles());
        } while ($randomRiddle2 == $randomRiddle1);

        do{
            $randomRiddle3 = rand(1,$this->riddleRepository->countRiddles());
        } while ($randomRiddle3 == $randomRiddle1 || $randomRiddle3 == $randomRiddle2);




        $game = Game::create()
            ->setRiddleId1($this->riddleRepository->selectRiddleGame($randomRiddle1)->riddle_id)
            ->setRiddleId2($this->riddleRepository->selectRiddleGame($randomRiddle2)->riddle_id)
            ->setRiddleId3($this->riddleRepository->selectRiddleGame($randomRiddle3)->riddle_id)
            ->setRiddleGameId(1)
            ->setTeamId($teamStats->team_id)
            ->setCurrentPoints(0);

        $game_id = $this->gameRepository->createGame($game);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();


        return $response
            ->withHeader('Location', "/game/{$game_id}/riddle/{$game->getRiddleGameId()}")
            ->withStatus(302);

    }






}
