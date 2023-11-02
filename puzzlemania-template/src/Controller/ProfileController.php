<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Salle\PuzzleMania\Service\ValidatorService;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class ProfileController {

    private const UPLOADS_DIR = __DIR__ . '/../../public/uploads';
    private const UNEXPECTED_ERROR = "You must select a file to upload";
    private const INVALID_EXTENSION_ERROR = "Only png and jpg images are allowed.";
    // We use this const to define the extensions that we are going to allow
    private const INVALID_SIZE_ERROR = "The size of the image must be less than 1MB.";

    private const INVALID_DIMENSION_ERROR = "The dimension of the image must be 400x400.";
    private const ALLOWED_EXTENSIONS = ['jpg', 'png'];
    private const ALLOWED_DIMENSIONS = [400, 400];
    public function __construct(
        private Twig    $twig,
        private UserRepository $userRepository,
    )
    {

    }

    public function showProfile(Request $request, Response $response): Response {
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'email' => $_SESSION['email'],
                'picture' => $this->userRepository->getUserByEmail($_SESSION['email'])->picture
            ]
        );
    }

    public function uploadPicture (Request $request, Response $response): Response {
        $uploadsDir = self::UPLOADS_DIR;

        if (!file_exists($uploadsDir) || !is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $uploadedFiles = $request->getUploadedFiles();

        $errors = [];

        /** @var UploadedFileInterface $uploadedFile */
        foreach ($uploadedFiles['files'] as $uploadedFile) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                $errors[] = sprintf(
                    self::UNEXPECTED_ERROR,
                    $uploadedFile->getClientFilename()
                );
                continue;
            }

            $name = $uploadedFile->getClientFilename();



            $fileInfo = pathinfo($name);

            $format = $fileInfo['extension'];
            $mimetype = mime_content_type(($uploadedFile->getStream()->getMetadata('uri')));

            $image_info = getimagesize($uploadedFile->getStream()->getMetadata('uri'));
            $image_width = $image_info[0];
            $image_height = $image_info[1];

            if (!$this->isValidFormat($format, $mimetype)) {
                $errors[] = sprintf(self::INVALID_EXTENSION_ERROR, $format);
                continue;
            }

            if (!$this->isValidSize($uploadedFile->getSize())) {
                $errors[] = sprintf(self::INVALID_SIZE_ERROR, $format);
                continue;
            }


            if (!$this->isValidDimension($image_width, $image_height)) {
                $errors[] = sprintf(self::INVALID_DIMENSION_ERROR, $format);
                continue;
            }


            $endName = substr($name, -4);
            $new_name = substr($name, 0, -4);
            //$my_random_id = $name;
            $my_random_id   = uniqid ( $new_name );
            $my_random_id .= $endName;
            $this->userRepository->updateUserPicture($my_random_id);




            // We should generate a custom name here instead of using the one coming form the form
            $uploadedFile->moveTo( "uploads/$my_random_id");

        }



        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'email' => $_SESSION['email'],
                'picture' => $this->userRepository->getUserByEmail($_SESSION['email'])->picture,
                'errors' => $errors,
            ]
        );
    }

    private function isValidFormat(string $extension, string $mimetype): bool {
        $allowedMimetypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];
        return isset($allowedMimetypes[$mimetype]) && $allowedMimetypes[$mimetype] === $extension;
    }

    private function isValidDimension(int $width, int $height): bool {
        if ($width <= self::ALLOWED_DIMENSIONS[0] && $height <= self::ALLOWED_DIMENSIONS[1]){
            return true;
        } else {
            return false;
        }
    }

    private function isValidSize(int $size): bool{
        if ($size <= 10^6){
            return true;
        }
        else{
            return false;
        }
    }

}
