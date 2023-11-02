<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Salle\PuzzleMania\Model\Game;
use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Model\User;
use Salle\PuzzleMania\Repository\GameRepository;
use Salle\PuzzleMania\Repository\RiddleRepository;
use Salle\PuzzleMania\Repository\TeamRepository;
use Salle\PuzzleMania\Service\ValidatorService;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use function DI\create;

class InGameController
{
    public function __construct(
        private Twig    $twig,
        private RiddleRepository $riddleRepository,
        private GameRepository $gameRepository,
        private TeamRepository $teamRepository
    )
    {

    }

    public function startFirstGame(Request $request, Response $response, array $args): Response
    {

        $game_id = (int) $args['game_id'];
        $riddle_id = (int) $args['riddleGameId'];
        $game = $this->gameRepository->getGameById($game_id);
        if ($riddle_id == 1){
            $riddle = $this->riddleRepository->getRiddleById($game->riddle_id1);
            $title = "FIRST";
        } elseif($riddle_id == 2){
            $riddle = $this->riddleRepository->getRiddleById($game->riddle_id2);
            $title = "SECOND";
        } else{
            $riddle = $this->riddleRepository->getRiddleById($game->riddle_id3);
            $title = "THIRD";
        }
        $button = "Solve";


        return $this->twig->render(
            $response,
            'inGame.twig',
            [
                "game" => $game,
                "riddle" => $riddle,
                "title" => $title,
                "button" => $button,
            ]
        );
    }

    public function sendRiddle(Request $request, Response $response,array $args): Response
    {
        $data = $request->getParsedBody();
        $correct = false;
        $showMessage = false;
        $game_id = (int) $args['game_id'];
        $riddle_id = (int) $args['riddleGameId'];
        $game = $this->gameRepository->getGameById($game_id);
        $firstQuestion = 0;
        $sum_points = -10;



        if (isset($_POST['Solve'])){
            $answer = $data['answer'];
            if ($riddle_id == 1){
                $riddle = $this->riddleRepository->getRiddleById($game->riddle_id1);
                $firstQuestion = 10;
                $title = "FIRST";
            } elseif($riddle_id == 2){
                $riddle = $this->riddleRepository->getRiddleById($game->riddle_id2);
                $title = "SECOND";
            } else{
                $riddle = $this->riddleRepository->getRiddleById($game->riddle_id3);
                $title = "THIRD";
            }
            if ($riddle->answer == $answer){
                $correct = true;
                $sum_points = 10;
            }

            $solution = $riddle->answer;
            $button = "Next";
            $current_points = $game->current_points + $sum_points + $firstQuestion;

            $game = Game::create()
                ->setGameId($game->game_id)
                ->setCurrentPoints($current_points);

            $this->gameRepository->addCurrentPoints($game);
            
            return $this->twig->render(
                $response,
                'inGame.twig',
                [
                    "game" => $game,
                    "riddle" => $riddle,
                    "title" => $title,
                    "button" => $button,
                    "correct" => $correct,
                    "solution" => $solution,
                    "next" => true,
                ]
            );
        }
        else{
            if ($game->current_points == 0){
                $gameNext = 4;
            } else{
                $gameNext = $game->riddleGame_id + 1;
            }

            if ($gameNext > 3){

                $data = $request->getParsedBody();
                if (isset($_POST['Finish'])){
                    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                    return $response
                        ->withHeader('Location', $routeParser->urlFor("teamStats"))
                        ->withStatus(302);
                }
                else{
                    $lastTeam = $this->teamRepository->getTeamById($game->team_id);
                    if($game->current_points==0){
                        $points = $lastTeam->total_points;
                    }else{
                        $points= $game->current_points;
                    }

                    $team = Team::create()
                        ->setTeamId($game->team_id)
                        ->setTotalPoints($points);
                    $this->teamRepository->addPoints($team);


                    $button = "Finish";


                    return $this->twig->render(
                        $response,
                        'inGame.twig',
                        [
                            "game" => $game,
                            "button" => $button,
                            "points" => $game->current_points,
                            "finish" => true,
                        ]
                    );
                }




            }
            else{
                $this->gameRepository->setRiddleGameId($gameNext,$game->game_id);

                return $response
                    ->withHeader('Location', "/game/{$game_id}/riddle/{$gameNext}")
                    ->withStatus(302);
            }
        }





    }








}
