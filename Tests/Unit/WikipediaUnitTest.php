<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Denason\Wikipedia\Facades\Wikipedia;

class WikipediaUnitTest extends TestCase
{
    public function test_summary_returns_expected_value()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '123' => [
                            'extract' => 'Tehran is the capital of Iran.'
                        ]
                    ]
                ]
            ])
        ]);

        $summary = Wikipedia::summary('Tehran');
        $this->assertIsString($summary);
        $this->assertStringContainsString('Tehran', $summary);
    }

    public function test_extract_returns_expected_value()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '123' => [
                            'extract' => 'This is a plain extract of Tehran.'
                        ]
                    ]
                ]
            ])
        ]);

        $data = Wikipedia::extract('Tehran', 'extracts', ['explaintext' => true]);
        $this->assertIsString($data);
        $this->assertStringContainsString('plain', $data);
    }

    public function test_info_box_returns_data()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'parse' => [
                    'text' => [
                        '*' => '<table class="infobox"><tr><th>Born</th><td>June 28</td></tr></table>'
                    ]
                ]
            ])
        ]);

        $infoBox = Wikipedia::infoBox('Elon Musk');
        $this->assertIsArray($infoBox);
        $this->assertArrayHasKey('Born', $infoBox);
        $this->assertEquals('June 28', $infoBox['Born']);
    }

    public function test_image_url_returns_url()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '123' => [
                            'thumbnail' => [
                                'source' => 'https://upload.wikimedia.org/image.jpg'
                            ]
                        ]
                    ]
                ]
            ])
        ]);

        $url = Wikipedia::imageUrl('Tehran');
        $this->assertIsString($url);
        $this->assertStringContainsString('wikimedia.org', $url);
    }

    public function test_categories_returns_array()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '123' => [
                            'categories' => [
                                ['title' => 'Category:Capitals in Asia'],
                                ['title' => 'Category:Iran']
                            ]
                        ]
                    ]
                ]
            ])
        ]);

        $categories = Wikipedia::categories('Tehran');
        $this->assertIsArray($categories);
        $this->assertTrue(
            in_array('Category:Capitals in Asia', array_column($categories, 'title'))
        );
    }

    public function test_html_returns_html_content()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'parse' => [
                    'text' => [
                        '*' => '<p>Tehran is the capital of Iran.</p>'
                    ]
                ]
            ])
        ]);

        $html = Wikipedia::html('Tehran');
        $this->assertIsString($html);
        $this->assertStringContainsString('<p>', $html);
    }

    public function test_raw_returns_wikitext()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '123' => [
                            'revisions' => [
                                [
                                    'slots' => [
                                        'main' => [
                                            '*' => '== Tehran =='
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
        ]);

        $raw = Wikipedia::raw('Tehran');


        $text = $raw[0]['slots']['main']['*'] ?? null;

        $this->assertIsString($text);
        $this->assertStringContainsString('Tehran', $text);
    }





    public function test_url_returns_full_url()
    {
        $url = Wikipedia::url('Tehran');
        $this->assertStringContainsString('wikipedia.org', $url);
        $this->assertStringContainsString('Tehran', $url);
    }

    public function test_description_returns_expected_value()
    {
        Http::fake([
            'https://*.wikidata.org/*' => Http::response([
                'entities' => [
                    'Q123' => [
                        'descriptions' => [
                            'en' => ['value' => 'Capital and largest city in Iran']

                        ]
                    ]
                ]
            ])
        ]);

        $desc = Wikipedia::description('Tehran');
        $this->assertStringContainsString('Capital', $desc);
        $this->assertStringContainsString('Iran', $desc);
    }

    public function test_search_returns_results()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                'query' => [
                    'search' => [
                        ['title' => 'Tehran', 'snippet' => 'Tehran is the capital of Iran'],
                        ['title' => 'Tehran Province', 'snippet' => 'Province around Tehran']
                    ]
                ]
            ])
        ]);

        $results = Wikipedia::search('Tehran');
        $this->assertIsArray($results);
        $this->assertTrue(
            in_array('Tehran', array_column($results, 'title')),
            'Search results should contain a title "Tehran"'
        );
    }


    public function test_suggest_returns_suggestions()
    {
        Http::fake([
            'https://*.wikipedia.org/*' => Http::response([
                1,
                ['Tehran', 'Tehrani', 'Tehrangeles']
            ])
        ]);

        $suggestions = Wikipedia::suggest('Tehrn');
        $this->assertIsArray($suggestions);
        $this->assertContains('Tehran', $suggestions);
    }
}
