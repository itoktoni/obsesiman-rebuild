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
use GuzzleHttp\Client;

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
        if(!empty(env('BOT_TELEGRAM')) && !empty(env('TELEGRAM_ID'))){

            $client  = new Client();
            $url = "https://api.telegram.org/bot".env("BOT_TELEGRAM")."/sendMessage";//<== ganti jadi token yang kita tadi
            $data    = $client->request('GET', $url, [
            'json' =>[
                "chat_id" => env("TELEGRAM_ID"), //<== ganti dengan id_message yang kita dapat tadi
                "text" =>
                        "File : ".$e->getFile().
                        "\nLine : ".$e->getLine().
                        "\nCode : ".$e->getCode().
                        "\nMessage : ".$e->getMessage().
                        "\nRequest : ".request()->getUri().
                        "\nMethod : ".request()->getMethod()
                ,"disable_notification" => true
                ]
            ]);
        }

        if(request()->hasHeader('authorization')){

            if($e instanceof ValidationException){
                return Notes::validation($e->getMessage());
            }

            if($e instanceof ModelNotFoundException){
                return Notes::error($e->getMessage());
            }

            if($e instanceof NotFoundHttpException){
                return Notes::error($e->getMessage());
            }

            if($e instanceof QueryException){
                return Notes::error($e->getMessage());
            }

            return Notes::error($e->getMessage(), 'Error '.$e->getCode());
        }

        if ($this->isHttpException($e)) {
            return response()->view('errors.custom', ['exception' => $e]);
            return $this->renderHttpException($e);
        } else {
            return parent::render($request, $e);
        }
    }
}
