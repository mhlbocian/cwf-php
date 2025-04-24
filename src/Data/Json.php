<?php

/*
 * CWF-PHP Framework
 * 
 * File: Data\Json.php
 * Description: Common interface for JSON files
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Data;

use Mhlbocian\CwfPhp\Interfaces\Data\Json as IJson;

final class Json implements IJson {

    private array $json_data = [];
    private bool $json_changed = false;

    #[\Override]
    public function __construct(private string $json_file) {

        if (!\file_exists($json_file)) {
            $this->Create();
        }

        $fcnt = \file_get_contents($json_file);
        $this->json_data = \json_decode($fcnt, true) ?? [];
    }

    private function Create(): void {
        if (!\touch($this->json_file)) {

            throw new \Exception("JSON: create error in file `{$this->json_file}`");
        }
    }

    #[\Override]
    public function Fetch(): array {

        return $this->json_data;
    }

    #[\Override]
    public function Get(string $key): mixed {
        if (!\key_exists($key, $this->json_data)) {

            throw new \Exception("JSON: key `{$key}` does not exist in "
                            . "`{$this->json_file}`");
        }

        return $this->json_data[$key];
    }

    private function Save(): void {
        if (!($fh = \fopen($this->json_file, "w"))) {

            throw new \Exception("JSON: write error in file "
                            . "`{$this->json_file}`");
        }

        \fwrite($fh, \json_encode($this->json_data));
        \fclose($fh);
    }

    #[\Override]
    public function Set(string $key, mixed $value): void {
        $this->json_changed = true;
        $this->json_data[$key] = $value;
    }

    #[\Override]
    public function Unset(string $key): void {
        $data_key = \array_search($key, $this->json_data, true);

        if ($key !== false) {
            $this->json_changed = true;
            \array_splice($this->json_data, $data_key, 1);
        }
    }

    public function __destruct() {
        if ($this->json_changed) {
            $this->Save();
        }
    }
}
