<?php

use Denason\Wikipedia\WikipediaInterface;

if (!function_exists('wiki')) {
    /**
     * Get the WikipediaInterface instance from the container.
     *
     * @return WikipediaInterface
     */
    function wiki(): WikipediaInterface
    {
        return app(WikipediaInterface::class);
    }
}
