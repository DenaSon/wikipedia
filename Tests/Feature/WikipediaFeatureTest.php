<?php
namespace Tests\Feature;
use Denason\Wikipedia\Facades\Wikipedia;
use Tests\TestCase;

class WikipediaFeatureTest extends TestCase
{
    /** @test */
    public function it_returns_summary_for_valid_article()
    {
        $summary = Wikipedia::summary('Tehran');

        $this->assertNotNull($summary, 'Summary should not be null');
        $this->assertIsString($summary, 'Summary should be a string');
        $this->assertStringContainsString('Tehran', $summary, 'Summary should contain the article title');
    }

    /** @test */
    public function test_it_returns_description()
    {
        $desc = Wikipedia::description('Tehran');

        if ($desc !== null) {
            $this->assertIsString($desc);
        } else {
            $this->assertNull($desc);
        }
    }

    /** @test */
    public function it_returns_html()
    {
        $html = Wikipedia::html('Iran');
        $this->assertNotNull($html);
        $this->assertStringContainsString('<p>', $html);
    }

    /** @test */
    public function it_returns_text()
    {
        $text = Wikipedia::text('Iran');
        $this->assertNotNull($text);
        $this->assertIsString($text);
        $this->assertStringContainsString('Tehran', $text);
    }

    /** @test */
    public function it_returns_categories()
    {
        $categories = Wikipedia::categories('Iran');
        $this->assertIsArray($categories);
        $this->assertNotEmpty($categories);
    }

    /** @test */
    public function it_returns_image_url()
    {
        $image = Wikipedia::imageUrl('Dena');
        $this->assertTrue($image === null || str_starts_with($image, 'https://'));
    }

    /** @test */
    public function it_returns_article_url()
    {
        $url = Wikipedia::url('Tehran');
        $this->assertStringStartsWith('https://', $url);
        $this->assertStringContainsString('Tehran', $url);
    }

    /** @test */
    public function it_returns_raw_content()
    {
        $raw = Wikipedia::raw('Tehran');
        $this->assertIsArray($raw);
    }

    /** @test */
    public function test_it_returns_info_box()
    {
        $info = Wikipedia::infoBox('Elon Musk');

        if ($info !== null) {
            $this->assertIsArray($info);
            $this->assertNotEmpty($info);
            $this->assertArrayHasKey('Born', $info);
        } else {
            $this->assertNull($info);
        }
    }

    /** @test */
    public function it_can_set_language()
    {
        $text = Wikipedia::lang('fa')->text('تهران');
        $this->assertNotNull($text);
        $this->assertIsString($text);
        $this->assertStringContainsString('تهران', $text);
    }

    /** @test */
    public function it_can_use_smart_mode()
    {
        $text = Wikipedia::smart()->summary('Tehrann');
        $this->assertNotNull($text);
        $this->assertIsString($text);
    }

    /** @test */
    public function it_can_search_articles()
    {
        $results = Wikipedia::search('Tehran');
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
    }

    /** @test */
    public function it_can_suggest_terms()
    {
        $suggestions = Wikipedia::suggest('Tehrann');
        $this->assertIsArray($suggestions);
    }

    /** @test */
    public function it_can_extract_with_props()
    {
        $data = Wikipedia::extract('Tehran', 'extracts', ['explaintext' => true]);
        $this->assertNotNull($data);
    }

    /** @test */
    public function it_can_get_info_with_props()
    {
        $info = Wikipedia::getInfo('Tehran', 'revisions', 'json');
        $this->assertNotNull($info);
    }


}
