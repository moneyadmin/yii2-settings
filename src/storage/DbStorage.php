<?php
/**
 * @link https://github.com/LAV45/yii2-settings
 * @copyright Copyright (c) 2016 LAV45
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\settings\storage;

use yii\db\Query;
use yii\db\Connection;
use yii\di\Instance;
use yii\base\Object;

/**
 * Class DbStorage
 * @package lav45\settings\storage
 */
class DbStorage extends Object implements StorageInterface
{
    /**
     * @var Connection|array|string
     */
    public $db = 'db';
    /**
     * @var string
     */
    public $tableName = '{{%settings}}';

    /**
     * Initializes the application component.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    public function getValue($key)
    {
        return (new Query())
            ->select(['data'])
            ->from($this->tableName)
            ->where(['id' => $key])
            ->limit(1)
            ->createCommand($this->db)
            ->queryScalar();
    }

    public function setValue($key, $value)
    {
        $query = (new Query())->createCommand($this->db);
        $result = $query->update($this->tableName, ['data' => $value], ['id' => $key])->execute();
        if ($result == 0) {
            $result = $query->insert($this->tableName, ['id' => $key, 'data' => $value])->execute();
        }
        return $result > 0;
    }

    public function deleteValue($key)
    {
        $result = (new Query())->createCommand($this->db)
            ->delete($this->tableName, ['id' => $key])
            ->execute();

        return $result > 0;
    }
}
