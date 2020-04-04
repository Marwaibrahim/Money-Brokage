<?php

namespace App\Exceptions;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
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
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedHttpException) {
            $preException = $exception->getPrevious();
            if ($preException instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                return response()->json(['status'=> ['code'=>401,'sucess'=>false,'message'=>'TOKEN_EXPIRED']],401);
            }
            else if ($preException instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
            {
                return response()->json(['status'=>['code'=>'401','success'=>false,'message' => 'TOKEN_INVALID']],401);
            }
            else if ($preException instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                return response()->json(['status'=>['code'=>'401','success'=>false,'message' =>'TOKEN_BLACKLISTED']],401);
            }
        }
        if ($exception->getMessage() === 'Token not provided')
        {
            return response()->json(['status'=>['code'=>'401','success'=>false,'message' => 'Token not provided']],401);
        }
        if ($exception->getMessage() === 'Token has expired')
        {
            return response()->json(['status'=>['code'=>'401','success'=>false,'message' => 'Token has expired']],401);
        }
        return parent::render($request, $exception);
        }
        // return parent::render($request, $exception);
    }

