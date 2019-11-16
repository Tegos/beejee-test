<?php
/**
 * User: Zemlyansky Alexander <astrolog@online.ua>
 */

namespace app\models;

use app\system\App;
use app\system\Model;

class Login extends Model {

    public function tableName() {
        // actually there's no table in DB but in the future it might well be!
        return 'user';
    }

    public function validate($data) {
        $this->errors = null;

        if (!strlen($data['name'])) {
            $this->errors[] = 'Login cannot be empty';
        }
        if (!strlen($data['password'])) {
            $this->errors[] = 'Pasword cannot be empty';
        }

        if (!empty($this->errors)) {
            return;
        }

        if ($data['name'] != App::getConfig('adminLogin')) {
            $this->errors[] = 'Wrong login';
        }
        if ($data['password'] != App::getConfig('adminPassword')) {
            $this->errors[] = 'Wrong password';
        }

        return !count($this->errors);
    }

}