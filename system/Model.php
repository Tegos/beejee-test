<?php
/**
 * User: Zemlyansky Alexander <astrolog@online.ua>
 */

namespace app\system;

use function system\dbConnect;

/**
 * Class Model
 * The base Model object
 *
 * It is not an active record object and there's no query builder, since I have not much time for doing it. But a kind of model anyway.
 */
class Model extends Object {
    protected $errors;

    public function tableName() {
        return null;
    }

    /**
     * Validation process of a single record data, must be overriden in a child model to perform a check
     *
     * @return bool
     */
    protected function validate($data) {
        return true;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function select(string $fields, $where = null, $order = null, $limit = null, $startFrom = null) {
        $where = $where ? " WHERE $where" : null;
        $order = $order ? " ORDER BY $order" : null;
        $limit = $limit ? (" LIMIT " . ($startFrom ? "$startFrom, " : null) . $limit) : null;

        $result = mysqli_query(dbConnect(), "SELECT $fields FROM ". $this->tableName() . " $where $order $limit");

        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function insert(array $data) {
        list($fields, $values) = [[], []];

        foreach ($data as $key => $value) {
            $fields[] = "`" . $key . "`";
            $values[] = "'" . $value . "'";
        }

        $fields = join(',', $fields);
        $values = join(',', $values);

        return mysqli_query(dbConnect(),"INSERT INTO ". $this->tableName() . " ($fields) VALUES($values)") !== false;
    }

    public function update(array $values, $where) {
        $fields = [];
        foreach ($values as $key => $value) {
            $fields[] = "$key = '$value'";
        }
        $fieldsStr = join(', ', $fields);

        return mysqli_query(dbConnect(),"UPDATE ". $this->tableName() . " SET $fieldsStr " . ($where ? " WHERE $where" : null)) !== false;
    }

    public function delete($where) {
        $where = $where ? " WHERE $where" : null;
        return mysqli_query(dbConnect(), "DELETE * FROM ". $this->tableName() . " $where");
    }

    public function count() {
        $result = mysqli_query(dbConnect(), "SELECT COUNT(*) FROM ". $this->tableName());
        return mysqli_fetch_row($result)[0];
    }
}