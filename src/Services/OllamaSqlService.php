<?php

namespace Bjserranoweb\OllamaLaravelWizard\Services;

use Illuminate\Support\Facades\DB;

class OllamaSqlService extends AbstractOllamaService
{
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
}