<?php

namespace ThirstPlugin\Tables;

abstract class BaseTable
{
    protected $table;

    public function __construct()
    {
        $this->table = new \flexible_table(static::getUniqueId());
        $this->setHeaders($this->getHeaders());
        $this->table->define_baseurl($this->getBaseUrl());
    }

    public function setHeaders(array $headers)
    {
        $columns = $headers = [];
        foreach ($this->getHeaders() as $key => $head) {
            $columns[] = static::getUniqueId() . $key;
            $headers[] = get_string($head, 'local_thirst');
        }
        $this->table->define_headers($headers);
        $this->table->define_columns($columns);
    }

    public function table(): \flexible_table
    {
        return $this->table;
    }

    public function output()
    {
        $this->table->setup();
        $this->setData($this->table);
        $this->table->finish_output();
    }

}