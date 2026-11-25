<?php

// Local Herd/Valet driver: replicate the Apache .htaccess clean-URL behavior
// so routes like /signin resolve to signin.php.

class LocalValetDriver extends Valet\Drivers\BasicValetDriver
{
    /**
     * Resolve the front controller for the incoming request.
     */
    public function frontControllerPath(string $sitePath, string $siteName, string $uri): ?string
    {
        $uri = rtrim($uri, '/');

        $candidates = [
            $sitePath.$uri,
            $sitePath.$uri.'.php',          // clean URLs: /signin -> /signin.php
            $sitePath.$uri.'/index.php',
            $sitePath.'/index.php',         // catch-all, mirrors .htaccess
        ];

        foreach ($candidates as $candidate) {
            if ($this->isActualFile($candidate)) {
                return $this->serve($candidate, $sitePath);
            }
        }

        return null;
    }

    private function serve(string $candidate, string $sitePath): string
    {
        $_SERVER['SCRIPT_FILENAME'] = $candidate;
        $_SERVER['SCRIPT_NAME'] = str_replace($sitePath, '', $candidate);
        $_SERVER['DOCUMENT_ROOT'] = $sitePath;

        return $candidate;
    }
}
