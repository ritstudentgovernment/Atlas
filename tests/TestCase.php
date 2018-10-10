<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var array
     *
     * The $deletes variable is to be set when you create a new object, use it in the tests, and then wish for it to
     * be deleted upon the destruction of the test object. The variables listed here must be present in $this with a
     * visibility of protected or public. It is assumed that these variables are Eloquent models.
     */
    protected $deletes = [];

    /**
     * Delete the Eloquent Models defined in $deletes.
     *
     * @return void
     */
    protected function deleteVariables()
    {
        foreach($this->deletes as $variableToDelete){

            if ($this->$variableToDelete instanceof Model) {

                $this->$variableToDelete->delete();

            }

        }
    }

    /**
     * Upon the destruction of a test, delete the models created in it.
     */
    public function tearDown()
    {

        $this->deleteVariables();

    }

}
