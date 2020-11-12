<?php

/**
 * Demographics model
 */
class Application_Model_Table_SystemTasks extends Teabag_Db_Table
{
    protected $_name = 'wallboards.system_tasks';
    protected $_rowClass = 'Application_Model_SystemTask';
    protected $_referenceMap = array(
    );

    public function fetchtasks(){

      $select = $this->select()
                     ->setIntegrityCheck(false)
                     ->from(array('systask'=>'wallboards.system_tasks'), array(
                        'task_id',
                        'task_group',
                        'task_title',
                        'task_runby',
                        //'created'=>new Zend_Db_Expr("DATE_FORMAT(systask.task_created,'%H:%i:%s')"),
                        // 'ucount'=>new Zend_Db_Expr("SUM(goog.users)"),
                        // 'nucount'=>new Zend_Db_Expr("SUM(goog.newusers)"),
                        // 'secount'=>new Zend_Db_Expr("SUM(goog.sessions)")
                      ))
                      ->joinleft(array('syshistory'=>'system_tasks_history'),'syshistory.task_id = systask.task_id and syshistory.created >= "'.date('Y-m-d 00:00:00').'"',
                      array(
                        'lastran'=>new Zend_Db_Expr("DATE_FORMAT(MAX(syshistory.created),'%H:%i:%s')"),
                        'timediff'=>new Zend_Db_Expr("TIMESTAMPDIFF(MINUTE,MAX(syshistory.created),NOW())"),
                        'notes','created',
                        'runtime'=>new Zend_Db_Expr("CASE
                                                          WHEN DATE_FORMAT(MAX(syshistory.created),'%H:%i:%s') > systask.task_runby AND systask.task_type = 1 THEN 'yellow'
                                                          WHEN DATE_FORMAT(MAX(syshistory.created),'%H:%i:%s') < systask.task_runby AND systask.task_type = 1 THEN 'green'
                                                          WHEN MAX(syshistory.created) IS NULL AND '".date('H:i:s')."' < systask.task_runby AND systask.task_type = 1 THEN 'grey'
                                                          WHEN MAX(syshistory.created) IS NULL AND '".date('H:i:s')."' > systask.task_runby AND systask.task_type = 1 THEN 'red'
                                                          WHEN TIMESTAMPDIFF(MINUTE,MAX(syshistory.created),NOW()) <= systask.task_scope AND systask.task_type = 2 THEN 'green'
                                                          WHEN TIMESTAMPDIFF(MINUTE,MAX(syshistory.created),NOW()) > systask.task_scope AND systask.task_type = 2 THEN 'red'
                                                          ELSE 'elsered'
                                                      END "),
                      ))
                      //->where('systask.task_id = 11')
                      ->group('systask.task_id')
                      ;



      // $select->where('goog.created >= ?','2019-07-01');
      // $select->group("DATE_FORMAT(goog.created,'%Y-%m')");

      $items = $this->fetchAll($select);
      return $items;

    }


}
