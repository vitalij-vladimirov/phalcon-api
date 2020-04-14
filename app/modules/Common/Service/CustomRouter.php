<?php
declare(strict_types=1);

namespace Common\Service;

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Mvc\RouterInterface;
use Common\BaseClasses\Injectable;
use Common\ApiException\ApiException;
use Common\ApiException\NotFoundApiException;
use Common\Entity\RequestEntity;
use Common\File;
use Common\Text;
use Common\Variable;

final class CustomRouter extends Injectable implements RouterInterface
{
    public function getRoutes(Micro $app): void
    {
        try {
            $this->runRequest($this->getRequest(), $app);
        } catch (ApiException $exception) {
            $response = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ];

            if (count($exception->getData())) {
                $response['data'] = $exception->getData();
            }

            (new Response())
                ->setStatusCode($exception->getHttpCode(), $exception->getMessage())
                ->setJsonContent($response)
                ->send()
            ;

            exit;
        }
    }

    private function getRequest(): RequestEntity
    {
        $modulesDir = $this->di->get('config')->application->modulesDir;

        [$urlPath] = explode('?', $this->request->getURI());

        $request = (new RequestEntity())
            ->setMethod(Text::lower($this->request->getMethod()))
            ->setQuery(Variable::restoreArrayTypes($this->request->getQuery()))
            ->setPath($urlPath)
        ;

        $urlSplitter = explode('/', $urlPath);

        if (count($urlSplitter) < 2 || (count($urlSplitter) < 3 && $urlSplitter[1] === $request::REQUEST_TYPE_API)) {
            throw new NotFoundApiException();
        }

        if ($urlSplitter[1] === $request::REQUEST_TYPE_API) {
            $request
                ->setType($request::REQUEST_TYPE_API)
                ->setModule(Text::camelize($urlSplitter[2]))
                ->setParams(Variable::restoreArrayTypes(array_slice($urlSplitter, 3)))
            ;
        } else {
            $request
                ->setType($request::REQUEST_TYPE_VIEW)
                ->setModule(Text::camelize($urlSplitter[1]))
                ->setParams(Variable::restoreArrayTypes(array_slice($urlSplitter, 2)))
            ;
        }

        if (!File::exists($modulesDir . '/' . $request->getModule())) {
            throw new NotFoundApiException();
        }

        return $request;
    }

    private function runRequest(RequestEntity $request, Micro $app): Micro
    {
        $routesClass = '\\' . $request->getModule() . '\\Config\Routes';
        if (!class_exists($routesClass)) {
            throw new NotFoundApiException();
        }

        $responseData = (new $routesClass($request))->get();

        $app->{$request->getMethod()}(
            $request->getPath(),
            static function () use ($app, $responseData) {
                // TODO: describe different data types
                $app->response
                    ->setJsonContent($responseData)
                    ->send()
                ;
            }
        );

        return $app;
    }
}
