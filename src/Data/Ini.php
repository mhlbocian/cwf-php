<?php

/*
 * CWF-PHP Framework
 * 
 * File: Data\Json.php
 * Description: Common interface for INI files
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Data;

use CwfPhp\CwfPhp\Interfaces\Data\ConfigFileInterface;

final class Ini implements ConfigFileInterface {

    public bool $useSections = false;
    private bool $fileExists = false;
    private bool $fileLoaded = false;
    private bool $fileChanged = false;
    private array $fileData;

    #[\Override]
    public function clear(): void {
        if (!$this->fileExists) {
            $this->create();
        }
        
        $this->fileData = [];
        $this->save();
    }

    #[\Override]
    public function create(): void {
        if (!$this->fileExists) {
            if (!touch($this->file)) {

                throw new \Error("DATA[Ini]: couldn't create file");
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

        return $this->json_data;
    }

    #[\Override]
    public function get(string $key): mixed {
        $this->load();

        if (!\key_exists($key, $this->fileData)) {
            $err_msg = "DATA[Ini]: the key '{$key}' does not exist in the "
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

        $this->Create();

        if (!$this->fileData = \parse_ini_file($this->file, $this->useSections)) {
            $this->fileData = [];
        }

        $this->fileLoaded = true;
    }

    private function processSection(string $name, array $data): string {
        $output = "[{$name}]" . \PHP_EOL;

        foreach ($data as $key => $value) {
            $output .= "{$key} = '{$value}'" . \PHP_EOL;
        }

        return $output;
    }

    private function save(): void {
        $output = "";

        foreach ($this->fileData as $key => $value) {
            if ($this->useSections) {
                $output .= $this->processSection($key, $value) . \PHP_EOL;
            } else {
                $output .= "{$key} = '{$value}'" . \PHP_EOL;
            }
        }

        if (!($fh = \fopen($this->file, "w"))) {
            $err_msg = "DATA[Ini]: write error in the file "
                    . "'{$this->file}'";

            throw new \Error($err_msg);
        }

        \fwrite($fh, $output);
        \fclose($fh);
    }

    #[\Override]
    public function __construct(public readonly string $file) {
        $this->fileExists = file_exists($file);
    }

    public function __destruct() {
        if (!$this->fileChanged) {

            return;
        }

        $this->save();
    }
}
