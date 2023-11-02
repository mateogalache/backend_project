<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Repository\RiddleRepository;
use Salle\PuzzleMania\Service\RiddleReader;
use Salle\PuzzleMania\Service\ValidatorService;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class SignInController
{
    private ValidatorService $validator;
    private RiddleReader $riddleReader;

    public function __construct(
        private Twig           $twig,
        private UserRepository $userRepository,
        private Messages       $flash,
        private RiddleRepository $riddleRepository,

    )
    {
        $this->validator = new ValidatorService();
        $this->riddleReader = new RiddleReader($this->riddleRepository);
    }

    public function showSignInForm(Request $request, Response $response): Response
    {
        $messages = $this->flash->getMessages();

        $notifications = $messages['notifications'] ?? [];

        return $this->twig->render($response, 'sign-in.twig', ["notifs" => $notifications]);
    }

    public function showHome(Request $request, Response $response): Response
    {
        $isLoggedIn = isset($_SESSION['user_id']);

        if (!isset($_SESSION['user_id'])) {
            $username = "stranger";
        } else {
            $user = $this->userRepository->getUserById(intval($_SESSION['user_id']));
            $username = explode('@', $user->email)[0];
        }
        //session_destroy();
        return $this->twig->render($response, 'home.twig', [
            "username" => $username
        ]);
    }

    public function signIn(Request $request, Response $response): Response
    {
        //$this->riddleReader->createRiddle();

        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];

        $errors['email'] = $this->validator->validateEmail($data['email']);
        $errors['password'] = $this->validator->validatePassword($data['password']);
        if ($errors['email'] == '') {
            unset($errors['email']);
        }
        if ($errors['password'] == '') {
            unset($errors['password']);
        }
        if (count($errors) == 0) {
            // Check if the credentials match the user information saved in the database
            $user = $this->userRepository->getUserByEmail($data['email']);
            if ($user == null) {
                $errors['email'] = 'User with this email address does not exist.';
            } else if ($user->password != md5($data['password'])) {
                $errors['password'] = 'Your email and/or password are incorrect.';
            } else {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['email'] = $user->email;
                return $response->withHeader('Location', '/')->withStatus(302);
            }
        }
        return $this->twig->render(
            $response,
            'sign-in.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('signIn')
            ]
        );
    }
}
