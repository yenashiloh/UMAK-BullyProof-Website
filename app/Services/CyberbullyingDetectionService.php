<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CyberbullyingDetectionService
{
    protected $pythonScript;
    protected $pythonInterpreter;

    public function __construct()
    {
        // store both the script path and Python interpreter path from .env
        $this->pythonScript = base_path('app/python/app.py');
        $this->pythonInterpreter = env('PYTHON_PATH');
    }

    /**
     * analyze text for cyberbullying
     */
    public function analyze($text)
    {
        try {
            // input validation
            if (empty($text)) {
                throw new Exception('Empty input text provided');
            }

            // Sanitize the input text to handle special characters
            // This will replace any invalid UTF-8 sequences with the Unicode replacement character
            $sanitizedText = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

            // Log the exact input text
            Log::debug('Cyberbullying Analysis Input Text', [
                'input_text' => $sanitizedText,
                'input_length' => strlen($sanitizedText)
            ]);

            // verify Python script exists
            if (!file_exists($this->pythonScript)) {
                throw new Exception("Python script not found at: {$this->pythonScript}");
            }

            // verify Python interpreter exists
            if (empty($this->pythonInterpreter) || !file_exists($this->pythonInterpreter)) {
                throw new Exception("Python interpreter not found at: {$this->pythonInterpreter}");
            }

            // Only use the stdin method for more reliable passing of text data
            $descriptorSpec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w']   // stderr
            ];

            $command = sprintf(
                '%s %s',
                escapeshellarg($this->pythonInterpreter),
                escapeshellarg($this->pythonScript)
            );

            // Log the command being executed
            Log::debug('Executing Python command', [
                'command' => $command
            ]);

            $process = proc_open($command, $descriptorSpec, $pipes);

            if (!is_resource($process)) {
                throw new Exception("Failed to open process for Python script execution");
            }

            // Directly detect obvious cyberbullying phrases in PHP first
            $obviousPhrases = ['kill yourself', 'go kill yourself', 'fucking stupid', 'worthless', 'loser'];
            $lowercaseText = strtolower($sanitizedText);
            $obviousDetection = false;

            foreach ($obviousPhrases as $phrase) {
                if (strpos($lowercaseText, $phrase) !== false) {
                    Log::debug('Obvious cyberbullying phrase detected in PHP', ['phrase' => $phrase]);
                    $obviousDetection = true;
                    break;
                }
            }

            if ($obviousDetection) {
                // Still run the Python script for logging purposes
                fwrite($pipes[0], $sanitizedText);
                fclose($pipes[0]);

                // Read output but we'll override it
                $output = stream_get_contents($pipes[1]);
                $errors = stream_get_contents($pipes[2]);

                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);

                Log::debug('Overriding Python output due to obvious cyberbullying detected in PHP', [
                    'original_output' => $output,
                    'errors' => $errors
                ]);

                return [
                    'error' => null,
                    'analysisResult' => 'Cyberbullying Detected',
                    'analysisProbability' => 90.0
                ];
            }

            // If no obvious detection, process through Python
            // Use binary mode when writing to stdin to preserve encoding
            fwrite($pipes[0], $sanitizedText);
            fclose($pipes[0]);

            // Read output
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);

            // Close pipes
            fclose($pipes[1]);
            fclose($pipes[2]);

            // Close process
            $returnVar = proc_close($process);

            // Log detailed debug information
            Log::debug('Cyberbullying Analysis Process', [
                'command' => $command,
                'output' => $output,
                'errors' => $errors,
                'return_code' => $returnVar
            ]);

            if ($returnVar !== 0) {
                throw new Exception("Python script execution failed: " . ($errors ?: 'Unknown error'));
            }

            // handle empty output
            if (empty($output)) {
                throw new Exception('No output from Python script');
            }

            // parse JSON response
            $result = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response: ' . json_last_error_msg() . ' - Output: ' . substr($output, 0, 500));
            }

            return [
                'error' => $result['error'] ?? null,
                'analysisResult' => $result['result'] ?? 'Analysis Failed',
                'analysisProbability' => $result['probability'] ?? 0
            ];
        } catch (Exception $e) {
            Log::error('Cyberbullying Analysis Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input_text' => $text,
                'script_path' => $this->pythonScript,
                'python_interpreter' => $this->pythonInterpreter
            ]);

            return [
                'error' => 'Analysis failed: ' . $e->getMessage(),
                'analysisResult' => 'Analysis Failed',
                'analysisProbability' => 0
            ];
        }
    }
}