<?php

namespace Paradowski\CheckAndSeed;

use Carbon\Carbon;
// Note that this seeder is specifically made to work with laravel's Seeder class,
// so we're assuming you're adding this to a laravel project.
use Illuminate\Database\Seeder;

abstract class StructuralSeederBase extends Seeder
{

    /**
     * Returns the name of the database table this seeder is associated with.
     *
     * @return string The name of the database table
     */
    abstract protected function getTableName();

    /**
     * Determines if a record with the given data already exists in the database.
     *
     * @param array $recordData An array of data to check for
     * @return boolean
     */
    protected function recordAlreadyExists($recordData = [])
    {
        $record = DB::table($this->getTableName())->where($recordData)->get();

        if ($record->count() === 0) {
            return false;
        }

        return true;
    }

    /**
     * Inserts a record in the database using the provided data.
     *
     * @param array $recordData An array of data to use for the record content
     * @param boolean $includeTimestamps Set to false if you don't want to include timestamp values
     * @return void
     */
    protected function insertRecord($recordData = [], $includeTimestamps = true)
    {
        if ($includeTimestamps) {
            $timesamps = [
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
            $recordData = array_merge($recordData, $timesamps);
        }

        DB::table($this->getTableName())->insert($recordData);
    }

    /**
     * Loop through the provided records and attempt to insert each into the database.
     *
     * @param array $records An array of record data (array) to insert
     * @param boolean $includeTimestamps Wether or not to include the current timestamp
     * @return void
     */
    protected function insertRecords($records = [], $includeTimestamps = true)
    {
        if (count($records) === 0) {
            throw new \Exception("No records provided.");
        }

        foreach ($records as $record) {
            if (!$this->recordAlreadyExists($record)) {
                $this->insertRecord($record, $includeTimestamps);
            }
        }
    }
}
