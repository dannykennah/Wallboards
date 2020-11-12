<?php

class Admin_Form_Editop extends Teabag_Form {

    public function __construct($options = null) {
        parent::__construct($options);
        $departmentsModal = new Application_Model_Table_GetDepartments();
        $departments = $departmentsModal->fetchAll()->ToArray();
        $list=[];
        foreach ($departments as $department) {
          $list[$department['department_id']]=$department['department_id'];
          $list[$department['department_id']]=$department['name'];
        }
        $this->addElement(new Teabag_Form_Element_Text('name', array(
            'filters' => array('StringTrim'),
            'label' => 'Fullname',
            'class' => 'form-control',
            'required'   => true
        )));

        $this->addElement(new Teabag_Form_Element_Text('op_code', array(
            'filters' => array('StringTrim'),
            'label' => 'Caseflow Operator Code',
            'class' => 'form-control',
            'required'   => true
        )));

        $this->addElement(new Teabag_Form_Element_Text('dialer_id', array(
            'filters' => array('StringTrim'),
            'label' => 'Dialer Login id',
            'class' => 'form-control',
            'required'   => true
        )));
        $this->addElement(new Teabag_Form_Element_Select('department_id', array(
            'label' => 'Department/Team',
            'class' => 'form-control',
            'multioptions' =>  $list,
            'required'   => true
        )));


        $this->addElement(new Teabag_Form_Element_Button('submit_edit', array(
            'class' => 'form-control',
            'label' => 'Save',
            'type' => 'submit',
            'value' => 1
        )));
    }
}
