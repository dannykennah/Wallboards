<?php

Class Admin_Form_Search extends Teabag_Form 
{
    
    private $defaultRepCodes = array(
        '10LE', '1LE', '1LET', '2LE', '2LET', '3LE', '3LET', '4LE', '4LET', '5LE', 
        '6LE', '7LE', '8LE', '9LE', 'EM1', 'EM10', 'EM11', 'EM12', 'EM13', 'EM14', 'EM15', 
        'EM16', 'EM19', 'EM2', 'EM20', 'EM21', 'EM22', 'EM25', 'EM27', 'EM3', 'EM31',
        'EM32', 'EM4', 'EM40', 'EM42', 'EM43', 'EM44', 'EM45', 'EM5', 'EM6', 'EM7', 
        'EM8', 'EM9', 'EMA', 'IV1', 'IV7', 'IV8', 'IV10', 'IV11',
        'IV14', 'IV15', 'IV16', 'IV19', 'IV2', 'IV20', 'IV21', 'IV3', 'IV4', 'IV5', 
        'IV6', 'IV9', 'IVR', 'SM1', 'SM10', 'SM13', 'SM14', 'SM15', 'SM16', 'SM19',
        'SM2', 'SM20', 'SM21', 'SM22', 'SM25', 'SM26', 'SM27', 'SM3', 'SM30', 'SM31',
        'SM32', 'SM35', 'SM36', 'SM37', 'SM38', 'SM4', 'SM41', 'SM42', 'SM43', 'SM44', 'SM45', 'SM46',
        'SM5', 'SM6', 'SM7', 'SM8', 'SM9', 'SMS'
    );
    
    public function __construct($options = null) 
    {
        parent::__construct($options);

        $debtorsMapper = new Application_Model_Table_Debtors();
        $transactionsMapper = new Application_Model_Table_Transactions();
        
        $sectors = $debtorsMapper->fetchSectors()
                                 ->toArrayForForm('sector', 'sector');

        $this->addElement(new Teabag_Form_Element_Select('sector', array(   
            'label' => 'Sector',
            'class' => 'form-control',
            'multioptions' => array('' => 'Any') + $sectors,
        )));
		
		
		$Mclients = $debtorsMapper->fetchMasterLsc();				
			
		$Mclientlist=array();
		foreach ($Mclients as $client) {
			if ($client['master_client']) {
				$Mclientlist[$client['master_client']][$client['client_code']] = strtolower($client['client_code']);
			} else {
				$Mclientlist['other'][$client['client_code']] = strtolower($client['client_code']);
			}
		}
		
		
        //$clients = $debtorsMapper->fetchClients()
        //                 ->toArrayForForm('client_code', 'client_code');

        $this->addElement(new Teabag_Form_Element_MultiSelect('client', array(   
            'label'      => 'Client',
            'class' => 'form-control',
            'multiOptions' => $Mclientlist
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('debt_value_from', array(   
            'label' => 'Debt value',
            'class' => 'form-control',
            'validators'=> array(
                array('Digits')
            )
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('debt_value_to', array(   
            'label' => 'To',
            'class' => 'form-control',
            'validators'=> array(
                array('Digits')
            )
        )));
        
        $this->addDisplayGroup(array(
                    'debt_value_from',
                    'debt_value_to',
        ),'debt_value');

       $this->addElement(new Teabag_Form_Element_Text('last_pay_date_from', array(   
            'label' => 'Last pay date from',
            'class' => 'from form-control',
        )));

        $this->addElement(new Teabag_Form_Element_Text('last_pay_date_to', array(   
            'label' => 'To',
            'class' => 'to form-control',
        )));
        $this->addDisplayGroup(array(
                    'last_pay_date_from',
                    'last_pay_date_to',
        ),'last_pay_date');

        $this->addElement(new Teabag_Form_Element_Text('previous_payment_from', array(   
            'label'      => 'Last Payment Value From',
            'class' => 'form-control',
            'placeholder'=> '5'
        )));

        $this->addElement(new Teabag_Form_Element_Text('previous_payment_to', array(   
            'label'      => 'Last Payment Value To',
            'class' => 'form-control',
            'placeholder'=> '10'
        )));

        $this->addElement(new Teabag_Form_Element_Text('age_from_loading_from', array(   
            'label' => 'Age from loading',
            'class' => 'from form-control'
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('age_from_loading_to', array(   
            'label' => 'To',
            'class' => 'to form-control'
        )));
        
        $this->addDisplayGroup(array(
                    'age_from_loading_from',
                    'age_from_loading_to',
        ),'age_from_loading');
        
        $this->addElement(new Teabag_Form_Element_Text('dob_year_from', array(   
            'label' => 'DOB Year From',
            'class' => 'form-control',
            'validators'=> array(
                array('StringLength', true, array(4, 4)),
                array('Digits')
            )
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('dob_year_to', array(   
            'label' => 'To',
            'class' => 'form-control',
            'validators'=> array(
                array('StringLength', true, array(4, 4)),
                array('Digits')
            )
        )));
        
        $this->addDisplayGroup(array(
                    'dob_year_from',
                    'dob_year_to',
        ),'dob_year');
        
        $this->addElement(new Teabag_Form_Element_Text('dob_from', array(   
            'label' => 'DOB From',
            'class' => 'date_from form-control'
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('dob_to', array(   
            'label' => 'To',
            'class' => 'date_to form-control'
        )));
        
        $this->addDisplayGroup(array(
                    'dob_from',
                    'dob_to',
        ),'dob');

        $this->addElement(new Teabag_Form_Element_Select('demographic', array(   
            'label' => 'Demographic',
            'class' => 'form-control',
            'multioptions' => array(
                ''   => 'Any',
                'AB' => 'Very affluent',
                'C1' => 'Affluent',
                'C2' => 'Average Area',
                'DE' => 'Least affluent',
            ),
        )));

        $this->addElement(new Teabag_Form_Element_Select('outbound', array(   
            'label' => 'Search outbound contact',
            'class' => 'form-control',
            'required'   => true,
            'multioptions' => array(
                'no'  => 'No',
                'yes_found' => 'Yes - is found',
                'yes_notfound' => 'Yes - is not found',
            ),
        )));
        
        $outBoundType = $transactionsMapper->fetchEvents()->toArrayForForm('event_code', 'event_code');
        $emails = array();
        $sms = array();
        $ivr = array();
        $other = array();
        foreach ($outBoundType as $key => $event) {
            if (substr( strtolower($event), 0, 5 ) === "email") {
                $emails[$key] = $event;
                unset($outBoundType[$key]);
            } else if (substr( strtolower($event), 0, 3 ) === "sms") {
                $sms[$key] = $event;
                unset($outBoundType[$key]);
            } else if (substr( strtolower($event), 0, 3 ) === "ivr") {
                $ivr[$key] = $event;
                unset($outBoundType[$key]);
            } else if (in_array(strtolower($event), array('blank', 'scotcall') )) {
                $other[$key] = $event;
                unset($outBoundType[$key]);
            } else {
                
            }
        }

        $this->addElement(new Teabag_Form_Element_MultiSelect('outbound_type', array(   
            'label'      => 'Of type',
            'class'      => 'form-control',
            'multioptions' => array(
                'Email' => $emails,
                'SMS'   => $sms,
                'IVR'   => $ivr,
                'Letters' => $outBoundType,
                'Other' => $other
            )
        )));

        $this->addElement(new Teabag_Form_Element_Text('outbound_within_from', array(   
            'label' => 'Outbound Date From',
            'class' => 'from form-control'
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('outbound_within_to', array(   
            'label' => 'To',
            'class' => 'to form-control'
        )));
        
        $this->addDisplayGroup(array(
                    'outbound_within_from',
                    'outbound_within_to',
        ),'outbound_within');

        $this->addElement(new Teabag_Form_Element_Select('inbound', array(   
            'label' => 'Search inbound contact',
            'required'   => true,
            'class'   => 'form-control',
            'multioptions' => array(
                'no'  => 'No',
                'yes_found' => 'Yes - is found',
                'yes_notfound' => 'Yes - is not found',
            ),
        )));

        $this->addElement(new Teabag_Form_Element_MultiSelect('inbound_type', array(   
            'label' => 'Of type',
            'class'   => 'form-control',
            'multioptions' => array(
                ''   => '',
                'any' => 'Any',
                'MO2599' => 'MO2599 Web-chat engaged',
                'MO2513' => 'MO2513 Website Message Received',
                'MO2514' => 'MO2514 Website Message respond',
                'MO1044' => 'MO1044 XEmail from client',
                'MO1043' => 'MO1043 XEmail from Customer received',
                'MO1045' => 'MO1045 XEmail from Third Party',
                'MO1041' => 'MO1041 XEmail Sent to Client',
                'MO1042' => 'MO1042 XEmail Sent to Customer',
                'MO1046' => 'MO1046 XEmail to Third Party',
                'MO1991' => 'MO1991 XIN - Discussed Account',
                'MO1050' => 'MO1050 XIN - DMA enquiry',
                'MO1012' => 'MO1012 XIN - Inbound Dispute',
                'MO1011' => 'MO1011 XIN - Inbound Payment received',
                'MO1013' => 'MO1013 XIN - Inbound Query',
                'MO1010' => 'MO1010 XIN - Inbound signpost to DMA',
                'MO1131' => 'MO1131 Debtor Phoned',
                'MO1040' => 'MO1040 XIN - Our Client Called CRS',
                'MO1411' => 'MO1411 XIN - SMS message received',
                'MO1412' => 'MO1412 XIN - Third Party Phoned',
                'MO1999' => 'MO1999 XOUT - Discussed Account',
                'MO1051' => 'MO1051 XOUT - DMA enquiry',
                'MO1130' => 'MO1130 XOUT - Outbound Dispute',
                'MO1122' => 'MO1122 XOUT - Outbound no contact/ message left',
                'MO1121' => 'MO1121 XOUT - Outbound Payment received',
                'MO1160' => 'MO1160 XOUT - Outbound Query',
                'MO1120' => 'MO1120 XOUT - Outbound Wrong Number',
                'MO1150' => 'MO1150 XOUT - Phoned Auth Third Party',
                'MO1030' => 'MO1030 XOUT - Phoned Client',
                'MO1140' => 'MO1140 XOUT Phoned Work HC',
                'MO1145' => 'Debtor Correspondence Received',
                'MO1144' => 'MO1144 I&E Received',
                'MO1347' => 'MO1347 Response form received',
                'MO1227' => 'MO1227 Website Visit',
            ),
        )));

        $this->addElement(new Teabag_Form_Element_Text('inbound_within_from', array(   
            'label' => 'Inbound Date From',
            'class' => 'from form-control'
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('inbound_within_to', array(   
            'label' => 'To',
            'class' => 'to form-control'
        )));
        
        $this->addDisplayGroup(array(
                    'inbound_within_from',
                    'inbound_within_to',
        ),'inbound_within');

        $reps = $debtorsMapper->fetchRepCode()
                                 ->toArrayForForm('rep_code', 'rep_code');
        
        $this->addElement(new Teabag_Form_Element_MultiSelect('rep_code', array(   
            'label'      => 'Rep code',
            'class' => 'form-control',
            'multioptions' => array('' => 'Any') + $reps,
            'value' => $this->defaultRepCodes
        )));
        
        $this->addElement(new Teabag_Form_Element_Select('gender', array(   
            'label' => 'Gender',
            'class' => 'form-control',
            'multioptions' => array('' => 'Any', 'male' => 'Male', 'female' => 'Female'),
            'value' => ''
        )));

//        $this->addElement(new Teabag_Form_Element_Text('payment_type', array(   
//            'label'      => 'Payment type',
//        )));

        $this->addElement(new Teabag_Form_Element_Select('export_format', array(   
            'label' => 'Export for...',
            'class' => 'form-control',
            'required'   => true,
            'multioptions' => array(
                'email' => 'Email',
                'sms'   => 'SMS',
                'ivr'   => 'IVR',
                'post'  => 'Post'
            ),
        )));
        
        $this->addElement(new Teabag_Form_Element_Select('phone_type', array(   
            'label' => 'Mobiles only',
            'class' => 'form-control',
            'multioptions' => array(
                'n'  => 'No',
                'y' => 'Yes'
            ),
            'Value' => 'n'
        )));
        
        $smsType = array();
        for($i = 1; $i <= 25; $i++) {
            $smsType['sms' . $i] = 'sms' . $i;
        }
        
        $this->addElement(new Teabag_Form_Element_Select('sms_type', array(   
            'label' => 'Sms type',
            'class' => 'form-control',
            'multioptions' => array('' => 'Any') +  $smsType
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('letter_template', array(   
            'label' => 'Letter template',
            'class' => 'form-control'
        )));
        
        $this->addElement(new Teabag_Form_Element_Select('ivr_type', array(   
            'label' => 'IVR type',
            'class' => 'form-control',
            'multioptions' => array(
                '01422315208' => 'Barclays',
                '01422315213' => 'DMA',
                '01422315231' => 'Default',
                '01422324520' => 'Insurance',
                '01422324525' => 'Credit Finanace',
                '01422324527' => 'Utilities',
                '01422324510' => 'Call Centre'
            ),
        )));
        
        $emailType = array();
        for($i = 1; $i <= 20; $i++) {
            $emailType['email' . $i] = 'Email' . $i;
        }
        
        $this->addElement(new Teabag_Form_Element_Select('email_type', array(   
            'label' => 'Email type',
            'class' => 'form-control',
            'multioptions' => $emailType
        )));
        
        $this->addElement(new Teabag_Form_Element_Text('discount_offer', array(   
            'label' => 'Discount offer',
            'class' => 'form-control'
        )));

        $this->addElement(new Teabag_Form_Element_Select('callback_type', array(   
            'label' => 'Call back',
            'class' => 'form-control',
            'multioptions' => array(
                'true' => 'True',
                'false'  => 'False',
            ),
        )));
        
        $this->addElement(new Teabag_Form_Element_Select('include_client_name', array(   
            'label' => 'Include client name',
            'class' => 'form-control',
            'multioptions' => array(
                '' => 'No',
                'yes'  => 'Yes',
            ),
        )));
        
        
        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Go',
            'class' => 'form-control',
            'type' => 'submit'
        )));
        
    }
}