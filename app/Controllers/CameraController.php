<?php

namespace App\Controllers;

/**
 * Serves the FTP camera image with a stability check.
 *
 * The FTP client overwrites camera1.jpg every ~10 seconds. Reading the file
 * mid-write produces a truncated JPEG. This proxy waits until the file has not
 * been modified for at least STABLE_SECS seconds before serving it. While the
 * file is "hot" (FTP still writing), the last known-good copy is served instead.
 */
class CameraController extends Controller
{
    private const SOURCE  = '/public/uploads/camera1.jpg';
    private const STABLE  = '/storage/cache/camera1_stable.jpg';
    private const STABLE_SECS = 3;

    public function live(): void
    {
        $source = BASE_PATH . self::SOURCE;
        $stable = BASE_PATH . self::STABLE;

        if (!file_exists($source)) {
            http_response_code(404);
            exit;
        }

        $age = time() - filemtime($source);

        if ($age >= self::STABLE_SECS) {
            // File hasn't changed in 3+ seconds — FTP is done. Promote to stable.
            copy($source, $stable);
            $serve = $source;
        } elseif (file_exists($stable)) {
            // FTP is actively writing — serve last known-good copy.
            $serve = $stable;
        } else {
            // No stable copy yet (first run). Serve whatever we have.
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
