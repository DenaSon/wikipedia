# Wikipedia Laravel Package

A powerful, developer-friendly Laravel package for accessing Wikipedia’s vast knowledge graph. This package bridges the gap between your Laravel application and structured/unstructured content on Wikipedia. Whether you're building educational tools, integrating intelligent search, or enriching content with real-time knowledge, this package gives you full programmatic access to article text, infoboxes, images, and metadata—across multiple languages.

No more dealing with raw API calls! With this package, you can use a fluent interface, full Laravel Facade support, and smart query enhancements to get exactly the data you want.

## Features

- Retrieve full article content in HTML, plain text, or summary.
- Fully customizable multi-language support (e.g., English, Persian, French, etc.).
- Smart search and suggestions.
- Extract categories, infobox data, and article images.
- Full support for Facades and Interface-based implementation.


## Requirements
- PHP >= 8.0
- Laravel >= 9.x


## Installation

```bash
composer require denason/wikipedia
```

## Usage Examples

You can use the global `wiki()` helper or dependency injection with the interface to access all features.

### 1. Get article summary
```php
$summary = wiki()->summary('Albert Einstein');
echo $summary;
```
**Output:**
> Albert Einstein was a German-born theoretical physicist who developed the theory of relativity, one of the two pillars of modern physics.

### 2. Get full HTML content
```php
$html = wiki()->html('Python (programming language)');
```

### 3. Get plain text content
```php
$text = wiki()->text('Tehran');
```

### 4. Search with suggestions
```php
$results = wiki()->smart()->search('Newton');
```

### 5. Get article image
```php
$image = wiki()->imageUrl('Nikola Tesla');
```

### 6. Get categories
```php
$categories = wiki()->categories('Photosynthesis');
```
- Don't forget to use return to see the output.

### 7. Get infobox data
```php
 $infobox = wiki()->infoBox('iran');
 foreach ($infobox as $key => $info)
{
    echo $key. '=' . $info;
}
```

### 8. Get description (Wikidata)
```php
$desc = wiki()->description('Black hole');
echo $desc;
```
**Output:**
> region of spacetime where gravity is so strong that nothing — not even light — can escape

### 9. Use getInfo for advanced API call
```php
$data = wiki()->getInfo('Mars', 'extracts', 'json', ['exintro' => true, 'explaintext' => true]);
retutn $data;
```
**Output:**
> Array of raw data based on specified parameters.

### 10. Use extract for custom property
```php
$extract = wiki()->extract('Iran', 'extracts', ['explaintext' => true]);
return $extract;
```
**Output:**
> Iran, also called Persia, is a country in Western Asia.

## Available Methods

All methods are accessible via the `wiki()` helper or through dependency injection using the `WikipediaInterface`.

| Method | Description |
|--------|-------------|
| `summary(title, extra)` | Returns a short summary of the article. |
| `html(title)` | Retrieves the full article content in HTML. |
| `text(title)` | Retrieves the full article content as plain text. |
| `search(query)` | Performs a search for related article titles. |
| `suggest(query)` | Returns search autocomplete suggestions. |
| `smart()` | Enables smart suggestion-based search. If a keyword isn't found, it will return the most relevant alternative suggestion automatically. |
| `lang(code)` | Sets the query language (e.g., 'fa', 'en'). Use this when working with non-English titles. For example, for Persian: `wiki()->lang('fa')->summary('دنا');` |
| `categories(title)` | Returns article categories as an array. |
| `imageUrl(title)` | Returns the thumbnail image URL of the article. |
| `url(title)` | Returns the full URL to the article. |
| `raw(title)` | Returns the raw JSON data from Wikipedia API. |
| `infoBox(title)` | Extracts structured data from the article’s infobox. |
| `description(term)` | Returns the Wikidata description of the term. |
| `extract(title, prop, extra)` | Custom extract call from Wikipedia API. |
| `getInfo(title, prop, format, extra)` | General method for accessing Wikipedia API with advanced parameters. |

📌 How to :

### Dependency Injection Example

Instead of using the global `wiki()` helper, you can inject the `WikipediaInterface` directly into your controller, service, or job. This is especially useful when writing testable and decoupled code.

#### Example: Using WikipediaInterface in a Controller

```php
use Denason\Wikipedia\WikipediaInterface;

class ArticleController extends Controller
{
    public function show(WikipediaInterface $wiki)
    {
        $summary = $wiki->summary('Iran');

        return view('article.show', compact('summary'));
    }
}
```

This approach takes advantage of Laravel's service container to resolve the interface to its concrete implementation automatically.

-It also fully supports the Facades

## License
This package is open-sourced software licensed under the [MIT license](LICENSE).
