<?php

Class Admin_Form_Search_Base extends Teabag_Form 
{
    
    public function __construct($options = null) 
    {
        parent::__construct($options);
        
        $this->setMethod('get');

        $this->addElement(new Teabag_Form_Element_Text_Search('q', array(   
            'filters' => array('StringTrim'),
            'label' => 'Search',
            'required'   => true
        )));        

        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Go',
            'type' => 'submit'
        )));
        
    }
}