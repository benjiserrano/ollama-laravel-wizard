<?php

namespace Bjserranoweb\OllamaLaravelWizard\Services;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

abstract class AbstractOllamaService
{
    protected $config;
    protected $projectPath;
    protected $modelName;
    protected $client;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->projectPath = base_path();
        $this->modelName = $config['model_name'] ?? 'codellama';
        $this->client = new Client([
            'base_uri' => 'http://localhost:11434/',
            'timeout'  => $config['timeout'] ?? 30,
        ]);
    }

    protected function query($prompt)
    {
        try {
            $response = $this->client->post('api/generate', [
                'json' => [
                    'model' => $this->modelName,
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
} 