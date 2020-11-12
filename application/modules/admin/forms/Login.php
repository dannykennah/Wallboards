<?php

class Admin_Form_Login extends Teabag_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->addElement(new Teabag_Form_Element_Text('email_address', array(
            'filters' => array('StringTrim'),
            'label' => 'Email',
            'required'   => true
        )));

        $this->addElement(new Teabag_Form_Element_Password('new_password', array(
            'label' => 'Password',
            'required'   => true,
            'validators'=> array(
                array('StringLength', true, array(6, 40))
            )
        )));

        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Login',
            'type' => 'submit'
        )));
    }
}
