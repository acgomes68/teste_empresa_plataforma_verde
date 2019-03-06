<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Http\Controllers\ImportController;

class ImportQueue extends Job
{

    protected $import;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        $this->import = new ImportController();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->import->importall();
    }
}
