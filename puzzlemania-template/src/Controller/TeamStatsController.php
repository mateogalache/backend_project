<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Salle\PuzzleMania\Repository\TeamRepository;
use Salle\PuzzleMania\Service\ValidatorService;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class TeamStatsController
{

    public function __construct(
        private Twig    $twig,
        private TeamRepository $teamRepository,
        private Messages $flash,
        private UserRepository $userRepository,

    )
    {

    }

    public function showTeamStats(Request $request, Response $response): Response
    {

        $num_members = 0;

        $messages = $this->flash->getMessages();

        $notifications = $messages['notifications'] ?? [];

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        if(!$this->teamRepository->getTeamByMember1($user->id) && !$this->teamRepository->getTeamByMember2($user->id)) {
            $this->flash->addMessage(
                'notifications',
                'You need a team to see team stats!'
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

            $username1 = '';
            $username2= '';

            if ($teamStats->member1){
                $user1 = $this->userRepository->getUserById($teamStats->member1);
                $username1 = explode('@', $user1->email)[0];
                $num_members = $num_members + 1;
            }

            if ($teamStats->member2){
                $user2 = $this->userRepository->getUserById($teamStats->member2);
                $username2 = explode('@', $user2->email)[0];
                $num_members = $num_members + 1;
            }




            return $this->twig->render(
                $response,
                'teamStats.twig',
                [
                    "notifs" => $notifications,
                    "teamStats" => $teamStats,
                    "user1" => $username1,
                    "user2" => $username2,
                    "num_members" => $num_members,

                ]
            );
        }



    }

    public function sendTeamStats(Request $request, Response $response): Response
    {
        $num_members = 0;
        $showQrCode = false;
        $messages = $this->flash->getMessages();

        $notifications = $messages['notifications'] ?? [];

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        if (!$this->teamRepository->getTeamByMember1($user->id) && !$this->teamRepository->getTeamByMember2($user->id)) {
            $this->flash->addMessage(
                'notifications',
                'You need a team to see team stats!'
            );

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $response
                ->withHeader('Location', $routeParser->urlFor("teams"))
                ->withStatus(302);
        } else {
            if ($this->teamRepository->getTeamByMember1($user->id)) {
                $teamStats = $this->teamRepository->getTeamByMember1($user->id);

            } else {
                $teamStats = $this->teamRepository->getTeamByMember2($user->id);
            }

            $username1 = '';
            $username2 = '';

            if ($teamStats->member1) {
                $num_members = $num_members + 1;
                $user1 = $this->userRepository->getUserById($teamStats->member1);
                $username1 = explode('@', $user1->email)[0];
            }

            if ($teamStats->member2) {
                $num_members = $num_members + 1;
                $user2 = $this->userRepository->getUserById($teamStats->member2);
                $username2 = explode('@', $user2->email)[0];
            }
            if ($request->getMethod() == 'POST' ) {
                $showQrCode = true;
            }

            return $this->twig->render(
                $response,
                'teamStats.twig',
                [
                    "notifs" => $notifications,
                    "teamStats" => $teamStats,
                    "user1" => $username1,
                    "user2" => $username2,
                    "showQrCode" => $showQrCode,
                    "num_members" => $num_members

                ]
            );
        }
    }


    private function generateQr(int $teamId): void
    {
        $url = "http://localhost:8030/team-stats/$teamId";
        $data = array(
            'symbology' => 'QRCode',
            'code' => $url
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: image/png\r\n"
            )
        );

        $context = stream_context_create($options);
        $url = 'http://pw_barcode/BarcodeGenerator';

        $qrCode = file_get_contents($url, false, $context);
        file_put_contents("uploads/label.png", $qrCode);


    }









}
