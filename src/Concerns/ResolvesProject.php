<?php

namespace Dew\Cli\Concerns;

use Dew\Cli\Credentials;
use Dew\Cli\Project;

trait ResolvesProject
{
    public function project(): Project
    {
        // The project should be resolved from API endpoint dynamically based
        // on the Dew token, but now in the early stage of development, lets
        // use environment variables from shell instead for fast moving on.
        $project = new Project(getenv('DEW_PROJECT_NAME'), getenv('DEW_PROJECT_REGION'));

        $project->credentialsUsing(new Credentials(
            getenv('ALI_KEY_ID'), getenv('ALI_KEY_SECRET'), getenv('ALI_ACCOUNT_ID')
        ));

        return $project;
    }
}