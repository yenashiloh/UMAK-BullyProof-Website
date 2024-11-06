<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;
use Illuminate\Support\Facades\Log;

class CyberbullyingClassifier
{
    private $keywords;
    private $wordCounts;
    private $classCounts;
    private $vocabularySize;
    private $spacedWords;

    public function __construct()
    {
        $this->loadDatasets();
        
        $this->spacedWords = ['n i g g a', 'n a z i', 'j e w', 'f a g', 'c o o n', 'a s s', 'r a p e', 'di ck', 'd i c k', 'p o r n', 'p e n i s', 'h o e', 's l u t', 'slu t', 't w a t', 't w 4 t',
        'twa t', 'tw at', 'c u m', 'f ag', 'fa g', 'n ig', 'ni g', 'f u c k', 's hit', 'Ching Chong'];
    }

    private function loadDatasets()
    {
        try {
            $excelFile = storage_path('app/public/cyberbullying_datasets.xlsx');
            
            if (!file_exists($excelFile)) {
                throw new Exception("Excel file not found: $excelFile");
            }

            $spreadsheet = IOFactory::load($excelFile);

            // Get all sheet names
            $sheetNames = $spreadsheet->getSheetNames();
            Log::info("Available sheets in the Excel file: " . implode(', ', $sheetNames));

            $combinedDataset = [];

            foreach ($sheetNames as $sheetName) {
                if (stripos($sheetName, 'Tagalog') !== false || stripos($sheetName, 'English') !== false) {
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                    $data = $sheet->toArray();
                    
                    // Remove the header row
                    array_shift($data);

                    foreach ($data as $row) {
                        foreach ($row as $cell) {
                            if (!empty($cell)) {
                                $combinedDataset[] = $cell;
                            }
                        }
                    }
                }
            }

            $this->keywords = array_flip(array_map(function($word) {
                return strtolower(str_replace(' ', '', $word));
            }, $combinedDataset));

            Log::info("Datasets loaded successfully. Total keywords: " . count($this->keywords));
        } catch (Exception $e) {
            Log::error("Error loading datasets: " . $e->getMessage());
            $this->keywords = [];  // Initialize with an empty array to prevent further errors
        }
    }

    
    public function train(array $texts, array $labels)
    {
        foreach ($texts as $index => $text) {
            $label = $labels[$index];
            $this->classCounts[$label] = ($this->classCounts[$label] ?? 0) + 1;
            $words = $this->tokenize($text);
            foreach ($words as $word) {
                $this->wordCounts[$label][$word] = ($this->wordCounts[$label][$word] ?? 0) + 1;
            }
        }

        Cache::put('cyberbullying_word_counts', $this->wordCounts, now()->addDay());
        Cache::put('cyberbullying_class_counts', $this->classCounts, now()->addDay());
    }

    private function tokenize(string $text): array
    {
        $words = preg_split('/\W+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        foreach ($words as $word) {
            $result[] = $word;
            $result[] = str_replace(' ', '', $word);
        }
        return array_unique($result);
    }

    public function classify(string $text): array
    {
        $words = $this->tokenize($text);
        $scores = [
            'cyberbullying' => log(($this->classCounts['cyberbullying'] ?? 0) + 1),
            'not_cyberbullying' => log(($this->classCounts['not_cyberbullying'] ?? 0) + 1)
        ];

        foreach ($words as $word) {
            foreach (['cyberbullying', 'not_cyberbullying'] as $class) {
                $wordCount = ($this->wordCounts[$class][$word] ?? 0) + 1;
                $totalWords = array_sum($this->wordCounts[$class] ?? []) + $this->vocabularySize;
                $scores[$class] += log($wordCount / $totalWords);
            }
        }

        $totalScore = array_sum(array_map('exp', $scores));
        $cyberbullyingProbability = exp($scores['cyberbullying']) / $totalScore;

        return [
            'isCyberbullying' => $cyberbullyingProbability > 0.5,
            'cyberbullyingProbability' => $cyberbullyingProbability,
            'cyberbullyingPercentage' => $this->calculateCyberbullyingPercentage($words),
        ];
    }

    private function calculateCyberbullyingPercentage(array $words): float
    {
        $cyberbullyingScore = 0;
        foreach ($words as $word) {
            if (isset($this->keywords[strtolower($word)])) {
                $cyberbullyingScore++;
            }
        }
        return $words ? ($cyberbullyingScore / count($words)) * 100 : 0;
    }

    public function detectCyberbullying(string $text): array
    {
        $words = $this->tokenize($text);
        $detectedWords = [];
        $totalWords = count($words);
        $cyberbullyingWordCount = 0;

        foreach ($words as $word) {
            if (isset($this->keywords[$word])) {
                $detectedWords[] = $word;
                $cyberbullyingWordCount++;
            } else {
                // check for words with spaces
                $spacedWord = implode(' ', str_split($word));
                if (isset($this->keywords[str_replace(' ', '', $spacedWord)])) {
                    $detectedWords[] = $spacedWord;
                    $cyberbullyingWordCount++;
                }
            }
        }

        // check for spaced words
        foreach ($this->spacedWords as $spacedWord) {
            if (stripos($text, $spacedWord) !== false) {
                $detectedWords[] = $spacedWord;
                $cyberbullyingWordCount++;
            }
        }

        $cyberbullyingPercentage = $totalWords > 0 ? ($cyberbullyingWordCount / $totalWords) * 100 : 0;

        return [
            'isCyberbullying' => $cyberbullyingWordCount > 0,
            'cyberbullyingPercentage' => $cyberbullyingPercentage,
            'detectedWords' => array_unique($detectedWords),
        ];
    }
}