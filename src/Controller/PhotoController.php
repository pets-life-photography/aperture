<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\PhotoVersion;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    public const EXTENSIONS = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png'
    ];

    /** @var PhotoRepository */
    private $photos;

    /**
     * Constructor.
     *
     * @param PhotoRepository $photos
     */
    public function __construct(PhotoRepository $photos)
    {
        $this->photos = $photos;
    }

    /**
     * @Route("/photo", name="photo")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render(
            'photo/index.html.twig',
            [
                'controller_name' => 'PhotoController',
                'photos' => $this->photos->findAll()
            ]
        );
    }

    /**
     * @Route("/photo/{id}/{hash}.{extension}", name="photo_show")
     *
     * @param int    $id
     * @param string $hash
     * @param string $extension
     *
     * @return Response
     *
     * @throws NotFoundHttpException When the photo or its version could not be found.
     */
    public function show(int $id, string $hash, string $extension): Response
    {
        $photo = $this
            ->photos
            ->findOneBy(
                [
                    'id' => $id,
                    'contentType' => static::EXTENSIONS[$extension] ?? 'invalid'
                ]
            );

        if (!$photo instanceof Photo) {
            throw new NotFoundHttpException(
                'Photo does not exist.'
            );
        }

        $version = array_reduce(
            iterator_to_array($photo->getVersions()),
            function (
                ?PhotoVersion $carry,
                PhotoVersion $version
            ) use ($hash): ?PhotoVersion {
                if ($carry === null
                    && $version->getHash() === $hash
                ) {
                    $carry = $version;
                }

                return $carry;
            }
        );

        if (!$version instanceof PhotoVersion) {
            throw new NotFoundHttpException(
                'Photo does not exist.'
            );
        }

        return new Response(
            stream_get_contents($version->getContent()),
            200,
            ['content-type' => $photo->getContentType()]
        );
    }
}
