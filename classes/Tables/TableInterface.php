<?php

namespace ThirstPlugin\Tables;

interface TableInterface
{
    public static function getUniqueId(): string;

    public function getHeaders(): array;

    public function setData(\flexible_table $table): void;

    public function getBaseUrl(): string;
}