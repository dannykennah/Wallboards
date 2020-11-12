<?php

class Admin_Form_Target extends Teabag_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->addElement(new Teabag_Form_Element_Text('target', array(
            'filters' => array('StringTrim'),
            'label' => 'Revenue Target',
            'class' => 'form-control',
            'required'   => true
        )));
        $this->addElement(new Teabag_Form_Element_Text('worked_target', array(
            'filters' => array('StringTrim'),
            'label' => 'Accounts Worked Target',
            'class' => 'form-control',
            'required'   => true
        )));
        $this->addElement(new Teabag_Form_Element_Text('arrangements_target', array(
            'filters' => array('StringTrim'),
            'label' => 'Arrangement Target',
            'class' => 'form-control',
            'required'   => true
        )));

        $this->addElement(new Teabag_Form_Element_Button('submit_target', array(
            'class' => 'form-control',
            'label' => 'Ammend This Months Target',
            'type' => 'submit',
            'value' => 1
        )));
    }
}
