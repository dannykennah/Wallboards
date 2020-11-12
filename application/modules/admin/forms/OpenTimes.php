<?php

class Admin_Form_OpenTimes extends Teabag_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->addElement(new Teabag_Form_Element_Text_Time('start', array(
            'label' => 'Start Time',
            'class' => 'form-control',
            'required'   => true
        )));
        $this->addElement(new Teabag_Form_Element_Text_Time('end', array(
            'label' => 'End Time',
            'class' => 'form-control',
            'required'   => true
        )));
        $this->addElement(new Teabag_Form_Element_Text_Date('date', array(
            'label' => 'Date',
            'class' => 'form-control',
            'required'   => true
        )));
        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Save',
            'value' => 'opening',
            'class' => 'form-control crs-button',
            'type' => 'submit'
        )));
    }
}
