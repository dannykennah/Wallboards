<?php

Class Admin_Form_StartSpecificWallboard extends Teabag_Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $searchMapper = new Application_Model_Table_WallgroupData();

		$codesori = $searchMapper->fetchIndividualBoards()
                         ->toArrayForForm('screen_id', 'more_info');


		$this->addElement(new Teabag_Form_Element_Select('screen', array(
            'label' => 'Wallboard Individual',
            'class' => 'form-control',
            'multioptions' => $codesori,
        )));

        $this->addElement(new Teabag_Form_Element_Button('submit', array(
            'label' => 'Start',
            'class' => 'form-control crs-button',
            'value' => 'specific',
            'type' => 'submit'
        )));

    }
}
