<?php

Class Admin_Form_Searchcsr extends Teabag_Form 
{
        
    public function __construct($options = null) 
    {
        parent::__construct($options);

        $searchMapper = new Application_Model_Table_SC();
		
		$codesori = $searchMapper->fetchSearch()
                         ->toArrayForForm('groupcode', 'name');
		$codes=array();
		foreach ($codesori as $key => $code) {
			$codes[$key] = $code;
		}	
		
        
		$this->setMethod('post');
		$this->setAction('/admin/campaign_success_report');		
		
		$this->addElement(new Teabag_Form_Element_Text('date_from', array(   
            'label' => 'From',
            'class' => 'from form-control',
        )));

        $this->addElement(new Teabag_Form_Element_Text('date_to', array(   
            'label' => 'To',
            'class' => 'to form-control',
        )));
		
		$this->date_to->setValue(date('d-m-Y'));
		$this->date_from->setValue(date('01-m-Y'));
		
		
		
		$this->addElement(new Teabag_Form_Element_Select('mo_type', array(   
            'label' => 'MO Select',
            'class' => 'form-control',
            'multioptions' => $codes,
        )));
		
		$this->addElement(new Teabag_Form_Element_Checkbox('get_csv', array(   
            'label'    => 'Download report as CSV',
            'required' => false,          
        )));  
        
       
        $this->addElement(new Teabag_Form_Element_Button('submit_csr', array(
            'label' => 'Go',
            'class' => 'form-control',
            'type' => 'submit'
        )));
        
    }
}