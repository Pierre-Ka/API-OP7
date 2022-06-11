<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;


class ExceptionSubscriber implements EventSubscriberInterface
{

    private Security $security;
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, Security $security)
    {
        $this->security = $security;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => 'onException'
        ];
    }

    public function onException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode($this->getStatusCode($exception));
        $response->setContent($this->serializer->serialize($this->getErrorMessage($exception, $response), 'json'));
        $event->setResponse($response);
    }

    private function getStatusCode(Throwable $exception): int
    {
        return $this->determineStatusCode($exception, $this->security->getUser() !== null);
    }

    private function getErrorMessage(Throwable $exception, Response $response): array
    {
        if( get_class($exception) === NotFoundHttpException::class ) {
            $message = "Item or List not found";
        }
        else {
            $message = $exception->getMessage()  ;
        }

        $error = [
            'code' => $response->getStatusCode(),
            'message' => $message
        ];
        return $error;
    }

    private function determineStatusCode(Throwable $exception, bool $isUser): int
    {
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof AuthenticationException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
        } elseif (in_array(get_class($exception), [AccessDeniedException::class , AccessDeniedHttpException::class])) {
            $statusCode = $isUser ? Response::HTTP_FORBIDDEN : Response::HTTP_UNAUTHORIZED;
        } elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }
        return $statusCode === 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $statusCode;
    }
}