<?php

namespace App\Controller;

use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ServePrivateFileController
 * @package App\Controller
 */
class ServePrivateFileController extends AbstractController
{
    /**
     * Returns a Picture file for display.
     */
    #[Route('/serve-private-file/image/{id}', name: 'serve_private_picture', methods: ['GET'])]
    public function privatePictureServe(Picture $picture): BinaryFileResponse
    {
        return $this->fileServe($picture->getPath());
    }

    /**
     * Returns a private file for display.
     *
     * @param string $path
     * @return BinaryFileResponse
     */
    private function fileServe(string $path): BinaryFileResponse
    {
        $absolutePath = $this->getParameter('kernel.project_dir') . '/' . $path;

        if (!file_exists($absolutePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas.');
        }

        return new BinaryFileResponse($absolutePath);
    }
}
