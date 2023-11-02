<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Controller\API\RiddlesAPIController;
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
use GuzzleHttp\Client;

class RiddlesController
{


    public function __construct(
        private Twig $twig,
    )
    {

    }
    public function showRiddles(Request $request, Response $response): Response
    {
        $riddles = $this->getRiddles();

        $riddles = json_decode($riddles,true);

        return $this->twig->render(
            $response,
            'riddles.twig',
            [
                "riddles" => $riddles,
            ]
        );
    }


    public function enterRiddle(Request $request, Response $response): Response
    {

        $id = $_POST['riddle_id'];



        return $response->withHeader('Location', "/riddles/$id")->withStatus(302);
    }

    public function showOneRiddle(Request $request, Response $response,array $args): Response
    {
        $id = intval($args['id']);

        $riddles = $this->getOneRiddle($id);

        $riddles = json_decode($riddles,true);

        return $this->twig->render(
            $response,
            'riddles.twig',
            [
                "riddles" => $riddles,
            ]
        );
    }

    public function getRiddles(): mixed
    {
        $url = "http://nginx:80/api/riddle";
        $client = new Client();
        try{
            $res = $client->request('GET', $url, [
                'Content-type' => 'application/json',
            ]);
            $riddlesInfo = $res->getBody()->getContents();
        }catch (GuzzleException $e) {
        echo 'bad request';
        }

        return $riddlesInfo;
    }

    public function getOneRiddle(int $id): mixed
    {
        $url = "http://nginx:80/api/riddle/{$id}";
        $client = new Client();
        try {
            $res = $client->request('GET',$url, [
                'Content-type' => 'application/json',
            ]);
            $riddlesInfo = $res->getBody()->getContents();
        } catch (GuzzleException $e) {
            echo $e->getMessage();

        }
        return $riddlesInfo;
    }







}
