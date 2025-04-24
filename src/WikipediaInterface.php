<?php

namespace Denason\Wikipedia;

interface WikipediaInterface
{
    public function getInfo(string $title,string $prop,string $format,array $extra = []): mixed;

    /**
     * @param string $term
     * @return string
     */
    public function description(string $term) : ?string;

    public function extract(string $title, string $prop, array $extra = []): mixed;


    /**
     * Set the language of the Wikipedia instance ('fa', 'en' .e.g)
     *
     * @param string $lang
     * @return self
     */
    public function lang(string $lang): self;

    /**
     * Smart Search and use Suggest
     *
     * @return self
     */
    public function smart();

    /**
     * Search for related article titles.
     *
     * @param string $query
     * @return array
     */


    public function search(string $query): array;

    /**
     * Get autocomplete suggestions for a query.
     *
     * @param string $query
     * @return array
     */
    public function suggest(string $query): array;

    /**
     * Get the article summary/introduction.
     *
     * @param string $title
     * @param array $extra
     * @return string|null
     */
    public function summary(string $title,array $extra=[]): ?string;

    /**
     * Get the full article content as HTML.
     *
     * @param string $title
     * @return string|null
     */
    public function html(string $title): ?string;

    /**
     * Get the full article content as plain text.
     *
     * @param string $title
     * @return string|null
     */
    public function text(string $title): ?string;

    /**
     * Get the article categories.
     *
     * @param string $title
     * @return array
     */
    public function categories(string $title): array;

    /**
     * Get the thumbnail image URL of the article.
     *
     * @param string $title
     * @return string|null
     */
    public function imageUrl(string $title): ?string;

    /**
     * Get the full URL to the article.
     *
     * @param string $title
     * @return string
     */
    public function url(string $title): string;

    /**
     * Get the raw JSON data from Wikipedia API for a given title.
     *
     * @param string $title
     * @return array
     */
    public function raw(string $title): array;

    public function infoBox(string $title): ?array;


}
