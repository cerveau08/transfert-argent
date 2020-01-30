<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if($exception instanceof NotFoundHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Resource non trouvée !!'
            ];
            $response = new JsonResponse($data);
            $event->setResponse($response);
        }
        elseif($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];
            $response = new JsonResponse($data);
            $event->setResponse($response);
        }
        else{
            $data = [
                'message' => $exception->getMessage()
            ];
            $response = new JsonResponse($data);
            $event->setResponse($response);
        }
    }
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
