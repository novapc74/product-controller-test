<?php

namespace App\EventListener;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

#TODO добавить Monolog для логирования ошибок пря тут...
class ExceptionListener
{
    /**
     * @throws InvalidArgumentException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse();

        $data = [
            'success' => false,
        ];

        // 1. Обработка ошибок валидации (от #[MapRequestPayload])
        if ($exception instanceof UnprocessableEntityHttpException &&
            $exception->getPrevious() instanceof ValidationFailedException) {

            /** @var ValidationFailedException $validationException */
            $validationException = $exception->getPrevious();
            $violations = $validationException->getViolations();

            $data['violations'] = [];
            foreach ($violations as $violation) {
                $data['violations'][$violation->getPropertyPath()] = $violation->getMessage();
            }

            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $response->setData($data);
        } // 2. Обработка HTTP исключений (404, 403 и т.д.)
        elseif ($exception instanceof HttpExceptionInterface) {
            $data['message'] = $exception->getMessage();
            $response->setStatusCode($exception->getStatusCode());
            $response->setData($data);
        } // 3. Критические ошибки сервера (500)
        else {
            $data['message'] = 'Server error';
            #TODO добавить дев режим ...
             $data['debug'] = $exception->getMessage();

            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData($data);
        }

        $event->setResponse($response);
    }
}