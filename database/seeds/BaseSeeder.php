<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

abstract class BaseSeeder extends Seeder
{
    protected $model = null;
    protected $staticData = [];
    protected $numFakeData = 0;

    protected function factory($data)
    {
        if ((new $this->model()) instanceof Model) {
            try {
                $model = new $this->model();
                $model->fill($data);
                $model->save();
            } catch (Exception $ignored) {
                \Log::debug($ignored);
                if ($factory = factory($this->model)) {
                    $model = $factory->create($data);
                }
            }

            return $model;
        }

        return false;
    }

    private function make()
    {
        if (count($this->staticData)) {
            foreach ($this->staticData as $data) {
                $this->factory($data);
            }
        }
    }

    protected function seed($model = null)
    {
        $this->model = $model;
        $this->make();
    }
}
