<?php

/**
 * Transactions model
 */
class Application_Model_Table_Storedcttdailys extends Teabag_Db_Table
{
    protected $_name = 'stored_ctt_daily';
    protected $_rowClass = 'Application_Model_Storedcttdaily';

    /**
     * Find a transaction by its details
     * @param string $debtor_code
     * @param string $trans_code
     * @param string $event_code
     * @param string $event_date
     * @return Zend_Db_Table_Rowset
     */
    // public function findByOpcode($opcode)
    // {
    //     $select = $this->select()
    //                    ->where('op_code = ?', $opcode)
    //                    ;
    //
    //     return $this->fetchRow($select);
    // }


    public function collector_cash($sortbycolumn = null,$sort = null, $month = null){

     $select = $this->select(false)
                    ->from($this->_name, array('*'))
                    ->where('month = ?',$month);
     $rows = $this->fetchAll($select)->ToArray();


     if ($sortbycolumn) {
 					if ($sort==1) { $sortby=SORT_DESC; } else { $sortby=SORT_ASC; }
 				 array_multisort(array_column($rows, $sortbycolumn), $sortby, $rows);
 				}

     return $rows;

     }

}
