<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Plugins\Notes;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    // public function render($request, Throwable $exception)
    // {
    //     if(isset($exception->getStatusCode()) && in_array($exception->getStatusCode(), [402, 404])){
    //         return response()->view('errors.custom', ['exception' => $exception]);
    //     }
    // }

    public function render($request, Throwable $e)
    {
        Log::error($e->getMessage());
        if(request()->hasHeader('authorization')){

            if($e instanceof ValidationException){
                return response()->json(Notes::validation($e->getMessage()), 422);
            }

            if($e instanceof ModelNotFoundException){
                return response()->json(Notes::error($e->getMessage()), 400);
            }

            if($e instanceof NotFoundHttpException){
                return response()->json(Notes::notFound($e->getMessage()), 404);
            }

            if($e instanceof QueryException){
                return response()->json(Notes::notFound($e->getMessage()), 500);
            }

            return response()->json(Notes::error($e->getMessage()), $e->getCode() != 0 ? $e->getMessage() : 500);
        }

        if ($this->isHttpException($e)) {
            return response()->view('errors.custom', ['exception' => $e]);
            return $this->renderHttpException($e);
        } else {
            return parent::render($request, $e);
        }
    }
}
