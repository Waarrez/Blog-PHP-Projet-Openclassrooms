<?php

namespace Root\P5\Services;

class SlugService
{
    public function generateSlug(string $string): string
    {
        // Convertit en minuscules
        $slug = strtolower($string);
        // Remplace les espaces et autres caractères spéciaux par des tirets
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        // Supprime les tirets en début et fin de chaîne
        $slug = trim($slug, '-');
        return $slug;
    }
}
