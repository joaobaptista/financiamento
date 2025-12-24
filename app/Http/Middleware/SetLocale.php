<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $supported = (array) config('app.supported_locales', ['pt_BR', 'en', 'es']);

        $locale = $this->resolveLocale($request, $supported)
            ?? (string) config('app.locale', 'pt_BR');

        if (!in_array($locale, $supported, true)) {
            $locale = (string) config('app.fallback_locale', 'en');
        }

        app()->setLocale($locale);

        if (class_exists(\Carbon\Carbon::class)) {
            try {
                \Carbon\Carbon::setLocale($locale);
            } catch (\Throwable) {
                // Ignore invalid locale for Carbon.
            }
        }

        // Persist locale choice if explicitly provided (only when session is available).
        if ($request->hasSession()) {
            $explicit = $request->query('lang') ?? $request->header('X-Locale');
            if (is_string($explicit) && $explicit !== '' && in_array($locale, $supported, true)) {
                $request->session()->put('locale', $locale);
            }
        }

        return $next($request);
    }

    /**
     * @param  list<string>  $supported
     */
    private function resolveLocale(Request $request, array $supported): ?string
    {
        // 1) Explicit: query (?lang=pt_BR) or header (X-Locale: es)
        $explicit = $request->query('lang') ?? $request->header('X-Locale');
        if (is_string($explicit) && $explicit !== '') {
            $explicit = $this->normalizeLocale($explicit);
            if (in_array($explicit, $supported, true)) {
                return $explicit;
            }
        }

        // 2) Session (only when available)
        if ($request->hasSession()) {
            $sessionLocale = $request->session()->get('locale');
            if (is_string($sessionLocale) && $sessionLocale !== '') {
                $sessionLocale = $this->normalizeLocale($sessionLocale);
                if (in_array($sessionLocale, $supported, true)) {
                    return $sessionLocale;
                }
            }
        }

        return null;
    }

    private function normalizeLocale(string $locale): string
    {
        $locale = trim($locale);
        $locale = str_replace('-', '_', $locale);

        // Normalize common Portuguese tags.
        if (strtolower($locale) === 'pt_br' || strtolower($locale) === 'pt_br.utf8') {
            return 'pt_BR';
        }

        // Keep simple locales like "en" or "es".
        if (preg_match('/^[a-z]{2}$/i', $locale) === 1) {
            return strtolower($locale);
        }

        // Normalize region casing: pt_BR, en_US, etc.
        if (preg_match('/^([a-z]{2})_([a-z]{2})$/i', $locale, $m) === 1) {
            return strtolower($m[1]).'_'.strtoupper($m[2]);
        }

        return $locale;
    }
}
