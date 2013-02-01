<?php
/* 
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of NestedPDO
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class NestedPDO extends PDO {
    // Database drivers that support SAVEPOINTs.
    protected static $savepointTransactions = array("pgsql", "mysql");

    // The current transaction level.
    protected $transLevel = 0;

    protected function nestable() {
        return in_array($this->getAttribute(PDO::ATTR_DRIVER_NAME),
                        self::$savepointTransactions);
    }

    public function beginTransaction() {
        if($this->transLevel == 0 || !$this->nestable()) {
            parent::beginTransaction();
        } else {
            $this->exec("SAVEPOINT LEVEL{$this->transLevel}");
        }
        $this->transLevel++;
    }

    public function commit() {
        $this->transLevel--;

        if($this->transLevel == 0 || !$this->nestable()) {
            parent::commit();
        } else {
            $this->exec("RELEASE SAVEPOINT LEVEL{$this->transLevel}");
        }
    }

    public function rollBack() {
        $this->transLevel--;
        if($this->transLevel == 0 || !$this->nestable()) {
            parent::rollBack();
        } else {
            $this->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->transLevel}");
        }
    }
}
?>
