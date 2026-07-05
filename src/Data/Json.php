<?php

/*
 * CWF-PHP Framework
 * 
 * File: Data\Json.php
 * Description: Common interface for JSON files
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Data;

use CwfPhp\CwfPhp\Interfaces\Data\ConfigFileInterface;

final class Json implements ConfigFileInterface {

    private bool $fileExists = false;
    private bool $fileLoaded = false;
    private bool $fileChanged = false;
    private array $fileData;

    #[\Override]
    public function clear(): void {
        if(!$this->fileExists){
            $this->create();
        }
        
        $this->fileData = [];
        $this->save();
    }
    
    #[\Override]
    public function create(): void {
        if (!$this->fileExists) {
            if (!\touch($this->file)) {

                throw new \Error("DATA[Json]: couldn't create file");
            }
        }

        $this->fileExists = true;
    }

    #[\Override]
    public function exists(): bool {

        return $this->fileExists;
    }

    #[\Override]
    public function fetch(): array {
        $this->load();

        return $this->fileData;
    }

    #[\Override]
    public function get(string $key): mixed {
        $this->load();

        if (!\key_exists($key, $this->fileData)) {
            $err_msg = "DATA[Json]: the key '{$key}' does not exist in the "
                    . "file '{$this->file}'";

            throw new \Exception($err_msg);
        }

        return $this->fileData[$key];
    }

    #[\Override]
    public function set(string $key, mixed $value): void {
        $this->load();
        $this->fileData[$key] = $value;
        $this->fileChanged = true;
    }

    #[\Override]
    public function unset(string $key): void {
        $this->load();
        $data_key = \array_search($key, \array_keys($this->fileData), true);
        if ($data_key !== false) {
            $this->fileChanged = true;
            \array_splice($this->fileData, $data_key, 1);
        }
    }

    private function load(): void {
        if ($this->fileLoaded) {

            return;
        }

        $this->create();
        $file_content = \file_get_contents($this->file);
        $this->fileData = \json_decode($file_content, true) ?? [];
        $this->fileLoaded = true;
    }

    private function save(): void {
        if (!($fh = \fopen($this->file, "w"))) {
            $err_msg = "DATA[Json]: write error in the file "
                    . "'{$this->file}'";

            throw new \Error($err_msg);
        }

        \fwrite($fh, \json_encode($this->fileData));
        \fclose($fh);
    }

    #[\Override]
    public function __construct(public readonly string $file) {
        $this->fileExists = \file_exists($file);
    }

    public function __destruct() {
        if (!$this->fileChanged) {

            return;
        }

        $this->save();
    }
}
