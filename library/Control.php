<?php

namespace Hades;

use Cerberus\Cerberus;
use Cerberus\Action;
use Cerberus\Mircryption;
use Cerberus\Ccryption;

/**
 * Class Control
 * @package Hades
 * @author Stefan Hüsges
 * @link https://github.com/tronsha/hades Project on GitHub
 * @link https://github.com/tronsha/cerberus Cerberus Project on GitHub
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License
 */
class Control
{
    protected $db =  null;
    protected $action = null;

    public function __construct($db, $action)
    {
        $this->db = $db;
        $this->action = $action;
    }

    /**
     * @return Db|null
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return Action|null
     */
    public function getActions()
    {
        return $this->action;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function doJoin($param)
    {
        $join = $this->getActions()->join($param);
        Cerberus::msleep(2000);
        $status = $this->getDb()->getStatus(477);
        if ($status !== null) {
            return $status;
        }
        return $join;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function doPart($param)
    {
        $param = trim($param);
        if (empty($param) === true) {
            $param = $_SESSION['channel'];
        }
        if ($param == $_SESSION['channel']) {
            $_SESSION['channel'] = null;
        }
        if ($param !== null) {
            return $this->getActions()->part($param);
        }
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function doHop($param)
    {
        $result = $this->doPart($param);
        return $this->doJoin($result['channel']);
    }
}
