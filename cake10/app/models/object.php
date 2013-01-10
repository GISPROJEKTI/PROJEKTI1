<?php

class Object extends AppModel
{

    // public $validate = array(
    //     'name' => array(
    //         'rule' => 'notEmpty',
    //         'message' => 'Anna kyselylle nimi'
    //     )
    // );

    public $modifierFields = array(
        'icon',
        'strokeColor',
        'strokeOpacity',
        'strokeWeight'
    );

    public function beforeSave($options)
    {
        // debug($this->data);die;
        
        $this->_serializeModifiers();
        return true;
    }

    public function _serializeModifiers()
    {
        if (isset($this->data[$this->alias]['modifiers'])
            && is_array($this->data[$this->alias]['modifiers'])
        ){
            $modifiers = '';

            foreach ($this->modifierFields as $field) {
                if (isset($this->data[$this->alias]['modifiers'][$field])) {
                    $modifiers .= $field . '=' . str_replace(
                        array('=', '|'),
                        '',
                        $this->data[$this->alias]['modifiers'][$field]
                    ) . '|';
                }
            }
            $modifiers = substr($modifiers, 0, -1);

            $this->data[$this->alias]['modifiers'] = $modifiers;
        }

    }
}