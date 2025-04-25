<?php

namespace Denason\Wikipedia\Services;

use Denason\Wikipedia\Exceptions\WikipediaException;
use Denason\Wikipedia\WikipediaInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class WikipediaManager implements WikipediaInterface
{
    protected string $lang = 'en';
    protected bool $useFallback = false;

    /**
     * @throws WikipediaException
     * @throws ConnectionException
     */
    public function getInfo(string $title, string $prop = 'extracts', string $format = 'json', array $extra = []): mixed
    {

        $params = array_merge([
            'action' => 'query',
            'format' => $format,
            'titles' => $title,
            'prop' => $prop,
        ], $extra);

        $response = Http::timeout(5)->get($this->getApiUrl(), $params);

        if ($response->failed()) {
            throw new WikipediaException("Wikipedia API request failed: " . $response->body(), $response->status());
        }

        $data = $response->successful()
            ? ($response->json()['query']['pages'] ?? [])
            : [];


        if ($this->useFallback && $this->isEmptyResult($data, $prop)) {
            $suggestions = $this->suggest($title);
            if (!empty($suggestions)) {

                $params['titles'] = $suggestions[0];
                $response = Http::timeout(5)->get($this->getApiUrl(), $params);
                if ($response->failed()) {
                    throw new WikipediaException("Fallback request to Wikipedia failed: " . $response->body(), $response->status());
                }
                return $response->successful()
                    ? ($response->json()['query']['pages'] ?? [])
                    : [];
            }
        }

        return $data;
    }


    protected function isEmptyResult(array $data, string $prop = ''): bool
    {
        if (empty($data) || array_key_first($data) == '-1') {
            return true;
        }

        $page = current($data);

        $propMap = [
            'extracts' => 'extract',
            'revisions' => 'revisions',
            'categories' => 'categories',
            'pageimages' => 'thumbnail',
            'info' => 'length',
        ];

        $props = explode('|', $prop);

        foreach ($props as $p) {
            $field = $propMap[$p] ?? null;

            if ($field && array_key_exists($field, $page)) {
                $value = $page[$field];

                if (is_string($value) && trim($value) !== '') {
                    return false;
                }

                if (is_array($value) && !empty($value)) {
                    return false;
                }

                if (is_numeric($value) && $value > 0) {
                    return false;
                }
            }
        }

        return true;
    }



    public function infobox(string $title): array
    {

        if (!extension_loaded('dom')) {
            throw new WikipediaException('The ext-dom extension is required to use certain features of this feature. Please install it and try again.');
        }
        if (!extension_loaded('libxml')) {
            throw new WikipediaException('The ext-libxml extension is required to use certain features of this feature. Please install it and try again.');
        }


        $html = $this->html($title);
        if (empty($html)) {
            return [];
        }
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new \DOMXPath($dom);
        $infobox = $xpath->query("//table[contains(@class, 'infobox')]")->item(0);

        if (!$infobox) {
            return [];
        }

        $data = [];

        foreach ($infobox->getElementsByTagName('tr') as $row) {
            $th = $row->getElementsByTagName('th')->item(0);
            $td = $row->getElementsByTagName('td')->item(0);

            if ($th && $td) {
                $key = trim($th->textContent);
                $value = trim($td->textContent);
                $data[$key] = $value;
            }
        }

        return $data;
    }


    public function lang(string $lang): self
    {
        if (!preg_match('/^[a-zA-Z]{2}$/', $lang)) {
            throw new \InvalidArgumentException("Invalid language code: {$lang}");
        }
        $this->lang = $lang;
        return $this;
    }

    public function smart(): self
    {
        $this->useFallback = true;
        return $this;
    }

    public function search(string $query): array
    {
        $response = Http::get($this->getApiUrl(), [
            'action' => 'query',
            'list' => 'search',
            'format' => 'json',
            'srsearch' => $query,
        ]);

        if ($response->failed()) {
            throw new WikipediaException("Wikipedia API request failed: " . $response->body(), $response->status());
        }


        $results = $response->successful() ? $response->json()['query']['search'] ?? [] : [];

        if (empty($results) && $this->useFallback) {
            $suggestions = $this->suggest($query);
            if (!empty($suggestions)) {
                return $this->search($suggestions[0]);
            }
        }

        return collect($results)->map(function ($item) {
            return [
                'title' => $item['title'],
                'snippet' => strip_tags($item['snippet']),
                'url' => $this->url($item['title']),
            ];
        })->toArray();
    }

    public function suggest(string $query): array
    {
        $response = Http::timeout(5)->get("https://{$this->lang}.wikipedia.org/w/api.php", [
            'action' => 'opensearch',
            'format' => 'json',
            'search' => $query,
        ]);
        if ($response->failed()) {
            throw new WikipediaException("Wikipedia API request failed: " . $response->body(), $response->status());
        }


        return $response->successful() ? ($response->json()[1] ?? []) : [];
    }

    public function summary(string $title, array $extra = ['explaintext' => false]): ?string
    {
        return $this->extract($title, 'extracts', array_replace([
            'exintro' => true,
            'explaintext' => false,
        ], $extra));
    }


    public function text(string $title): ?string
    {
        return $this->extract($title, 'extracts', [
            'explaintext' => true,
        ]);
    }


    public function categories(string $title, int $depth = 2): array
    {
        $result = $this->extract($title, 'categories');


        if (is_array($result) && isset($result[0]['title']) && str_starts_with($result[0]['title'], 'Category:Redirects')) {
            $redirectTarget = $this->getRedirectTarget($title);
            if ($redirectTarget) {
                return $this->categories($redirectTarget, $depth);
            }
        }


        return is_array($result) ? $result : [];
    }


    public function imageUrl(string $title): ?string
    {
        $thumb = $this->extract($title, 'pageimages', [
            'pithumbsize' => 500,
        ]);

        return is_array($thumb) ? $thumb['source'] ?? null : null;
    }


    public function url(string $title): string
    {
        return "https://{$this->lang}.wikipedia.org/wiki/" . urlencode($title);
    }

    public function raw(string $title, int $depth = 2): array
    {
        $result = $this->extract($title, 'revisions', ['rvprop' => 'content']);


        if (is_array($result) && isset($result[0]['*']) && str_starts_with(trim($result[0]['*']), '#REDIRECT')) {
            if (preg_match('/\[\[(.*?)\]\]/', $result[0]['*'], $matches)) {
                $redirectTitle = $matches[1];
                return $this->raw($redirectTitle, $depth);
            }
        }


        return is_array($result) ? $result : [];
    }


    protected function getApiUrl(): string
    {
        return "https://{$this->lang}.wikipedia.org/w/api.php";
    }

    public function extract(string $title, string $prop, array $extra = []): mixed
    {
        $params = array_replace([
            'action' => 'query',
            'format' => 'json',
            'titles' => $title,
            'prop' => $prop,
        ], $extra);

        if (($params['action'] ?? '') === 'parse') {
            // فقط از 'page' استفاده کن نه 'titles' و نه 'prop'
            $params['page'] = $params['titles'] ?? $params['page'] ?? '';
            unset($params['titles'], $params['prop']);
            $response = Http::timeout(5)->get($this->getApiUrl(), $params);
            if ($response->failed()) {
                throw new WikipediaException("Wikipedia API request failed: " . $response->body(), $response->status());
            }

            return $response->json();
        }

        return $this->extractPageField($params, $this->defaultFieldFor($prop));
    }


    protected function extractPageField(array $params, string $field, int $depth = 0): mixed
    {
        if ($depth > 1) {
            return null;
        }

        $response = Http::timeout(5)->get($this->getApiUrl(), $params);
        if ($response->failed()) {
            throw new WikipediaException("Wikipedia API request failed: " . $response->body(), $response->status());
        }

        $pages = $response->json()['query']['pages'] ?? [];
        $page = current($pages);
        $result = $page[$field] ?? null;

        if (!$result && $this->useFallback) {
            $title = $params['titles'] ?? null;
            $suggestions = $title ? $this->suggest($title) : [];

            if (!empty($suggestions)) {
                $params['titles'] = $suggestions[0];
                return $this->extractPageField($params, $field, $depth + 1);
            }
        }

        return $result;
    }


    protected function defaultFieldFor(string $prop): string
    {
        return match ($prop) {
            'extracts' => 'extract',
            'revisions' => 'revisions',
            'categories' => 'categories',
            'pageimages' => 'thumbnail',
            default => 'extract', // fallback
        };
    }

    public function html(string $title, int $depth = 2): string
    {
        $response = $this->extract($title, 'text', ['action' => 'parse']);
        $html = $response['parse']['text']['*'] ?? '';

        if ($depth > 0 && str_contains(strtolower($html), 'redirectmsg')) {
            $redirectTarget = $this->getRedirectTarget($title);
            if ($redirectTarget) {
                return $this->html($redirectTarget, $depth - 1);
            }
        }

        return $html;
    }


    protected function getRedirectTarget(string $title): ?string
    {
        $result = $this->extract($title, 'revisions', ['rvprop' => 'content']);
        if (is_array($result) && isset($result[0]['*']) && str_starts_with(trim($result[0]['*']), '#REDIRECT')) {
            if (preg_match('/\[\[(.*?)\]\]/', $result[0]['*'], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function description(string $term): ?string
    {
        $info = $this->getInfo($term, 'description');
        $pageId = key($info);

        return $info[$pageId]['description'] ?? null;
    }
}
