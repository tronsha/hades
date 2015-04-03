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
}
