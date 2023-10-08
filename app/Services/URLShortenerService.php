<?php

namespace App\Services;

use AshAllenDesign\ShortURL\Classes\Builder;
use AshAllenDesign\ShortURL\Exceptions\ShortURLException;

class URLShortenerService {

    /**
     * @throws ShortURLException
     */
    public function shorten(string $url): string
    {
        $builder = new Builder();

        $shortURLObject = $builder->destinationUrl($url)->make();
        return $shortURLObject->default_short_url;
    }
}
