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
    private const SOURCE_TMP    = '/public/uploads/camera1.jpg.tmp';
    private const STABLE        = '/storage/cache/camera1_stable.jpg';
    private const MAX_STABLE_AGE = 300; // force-refresh stable after this many seconds

    public function live(): void
    {
        $source    = BASE_PATH . self::SOURCE;
        $sourceTmp = BASE_PATH . self::SOURCE_TMP;
        $stable    = BASE_PATH . self::STABLE;

        if (!file_exists($source) && !file_exists($sourceTmp)) {
            http_response_code(404);
            exit;
        }

        // Clear PHP's internal stat/file-info cache so filemtime() is fresh.
        clearstatcache(true, $source);
        clearstatcache(true, $sourceTmp);
        clearstatcache(true, $stable);

        $stableExists = file_exists($stable);
        $stableAge    = $stableExists ? (time() - filemtime($stable)) : PHP_INT_MAX;

        // Try primary source, then .tmp as fallback (Reolink "alternately overwrite"
        // writes to one file while the other sits complete from the previous cycle).
        $sourceValid = false;
        $liveSource  = null;
        foreach ([$source, $sourceTmp] as $candidate) {
            if (file_exists($candidate) && @getimagesize($candidate) !== false) {
                $sourceValid = true;
                $liveSource  = $candidate;
                break;
            }
        }

        if ($sourceValid) {
            // Source passes a basic JPEG integrity check — promote and serve it.
            copy($liveSource, $stable);
            $serve = $liveSource;
        } elseif ($stableExists && $stableAge <= self::MAX_STABLE_AGE) {
            // Source is mid-write — serve the last known-good copy.
            $serve = $stable;
        } else {
            // Safety valve: stable is too old (or missing) and both sources are bad.
            // Serve whichever source exists — better a rare partial frame than a frozen image.
            // The JS naturalWidth check on the client will discard it if undecodable.
            $serve = file_exists($source) ? $source : $sourceTmp;
        }

        header('Content-Type: image/jpeg');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($serve);
        exit;
    }
}
