<?php

Class Admin_Form_Searchlsc extends Teabag_Form 
{
        
    public function __construct($options = null) 
    {
        parent::__construct($options);

        $debtorsMapper = new Application_Model_Table_Debtors();
        
		$this->setMethod('post');
		$this->setAction('/application/process');		
		
		
        $clients = $debtorsMapper->fetchClientsLsc()
                         ->toArrayForForm('client_code', 'client_code');
		 
		$clients2=array();
		foreach ($clients as $key => $client) {
			$clients2[$key] = strtoupper($client);
		}	
		
		$Mclients = $debtorsMapper->fetchMasterLsc();				
			
		$Mclientlist=array();
		foreach ($Mclients as $client) {
			if ($client['master_client']) {
				$Mclientlist[$client['master_client']][$client['client_code']] = strtolower($client['client_code']);
			} else {
				$Mclientlist['other'][$client['client_code']] = strtolower($client['client_code']);
			}
		}
		//echo "<pre>";
		//print_r($Mclientlist);
		//echo "</pre>";
		
	
		
        $this->addElement(new Teabag_Form_Element_MultiSelect('client_lsc', array(   
            'label'      => 'Client',
            'class' => 'form-control text-uppercase',
            'multioptions' => $Mclientlist
        )));
        
		$this->addElement(new Teabag_Form_Element_Text('loaded_from', array(   
            'label' => 'Loaded From',
            'class' => 'from form-control',
        )));

        $this->addElement(new Teabag_Form_Element_Text('loaded_to', array(   
            'label' => 'To',
            'class' => 'to form-control',
        )));
		
		$this->loaded_to->setValue(date('d-m-Y'));
		$this->loaded_from->setValue(date('01-m-Y'));
		
		$this->addElement(new Teabag_Form_Element_Select('nil_paid', array(   
            'label' => 'Nil Paid',
            'class' => 'form-control',
            'multioptions' => array(
                ''   => 'Any Value',
                '1' => 'Nil Paid',
            ),
        )));
		
		$this->addElement(new Teabag_Form_Element_Text('amount', array(   
            'label' => 'Over £',
            'class' => 'form-control',
            'value' => '200',
        )));
		
		$this->addElement(new Teabag_Form_Element_Select('postcode', array(   
            'label' => 'Postcode Region',
            'class' => 'form-control',
            'multioptions' => array(
                '2'   => 'England & Wales',
                '3' => 'Scotland Only', 
                '4' => 'Ireland Only',
                '1' => 'Any'
            ),
        )));
		
		$this->addElement(new Teabag_Form_Element_Select('account_status', array(   
            'label' => 'Account Status',
            'class' => 'form-control',
            'multioptions' => array(
				'open' => 'Open',
                'all' => 'Both',
                'closed'   => 'Closed',
            ),
        )));
		
		$this->addElement(new Teabag_Form_Element_Text('closed_from', array(   
            'label' => 'Closed From',
            'class' => 'closefrom form-control',
        )));

        $this->addElement(new Teabag_Form_Element_Text('closed_to', array(   
            'label' => 'To',
            'class' => 'closeto form-control',
        )));
		
      
       
        $this->addElement(new Teabag_Form_Element_Button('submit_lsc', array(
            'label' => 'Go',
            'class' => 'form-control',
            'type' => 'submit'
        )));
        
    }
}