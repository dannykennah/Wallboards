<?php

class Admin_Form_CreateAdminUser extends Teabag_Form {
    
    public function __construct($options = null) {
        parent::__construct($options);
        
        $this->addElement(new Teabag_Form_Element_Text('username', array(   
            'filters' => array('StringTrim'),
            'label' => 'Email Address',
            'required'   => true,
            'validators'=> array(
                array('EmailAddress', true, array()),
                array('StringLength', true, array(null, 255))
            )
        )));
        
        $this->addElement(new Teabag_Form_Element_Password('new_password', array(   
            'label' => 'Password',
            'required'   => true,
            'validators'=> array(
                array('StringLength', true, array(6, 40))
            )       
        )));
        
        if ($this->getAttrib('is_edit')) {
            $this->new_password->setRequired(false);
        }
        
        $this->addElement(new Teabag_Form_Element_Password('password_confirm', array(   
            'label' => 'Password Confirmation',
            'required'   => true,
            'validators'=> array(
                array('Identical', true, array('token' => 'new_password')),
                array('StringLength', true, array(6, 40)),
            )
        )));
        
        if ($this->getAttrib('is_edit')) {
            $this->password_confirm->setRequired(false);
        }
        
        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Save',
            'type' => 'submit'            
        )));
    }
}