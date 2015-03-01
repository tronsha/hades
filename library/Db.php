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
            $sql = 'SELECT `password` FROM `web` WHERE `email` = ' . $this->conn->quote($mail) . ' LIMIT 0, 1';
            $stmt = $this->conn->query($sql);
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
            $sql = 'UPDATE `web` SET `sid` = ' . $this->conn->quote($sid) .' WHERE `email` = ' . $this->conn->quote($mail);
            $this->conn->query($sql);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getChannel($bot)
    {
        try {
            $sql = 'SELECT `channel`, `topic` FROM `channel` WHERE `bot_id` = ' . $this->conn->quote($bot) . ' ORDER BY `channel` ASC';
            $stmt = $this->conn->query($sql);
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
            $sql = 'SELECT `id`, `nick` AS `name`, `text`, `time` FROM `log` WHERE `id` > ' . $this->conn->quote($last) . ' AND `bot_id` = ' . $this->conn->quote($bot) . ' AND `command` LIKE "PRIVMSG" AND `rest` LIKE ' . $this->conn->quote($channel) . ' ORDER BY id DESC' . ($last == 0 ? ' LIMIT 50' : '');
            $stmt = $this->conn->query($sql);
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
            $sql = 'SELECT * FROM `bot` WHERE `stop` IS NULL ORDER BY id DESC';
            $stmt = $this->conn->query($sql);
            return $stmt->fetch();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
