<?php

namespace WKO\Controllers;

/**
 * Name: Render
 * Create the method that renders html views
 * @package Controller
 * @since 1.0.0
 */
abstract class Render
{
    /**
     * Render html files
     * @since 1.0.0
     *
     * @param string $file
     * @param array $file
     * @return void
     */
    public function render( $file, $dados )
    {
        extract($dados);
        ob_start();

        require_once __DIR__ . '/../Views/' . $file;
        $html = ob_get_clean();

        echo $html;
    }
}
