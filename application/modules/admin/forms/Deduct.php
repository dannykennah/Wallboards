<?php

class Admin_Form_Deduct extends Teabag_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->addElement(new Teabag_Form_Element_Text('amount', array(
            'filters' => array('StringTrim'),
            'label' => 'Cash Amount',
            'class' => 'form-control',
            'required'   => true
        )));

        $this->addElement(new Teabag_Form_Element_Text('rev', array(
            'filters' => array('StringTrim'),
            'label' => 'Revenue Amount',
            'class' => 'form-control',
            'required'   => true
        )));

        $OperatorModal = new Application_Model_Table_Operators();
        $operators = $OperatorModal->fetchAll(array('active = 1'))->toArrayForForm('user_id', 'op_code');

        $this->addElement(new Teabag_Form_Element_Select('from_operator', array(
            'label' => 'Deduct From',
            'class' => 'form-control',
            'multioptions' => array('' => 'Select Operator','0' => 'N/A') + $operators,
	          'required'   => true
        )));

        $this->addElement(new Teabag_Form_Element_Select('to_operator', array(
            'label' => 'Add To',
            'class' => 'form-control',
            'multioptions' => array('' => 'Select Operator','0' => 'N/A') + $operators,
            'required'   => true
        )));


        $this->addElement(new Teabag_Form_Element_Select('month', array(
                'label' => 'Month Select',
                'class' => 'form-control',
                'multioptions' => array(
                    '1' => 'Current Month',
                    '2' => 'Last Month',
                ),
            )));

        $this->addElement(new Teabag_Form_Element_Text('debt_code', array(
            'filters' => array('StringTrim'),
            'label' => 'Debt Code',
            'class' => 'form-control',
            'required'   => true
        )));

        $this->addElement(new Teabag_Form_Element_Textarea('reason', array(
            'filters' => array('StringTrim'),
            'label' => 'Reason',
            'class' => 'form-control',
            'required'   => true
        )));


        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'class' => 'form-control crs-button',
            'label' => 'Make Deductions',
            'type' => 'submit',
            'value' => 1
        )));
    }
}
