<?php

/*
 * Cerberus IRCBot
 * Copyright (C) 2008 - 2016 Stefan Hüsges
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, see <http://www.gnu.org/licenses/>.
 */

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
    public function msg($param)
    {
        $params = explode(' ', $param, 2);
        if (count($params) === 2) {
            return $this->getActions()->privmsg($params[0], $params[1]);
        }
        return false;
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
        $nick = $this->getActions()->nick($param);
        Cerberus::msleep(2000);
        $status = $this->getDb()->getStatus([432, 433]);
        if ($status !== null) {
            return $status;
        }
        return $nick;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function join($param)
    {
        $join = $this->getActions()->join($param);
        Cerberus::msleep(2000);
        $status = $this->getDb()->getStatus([475, 477]);
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
        if ($param === $_SESSION['channel']) {
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
    public function invite($param)
    {
        $param = trim($param);
        if (empty($param) === true) {
            return false;
        }
        $params = explode(' ', $param);
        $nick = $params[0];
        if (count($params) === 1) {
            $channel = $_SESSION['channel'];
        } else {
            $channel = $params[1];
        }
        $invite = $this->getActions()->invite($channel, $nick);
        $status = $this->getDb()->getStatus([403, 442, 443]);
        if ($status !== null) {
            return $status;
        }
        return $invite;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function op($param)
    {
        $param = trim($param);
        if (empty($param) === true) {
            $op = $this->getActions()->op($_SESSION['channel']);
        } else {
            $params = explode(' ', $param);
            $count = count($params);
            if ($count === 1) {
                $op = $this->getActions()->op($params[0]);
            } elseif ($count >= 2) {
                for ($i = 1; $i < $count; $i++) {
                    $op[] = $this->getActions()->op($params[0], $params[$i]);
                }
            }
        }
        return $op;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function deop($param)
    {
        $params = explode(' ', trim($param));
        $count = count($params);
        if ($count === 1) {
            return false;
        } elseif ($count >= 2) {
            for ($i = 1; $i < $count; $i++) {
                $deop[] = $this->getActions()->deop($params[0], $params[$i]);
            }
        }
        return $deop;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function kick($param)
    {
        list($channel, $user, $comment) = explode(' ', trim($param), 3);
        $kick = $this->getActions()->kick($channel, $user, $comment);
        Cerberus::msleep(2000);
        $status = $this->getDb()->getStatus([401, 442, 482]);
        if ($status !== null) {
            return $status;
        }
        return $kick;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function topic($param)
    {
        $topic = $this->getActions()->topic($_SESSION['channel'], $param);
        Cerberus::msleep(2000);
        $status = $this->getDb()->getStatus([403, 442, 482]);
        if ($status !== null) {
            return $status;
        }
        return $topic;
    }

    /**
     * @param string $param
     */
    public function crypt($param)
    {
        $params = explode(' ', $param);
        if (strtolower($params[0]) === 'unset') {
            unset($_SESSION['crypt'][$_SESSION['channel']]);
        } elseif (strtolower($params[0]) === 'set') {
            if (strtolower($params[1]) === 'encode') {
                $_SESSION['crypt'][$_SESSION['channel']]['encode'] = trim($params[2]);
            } elseif (strtolower($params[1]) === 'decode') {
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
