<?php

/**
 * Model for a single searchcode
 */
class Application_Model_Emailauto extends Teabag_Db_Table_Row
{


	public function fetchAllTickets($from,$to)
	{
		require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

				$db2 = new Zend_Db_Adapter_Pdo_Mysql(array(
					'host'     => 'creditresourcesolutions.co.uk',
					'username' => 'email_auto',
					'password' => 'T7xs?q14',
					'dbname'   => 'email_automation'
				));
				$db2->getConnection();

        $select = $db2->select()
                ->from('tickets', array('ticket_status','actioned',
					'total' => "COUNT(ticket_id)",
					 ))

				->where('tickets.ticket_created >= ?', $from)
                ->where('tickets.ticket_created <= ?', $to)
				->where('tickets.ticket_status = ?', 'solved')
				->group('tickets.actioned')
                ;

		$result = $db2->fetchAll($select);


        return $result;
	}


	public function fetchAllComments($from,$to)
	{
		require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

				$db2 = new Zend_Db_Adapter_Pdo_Mysql(array(
					'host'     => 'creditresourcesolutions.co.uk',
					'username' => 'email_auto',
					'password' => 'T7xs?q14',
					'dbname'   => 'email_automation'
				));
				$db2->getConnection();

        $select = $db2->select()
                ->from('comments', array('comment_channel','comment_direction',
					'total' => "COUNT(comment_ticket_id)",
					 ))

				->where('comments.comment_created >= ?', $from)
                ->where('comments.comment_created <= ?', $to)
                ->where('comments.comment_direction = ?', '2')
				->group('comments.comment_channel')
                ;

		$result = $db2->fetchAll($select);


        return $result;
	}



}
