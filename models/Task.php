<?php
/**
 * User: Zemlyansky Alexander <astrolog@online.ua>
 */

namespace app\models;

use app\system\App;
use app\system\Model;

class Task extends Model {

    public function tableName() {
        // actually there's no table in DB but in the future it might well be!
        return 'task';
    }

    public function validate($data) {
        $this->errors = null;

        if (!strlen($data['name'])) {
            $this->errors[] = 'Name cannot be empty';
        }
        if (!strlen($data['email'])) {
            $this->errors[] = 'Email cannot be empty';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Wrong email';
        }

        if (!strlen($data['text'])) {
            $this->errors[] = 'Text cannot be empty';
        }
        if (isset($data['edit']) && !strlen($data['status'])) {
            $this->errors[] = 'Status cannot be empty';
        }

        return !count($this->errors);

    }

}