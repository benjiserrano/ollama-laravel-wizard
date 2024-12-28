<?php

namespace Bjserranoweb\OllamaLaravelWizard\Services;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OllamaFileService extends AbstractOllamaService
{
    protected $fileTypes = [
        'model' => ['path' => 'app/Models', 'extension' => '.php'],
        'controller' => ['path' => 'app/Http/Controllers', 'extension' => '.php'],
        'migration' => ['path' => 'database/migrations', 'extension' => '.php'],
        'seeder' => ['path' => 'database/seeders', 'extension' => '.php'],
        'test' => ['path' => 'tests/Feature', 'extension' => '.php'],
        'view' => ['path' => 'resources/views', 'extension' => '.blade.php'],
        'route' => ['path' => 'routes', 'extension' => '.php'],
        'middleware' => ['path' => 'app/Http/Middleware', 'extension' => '.php'],
    ];


    /**
     * Genera un nuevo archivo basado en el contexto del proyecto
     */
    public function generateFile($type, $name, $specifications = [])
    {
        if (!isset($this->fileTypes[$type])) {
            throw new \Exception("Tipo de archivo no soportado: {$type}");
        }

        // Obtener contexto del proyecto
        $context = $this->getContextForFileType($type);
        
        // Construir el prompt
        $prompt = $this->buildFileGenerationPrompt($type, $name, $specifications, $context);
        
        // Obtener la respuesta de Ollama
        $response = $this->query($prompt);
        
        // Procesar y guardar el archivo
        return $this->saveGeneratedFile($type, $name, $response['response']);
    }

    /**
     * Obtiene el contexto relevante según el tipo de archivo
     */
    protected function getContextForFileType($type)
    {
        $context = [];
        
        switch ($type) {
            case 'model':
                $context['database'] = $this->getDatabaseSchema();
                $context['models'] = $this->scanDirectory(base_path('app/Models'));
                break;
            case 'controller':
                $context['routes'] = $this->scanDirectory(base_path('routes'));
                $context['controllers'] = $this->scanDirectory(base_path('app/Http/Controllers'));
                break;
            case 'migration':
                $context['migrations'] = $this->scanDirectory(base_path('database/migrations'));
                $context['schema'] = $this->getDatabaseSchema();
                break;
            case 'seeder':
                $context['seeds'] = $this->scanDirectory(base_path('database/seeders'));
                $context['schema'] = $this->getDatabaseSchema();
                break;
            case 'test':
                $context['tests'] = $this->scanDirectory(base_path('tests/Feature'));
                break;
            case 'view':
                $context['views'] = $this->scanDirectory(base_path('resources/views'));
                break;
            default:
                break;
        }
        
        return $context;
    }

    /**
     * Escanea un directorio y retorna el contenido de los archivos
     * @param string $path
     * @return array
     */
    protected function scanDirectory($path)
    {
        if (!File::exists($path)) {
            return [];
        }

        $files = File::allFiles($path);
        $contents = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php' || $file->getExtension() === 'blade.php') {
                $contents[$file->getRelativePathname()] = File::get($file->getPathname());
            }
        }

        return $contents;
    }

    /**
     * Construye el prompt para la generación de archivos
     */
    protected function buildFileGenerationPrompt($type, $name, $specifications = '[No specifications]', $context)
    {
        $prompt = "Generate a Laravel $type file with the following specifications:\n\n";
        $prompt .= "Name: $name\n";
        $prompt .= "Specifications: " . json_encode($specifications, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Project Context:\n" . json_encode($context, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Generate the complete PHP code for the $type file related to the $name and the provided specifications.\n";
        $prompt .= "Return ONLY the complete PHP code.\n";
        
        return $prompt;
    }

    /**
     * Guarda el archivo generado en la ubicación correcta
     */
    protected function saveGeneratedFile($type, $name, $content)
    {
        $path = $this->getFilePath($type, $name);
        
        // Crear el directorio si no existe
        File::makeDirectory(dirname($path), 0755, true, true);
        
        // Extraer el código PHP de la respuesta
        $code = $this->extractPhpCode($content);
        
        // Guardar el archivo
        if (File::put($path, $code)) {
            return [
                'success' => true,
                'path' => $path,
                'content' => $code
            ];
        }
        
        throw new \Exception("No se pudo guardar el archivo en: {$path}");
    }

    /**
     * Extrae el código PHP de la respuesta
     */
    protected function extractPhpCode($content)
    {
        if (preg_match('/```php\s*(.*?)\s*```/s', $content, $matches)) {
            return trim($matches[1]);
        }
        return trim($content);
    }

    /**
     * Obtiene la ruta completa para el nuevo archivo
     */
    protected function getFilePath($type, $name)
    {
        $basePath = base_path($this->fileTypes[$type]['path']);
        $extension = $this->fileTypes[$type]['extension'];
        
        switch ($type) {
            case 'model':
                return $basePath . '/' . Str::studly($name) . $extension;
            case 'controller':
                return $basePath . '/' . Str::studly($name) . 'Controller' . $extension;
            case 'migration':
                $timestamp = date('Y_m_d_His');
                return $basePath . '/' . $timestamp . '_create_' . Str::snake(Str::plural($name)) . '_table' . $extension;
            case 'view':
                // Convert dots to directory separators for nested views
                $name = str_replace('.', '/', $name);
                return $basePath . '/' . $name . $extension;
            case 'test':
                return $basePath . '/' . Str::studly($name) . 'Test' . $extension;
            case 'seeder':
                return $basePath . '/' . Str::studly($name) . 'Seeder' . $extension;
            case 'route':
                return $basePath . '/' . $name . $extension;
            case 'middleware':
                return $basePath . '/' . $name . $extension;
            default:
                return $basePath . '/' . $name . $extension;
        }
    }
}
