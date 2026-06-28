<?php

/*
 * CWF-PHP Framework
 * 
 * File: View.php
 * Description: Views class
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\View\Type;
use CwfPhp\CwfPhp\Interfaces\View as IView;
use CwfPhp\CwfPhp\Interfaces\View\View_Object as IObject;

final class View implements IView {

    private IObject $view;

    #[\Override]
    public function __construct(string $view, Type $type = Type::PHP) {
        switch ($type) {
            case Type::PHP:
                $this->view = new View\Php($view);
                break;
            case Type::HTML:
                $this->view = new View\Html($view);
                break;
            default:
                throw new \Error("VIEW: unknown view type");
        }
    }

    #[\Override]
    public function Bind(string $var, mixed $val): View {
        $this->view->Bind($var, $val);

        return $this;
    }

    private function Render(): string {

        return $this->view->Render();
    }

    #[\Override]
    public function __toString(): string {

        return $this->Render();
    }
}
