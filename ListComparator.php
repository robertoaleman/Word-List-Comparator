<?php
/** Word List Comparator  |   Author: Roberto Aleman, ventics.com  **/
class ListComparator {

    /**
     * Compares two word lists (string) and returns an array with the results.
     *
     * @param string $list1_content The content of the first list.
     * @param string $list2_content The content of the second list.
     * @return array An associative array with the results.
     */
    public function compareAndShow(string $list1_content, string $list2_content): array {
        $words1 = $this->normalizeContent($list1_content);
        $words2 = $this->normalizeContent($list2_content);

        return $this->processWords($words1, $words2);
    }

    /**
     * Reads a string, normalizes the words (to lowercase), and returns them as an array.
     *
     * @param string $content The text content to be processed.
     * @return array An array of words.
     */
    private function normalizeContent(string $content): array {
        // Split by newlines, trim whitespace, and convert to lowercase.
        $lines = explode("\n", $content);
        $words = array_map('trim', $lines);
        $words = array_map('mb_strtolower', $words);

        // Remove empty lines.
        return array_filter($words, 'strlen');
    }

    /**
     * Processes words to count repetitions and find the first position.
     *
     * @param array $list1 Array of words from list 1.
     * @param array $list2 Array of words from list 2.
     * @return array An associative array with the results.
     */
    private function processWords(array $list1, array $list2): array {
        $results = [];

        // OPTIMIZATION: Pre-calculate counts once.
        $count1 = array_count_values($list1);
        $count2 = array_count_values($list2);

        // Combine both lists to iterate over all unique words.
        $uniqueWords = array_unique(array_merge(array_keys($count1), array_keys($count2)));

        foreach ($uniqueWords as $word) {
            // Find the first position (0-based index).
            $position1 = array_search($word, $list1);
            $position2 = array_search($word, $list2);

            $results[$word] = [
                'repetitions1' => $count1[$word] ?? 0,
                'repetitions2' => $count2[$word] ?? 0,
                // Add 1 to the position for a 1-based index.
                'position1' => $position1 !== false ? $position1 + 1 : 'Not found',
                'position2' => $position2 !== false ? $position2 + 1 : 'Not found',
            ];
        }

        return $results;
    }

    /**
     * Unifies two word lists, removing duplicates and being case-insensitive.
     *
     * @param string $list1_content The content of the first list.
     * @param string $list2_content The content of the second list.
     * @return array An array of unique and normalized words.
     */
    public function unifyLists(string $list1_content, string $list2_content): array {
        $words1 = $this->normalizeContent($list1_content);
        $words2 = $this->normalizeContent($list2_content);

        // Combine both lists and get the unique values.
        $combinedList = array_merge($words1, $words2);

        // Return only the unique words.
        return array_values(array_unique($combinedList));
    }

    /**
     * Creates a text file with words that are common in both lists.
     *
     * @param array $commonWords An array of common words.
     * @param string $filePath The path for the output file.
     * @return bool True on success, false on failure.
     */
    public function createCommonWordsFile(array $commonWords, string $filePath): bool {
        $content = implode("\n", $commonWords);
        return file_put_contents($filePath, $content) !== false;
    }
}