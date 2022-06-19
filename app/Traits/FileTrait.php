<?php
namespace App\Traits;

use App\Models\File;

trait FileTrait
{
    public function files()
    {
        return $this->morphMany(File::class, 'owner_change');
    }

}
