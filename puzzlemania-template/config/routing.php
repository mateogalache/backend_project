<?php

declare(strict_types=1);

use DI\Container;
use Salle\PuzzleMania\Controller\API\RiddlesAPIController;
use Salle\PuzzleMania\Controller\API\UsersAPIController;
use Salle\PuzzleMania\Controller\GameController;
use Salle\PuzzleMania\Controller\InGameController;
use Salle\PuzzleMania\Controller\ProfileController;
use Salle\PuzzleMania\Controller\RiddlesController;
use Salle\PuzzleMania\Controller\SignUpController;
use Salle\PuzzleMania\Controller\SignInController;
use Salle\PuzzleMania\Controller\SignUpJoinTeamController;
use Salle\PuzzleMania\Controller\TeamController;
use Salle\PuzzleMania\Controller\TeamStatsController;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Salle\PuzzleMania\Middleware\StartSessionMiddleware;
use Slim\App;



function addRoutes(App $app, Container $container): void {
    $app->get('/',
        SignInController::class . ':showHome'
    )->setName('showHome');

    $app->get('/sign-in',
        SignInController::class . ':showSignInForm'
    )->setName('signIn');

    $app->post('/sign-in',
        SignInController::class . ':signIn');


    $app->get('/sign-up',
        SignUpController::class . ':showSignUpForm'
    )->setName('signUp');

    $app->post('/sign-up',
        SignUpController::class . ':signUp');


    $app->get('/profile',
        ProfileController::class . ':showProfile'
    )->setName('profile')
        ->add(AuthorizationMiddleware::class);


    $app->get('/game',
        GameController::class . ':showGame'
    )->setName('game')
        ->add(AuthorizationMiddleware::class);

    $app->post('/game',
        GameController::class . ':startGame'
    )->setName('startGame')
        ->add(AuthorizationMiddleware::class);

    $app->get('/game/{game_id}/riddle/{riddleGameId}',
        InGameController::class . ':startFirstGame'
    )->setName('startFirstGame')
        ->add(AuthorizationMiddleware::class);

    $app->post('/game/{game_id}/riddle/{riddleGameId}',
        InGameController::class . ':sendRiddle'
    )->setName('sendRiddle')
        ->add(AuthorizationMiddleware::class);


    $app->get('/join',
        TeamController::class . ':showTeam'
    )->setName('teams')
        ->add(AuthorizationMiddleware::class);

    $app->post('/join',
        TeamController::class . ':createTeam'
    )->setName('createTeam')
        ->add(AuthorizationMiddleware::class);


    $app->get('/team-stats',
        TeamStatsController::class . ':showTeamStats'
    )->setName('teamStats')
        ->add(AuthorizationMiddleware::class);

    $app->post('/team-stats',
        TeamStatsController::class . ':sendTeamStats'
    )->setName('teamStats')
        ->add(AuthorizationMiddleware::class);

    $app->get('/team-stats/{teamId}',
        SignUpJoinTeamController::class . ':showSignUpJoinForm'
    )->setName('teamStatsSignUp')
        ->add(AuthorizationMiddleware::class);

    $app->post('/team-stats/{teamId}',
        SignUpJoinTeamController::class . ':signUpJoin'
    )->setName('teamStatsSignUp')
        ->add(AuthorizationMiddleware::class);


    $app->get('/riddles',
        RiddlesController::class . ':showRiddles'
    )->setName('showRiddles')
        ->add(AuthorizationMiddleware::class);

    $app->post('/riddles',
        RiddlesController::class . ':enterRiddle'
    )->setName('enterRiddle')
        ->add(AuthorizationMiddleware::class);

    $app->get('/riddles/{id}',
        RiddlesController::class . ':showOneRiddle'
    )->setName('showOneRiddle')
        ->add(AuthorizationMiddleware::class);


    $app->post(
        '/profile',
        ProfileController::class . ':uploadPicture'
    )->setName('upload');


    $app->get(
        '/api/riddle',
        RiddlesAPIController::class. ':getRiddles'
    )->setName('riddles');

    $app->post(
        '/api/riddle',
        RiddlesAPIController::class. ':postRiddle'
    )->setName('postRiddle');

    $app->get(
        '/api/riddle/{id}',
        RiddlesAPIController::class. ':getOneRiddle'
    )->setName('getOneRiddle');

    $app->put(
        '/api/riddle/{id}',
        RiddlesAPIController::class. ':updateRiddle'
    )->setName('updateRiddle');

    $app->delete(
        '/api/riddle/{id}',
        RiddlesAPIController::class. ':deleteRiddle'
    )->setName('deleteRiddle');
}
