<?php

namespace JairoJeffersont\Helpers;

/**
 * Class Slugfy
 *
 * Provides a helper method to generate URL-friendly slugs from strings.
 *
 * This class is commonly used to transform names, titles, or labels into
 * lowercase, hyphen-separated strings suitable for URLs, filenames, or identifiers.
 *
 * The slug generation process includes:
 *  - Converting the string to lowercase
 *  - Transliteration of accented characters to ASCII
 *  - Removal of special characters
 *  - Trimming extra whitespace
 *  - Replacing spaces with hyphens
 *
 * @package JairoJeffersont\Helpers
 */
class Slugfy {

    /**
     * Generates a URL-friendly slug from a given string.
     *
     * This method takes an UTF-8 encoded string and converts it into a clean,
     * readable slug by applying the following transformations:
     *  - Converts all characters to lowercase
     *  - Removes accents and diacritics using transliteration
     *  - Removes special characters and symbols
     *  - Trims leading and trailing whitespace
     *  - Replaces one or more spaces with a single hyphen
     *
     * Example:
     *  Input:  "São Paulo Capital"
     *  Output: "sao-paulo-capital"
     *
     * @param string $nome
     *        The input string to be converted into a slug.
     *        Must be UTF-8 encoded.
     *
     * @return string
     *         A sanitized, lowercase, hyphen-separated slug string.
     */
    public static function slug(string $nome): string {
        $nome = strtolower($nome);

        // Remove acentos
        $nome = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);

        // Remove caracteres especiais
        $nome = preg_replace('/[^\p{L}\s-]/u', '', $nome);

        // Remove espaços extras
        $nome = trim($nome);

        // Substitui espaços por hífen
        $nome = preg_replace('/\s+/', '-', $nome);

        return $nome;
    }
}
