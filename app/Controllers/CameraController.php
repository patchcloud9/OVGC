<?php

namespace App\Controllers;

/**
 * Serves the FTP camera image with corruption protection.
 *
 * Strategy:
 *  1. Use getimagesize() to verify the JPEG is fully written (not mid-FTP).
 *  2. If valid, promote it to a stable cache copy and serve it.
 *  3. If corrupt/mid-write, serve the last known-good stable copy instead.
 *  4. Safety valve: if the stable copy is more than MAX_STABLE_AGE seconds old,
 *     force-serve the live file anyway to prevent a permanently stuck image.
 */
class CameraController extends Controller
{
    private const SOURCE        = '/public/uploads/camera1.jpg';
    private const STABLE        = '/storage/cache/camera1_stable.jpg';
    private const MAX_STABLE_AGE = 60; // force-refresh stable after this many seconds

    public function live(): void
    {
        $source = BASE_PATH . self::SOURCE;
        $stable = BASE_PATH . self::STABLE;

        if (!file_exists($source)) {
            http_response_code(404);
            exit;
        }

        // Clear PHP's internal stat/file-info cache so filemtime() is fresh.
        clearstatcache(true, $source);
        clearstatcache(true, $stable);

        $stableExists = file_exists($stable);
        $stableAge    = $stableExists ? (time() - filemtime($stable)) : PHP_INT_MAX;

        if ($stableAge > self::MAX_STABLE_AGE) {
            // Safety valve: stable copy is too old — serve live file regardless
            // and promote it, so the image can never be permanently stuck.
            copy($source, $stable);
            $serve = $source;
        } elseif (@getimagesize($source) !== false) {
            // Source passes a basic JPEG integrity check — promote and serve it.
            copy($source, $stable);
            $serve = $source;
        } elseif ($stableExists) {
            // Source is mid-write (corrupt) — serve the last known-good copy.
            $serve = $stable;
        } else {
            // No stable copy yet and source is bad — best effort.
            $serve = $source;
        }

        header('Content-Type: image/jpeg');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($serve);
        exit;
    }
}
