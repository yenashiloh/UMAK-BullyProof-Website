<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;
use Illuminate\Support\Facades\Log;

class CyberbullyingClassifier
{
    private $keywords = [];
    private $specialCharMappings;
    private $commonPrefixes;
    private $commonSuffixes;
    private const MAX_WORD_LENGTH = 100; // Increased to accommodate multi-word phrases
    private const CHUNK_SIZE = 1000;
    private $wordSeparators = ['-', '_', ' ', '.', '$'];

    private $spacedPatterns = [
        'n i g g a' => true,
        'n a z i' => true,
        'j e w' => true,
        'f a g' => true,
        'c o o n' => true,
        'a s s' => true,
        'r a p e' => true,
        'd i c k' => true,
        'p o r n' => true,
        'p e n i s' => true,
        'h o e' => true,
        's l u t' => true,
        'slut' => true,
        't w a t' => true,
        't w 4 t' => true,
        'twa t' => true,
        'tw at' => true,
        'c u m' => true,
        'f ag' => true,
        'fa g' => true,
        'n ig' => true,
        'ni g' => true,
        'f u c k' => true,
        's hit' => true,
        'Ching Chong' => true
    ];

    public function __construct()
    {
        $this->initializeSpecialCharMappings();
        $this->initializeCommonAffixes();
        $this->loadDatasets();
    }

    private function initializeSpecialCharMappings()
    {
        $this->specialCharMappings = [
            'a' => ['@', '4', '*', 'α', 'Α', '$'],
            'e' => ['3', '*', 'є', 'ε'],
            'i' => ['1', '!', '*', 'í', 'ί'],
            'o' => ['0', '*', 'ο', 'σ'],
            's' => ['$', '5', 'ѕ', 'z'],
            'l' => ['1', '|', '!', '/'],
            't' => ['7', '+'],
            'b' => ['8', 'β'],
            'g' => ['9', 'q'],
            'u' => ['v', 'υ'],
            'v' => ['u', 'ν'],
            'x' => ['×', '×'],
            'h' => ['#'],
            'k' => ['c'],
            'c' => ['k'],
            'n' => ['ñ'],
            'y' => ['j']
        ];
    }

    private function checkSpacedPatterns($text)
    {
        $text = strtolower($text);
        foreach ($this->spacedPatterns as $pattern => $value) {
            // Check exact match
            if ($text === strtolower($pattern)) {
                return true;
            }
            
            // Check pattern without spaces
            $noSpacePattern = str_replace(' ', '', strtolower($pattern));
            if (str_replace(' ', '', $text) === $noSpacePattern) {
                return true;
            }
            
            // Check pattern with varying number of spaces
            $flexiblePattern = preg_quote($pattern, '/');
            $flexiblePattern = str_replace(' ', '\s*', $flexiblePattern);
            if (preg_match("/^{$flexiblePattern}$/i", $text)) {
                return true;
            }
        }
        return false;
    }


    private function initializeCommonAffixes()
    {
        $this->commonPrefixes = ['ka', 'ma', 'pa', 'na', 'pina', 'nag', 'paka', 'naka', 'mag', 'pag'];
        $this->commonSuffixes = ['an', 'han', 'in', 'ers', 'ski', 'ing', 'hin', 'ng'];
    }

    private function generateAllVariations($word)
    {
        $variations = [];
        $word = trim($word);
        
        if (empty($word) || strlen($word) > self::MAX_WORD_LENGTH) {
            return $variations;
        }

        // Generate separator variations first
        $variations = $this->generateSeparatorVariations($word);

        // For each variation, generate character replacements
        $baseVariations = $variations;
        foreach ($baseVariations as $baseWord) {
            $lowerWord = strtolower($baseWord);
            
            // Generate special character variations
            foreach ($this->specialCharMappings as $letter => $replacements) {
                if (strpos($lowerWord, $letter) !== false) {
                    foreach ($replacements as $replacement) {
                        $variations[] = str_replace($letter, $replacement, $lowerWord);
                    }
                }
            }

            // For multi-word phrases
            if (strpos($baseWord, ' ') !== false) {
                $words = explode(' ', $baseWord);
                if (count($words) <= 3) {
                    // Add variations with different word combinations
                    $variations[] = implode('', $words); // No spaces
                    $variations[] = implode('-', $words); // With dashes
                    $variations[] = implode('_', $words); // With underscores
                }
            }

            // Handle common affixes
            foreach ($this->commonPrefixes as $prefix) {
                $variations[] = $prefix . $lowerWord;
            }

            foreach ($this->commonSuffixes as $suffix) {
                $variations[] = $lowerWord . $suffix;
            }

            // Add reversed character variations for simple obfuscation
            if (strlen($lowerWord) <= 10) { // Only for shorter words to avoid too many variations
                $variations[] = strrev($lowerWord);
            }

            // Handle repeating characters
            $variations[] = preg_replace('/(.)\1+/', '$1', $lowerWord);
        }

        // Add normalized versions
        foreach ($variations as $variation) {
            $variations[] = $this->normalizeText($variation);
        }

        return array_unique($variations);
    }
    private function loadDatasets()
    {
        try {
            if (Cache::has('cyberbullying_keywords')) {
                $this->keywords = Cache::get('cyberbullying_keywords');
                return;
            }

            $excelFile = storage_path('app/public/cyberbullying_datasets.xlsx');
            
            if (!file_exists($excelFile)) {
                throw new Exception("Excel file not found: $excelFile");
            }

            $spreadsheet = IOFactory::load($excelFile);
            $keywords = [];

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                if (stripos($sheetName, 'Tagalog') !== false || stripos($sheetName, 'English') !== false) {
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                    $highestRow = $sheet->getHighestRow();
                    
                    for ($startRow = 2; $startRow <= $highestRow; $startRow += self::CHUNK_SIZE) {
                        $endRow = min($startRow + self::CHUNK_SIZE - 1, $highestRow);
                        $data = $sheet->rangeToArray("A{$startRow}:D{$endRow}", null, true, false);
                        
                        foreach ($data as $row) {
                            foreach ($row as $cell) {
                                if (!empty($cell) && strlen($cell) <= self::MAX_WORD_LENGTH) {
                                    // Store variations with different separators
                                    foreach ($this->generateSeparatorVariations($cell) as $variation) {
                                        $keywords[strtolower($variation)] = true;
                                    }
                                    
                                    // Store normalized versions
                                    $normalizedWord = $this->normalizeText($cell);
                                    $keywords[$normalizedWord] = true;
                                    
                                    // Generate and store all possible variations
                                    foreach ($this->generateAllVariations($cell) as $variation) {
                                        $keywords[strtolower($variation)] = true;
                                    }
                                }
                            }
                        }
                        
                        gc_collect_cycles();
                    }
                }
            }

            $this->keywords = $keywords;
            Cache::put('cyberbullying_keywords', $this->keywords, now()->addDay());
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            gc_collect_cycles();

            Log::info("Datasets loaded successfully. Total keywords: " . count($this->keywords));
        } catch (Exception $e) {
            Log::error("Error loading datasets: " . $e->getMessage());
            $this->keywords = [];
        }
    }

    private function generateSeparatorVariations($word)
    {
        $variations = [];
        $word = trim($word);
        
        if (empty($word)) {
            return $variations;
        }

        // Add original word
        $variations[] = $word;
        
        // Generate variations with different separators
        foreach ($this->wordSeparators as $separator) {
            // Replace all possible separators with the current separator
            $variation = str_replace($this->wordSeparators, $separator, $word);
            $variations[] = $variation;
            
            // Also add version without any separator
            $variations[] = str_replace($separator, '', $variation);
        }
        
        // Add lowercase versions
        foreach ($variations as $variation) {
            $variations[] = strtolower($variation);
        }

        // For multi-word phrases, add variations with different word orderings
        if (strpos($word, ' ') !== false) {
            $words = explode(' ', $word);
            if (count($words) <= 3) { // Limit to phrases of 3 words or less
                // Add original phrase with normalized spacing
                $variations[] = implode(' ', $words);
                
                // Add version without spaces
                $variations[] = implode('', $words);
                
                // Add version with dashes
                $variations[] = implode('-', $words);
            }
        }

        return array_unique($variations);
    }

    public function detectCyberbullying(string $text): array
    {
        if (empty($text)) {
            return $this->getEmptyResult();
        }

        $text = substr($text, 0, 2000); // Increased limit to handle longer text
        $detectedWords = [];
        $cyberbullyingWordCount = 0;

        // Split text into words and phrases
        $words = preg_split('/[\s,]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Check individual words and their variations
        foreach ($words as $word) {
            if (empty($word) || strlen($word) > self::MAX_WORD_LENGTH) {
                continue;
            }

            if ($this->checkWordAndVariations($word, $detectedWords)) {
                $cyberbullyingWordCount++;
            }
        }

        // Check phrases up to 3 words
        for ($windowSize = 2; $windowSize <= 3; $windowSize++) {
            for ($i = 0; $i <= count($words) - $windowSize; $i++) {
                $phrase = implode(' ', array_slice($words, $i, $windowSize));
                if (strlen($phrase) <= self::MAX_WORD_LENGTH) {
                    if ($this->checkWordAndVariations($phrase, $detectedWords)) {
                        $cyberbullyingWordCount++;
                    }
                }
            }
        }

        $totalWords = count($words);
        $cyberbullyingPercentage = $totalWords > 0 ? ($cyberbullyingWordCount / $totalWords) * 100 : 0;

        // Convert all detected words to lowercase
        $detectedWords = array_map('strtolower', array_unique($detectedWords));

        return [
            'isCyberbullying' => $cyberbullyingWordCount > 0,
            'cyberbullyingPercentage' => round($cyberbullyingPercentage, 2),
            'detectedWords' => $detectedWords,
            'totalWordsAnalyzed' => $totalWords,
            'offensiveWordCount' => $cyberbullyingWordCount
        ];
    }

    private function checkWordAndVariations($word, &$detectedWords): bool
    {
        $word = trim($word);
        
        // Check original word
        if ($this->isOffensiveWord($word)) {
            $detectedWords[] = strtolower($word);
            return true;
        }

        // Check all possible variations including separators
        foreach ($this->generateSeparatorVariations($word) as $separatorVariation) {
            if ($this->isOffensiveWord($separatorVariation)) {
                $detectedWords[] = strtolower($word);
                return true;
            }

            // Check all character variations for each separator variation
            foreach ($this->generateAllVariations($separatorVariation) as $variation) {
                if ($this->isOffensiveWord($variation)) {
                    $detectedWords[] = strtolower($word);
                    return true;
                }
            }
        }

        return false;
    }

    private function isOffensiveWord(string $word): bool
    {
        if (empty($word)) {
            return false;
        }

        $word = strtolower($word);

        // Check against spaced patterns first
        if ($this->checkSpacedPatterns($word)) {
            return true;
        }

        // Check original word
        if (isset($this->keywords[$word]) || isset($this->spacedPatterns[$word])) {
            return true;
        }

        // Check normalized version
        $normalizedWord = $this->normalizeText($word);
        return isset($this->keywords[$normalizedWord]) || isset($this->spacedPatterns[$normalizedWord]);
    }

    private function normalizeText($text)
    {
        if (empty($text)) {
            return '';
        }

        $text = strtolower(trim($text));
        
        // Normalize spaces and separators
        $text = str_replace($this->wordSeparators, '', $text);
        
        // Remove repeated characters
        $text = preg_replace('/(.)\1{2,}/', '$1', $text);
        
        // Normalize special characters
        foreach ($this->specialCharMappings as $letter => $replacements) {
            $text = str_replace($replacements, $letter, $text);
        }

        return $text;
    }

    private function getEmptyResult(): array
    {
        return [
            'isCyberbullying' => false,
            'cyberbullyingPercentage' => 0,
            'detectedWords' => [],
            'totalWordsAnalyzed' => 0,
            'offensiveWordCount' => 0
        ];
    }
}