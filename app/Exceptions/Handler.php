<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use RuntimeException;

class Handler extends ExceptionHandler
{
    /**
     * AA list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            $this->handleExceptionWhenApplicationIsNotReady();
            Bugsnag::notifyException($e);
            $this->exceptionLogs($e);
        });
    }

     private function handleExceptionWhenApplicationIsNotReady()
     {
         if (! file_exists(__DIR__.'/../../.env')) {
             header('Location: probe.php');
             exit();
         }
     }

     private function exceptionLogs($e){
         if ($this->shouldBeLoggedInDB($e) && isInstall()) {
             // Log exception to database
             \Logger::exception($e);
         }
     }

    private function shouldBeLoggedInDB(Throwable $exception)
    {
        $notAllowedExceptions = [PDOException::class, NotFoundHttpException::class, AuthenticationException::class];
        foreach ($notAllowedExceptions as $notAllowedException) {
            if ($exception instanceof $notAllowedException) {
                return false;
            }
        }

        return true;
    }


}
