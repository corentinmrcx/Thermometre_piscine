<?php

declare(strict_types=1);

namespace Html;
class WebPage
{
    use StringEscaper;

    private string $head;
    private string $title;
    private string $body;

    public function __construct(string $title = "")
    {
        $this -> title = $title;
        $this -> head = "";
        $this -> body = "";
    }

    /**
     * Retourne le contenue de HEAD.
     * @return string
     */
    public function getHead(): string
    {
        return $this->head;
    }

    /**
     * Retourne le titre de la page.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Modificateur de la valeur du titre.
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Retourne le contenue du body.
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Fonction qui ajoute du contenu a la balise HEAD.
     * @param string $content
     * @return void
     */
    public function appendToHead(string $content): void
    {
        $this -> head .= $content;
    }

    /**
     * Fonction qui ajoute du CSS.
     * @param string $css
     * @return void
     */
    public function appendCss(string $css): void
    {
        $this -> head .= <<<HTML
        <style>{$css}</style>
        HTML;
    }

    /**
     * Fonction qui ajoute la balise link vers le fichier css.
     * @param string $url
     * @return void
     */
    public function appendCssUrl(string $url): void
    {
        $this -> head .= <<<HTML
        <link rel="stylesheet" href={$url}>
    HTML;
    }

    /**
     * Fonction qui ajoute un script JS.
     * @param string $js
     * @return void
     */
    public function appendJS(string $js): void
    {
        $this -> head .= <<<HTML
        <script>{$js}</script>
        HTML;
    }

    /**
     * Fonction qui ajoute un lien vers un fichier JS.
     * @param string $url
     * @return void
     */
    public function appendJsUrl(string $url): void
    {
        $this -> head .= <<<HTML
        <script src="{$url}"></script>
    HTML;
    }

    /**
     * Fonction qui ajoute du contenue a la balise body.
     * @param string $content
     * @return void
     */
    public function appendContent(string $content): void
    {
        $this -> body .= $content;
    }

    /**
     * Fonction qui convertit en HTML nos différentes instances.
     * @return string
     */
    public function toHTML(): string
    {
        return <<<HTML
        <!doctype html>
        <html lang="fr">
        <head>
        <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>{$this->title}</title>
            {$this -> head}
        </head>
        <body>
          {$this->body}
        </body>
        </html>
HTML;
    }

    /**
     * Fonction qui donne la date et l'heure de la dernière modification du script principale.
     * @return string
     */
    public function getLastModification(): string
    {
        return date("d F Y  H:i:s.", getlastmod());
    }
}
