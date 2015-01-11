<?php

use Phalcon\DI\FactoryDefault as PhDi;

class ModelBase extends \Phalcon\Mvc\Model {

    /**
    * Override default interface
    */
    public function getMessages( $filter = null ) {
        $messages = array();
        foreach (parent::getMessages() as $message) {
            switch ($message->getType()) {
                case 'InvalidCreateAttempt':
                    $messages[] = 'The record cannot be created because it already exists';
                    break;
                case 'InvalidUpdateAttempt':
                    $messages[] = 'The record cannot be updated because it already exists';
                    break;
                case 'PresenceOf':
                    $messages[] = 'The field ' . $message->getField() . ' is mandatory';
                    break;
            }
        }
        return $messages;
    }

    /**
    * Get Enum Column values
    *
    * @access public
    * @param {string} $columnName
    * @return {array}
    */
    public function getEnumValues( $columnName = null ) {

        $di = PhDi::getDefault();

        if ( $columnName == null ) {
            return array();
        }

        $sql = "SHOW COLUMNS FROM `" . $this->getSource() . "` LIKE '{$columnName}'";
        $resultSet = $di['db']->query($sql);
        $resultSet->setFetchMode(Phalcon\Db::FETCH_ASSOC);
        $result = $resultSet->fetchAll($resultSet);
        if (!empty($result)) {
            $types = null;
            if (isset($result[0]['Type'])) {
                $types = $result[0]['Type'];
            } else {
                return array();
            }
            $values = explode("','", preg_replace("/(enum)\('(.+?)'\)/","\\2", $types));
            $assoc_values = array();
            foreach ($values as $value) {
                $assoc_values[$value] = ucwords(str_replace('_', ' ', $value));
            }
            return $assoc_values;
        }
        return false;
    }

}
