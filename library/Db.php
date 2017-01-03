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

use Cerberus\Db as BaseDb;

/**
 * Class Db
 * @package Hades
 * @author Stefan Hüsges
 * @link https://github.com/tronsha/hades Project on GitHub
 * @link https://github.com/tronsha/cerberus Cerberus Project on GitHub
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License
 */
class Db extends BaseDb
{
    public function __construct($config)
    {
        parent::__construct($config);
        if (isset($_SESSION['bot']) === true) {
            $this->botId = $_SESSION['bot'];
        }
    }

     /**
     * @param string $mail
     * @return string;
     */
    public function getHash($mail)
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $stmt = $qb
                ->select('password')
                ->from('web')
                ->where('email = ?')
                ->setMaxResults(1)
                ->setParameter(0, $mail)
                ->execute();
            return $stmt->fetch()['password'];
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param string $mail
     * @param string $sid
     */
    public function setSession($mail, $sid)
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->update('web')
                ->set('sid', '?')
                ->where('email = ?')
                ->setParameter(0, $sid)
                ->setParameter(1, $mail)
                ->execute();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param string|null $channel
     * @return array
     */
    public function getChannel($channel = null)
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('channel', 'topic')
                ->from('channel')
                ->where('bot_id = ?')
                ->addOrderBy('channel', 'ASC')
                ->setParameter(0, $this->botId);
            if ($channel !== null) {
                $qb ->andWhere('channel = ?')
                    ->setParameter(1, $channel);
            }
            $stmt = $qb->execute();
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param string $channel
     * @return array
     */
    public function getUser($channel)
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('username')
                ->from('channel_user')
                ->where('bot_id = ?')
                ->andWhere('channel = ?')
                ->addOrderBy('username', 'ASC')
                ->setParameter(0, $this->botId)
                ->setParameter(1, $channel);
            $stmt = $qb->execute();
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getWhisperList()
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('IF(direction = "<", nick, channel) AS channel')
                ->from('log_privmsg')
                ->where('bot_id = ?')
                ->andWhere('SUBSTR(channel, 1, 1) NOT IN ("#", "&")')
                ->setParameter(0, $this->botId)
                ->groupBy('channel');
            $stmt = $qb->execute();
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param int $last
     * @param string $channel
     * @return array;
     */
    public function getChannelOutput($last, $channel)
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('id', 'nick AS name', 'text', 'time', 'direction')
                ->from('log_privmsg')
                ->where('id > ?')
                ->andWhere('bot_id = ?')
                ->andWhere('channel = ?')
                ->addOrderBy('id', 'DESC')
                ->setParameter(0, $last)
                ->setParameter(1, $this->botId)
                ->setParameter(2, $channel);
            if ($last === 0) {
                $qb->setMaxResults(2000);
            }
            $stmt = $qb->execute();
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getLastBot()
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('id')
                ->from('bot')
                ->addOrderBy('id', 'DESC');
            if ($this->config['driver'] === 'pdo_sqlite') {
                $qb->where('stop = \'NULL\'');
            } else {
                $qb->where('stop IS NULL');
            }
            $stmt = $qb->execute();
            return $stmt->fetch();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getBotData()
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('*')
                ->from('bot')
                ->where('id = ?')
                ->setParameter(0, $this->botId);
            $stmt = $qb->execute();
            return $stmt->fetch();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
