<?php

namespace Hades;

use Cerberus\Cerberus;

/**
 * Class Command
 * @package Hades
 * @author Stefan Hüsges
 * @link https://github.com/tronsha/hades Project on GitHub
 * @link https://github.com/tronsha/cerberus Cerberus Project on GitHub
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License
 */
class Command
{
    protected $db =  null;
    protected $action = null;

    public function __construct($action, $db)
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
    public function me($param)
    {
        return $this->getActions()->me($_SESSION['channel'], $param);
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function nick($param)
    {
        return $this->getActions()->nick($param);
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function join($param)
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
    public function part($param)
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
    public function hop($param)
    {
        $result = $this->part($param);
        return $this->join($result['channel']);
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function topic($param)
    {
        $this->getActions()->topic($_SESSION['channel'], $param);
        Cerberus::msleep(2000);
        $status = $this->getDb()->getStatus([482, 442, 403]);
        return $status;
    }

    /**
     * @param string $param
     */
    public function crypt($param)
    {
        $params = explode(' ', $param);
        if (strtolower($params[0]) == 'unset') {
            unset($_SESSION['crypt'][$_SESSION['channel']]);
        } elseif (strtolower($params[0]) == 'set') {
            if (strtolower($params[1]) == 'encode') {
                $_SESSION['crypt'][$_SESSION['channel']]['encode'] = trim($params[2]);
            } elseif (strtolower($params[1]) == 'decode') {
                $_SESSION['crypt'][$_SESSION['channel']]['decode'] = trim($params[2]);
            } else {
                $_SESSION['crypt'][$_SESSION['channel']]['encode'] = trim($params[1]);
                $_SESSION['crypt'][$_SESSION['channel']]['decode'] = trim($params[1]);
            }
        } else {
            $_SESSION['crypt'][$_SESSION['channel']]['method'] = trim($params[0]);
        }
    }
}
