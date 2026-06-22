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

use CwfPhp\CwfPhp\Interfaces\Data\Driver as IDriver;

final class Ini implements IDriver {

    public bool $sections = false;
    private bool $file_exists = false;
    private bool $file_loaded = false;
    private bool $file_changed = false;
    private array $ini_data;

    #[\Override]
    public function Clear(): void {
        if (!$this->file_exists) {
            $this->Create();
        }
        
        $this->ini_data = [];
        $this->Save();
    }

    #[\Override]
    public function Create(): void {
        if (!$this->file_exists) {
            if (!touch($this->file)) {

                throw new \Error("DATA[Ini]: couldn't create file");
            }
        }

        $this->file_exists = true;
    }

    #[\Override]
    public function Exists(): bool {

        return $this->file_exists;
    }

    #[\Override]
    public function Fetch(): array {
        $this->Load();

        return $this->json_data;
    }

    #[\Override]
    public function Get(string $key): mixed {
        $this->Load();

        if (!\key_exists($key, $this->ini_data)) {
            $err_msg = "DATA[Ini]: the key '{$key}' does not exist in the "
                    . "file '{$this->file}'";

            throw new \Exception($err_msg);
        }

        return $this->ini_data[$key];
    }

    #[\Override]
    public function Set(string $key, mixed $value): void {
        $this->Load();
        $this->ini_data[$key] = $value;
        $this->file_changed = true;
    }

    #[\Override]
    public function Unset(string $key): void {
        $this->Load();
        $data_key = \array_search($key, \array_keys($this->ini_data), true);
        if ($data_key !== false) {
            $this->file_changed = true;
            \array_splice($this->ini_data, $data_key, 1);
        }
    }

    private function Load(): void {
        if ($this->file_loaded) {

            return;
        }

        $this->Create();

        if (!$this->ini_data = \parse_ini_file($this->file, $this->sections)) {
            $this->ini_data = [];
        }

        $this->file_loaded = true;
    }

    private function ProcessSection(string $name, array $data): string {
        $output = "[{$name}]" . \PHP_EOL;

        foreach ($data as $key => $value) {
            $output .= "{$key} = '{$value}'" . \PHP_EOL;
        }

        return $output;
    }

    private function Save(): void {
        $output = "";

        foreach ($this->ini_data as $key => $value) {
            if ($this->sections) {
                $output .= $this->ProcessSection($key, $value) . \PHP_EOL;
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
        $this->file_exists = file_exists($file);
    }

    public function __destruct() {
        if (!$this->file_changed) {

            return;
        }

        $this->Save();
    }
}
