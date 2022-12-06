<?php

namespace ThirstPlugin\Tables;

class AccessTokenProjectsTable extends BaseTable implements TableInterface
{
    /** @var mixed[] $projects */
    private $projects;

    public static function getUniqueId(): string
    {
        return 'access-token-projects';
    }

    public function setProjects(array $projects)
    {
        $this->projects = $projects;
        return $this;
    }

    public function getHeaders(): array
    {
        return [
            'id', 'title'
        ];
    }

    public function getBaseUrl(): string
    {
        return 'thirst.api';
    }

    public function setData(\flexible_table $table): void
    {
        foreach ($this->projects as $project) {
            $table->add_data([
                $project->id,
                $project->title
            ]);
        }
    }
}