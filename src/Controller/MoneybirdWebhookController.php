<?php

namespace App\Controller;

use App\Event\MoneybirdWebhookReceivedEvent;
use Mediact\DataContainer\DataContainer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoneybirdWebhookController extends AbstractController
{
    /**
     * @Route("/moneybird/webhook", name="moneybird_webhook", methods={"POST", "GET"})
     *
     * @param Request                  $request
     * @param EventDispatcherInterface $dispatcher
     *
     * @return Response
     */
    public function index(
        Request $request,
        EventDispatcherInterface $dispatcher
    ): Response {
        $response = new Response();

        if ($request->isMethod('post')) {
            $update = new DataContainer(
                json_decode(
                    $request->getContent(),
                    true
                )
            );

            $action = $update->get('action');
            $event  = new MoneybirdWebhookReceivedEvent(
                $action,
                $update->node('state')
            );

            $dispatcher->dispatch(
                MoneybirdWebhookReceivedEvent::NAME,
                $event
            );
            $dispatcher->dispatch(
                sprintf(
                    '%s.%s',
                    MoneybirdWebhookReceivedEvent::NAME,
                    $action
                ),
                $event
            );

            $response->setContent($action);
        }

        return $response;
    }
}
