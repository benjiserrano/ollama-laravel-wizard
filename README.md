
# Ollama Laravel Wizard

**Ollama Laravel Wizard** is a Laravel package that integrates Ollama to automate queries and generate files using large language models (LLMs). Simplify repetitive tasks and boost your workflows with local AI capabilities.

---

## Features
- **Seamless Integration**: Easily connect Laravel with Ollama for local AI-powered automation.
- **Automated Queries**: Execute complex queries with ease.
- **File Generation**: Automatically create custom files based on your requirements.
- **Configurable**: Customize behavior through a simple configuration file.
- **Extensible**: Adapt the package to meet specific project needs.

---

## Requirements
- Laravel 9+
- PHP 8+
- [Ollama installed locally](https://ollama.com/)

---

## Installation
1. Install Ollama on your system. For example, on macOS, run:
   ```bash
   brew install ollama
   ```
   For other operating systems, follow the instructions on the [official Ollama website](https://ollama.com/).

2. Download a model to use with Ollama:
   ```bash
   ollama pull openchat
   ```

3. Install the package via Composer:
   ```bash
   composer require bjserranoweb/ollama-laravel-wizard
   ```

4. Publish the configuration file:
   ```bash
   php artisan vendor:publish --tag="ollama-laravel-config"
   ```

---

## Usage
Here’s an example of how to use the Ollama Laravel Wizard:

```php
use Cloudstudio\Ollama\Facades\Ollama;

// Example of generating a response
$response = Ollama::agent('You are an assistant that helps with coding tasks...')
    ->prompt('Generate a Laravel controller for user authentication')
    ->model('llama2')
    ->options(['temperature' => 0.7])
    ->stream(false)
    ->ask();

echo $response;
```

---

## Configuration
The configuration file `config/ollama.php` allows you to set the default model, API settings, and other options.  

Example configuration:
```php
return [
    'model' => 'llama2',
    'api_url' => 'http://localhost:11434',
    'default_options' => [
        'temperature' => 0.7,
        'max_tokens' => 1000,
    ],
];
```

---

## Contributing
Contributions are welcome! Please submit a pull request or open an issue if you find a bug or have a feature request.

---

## License
This project is licensed under the [MIT License](LICENSE).
