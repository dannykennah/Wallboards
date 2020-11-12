<?php

Class Admin_Form_EditMocodes extends Teabag_Form 
{
        
    public function __construct($options = null) 
    {
        parent::__construct($options);

	
		// 'groupcode','name','is_active','code'
		$this->addElement(new Teabag_Form_Element_Text('groupcode', array(   
            'label' => 'Group Code',
            'class' => 'form-control',
			'required' => true,
        )));
		
		$this->addElement(new Teabag_Form_Element_Text('name', array(   
            'label' => 'Name',
            'class' => 'form-control',
			'required' => true,
        )));
		
		$this->addElement(new Teabag_Form_Element_Text('code', array(   
            'label' => 'MOCode',
            'class' => 'form-control',
			'required' => true,
        )));
       
        $this->addElement(new Teabag_Form_Element_Button('submit_mocodes', array(
            'label' => 'Go',
            'class' => 'form-control',
            'type' => 'submit'
        )));
        
    }
}