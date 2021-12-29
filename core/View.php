<?php 
namespace core;

use core\Exceptions\RequiredFileNotFound;

class View{
    private string $view = '';
    private array $params = [];

    public function __construct(string $view, array $params = []){
        $viewFullPath = Application::VIEWS_DIR() . \DIRECTORY_SEPARATOR . $view . ".php";
        if (!file_exists($viewFullPath))
            throw new RequiredFileNotFound($viewFullPath);
        $this->view = $view;
        $this->params = $params;
    }

    public function render(){
        $viewContent = $this->renderViewContent($this->view, $this->params);
        $layoutContent = $this->renderLayoutContent();
        if ($layoutContent !== Null){
           return \str_replace('{{content}}', $viewContent, $layoutContent);
        }
        return $viewContent;
    }

    private function renderLayoutContent(){
        if (Application::APP()->getController())
            $layout = Application::APP()->getController()->layout();
        else
            $layout = $_SESSION['config']['layout'] ?? Application::APP()->layout();
        if ($layout){
            \ob_start();
                include_once(Application::VIEWS_DIR() . \DIRECTORY_SEPARATOR . "layouts" . \DIRECTORY_SEPARATOR . $layout . ".php");
            return \ob_get_clean();
        }
        return Null;
    }

    private function renderViewContent(string $view, $params = []){
        \ob_start();
        \extract($params);
        include_once(Application::VIEWS_DIR() . \DIRECTORY_SEPARATOR . "{$view}.php");
        return \ob_get_clean();
    }
}