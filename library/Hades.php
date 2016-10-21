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

use Cerberus\Action;
use Cerberus\Ccryption;
use Cerberus\Cerberus;
use Cerberus\Mircryption;
use Cerberus\Translate;

/**
 * Class Hades
 * @package Hades
 * @author Stefan Hüsges
 * @link https://github.com/tronsha/hades Project on GitHub
 * @link https://github.com/tronsha/cerberus Cerberus Project on GitHub
 * @license http://www.gnu.org/licenses/gpl-3.0 GNU General Public License
 */
class Hades
{
    protected $session = '';
    protected $config = null;
    protected $db = null;
    protected $action = null;
    protected $translate = null;

    /**
     *
     */
    public function __construct()
    {
        session_start();
        $this->session = session_id();
        $this->config = Cerberus::loadConfig();
        $this->db = new Db($this->config['db']);
        $this->db->connect();
        $this->action = new Action(null, $this->db);
        $this->translate = new Translate();
        $this->translate->setLanguage('de');
        $this->translate->loadTranslationFile('frontend');
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
     * @param string $host
     */
    public function setHost($host)
    {
        if ($_GET['pw'] === md5($this->config['frontend']['password'])) {
            $path = Cerberus::getPath();
            $content = file_get_contents($path . '/config.ini');
            $content = preg_replace('/host\ \=\ [0-9\.]+/', 'host = ' . $host, $content);
            file_put_contents($path . '/config.ini', $content);
        }
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function login($username, $password)
    {
        $hash = $this->getDb()->getHash($username);
        if (password_verify($password, $hash) === true) {
            $_SESSION['username'] = $username;
            $this->getDb()->setSession($_SESSION['username'], session_id());
            header('Location: index.php');
        }
    }

    /**
     *
     */
    public function logout()
    {
        $this->getDb()->setSession($_SESSION['username'], '');
        session_unset();
        session_destroy();
        header('Location: login.php');
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $_SESSION['channel'] = $channel;
        $_SESSION['last'] = 0;
    }

    /**
     * @param string|null $channel
     * @return string
     */
    public function getChannel($channel = null)
    {
        $channel = $this->getDb()->getChannel($channel);
        foreach ($channel as &$value) {
            $value['topic'] = htmlentities($value['topic']);
        }
        return json_encode($channel);
    }

    /**
     * @return string
     */
    public function getWhisper()
    {
        $channel = $this->getDb()->getWhisperList();
        return json_encode($channel);
    }

    /**
     * @return string
     */
    public function getUser()
    {
        if (empty($_SESSION['channel'])) {
            return json_encode(null);
        }
        $user = $this->getDb()->getUser($_SESSION['channel']);
        return json_encode($user);
    }

    /**
     * @return string
     */
    public function getChannellist()
    {
        $channelList = $this->getDb()->getChannellist();
        foreach ($channelList as &$value) {
            $value['topic'] = htmlentities($value['topic']);
        }
        return json_encode($channelList);
    }

    /**
     * @return bool
     */
    public function isLoggedin()
    {
        return empty($_SESSION['username']) ? false : true;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->getDb()->setPassword($this->session, $hash);
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        if (isset($_SESSION['channel']) === false) {
            return json_encode(null);
        }
        if (isset($_SESSION['last']) === false) {
            $_SESSION['last'] = 0;
        }
        $data = $this->getDb()->getChannelOutput($_SESSION['last'], $_SESSION['channel']);
        if (count($data) > 0) {
            $_SESSION['last'] = $data[0]['id'];
            krsort($data);
            $data = array_values($data);
            $formatter = new Formatter;
            foreach ($data as $key => &$value) {
                if (preg_match('/\+LT ([0-9A-Z]+) (BEGIN|END|PART)(?: ([0-9]+) ([a-zA-Z0-9\+\/\=]+))?/i', $value['text'], $matches)) {
                    if ($matches[2] === 'BEGIN') {
                        $_SESSION['longtext'][$matches[1]] = [];
                        unset($data[$key]);
                    } elseif ($matches[2] === 'PART') {
                        $_SESSION['longtext'][ $matches[1]][$matches[3]] = $matches[4];
                        unset($data[$key]);
                    } elseif ($matches[2] === 'END') {
                        $text = gzuncompress(base64_decode(implode('', $_SESSION['longtext'][$matches[1]])));
                        if (strtoupper(hash('crc32b', $text)) === substr($matches[1], 0, 8)) {
                            $value['text'] = $text;
                        } else {
                            $value['text'] = print_r($_SESSION['longtext'][$matches[1]], true);
                        }
                        unset($_SESSION['longtext'][$matches[1]]);
                    }
                }
                if (preg_match('/\+(OK|CC) (.+)/i', $value['text'], $matches)) {
                    if (
                        (
                            empty($_SESSION['crypt'][$_SESSION['channel']]['decode']) === false
                            &&
                            $value['direction'] === '<'
                        )
                        ||
                        (
                            empty($_SESSION['crypt'][$_SESSION['channel']]['encode']) === false
                            &&
                            $value['direction'] === '>'
                        )
                    ) {
                        if ($value['direction'] === '>') {
                            $key = $_SESSION['crypt'][$_SESSION['channel']]['encode'];
                        } else {
                            $key = $_SESSION['crypt'][$_SESSION['channel']]['decode'];
                        }
                        if ($matches[1] === 'OK') {
                            $value['crypt'] = $value['text'];
                            $value['text'] = Mircryption::decode($matches[2], $key);
                        } elseif ($matches[1] === 'CC') {
                            $value['crypt'] = $value['text'];
                            $value['text'] = Ccryption::decode($matches[2], $key);
                        }
                    }
                }
                if (preg_match("/\x01([A-Z]+)( .+)?\x01/i", $value['text'], $matches)) {
                    if ($matches[1] === 'ACTION') {
                        $value['text'] = $matches[2];
                        $value['action'] = '1';
                    }
                }
                $value['text'] = $formatter->irc2html($value['text']);
                $value['text'] = preg_replace('/https?:\/\/\S+\.(?:png|jpg|jpeg|gif)(\?\S+)?/i', '<img src="$0" alt="$0">', $value['text']);
                $value['text'] = preg_replace('/data:image\/[a-z]+\;base64\,[a-zA-Z0-9\+\/]+\=*/i', '<img src="$0">', $value['text']);
                $value['text'] = preg_replace('/https?:\/\/\S+\.(mp3|wav)(\?\S+)?/i', '<audio controls><source src="$0" type="audio/$1"></audio>', $value['text']);
                $value['text'] = preg_replace('/data:(audio\/[a-z]+)\;base64\,[a-zA-Z0-9\+\/]+\=*/i', '<audio controls><source src="$0" type="$1"></audio>', $value['text']);
                $value['text'] = preg_replace('/https?:\/\/\S+\.(mp4|ogg)(\?\S+)?/i', '<video controls><source src="$0" type="video/$1"></video>', $value['text']);
                $value['text'] = preg_replace('/data:(video\/[a-z]+)\;base64\,[a-zA-Z0-9\+\/]+\=*/i', '<video controls><source src="$0" type="$1"></video>', $value['text']);
                $value['text'] = preg_replace('/(?:https:\/\/youtu.be\/)(.+)/i', '<iframe class="youtube" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $value['text']);
                $value['text'] = preg_replace('/https:\/\/www.youtube.com\/watch\?(?:\S*?)?v\=([^\?&=]+)(?:\S*)/i', '<iframe class="youtube" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $value['text']);
                $value['text'] = preg_replace('/https?:\/\/vimeo.com\/([0-9]+)(?:\S*)/i', '<iframe src="https://player.vimeo.com/video/$1?byline=0" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', $value['text']);
                $value['text'] = preg_replace('/https?:\/\/jsfiddle.net\/([^\.\s]+)/i', '<iframe class="jsfiddle" src="//jsfiddle.net/$1embedded/" frameborder="0" allowfullscreen></iframe>', $value['text']);
                $value['text'] = preg_replace('/https?:\/\/pastebin.com\/(?:raw.php\?i\=)?([^\.\s\=]+)/i', '<iframe class="pastebin" src="http://pastebin.com/embed_iframe.php?i=$1" frameborder="0" allowfullscreen></iframe>', $value['text']);
                $value['text'] = preg_replace('/(?![^<]+>)https?:\/\/\S+/i', '<span class="link">$0</span>', $value['text']);
                $value['text'] = preg_replace('/(?![^<]+>)#[^,\s]+/i', '<span class="channel">$0</span>', $value['text']);
            }
        }
        return json_encode($data);
    }

    /**
     * @param string $input
     * @return string
     */
    public function useInput($input)
    {
        if (substr($input, 0, 1) !== '/') {
            if (
                empty($_SESSION['crypt'][$_SESSION['channel']]['method']) === false
                &&
                empty($_SESSION['crypt'][$_SESSION['channel']]['encode']) === false
            ) {
                $key = $_SESSION['crypt'][$_SESSION['channel']]['encode'];
                if ($_SESSION['crypt'][$_SESSION['channel']]['method'] === 'mirc') {
                    $input = '+OK ' . Mircryption::encode($input, $key);
                } elseif ($_SESSION['crypt'][$_SESSION['channel']]['method'] === 'cc') {
                    $input = '+CC ' . Ccryption::encode($input, $key);
                }
            }
            if (strlen($input) > 256) {
                $i = 0;
                $uniqid = strtoupper(uniqid(hash('crc32b', $input)));
                $inputGz = gzcompress($input, 9);
                $inputGz64 = base64_encode($inputGz);
                $array = explode(' ', trim(chunk_split($inputGz64, 256, ' ')));
                $this->getActions()->privmsg($_SESSION['channel'], '+LT ' . $uniqid . ' BEGIN', 10);
                foreach ($array as $part) {
                    $this->getActions()->privmsg($_SESSION['channel'], '+LT ' . $uniqid . ' PART ' . ++$i . ' ' . $part, 10);
                }
                $this->getActions()->privmsg($_SESSION['channel'], '+LT ' . $uniqid . ' END', 10);
            } else {
                $return = $this->getActions()->privmsg($_SESSION['channel'], $input);
            }
        } else {
            preg_match_all('/^\/([a-z]+)(\ (.*))?$/i', $input, $matches, PREG_SET_ORDER);
            $return = $this->doAction($matches[0][1], isset($matches[0][3]) ? $matches[0][3] : null);
        }
        return json_encode($return);
    }

    /**
     * @param string $action
     * @param string $param
     * @return string
     */
    public function doAction($action, $param)
    {
        $command = new Command($this->getActions(), $this->getDb());
        $action = strtolower($action);
        $param = trim($param);
        $data = json_encode(['channel' => $_SESSION['channel'], 'param' => $param]);
        switch ($action) {
            case 'exit':
            case 'logout':
                return ['action' => 'logout'];
                break;
            case 'msg':
            case 'privmsg':
                return $command->msg($param);
                break;
            case 'me':
                return $command->me($param);
                break;
            case 'nick':
                return $command->nick($param);
                break;
            case 'join':
                return $command->join($param);
                break;
            case 'part':
                return $command->part($param);
                break;
            case 'hop':
                return $command->hop($param);
                break;
            case 'invite':
                return $command->invite($param);
                break;
            case 'op':
                return $command->op($param);
                break;
            case 'deop':
                return $command->deop($param);
                break;
            case 'kick':
                return $command->kick($param);
                break;
            case 'topic':
                return $command->topic($param);
                break;
            case 'crypt':
                return $command->crypt($param);
                break;
            case 'list':
                return $command->channelList();
                break;
            default:
                return $this->getActions()->control($action, $data);
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        $status = $this->getDb()->getStatus();
        return json_encode($status);
    }

    /**
     * @return int
     */
    public function getBotId()
    {
        $bot = $this->getDb()->getLastBot();
        return $bot['id'];
    }

    /**
     * @return string
     */
    public function isRunning()
    {
        $bot = $this->getDb()->getBotData();
        if ($bot['stop'] !== null) {
            return json_encode(false);
        }
        if ((time() - strtotime($bot['ping'])) > 600) {
            return json_encode(false);
        }
        return json_encode(true);
    }

    /**
     * @param string $text
     * @param array $array
     * @param mixed $lang
     * @return string
     */
    public function __($text, $array = [], $lang = null)
    {
        return $this->translate->__($text, $array, $lang);
    }
}
