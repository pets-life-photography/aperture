<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\PhotoVersion;
use App\Repository\PhotoRepository;
use App\Repository\PhotoVersionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /** @var PhotoVersionRepository */
    private $versions;

    /**
     * Constructor.
     *
     * @param PhotoRepository        $photos
     * @param PhotoVersionRepository $versions
     */
    public function __construct(
        PhotoRepository $photos,
        PhotoVersionRepository $versions
    ) {
        $this->photos   = $photos;
        $this->versions = $versions;
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
     * @param Request $request
     * @param int     $id
     * @param string  $hash
     * @param string  $extension
     *
     * @return Response
     *
     * @throws NotFoundHttpException When the photo or its version could not be found.
     */
    public function show(
        Request $request,
        int $id,
        string $hash,
        string $extension
    ): Response {
        $response = new Response(
            '',
            200,
            ['Content-Type' => static::EXTENSIONS[$extension] ?? 'text/plain']
        );

        $response->setEtag($hash);
        $response->setPrivate();
        $response->setImmutable(true);
        $response->setExpires(new DateTimeImmutable('+200 years'));
        $response->headers->addCacheControlDirective('no-transform', true);
        $response->headers->addCacheControlDirective('only-if-cached', true);

        if (!$response->isNotModified($request)) {
            $builder = $this->versions->createQueryBuilder('version');
            $builder->select('version');
            $builder->where('version.hash = :hash');
            $builder->innerJoin(
                Photo::class,
                'photo',
                Join::WITH,
                'version.photo = photo'
            );
            $builder->andWhere('photo.id = :photoId');
            $builder->andWhere('photo.contentType = :contentType');

            $builder->setParameters(
                [
                    ':photoId' => $id,
                    ':hash' => $hash,
                    ':contentType' => static::EXTENSIONS[$extension] ?? 'invalid'
                ]
            );

            $version = $builder->getQuery()->getSingleResult();

            if (!$version instanceof PhotoVersion) {
                throw new NotFoundHttpException(
                    'Photo does not exist.'
                );
            }

            $response->setContent(
                stream_get_contents($version->getContent())
            );
        }

        return $response;
    }
}
