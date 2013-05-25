<?php
/**
 * CDbCommand 在SAE平台 getPdoInstance 增加 sql参数 来判断是否需要切换到 写服务器的conn
 * @author biner <huanghuibin@gmail.com>
 */

/**
 * This file contains the CDbCommand class.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CDbCommand represents an SQL statement to execute against a database.
 *
 * It is usually created by calling {@link CDbConnection::createCommand}.
 * The SQL statement to be executed may be set via {@link setText Text}.
 *
 * To execute a non-query SQL (such as insert, delete, update), call
 * {@link execute}. To execute an SQL statement that returns result data set
 * (such as SELECT), use {@link query} or its convenient versions {@link queryRow},
 * {@link queryColumn}, or {@link queryScalar}.
 *
 * If an SQL statement returns results (such as a SELECT SQL), the results
 * can be accessed via the returned {@link CDbDataReader}.
 *
 * CDbCommand supports SQL statment preparation and parameter binding.
 * Call {@link bindParam} to bind a PHP variable to a parameter in SQL.
 * Call {@link bindValue} to bind a value to an SQL parameter.
 * When binding a parameter, the SQL statement is automatically prepared.
 * You may also call {@link prepare} to explicitly prepare an SQL statement.
 *
 * Starting from version 1.1.6, CDbCommand can also be used as a query builder
 * that builds a SQL statement from code fragments. For example,
 * <pre>
 * $user = Yii::app()->db->createCommand()
 *     ->select('username, password')
 *     ->from('tbl_user')
 *     ->where('id=:id', array(':id'=>1))
 *     ->queryRow();
 * </pre>
 *
 * @property string $text The SQL statement to be executed.
 * @property CDbConnection $connection The connection associated with this command.
 * @property PDOStatement $pdoStatement The underlying PDOStatement for this command
 * It could be null if the statement is not prepared yet.
 * @property string $select The SELECT part (without 'SELECT') in the query.
 * @property boolean $distinct A value indicating whether SELECT DISTINCT should be used.
 * @property string $from The FROM part (without 'FROM' ) in the query.
 * @property string $where The WHERE part (without 'WHERE' ) in the query.
 * @property mixed $join The join part in the query. This can be an array representing
 * multiple join fragments, or a string representing a single jojin fragment.
 * Each join fragment will contain the proper join operator (e.g. LEFT JOIN).
 * @property string $group The GROUP BY part (without 'GROUP BY' ) in the query.
 * @property string $having The HAVING part (without 'HAVING' ) in the query.
 * @property string $order The ORDER BY part (without 'ORDER BY' ) in the query.
 * @property string $limit The LIMIT part (without 'LIMIT' ) in the query.
 * @property string $offset The OFFSET part (without 'OFFSET' ) in the query.
 * @property mixed $union The UNION part (without 'UNION' ) in the query.
 * This can be either a string or an array representing multiple union parts.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.db
 * @since 1.0
 */
class SAEDbCommand extends CDbCommand
{

	/**
	 * Prepares the SQL statement to be executed.
	 * For complex SQL statement that is to be executed multiple times,
	 * this may improve performance.
	 * For SQL statement with binding parameters, this method is invoked
	 * automatically.
	 */
	public function prepare()
	{
		if($this->_statement==null)
		{
			try
			{
				// SAE getPdoInstance 增加 sql参数 来判断是否需要切换到 写服务器的conn
				$this->_statement=$this->getConnection()->getPdoInstance($this->getText())->prepare($this->getText());
				$this->_paramLog=array();
			}
			catch(Exception $e)
			{
				Yii::log('Error in preparing SQL: '.$this->getText(),CLogger::LEVEL_ERROR,'system.db.CDbCommand');
                $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
				throw new CDbException(Yii::t('yii','CDbCommand failed to prepare the SQL statement: {error}',
					array('{error}'=>$e->getMessage())),(int)$e->getCode(),$errorInfo);
			}
		}
	}
}
