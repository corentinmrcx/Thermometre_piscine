<?php

declare(strict_types=1);

namespace Html;

trait StringEscaper
{
    /**
     * Fonction renvoie une chaine de caractère sans caractère spéciaux qui peuvent dégrader la page.
     * @param string $string
     * @return string
     */
    public function escapeString(?string $string): string
    {
        if ($string == null) {
            return "";
        }
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
    }

    public function stripTagsAndTrim(?string $string): string{
        if ($string == null){
            return "";
        }
        return trim(strip_tags($string));
    }
}
