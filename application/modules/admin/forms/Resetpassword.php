<?php

class Admin_Form_Resetpassword extends Teabag_Form {
    
    public function __construct($options = null) {
        parent::__construct($options);
        
        $this->addElement(new Teabag_Form_Element_Password('new_password', array(   
            'label' => 'Password',
            'required'   => true,
            'validators'=> array(
                array('StringLength', true, array(6, 40))
            )       
        )));
        
        $this->addElement(new Teabag_Form_Element_Password('password_confirm', array(   
            'label' => 'Password Confirmation',
            'required'   => true,
            'validators'=> array(
                array('Identical', true, array('token' => 'new_password')),
                array('StringLength', true, array(6, 40)),
            )
        )));
        
        $this->addElement(new Teabag_Form_Element_Button('submit', array(
           'type' => 'submit',
           'label' => 'Save'
        )));
    }
}
