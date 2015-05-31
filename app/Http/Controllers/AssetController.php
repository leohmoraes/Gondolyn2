<?php

use App\Services\AppServices;
use App\Services\AccountServices;
use Illuminate\Filesystem\Filesystem;

class AssetController extends BaseController
{
    public function asPublic($encFileName)
    {
        $fileName = Crypto::decrypt($encFileName);

        $fileSystem = new Filesystem;

        $storagePath = App::storagePath().'/app/';
        $ext = $fileSystem->extension($fileName);
        $fileSize = $fileSystem->size($storagePath.$fileName);

        $contentType = $fileSystem->mimeType($storagePath.$fileName);

        if ( ! $contentType) {
            $contentType = "application/".$ext;
        }

        header("Content-Type: ".$contentType);
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header("Content-Transfer-Encoding: binary");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header("Content-Length: ".$fileSize);
        header("Accept-Ranges: bytes");

        readfile($storagePath.$fileName);
    }

    public function asDownload($encFileName, $encRealFileName)
    {
        $fileName = Crypto::decrypt($encFileName);
        $realFileName = Crypto::decrypt($encRealFileName);

        $fileSystem = new Filesystem;

        $storagePath = App::storagePath().'/app/';
        $ext = $fileSystem->extension($fileName);
        $fileSize = $fileSystem->size($storagePath.$fileName);

        $contentType = $fileSystem->mimeType($storagePath.$fileName);

        if ( ! $contentType) {
            $contentType = "application/".$ext;
        }

        header("Content-Type: ".$contentType);
        header("Content-Disposition: attachment; filename='".$realFileName.'.'.$ext."'");
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header("Content-Transfer-Encoding: binary");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header("Content-Length: ".$fileSize);
        header("Accept-Ranges: bytes");

        readfile($storagePath.$fileName);
    }
}
