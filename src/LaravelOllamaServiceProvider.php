<?php

namespace Bjserranoweb\OllamaLaravelWizard;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LaravelOllamaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ollama.php', 'ollama');
        
        $this->app->singleton('ollama', function ($app) {
            return new OllamaService(config('ollama'));
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../config/ollama.php' => config_path('ollama.php'),
        ], 'config');
    }
}
class OllamaService
{
    protected $config;
    protected $projectPath;
    protected $modelName;
    
    protected $fileTypes = [
        'model' => 'app/Models',
        'controller' => 'app/Http/Controllers',
        'migration' => 'database/migrations',
        'seeder' => 'database/seeders',
        'test' => 'tests/Feature',
        'view' => 'resources/views',
    ];
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->projectPath = base_path();
        $this->modelName = $config['model_name'] ?? 'codellama';
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:11434/',
            'timeout'  => $config['timeout'] ?? 30,
        ]);
    }

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
        }
        
        return $context;
    }

    /**
     * Construye el prompt para la generación de archivos
     */
    protected function buildFileGenerationPrompt($type, $name, $specifications, $context)
    {
        $prompt = "Generate a Laravel $type file with the following specifications:\n\n";
        $prompt .= "Name: $name\n";
        $prompt .= "Specifications: " . json_encode($specifications, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Project Context:\n" . json_encode($context, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Please generate the complete PHP code for this file.";
        
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
     * Obtiene la ruta completa para el nuevo archivo
     */
    protected function getFilePath($type, $name)
    {
        $basePath = base_path($this->fileTypes[$type]['path']);
        
        switch ($type) {
            case 'model':
                return $basePath . '/' . Str::studly($name) . '.php';
            case 'controller':
                return $basePath . '/' . Str::studly($name) . 'Controller.php';
            case 'migration':
                $timestamp = date('Y_m_d_His');
                return $basePath . '/' . $timestamp . '_create_' . Str::snake(Str::plural($name)) . '_table.php';
            default:
                return $basePath . '/' . $name . '.php';
        }
    }

    /**
     * Ejecuta una consulta SQL generada
     */
    public function executeQuery($description)
    {
        // Obtener el esquema de la base de datos
        $schema = $this->getDatabaseSchema();
        
        // Construir el prompt para la consulta
        $prompt = $this->buildQueryPrompt($description, $schema);
        
        // Obtener la respuesta de Ollama
        $response = $this->query($prompt);
        
        // Extraer y ejecutar la consulta SQL
        $query = $this->extractSqlQuery($response['response']);
        
        if ($this->isQuerySafe($query)) {
            try {
                $result = DB::unprepared($query);
                return [
                    'success' => true,
                    'query' => $query,
                    'result' => $result
                ];
            } catch (\Exception $e) {
                throw new \Exception("Error ejecutando la consulta: " . $e->getMessage());
            }
        }
        
        throw new \Exception("La consulta generada no es segura para ejecutar");
    }

    /**
     * Obtiene el esquema completo de la base de datos
     */
    protected function getDatabaseSchema()
    {
        $schema = [];
        
        // Obtener todas las tablas
        $tables = DB::select('SHOW TABLES');
        $dbName = 'Tables_in_ollamaintegration';
        
        foreach ($tables as $table) {
            $tableName = $table->$dbName;
            
            // Obtener información de las columnas
            $columns = DB::select("SHOW COLUMNS FROM {$tableName}");
            
            $schema[$tableName] = [
                'columns' => [],
                'foreign_keys' => []
            ];
            
            foreach ($columns as $column) {
                $schema[$tableName]['columns'][$column->Field] = [
                    'type' => $column->Type,
                    'nullable' => $column->Null === 'YES',
                    'default' => $column->Default,
                    'key' => $column->Key
                ];
            }
            
            // Obtener llaves foráneas
            $foreignKeys = DB::select("
                SELECT 
                    COLUMN_NAME as column_name,
                    REFERENCED_TABLE_NAME as foreign_table,
                    REFERENCED_COLUMN_NAME as foreign_column
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL",
                [config('database.connections.mysql.database'), $tableName]
            );
            
            foreach ($foreignKeys as $fk) {
                $schema[$tableName]['foreign_keys'][] = [
                    'local_column' => $fk->column_name,
                    'foreign_table' => $fk->foreign_table,
                    'foreign_column' => $fk->foreign_column
                ];
            }
        }
        
        return $schema;
    }

    /**
    * Construye el prompt para generar una consulta SQL
    */
    protected function buildQueryPrompt($description, $schema)
    {
        $prompt = "Given the following database schema:\n\n";
        $prompt .= json_encode($schema, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Generate a MySQL query that accomplishes the following:\n";
        $prompt .= $description . "\n\n";
        $prompt .= "Please generate only the MySQL-compatible SQL query, wrapped in SQL code blocks. ";
        $prompt .= "The query should be safe and not include any DELETE or TRUNCATE operations.\n";
        $prompt .= "Use MySQL data types (e.g., INT, VARCHAR, etc.) and MySQL-specific syntax.\n";
        $prompt .= "Example format:\n```sql\nSELECT * FROM users;\n```";
        
        return $prompt;
    }

    /**
    * Envía una consulta a Ollama y obtiene la respuesta
    *
    * @param string $prompt El prompt para enviar a Ollama
    * @return array La respuesta de Ollama
    * @throws \Exception Si hay un error en la comunicación
    */
    protected function query($prompt)
    {
        try {
            $response = $this->client->post('api/generate', [
                'json' => [
                    'model' => $this->config['model_name'],
                    'prompt' => $prompt,
                    'stream' => false
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if (!$result || !isset($result['response'])) {
                throw new \Exception("Respuesta inválida de Ollama");
            }
            
            return $result;
            
        } catch (\Exception $e) {
            throw new \Exception("Error al consultar Ollama: " . $e->getMessage());
        }
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
     * Extrae la consulta SQL de la respuesta
     */
    protected function extractSqlQuery($content)
    {
        if (preg_match('/```sql\s*(.*?)\s*```/s', $content, $matches)) {
            return trim($matches[1]);
        }
        return trim($content);
    }

    /**
     * Verifica si una consulta SQL es segura
     */
    protected function isQuerySafe($query)
    {
        $dangerousKeywords = ['TRUNCATE', 'DELETE'];
        $query = strtoupper($query);
        
        foreach ($dangerousKeywords as $keyword) {
            if (strpos($query, $keyword) !== false) {
                return false;
            }
        }
        
        return true;
    }

    // Métodos existentes...
}
