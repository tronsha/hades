<?php

/*
 * Cerberus IRCBot
 * Copyright (C) 2008 - 2017 Stefan Hüsges
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
        if (2 === count($params)) {
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
        if (null !== $status) {
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
        if (null !== $status) {
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
        if (true === empty($param)) {
            $param = $_SESSION['channel'];
        }
        if ($param === $_SESSION['channel']) {
            $_SESSION['channel'] = null;
        }
        if (null !== $param) {
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
        if (true === empty($param)) {
            return false;
        }
        $params = explode(' ', $param);
        $nick = $params[0];
        if (1 === count($params)) {
            $channel = $_SESSION['channel'];
        } else {
            $channel = $params[1];
        }
        $invite = $this->getActions()->invite($channel, $nick);
        $status = $this->getDb()->getStatus([403, 442, 443]);
        if (null !== $status) {
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
        if (true === empty($param)) {
            $op = $this->getActions()->op($_SESSION['channel']);
        } else {
            $params = explode(' ', $param);
            $count = count($params);
            if (1 === $count) {
                $op = $this->getActions()->op($params[0]);
            } elseif (2 <= $count) {
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
        if (1 === $count) {
            return false;
        } elseif (2 <= $count) {
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
        if (null !== $status) {
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
        if (null !== $status) {
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
        if ('unset' === strtolower($params[0])) {
            unset($_SESSION['crypt'][$_SESSION['channel']]);
        } elseif ('set' === strtolower($params[0])) {
            if ('encode' === strtolower($params[1])) {
                $_SESSION['crypt'][$_SESSION['channel']]['encode'] = trim($params[2]);
            } elseif ('decode' === strtolower($params[1])) {
                $_SESSION['crypt'][$_SESSION['channel']]['decode'] = trim($params[2]);
            } else {
                $_SESSION['crypt'][$_SESSION['channel']]['encode'] = trim($params[1]);
                $_SESSION['crypt'][$_SESSION['channel']]['decode'] = trim($params[1]);
            }
        } else {
            $_SESSION['crypt'][$_SESSION['channel']]['method'] = trim($params[0]);
        }
    }

    /**
     * @return mixed
     */
    public function channelList()
    {
        return $this->getActions()->channelList();
    }
}
