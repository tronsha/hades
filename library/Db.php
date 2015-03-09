<?php

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
//            $sql = 'SELECT `password` FROM `web` WHERE `email` = ' . $this->conn->quote($mail) . ' LIMIT 0, 1';
//            $stmt = $this->conn->query($sql);
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
//            $sql = 'UPDATE `web` SET `sid` = ' . $this->conn->quote($sid) .' WHERE `email` = ' . $this->conn->quote($mail);
//            $this->conn->query($sql);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param int $bot
     * @param string|null $channel
     * @return array
     */
    public function getChannel($bot, $channel = null)
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('channel', 'topic')
                ->from('channel')
                ->where('bot_id = ?')
                ->addOrderBy('channel', 'ASC')
                ->setParameter(0, $bot);
            if ($channel !== null) {
                $qb ->andWhere('channel = ?')
                    ->setParameter(1, $channel);
            }
            $stmt = $qb->execute();
//            $sql = 'SELECT `channel`, `topic` FROM `channel` WHERE `bot_id` = ' . $this->conn->quote($bot) . ($channel !== null ? ' AND `channel` = ' . $this->conn->quote($channel)  : '') . ' ORDER BY `channel` ASC';
//            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param int $last
     * @param string $channel
     * @param int $bot
     * @return array;
     */
    public function getChannelOutput($last, $channel, $bot)
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $qb ->select('id', 'nick AS name', 'text', 'time')
                ->from('log')
                ->where('id > ? AND bot_id = ? AND command LIKE \'PRIVMSG\' AND rest LIKE ?')
                ->addOrderBy('id', 'DESC')
                ->setParameter(0, $last)
                ->setParameter(1, $bot)
                ->setParameter(2, $channel);
            if ($last == 0) {
                $qb->setMaxResults(100);
            }
            $stmt = $qb->execute();
//            $sql = 'SELECT `id`, `nick` AS `name`, `text`, `time` FROM `log` WHERE `id` > ' . $this->conn->quote($last) . ' AND `bot_id` = ' . $this->conn->quote($bot) . ' AND `command` LIKE "PRIVMSG" AND `rest` LIKE ' . $this->conn->quote($channel) . ' ORDER BY id DESC' . ($last == 0 ? ' LIMIT 100' : '');
//            $stmt = $this->conn->query($sql);
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
            $stmt = $qb
                ->select('id')
                ->from('bot')
                ->where('stop IS NULL OR stop = \'NULL\'')
                ->addOrderBy('id', 'DESC')
                ->execute();
//            $sql = 'SELECT * FROM `bot` WHERE `stop` IS NULL ORDER BY id DESC';
//            $stmt = $this->conn->query($sql);
            return $stmt->fetch();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
