<?php

namespace ThirstPlugin\Traits;

trait IsResource
{

    abstract protected static function getTable(): string;

    abstract public function populate($data);

    /**
     * Updates record based on populated attributes
     *
     * @return void
     */
    public function update()
    {
        self::db()->update_record(static::getTable(), $this);
        return $this;
    }

    /**
     * Creates the record and populates object with newly created id
     *
     * @return void
     */
    public function create()
    {
        $this->id = self::db()->insert_record(static::getTable(), $this);
        return $this;
    }

    /**
     * Deletes record from the database based on its ID
     *
     * @return boolean
     */
    public function delete()
    {
        return self::db()->delete_records(static::getTable(), ['id' => $this->id]);
    }

    /**
     * Get the record based on the data submitted
     *
     * @see https://docs.moodle.org/dev/Data_manipulation_API#get_record
     * @param array $data
     * @return null|array
     */
    public static function get(array $data)
    {
        $result = self::db()->get_records(static::getTable(), $data);
        return $result
            ? array_map(function($record) {
                return new static($record);
            }, $result)
            : null;
    }

    /**
     * Returns all records from the table
     *
     * @return array
     */
    public static function all(): array
    {
        $result = self::db()->get_records(static::getTable());
        return array_map(function($record) {
            return new static($record);
        }, $result);
    }

    /**
     * Find the record single record in the database
     *
     * @param integer $id
     * @return null|object
     */
    public static function find(int $id)
    {
        $result = self::db()->get_record(static::getTable(), ['id' => $id]);
        return $result ? new static($result) : null;
    }

    /**
     * Checks whether the table exists or not before executing query
     *
     * @return boolean
     */
    public static function tableExists(): bool
    {
        $columns = self::db()->get_columns(static::getTable());
        return !empty($columns);
    }
}