<?php

Class Admin_Form_StartWallboard extends Teabag_Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $searchMapper = new Application_Model_Table_Wallgroups();

		$codesori = $searchMapper->fetchAllBoards()
                         ->toArrayForForm('id', 'group_name');


		$this->addElement(new Teabag_Form_Element_Select('group', array(
            'label' => 'Wallboard Group',
            'class' => 'form-control',
            'multioptions' => $codesori,
        )));

        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Start',
            'class' => 'form-control crs-button',
            'type' => 'submit'
        )));

    }
}
