<?php

namespace App\Models\Observers;

class Role {

    public function deleting($model)
    {
        if ($model->groups())
        {
            $model->groups()->detach();
        }

        return true;
    }
}
