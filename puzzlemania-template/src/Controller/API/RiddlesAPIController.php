<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Controller\API;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Model\Game;
use Salle\PuzzleMania\Model\Riddle;
use Salle\PuzzleMania\Repository\RiddleRepository;


class RiddlesAPIController
{

    public function __construct(
        private RiddleRepository $riddleRepository,
    )
    {

    }

    public function getRiddles(Request $request, Response $response): Response
    {
        $data = $this->riddleRepository->getAllRiddles();

        $response->getBody()->write(json_encode([$data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function postRiddle(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        //var_dump($data);

        $riddle = Riddle::create()
            ->setRiddleId($data['id'])
            ->setUserId($data['userId'])
            ->setRiddle($data['riddle'])
            ->setAnswer($data['answer']);
        $this->riddleRepository->createRiddleAPI($riddle);

        /*$errors['id'] = $this->validateId($data);
        $errors['empty'] = $this->EmptyRiddle($data);

        if ($errors['id'] != null || $errors['empty'] != null) {
            if($errors['id'] != null){
                $response->getBody()->write(json_encode(['errors' => $errors['id']]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }else{
                $response->getBody()->write(json_encode(['errors' => $errors['empty']]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        }*/

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getOneRiddle(Request $request, Response $response,array $args): Response {
        // This method decodes the received json
        //$data = $request->getParsedBody();

        $riddle_id = intval($args['id']);
        $data = $this->riddleRepository->getRiddleById($riddle_id);
        $errors['id'] = $this->validateId($data);

        if ($errors['id'] != null) {
            $response->getBody()->write(json_encode(['errors' => $errors['id']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode([$data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateRiddle(Request $request, Response $response,array $args): Response {
        // This method decodes the received json
        $data = $request->getParsedBody();

        $riddle = Riddle::create()
            ->setRiddleId(intval($args['id']))
            ->setUserId($data['userId'])
            ->setRiddle($data['riddle'])
            ->setAnswer($data['answer']);
        $this->riddleRepository->updateRiddle($riddle);

        $errors['id'] = $this->validateId($data);
        $errors['empty'] = $this->EmptyRiddle($data);

        if ($errors['id'] != null || $errors['empty'] != null) {
            if($errors['id'] != null){
                $response->getBody()->write(json_encode(['errors' => $errors['id']]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }else{
                $response->getBody()->write(json_encode(['errors' => $errors['empty']]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteRiddle(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $id = intval($args['id']);
        $this->riddleRepository->deleteRiddle($id);
        $errors['id'] = $this->validateId($data);

        if ($errors['id'] != null) {
            $response->getBody()->write(json_encode(['errors' => $errors['id']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        //$response->getBody()->write(json_encode(['responseData' => 'everything is fine']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function validateId(array $data): ?string
    {
        if (!$this->riddleRepository->getRiddleById($data['id'])) {
            return 'Riddle with id' . $data['id'] . "does not exist";
        }
        else{
            return null;
        }

    }

    private function EmptyRiddle(array $data): ?string
    {
        if (EMPTY($data['riddle']) || EMPTY($data['user_id']) || EMPTY($data['answer'])) {
            return "'riddle' and/or 'answer' and/or 'userId' key missing";
        }else{
            return null;
        }
    }
}
