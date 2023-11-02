<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Model\User;
use Salle\PuzzleMania\Repository\TeamRepository;
use Salle\PuzzleMania\Service\ValidatorService;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class TeamController
{



    public function __construct(
        private Twig    $twig,
        private UserRepository $userRepository,
        private TeamRepository $teamRepository,
        private Messages $flash,

    )
    {

    }

    public function showTeam(Request $request, Response $response): Response
    {

        $num_members = 0;

        $messages = $this->flash->getMessages();

        $notifications = $messages['notifications'] ?? [];

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        if($this->teamRepository->getTeamByMember1($user->id) || $this->teamRepository->getTeamByMember2($user->id)) {
            $this->flash->addMessage(
                'notifications',
                'You already have a team!'
            );

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $response
                ->withHeader('Location', $routeParser->urlFor("teamStats"))
                ->withStatus(302);
        }
        else{
            $teams = $this->teamRepository->getAllTeams();

            return $this->twig->render(
                $response,
                'teams.twig',
                [
                    'email' => $_SESSION['email'],
                    'teams' => $teams,
                    "notifs" => $notifications,
                ]
            );
        }

    }

    public function createTeam(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $data = $request->getParsedBody();
        $teams = $this->teamRepository->getAllTeams();

        if (isset($data['createSubmit'])) {
            $teamName = $data['teamName'];

            if (strlen($teamName) > 255) {
                $errors['name'] = "The name exceeds the limit of 255 characters!";
                return $this->twig->render(
                    $response,
                    'teams.twig',
                    [
                        'errors' => $errors,
                        'teams' => $teams,
                    ]
                );
            }
            $checkTeam = $this->teamRepository->getTeamByName($data['teamName']);
            if ($checkTeam != null) {
                $errors['name'] = "Team name already taken!";
                return $this->twig->render(
                    $response,
                    'teams.twig',
                    [
                        'errors' => $errors,
                        'teams' => $teams,
                    ]
                );

            } else{
                $user = $this->userRepository->getUserByEmail($_SESSION['email']);

                $team = Team::create()
                    ->setName($data['teamName'])
                    ->setMember1($user->id)
                    ->setTotalPoints(0);
                $this->teamRepository->createTeam($team);

                return $response
                    ->withHeader('Location', $routeParser->urlFor("teamStats"))
                    ->withStatus(302);
            }


        }

        else {

            $user = $this->userRepository->getUserByEmail($_SESSION['email']);

            $team = Team::create()
                ->setName($data['teamName'])
                ->setMember2($user->id);
            $this->teamRepository->joinTeam($team);

            return $response
                ->withHeader('Location', $routeParser->urlFor("teamStats"))
                ->withStatus(302);
        }
    }

}
