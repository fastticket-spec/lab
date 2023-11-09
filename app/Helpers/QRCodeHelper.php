<?php

namespace App\Helpers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeHelper
{
    public static function getQRCode(string $text, ?string $fileType = 'svg')
    {
        if ($fileType == 'svg') {
            $type = QRCode::OUTPUT_MARKUP_SVG;
        } else if ($fileType == 'png') {
            $type = QRCode::OUTPUT_IMAGE_PNG;
        } else {
            $type = QRCode::OUTPUT_MARKUP_SVG;
        }

        $options = new QROptions(
            [
                'eccLevel' => QRCode::ECC_L,
                'outputType' => $type,
                'version' => 20
            ]
        );

        return (new QRCode($options))->render($text);
    }
}
