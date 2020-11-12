<?php

class Admin_Form_ResendPassword extends Admin_Form_Login {
    
    public function __construct($options = null) {
        parent::__construct($options);
        $this->removeElement('new_password');
        $this->getElement('submit')->setLabel('Reset password');
    }
}
