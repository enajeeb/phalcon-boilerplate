<?php
use Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Users extends \ModelBase {

    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    public $id;
    public $group_id;
    public $first_name;
    public $last_name;
    public $username;
    public $password;
    public $status = 'pending';
    public $hashtoken;
    public $hashtoken_reset;
    public $hashtoken_expire;
    public $created;
    public $modified;
    public $modified_by;

    public function initialize() {

        // relationships
        $this->belongsTo("group_id", "Groups", "id");

    }

    public function validation() {

        $this->validate(new Uniqueness(
            array(
                "field"   => "username",
                "message" => "The email must be unique"
            )
        ));

        return $this->validationHasFailed() != true;
    }

}
