<?php

namespace Fooino\Core\Http\Middleware;

use Fooino\Core\Tasks\Language\SetLanguageTask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Closure;
use DateTimeZone;

class CheckRequestHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (
            blank($request->header('Accept-language')) ||
            !$this->isValidLanguage($request->header('Accept-language'))
        ) {
            $request->headers->set('Accept-language', 'fa');
        }

        config(['app.locale'            => $request->header('Accept-language')]);
        config(['translatable.locale'   => $request->header('Accept-language')]);
        Carbon::setLocale(locale: $request->header('Accept-language'));
        app(SetLanguageTask::class)->run(language: $request->header('Accept-language'));

        if (
            blank($request->header('user-timezone')) ||
            !$this->isValidTimezone($request->header('user-timezone'))
        ) {
            $request->headers->set('user-timezone', 'Asia/Tehran');
        }

        setUserTimezone($request->header('user-timezone'));


        return $next($request);
    }

    private function isValidMimeType(string $mimeType): bool
    {
        return \in_array($mimeType, $this->validMimeTypes());
    }

    private function isValidLanguage(string $language): bool
    {
        return \strpos($language, '-') === false ? \in_array(\strlen($language), [2, 4]) : true;
    }

    private function isValidTimezone(string $timezone): bool
    {
        return \in_array($timezone, DateTimeZone::listIdentifiers());
    }

    private function validMimeTypes(): array
    {
        return [
            // '*/*',
            'text/plain',
            'text/html',
            'text/css',
            'text/xml',

            'multipart/form-data',

            'application/json',
            'application/vnd.api+json',
            'application/ld+json',
            'application/pdf',
            'application/xml',
            'application/zip',
            'application/msword',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/x-www-form-urlencoded',
            'application/xhtml+xml',

            'audio/mpeg',
            'audio/ogg',

            'image/*',
            'image/png',
            'image/webp',
            'image/jpeg',
            'image/jpg',
            'image/gif',
            'image/svg+xml',
        ];
    }
}
