<?php

namespace Denason\Wikipedia;

/**
 * Interface WikipediaInterface
 *
 * This interface defines a contract for interacting with the Wikipedia API,
 * including methods for fetching content, metadata, summaries, images,
 * categories, search functionality, and language settings.
 *
 * @package Denason\Wikipedia
 */
interface WikipediaInterface
{
    /**
     * Retrieve detailed information about a Wikipedia article.
     *
     * @param string $title  The title of the article.
     * @param string $prop   The properties to fetch (e.g., "extracts", "revisions", "categories").
     * @param string $format The desired response format (e.g., "json").
     * @param array  $extra  Additional API parameters.
     * @return mixed
     */
    public function getInfo(string $title, string $prop, string $format, array $extra = []): mixed;

    /**
     * Get a short description of the given term, if available.
     *
     * @param string $term The term to describe.
     * @return string|null The description, or null if not found.
     */
    public function description(string $term): ?string;

    /**
     * Fetch specific content from a Wikipedia article by property.
     *
     * @param string $title The article title.
     * @param string $prop  The property to extract (e.g., "extracts", "categories").
     * @param array  $extra Optional additional parameters.
     * @return mixed
     */
    public function extract(string $title, string $prop, array $extra = []): mixed;

    /**
     * Set the language of the Wikipedia API (e.g., 'fa', 'en').
     *
     * @param string $lang ISO 639-1 language code.
     * @return self
     */
    public function lang(string $lang): self;

    /**
     * Enable fallback search using suggestions for invalid or misspelled titles.
     *
     * @return self
     */
    public function smart(): self;

    /**
     * Search Wikipedia for articles related to the given query.
     *
     * @param string $query The search keyword(s).
     * @return array An array of search result entries.
     */
    public function search(string $query): array;

    /**
     * Get autocomplete-like search suggestions for the given query.
     *
     * @param string $query The term to suggest alternatives for.
     * @return array An array of suggested terms.
     */
    public function suggest(string $query): array;

    /**
     * Get a short summary or introduction of a Wikipedia article.
     *
     * @param string $title The article title.
     * @param array $extra Optional parameters like 'explaintext' => true.
     * @return string|null The extracted summary or null if not available.
     */
    public function summary(string $title, array $extra = []): ?string;

    /**
     * Get the full article content as HTML.
     *
     * @param string $title The article title.
     * @return string|null The HTML content or null on failure.
     */
    public function html(string $title): ?string;

    /**
     * Get the full article content as plain text.
     *
     * @param string $title The article title.
     * @return string|null The plain text content or null on failure.
     */
    public function text(string $title): ?string;

    /**
     * Get the list of categories the article belongs to.
     *
     * @param string $title The article title.
     * @return array An array of categories.
     */
    public function categories(string $title): array;

    /**
     * Get the URL of the thumbnail image associated with the article.
     *
     * @param string $title The article title.
     * @return string|null The image URL or null if unavailable.
     */
    public function imageUrl(string $title): ?string;

    /**
     * Get the full Wikipedia URL of the article.
     *
     * @param string $title The article title.
     * @return string The complete article URL.
     */
    public function url(string $title): string;

    /**
     * Get the raw revision or wikitext content of the article.
     *
     * @param string $title The article title.
     * @return array An array containing raw content or metadata.
     */
    public function raw(string $title): array;

    /**
     * Parse and return the infobox data from the article, if available.
     *
     * @param string $title The article title.
     * @return array|null Key-value pairs of infobox data, or null if not found.
     */
    public function infoBox(string $title): ?array;
}
