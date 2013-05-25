<?php
/**
 * 针对SAE，加入了读写分离。配合CDbCommand.php
 * @author biner <huanghuibin@gmail.com>
 */

/**
 * CDbConnection class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CDbConnection represents a connection to a database.
 *
 * CDbConnection works together with {@link CDbCommand}, {@link CDbDataReader}
 * and {@link CDbTransaction} to provide data access to various DBMS
 * in a common set of APIs. They are a thin wrapper of the {@link http://www.php.net/manual/en/ref.pdo.php PDO}
 * PHP extension.
 *
 * To establish a connection, set {@link setActive active} to true after
 * specifying {@link connectionString}, {@link username} and {@link password}.
 *
 * The following example shows how to create a CDbConnection instance and establish
 * the actual connection:
 * <pre>
 * $connection=new CDbConnection($dsn,$username,$password);
 * $connection->active=true;
 * </pre>
 *
 * After the DB connection is established, one can execute an SQL statement like the following:
 * <pre>
 * $command=$connection->createCommand($sqlStatement);
 * $command->execute();   // a non-query SQL statement execution
 * // or execute an SQL query and fetch the result set
 * $reader=$command->query();
 *
 * // each $row is an array representing a row of data
 * foreach($reader as $row) ...
 * </pre>
 *
 * One can do prepared SQL execution and bind parameters to the prepared SQL:
 * <pre>
 * $command=$connection->createCommand($sqlStatement);
 * $command->bindParam($name1,$value1);
 * $command->bindParam($name2,$value2);
 * $command->execute();
 * </pre>
 *
 * To use transaction, do like the following:
 * <pre>
 * $transaction=$connection->beginTransaction();
 * try
 * {
 *	$connection->createCommand($sql1)->execute();
 *	$connection->createCommand($sql2)->execute();
 *	//.... other SQL executions
 *	$transaction->commit();
 * }
 * catch(Exception $e)
 * {
 *	$transaction->rollback();
 * }
 * </pre>
 *
 * CDbConnection also provides a set of methods to support setting and querying
 * of certain DBMS attributes, such as {@link getNullConversion nullConversion}.
 *
 * Since CDbConnection implements the interface IApplicationComponent, it can
 * be used as an application component and be configured in application configuration,
 * like the following,
 * <pre>
 * array(
 *	 'components'=>array(
 *		 'db'=>array(
 *			 'class'=>'CDbConnection',
 *			 'connectionString'=>'sqlite:path/to/dbfile',
 *		 ),
 *	 ),
 * )
 * </pre>
 *
 * @property boolean $active Whether the DB connection is established.
 * @property PDO $pdoInstance The PDO instance, null if the connection is not established yet.
 * @property CDbTransaction $currentTransaction The currently active transaction. Null if no active transaction.
 * @property CDbSchema $schema The database schema for the current connection.
 * @property CDbCommandBuilder $commandBuilder The command builder.
 * @property string $lastInsertID The row ID of the last row inserted, or the last value retrieved from the sequence object.
 * @property mixed $columnCase The case of the column names.
 * @property mixed $nullConversion How the null and empty strings are converted.
 * @property boolean $autoCommit Whether creating or updating a DB record will be automatically committed.
 * @property boolean $persistent Whether the connection is persistent or not.
 * @property string $driverName Name of the DB driver.
 * @property string $clientVersion The version information of the DB driver.
 * @property string $connectionStatus The status of the connection.
 * @property boolean $prefetch Whether the connection performs data prefetching.
 * @property string $serverInfo The information of DBMS server.
 * @property string $serverVersion The version information of DBMS server.
 * @property integer $timeout Timeout settings for the connection.
 * @property array $attributes Attributes (name=>value) that are previously explicitly set for the DB connection.
 * @property array $stats The first element indicates the number of SQL statements executed,
 * and the second element the total time spent in SQL execution.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.db
 * @since 1.0
 */
class SAEDbConnection extends CDbConnection
{
	private $_pdo_master;
	private $_pdo_slave;
	/**
	 * Constructor.
	 * Note, the DB connection is not established when this connection
	 * instance is created. Set {@link setActive active} property to true
	 * to establish the connection.
	 * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
	 * @param string $username The user name for the DSN string.
	 * @param string $password The password for the DSN string.
	 * @see http://www.php.net/manual/en/function.PDO-construct.php
	 */
	public function __construct($dsn='',$username='',$password='')
	{
		//强制使用SAE 的数据库
		$dsn = $dsn_m = $this->getSaeDBConn('m');
		$username = SAE_MYSQL_USER;
		$password = SAE_MYSQL_PASS;
		$this->connectionString=$dsn;
		$this->username=$username;
		$this->password=$password;
	}

	/**
	 * 增加读写分离,返回不同的数据库PDO链接
	 */
	public function getSaePdoInstance($isRead=null)
	{
		return $isRead?$this->_pdo_slave:$this->_pdo_master;
	}
	/**
	 * 判断该SQL是否读操作 
	 */
	public function isReadOperation($sql)
	{
		return preg_match('/^\s*(SELECT|SHOW|DESCRIBE|PRAGMA)/i',$sql);
	}

	/**
	 * 主从库判断选择
	 */
	public function getSaeDBConn($type = 's')
	{
		$db_user = SAE_MYSQL_USER;
		$db_password = SAE_MYSQL_PASS;
		$db_host_m =  SAE_MYSQL_HOST_M;
		$db_host_s = SAE_MYSQL_HOST_S;
		$db_port =  SAE_MYSQL_PORT;
		$db_name = SAE_MYSQL_DB;

		//主库
		$dsn['m'] = 'mysql:host='.$db_host_m.';port='.$db_port.';dbname='.$db_name;
		//从库
		$dsn['s'] = 'mysql:host='.$db_host_s.';port='.$db_port.';dbname='.$db_name;

		if(empty($this->username) OR 1)
			$this->username = $db_user;
		if(empty($this->password) OR 1)
			$this->password = $db_password;

		$connectionString = $dsn[$type]?$dsn[$type]:$dsn['s'];
		if(empty($this->connectionString))
			$this->connectionString = $connectionString;
		return $connectionString;
	}

	/**
	 * Opens DB connection if it is currently not
	 * @throws CException if connection fails
	 */
	protected function open()
	{
		if($this->_pdo===null)
		{
			if(empty($this->connectionString))
				throw new CDbException('CDbConnection.connectionString cannot be empty.');
			try
			{
				Yii::trace('Opening DB connection','system.db.CDbConnection');
				/*
				$this->_pdo=$this->createPdoInstance();
				$this->initConnection($this->_pdo);
				*/
				$dsn = $this->getSaeDBConn('m');
				$this->connectionString=$dsn;
				$this->_pdo=$this->createPdoInstance();
				$this->initConnection($this->_pdo);
				$this->_pdo_master = $this->_pdo;

				$dsn = $this->getSaeDBConn('s');
				$this->connectionString=$dsn;
				$this->_pdo_slave=$this->createPdoInstance();
				$this->initConnection($this->_pdo_slave);

				$this->_active=true;
			}
			catch(PDOException $e)
			{
				if(YII_DEBUG)
				{
					throw new CDbException('CDbConnection failed to open the DB connection: '.
						$e->getMessage(),(int)$e->getCode(),$e->errorInfo);
				}
				else
				{
					Yii::log($e->getMessage(),CLogger::LEVEL_ERROR,'exception.CDbException');
					throw new CDbException('CDbConnection failed to open the DB connection.',(int)$e->getCode(),$e->errorInfo);
				}
			}
		}
	}

	/**
	 * Returns the PDO instance.
	 * @return PDO the PDO instance, null if the connection is not established yet
	 */
	public function getPdoInstance()
	{
		// 增加读写分离
		$isRead = self::isReadOperation($query);
		$pdo = self::getSaePdoInstance($isRead);
		return $pdo;
		// return $this->_pdo;
	}
}
