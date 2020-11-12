<?php

Class Default_Form_Unsubscribe extends Teabag_Form 
{
    
    public function __construct($options = null) 
    {
        parent::__construct($options);

        $this->addElement(new Teabag_Form_Element_Hidden('email', array(
        )));

        $this->addElement(new Teabag_Form_Element_Hidden('hash', array(
        )));

        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Unsubscribe',
            'type' => 'submit'
        )));
        
    }
}