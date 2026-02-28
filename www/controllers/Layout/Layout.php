<?php

namespace Controllers\Layout;

class Layout
{
    public function render(string $tab)
    {
        $layout = 'default';
        $class = '\Controllers\Layout\Tab' . '\\' . ucfirst($tab);

        if (class_exists($class)) {
            ob_start();

            $class::render();

            $content = ob_get_clean();

            // If a custom layout exists for the tab, use it instead of the default one
            if (file_exists(ROOT . '/views/layout/' . $tab . '.html.php')) {
                $layout = $tab;
            }

            include_once(ROOT . '/views/layout/' . $layout . '.html.php');
        } else {
            $this->notFound();
        }
    }

    /**
     *  Render Not found page
     */
    private function notFound()
    {
        include_once(ROOT . '/public/custom_errors/custom_404.html');
    }
}
