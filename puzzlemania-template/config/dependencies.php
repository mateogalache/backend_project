<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
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
use Salle\PuzzleMania\Repository\MySQLGameRepository;
use Salle\PuzzleMania\Repository\MySQLRiddleRepository;
use Salle\PuzzleMania\Repository\MySQLTeamRepository;
use Salle\PuzzleMania\Repository\MySQLUserRepository;
use Salle\PuzzleMania\Repository\PDOConnectionBuilder;
use Slim\Flash\Messages;
use Slim\Views\Twig;

function addDependencies(ContainerInterface $container): void
{
    $container->set(
        'view',
        function () {
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        }
    );

    $container->set('db', function () {
        $connectionBuilder = new PDOConnectionBuilder();
        return $connectionBuilder->build(
            $_ENV['MYSQL_ROOT_USER'],
            $_ENV['MYSQL_ROOT_PASSWORD'],
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_DATABASE']
        );
    });

    $container->set(
        'flash',
        function () {
            return new Messages();
        }
    );

    $container->set(AuthorizationMiddleware::class, function (ContainerInterface $container) {
        return new AuthorizationMiddleware($container->get('flash'));
    });


    $container->set('user_repository', function (ContainerInterface $container) {
        return new MySQLUserRepository($container->get('db'));
    });

    $container->set('team_repository', function (ContainerInterface $container) {
        return new MySQLTeamRepository($container->get('db'));
    });

    $container->set('riddle_repository', function (ContainerInterface $container) {
        return new MySQLRiddleRepository($container->get('db'));
    });

    $container->set('game_repository', function (ContainerInterface $container) {
        return new MySQLGameRepository($container->get('db'));
    });


    $container->set(
        SignInController::class,
        function (ContainerInterface $c) {
            return new SignInController($c->get('view'), $c->get('user_repository'), $c->get("flash"),
                $c->get('riddle_repository'));
        }
    );




    $container->set(
        SignUpController::class,
        function (ContainerInterface $c) {
            return new SignUpController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        ProfileController::class,
        function (ContainerInterface $c) {
            return new ProfileController($c->get('view'),$c->get('user_repository'));
        }
    );

    $container->set(
        GameController::class,
        function (ContainerInterface $c) {
            return new GameController($c->get('view'),$c->get('user_repository'),$c->get('team_repository'),
                $c->get("flash"),$c->get('game_repository'),$c->get('riddle_repository'));
        }
    );

    $container->set(
        TeamController::class,
        function (ContainerInterface $c) {
            return new TeamController($c->get('view'),$c->get('user_repository'),$c->get('team_repository'),
                $c->get("flash"));
        }
    );

    $container->set(
        TeamStatsController::class,
        function (ContainerInterface $c) {
            return new TeamStatsController($c->get('view'),$c->get('team_repository'),$c->get("flash"),
                $c->get('user_repository'));
        }
    );

    $container->set(
        InGameController::class,
        function (ContainerInterface $c) {
            return new InGameController($c->get('view'),$c->get('riddle_repository'),$c->get('game_repository'),
                $c->get('team_repository'));
        }
    );

    $container->set(
        SignUpJoinTeamController::class,
        function (ContainerInterface $c) {
            return new SignUpJoinTeamController($c->get('view'), $c->get('user_repository'),$c->get('team_repository'));
        }
    );

    $container->set(
        RiddlesController::class,
        function (ContainerInterface $c) {
            return new RiddlesController($c->get('view'));
        }
    );

    $container->set(
        RiddlesAPIController::class,
        function (ContainerInterface $c) {
            return new RiddlesAPIController($c->get('riddle_repository'));
        }
    );

}
