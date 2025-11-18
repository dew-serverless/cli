<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

use Dew\Cli\Dew;
use Dew\Cli\InteractsWithDew;

class DeleteDatabaseInstance
{
    use InteractsWithDew;

    /**
     * The project ID.
     */
    public int $projectId;

    /**
     * The name of the database instance.
     */
    public string $instance;

    /**
     * Configure project.
     */
    public function forProject(int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Configure database instance.
     */
    public function forInstance(string $name): self
    {
        $this->instance = $name;

        return $this;
    }

    /**
     * Delete the database instance.
     */
    public function delete(): void
    {
        Dew::make()
            ->delete('/api/projects/'.$this->projectId.'/databases/'.$this->instance, [
                'name' => $this->instance,
            ]);
    }
}
