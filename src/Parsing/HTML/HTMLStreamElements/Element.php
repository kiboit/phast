<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


interface Element {

    public function getStartStreamOffset();

    public function getEndStreamOffset();

    public function output();

}
