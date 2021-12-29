<?php
namespace core;

use InvalidArgumentException;
use RuntimeException;

class DotEnv{
    protected $path;

    public function __construct(string $path){
        if (!\file_exists($path)){
            throw new InvalidArgumentException("{$path} doesn't exist");
        }
        $this->path = $path;
    }

    public function load(){
        if (!is_readable($this->path)){
            throw new RuntimeException("{$this->path} is not readable");
        }

        $lines = file($this->path, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
        foreach($lines as $line){
            $line = trim($line);
            if (strpos($line, '#') === 0)
                continue;
            
            [$name, $value] = \explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!\array_key_exists($name, $_ENV)){
                $_ENV[$name] = $value;
            }
            
        }
    }
}