<?php

namespace Hades;

use Cerberus\Cerberus;
use Cerberus\Action;
use Cerberus\Mircryption;

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
    public function getUser()
    {
        if (empty($_SESSION['channel'])) {
            return json_encode(null);
        }
        $user = $this->getDb()->getUser($_SESSION['channel']);
        return json_encode($user);
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
            foreach ($data as &$value) {
                if (preg_match("/\+OK (.+)/i", $value['text'], $matches)) {
                    if (empty($_SESSION['mircryption'][$_SESSION['channel']]['decode']) === false) {
                        $key = $_SESSION['mircryption'][$_SESSION['channel']]['decode'];
                        $crypt = new Mircryption;
                        $value['crypt'] = $value['text'];
                        $value['text'] = $crypt->decode($matches[1], $key);
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
                $value['text'] = preg_replace('/(?:https:\/\/youtu.be\/)(.+)/i', '<iframe class="youtube" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $value['text']);
                $value['text'] = preg_replace('/https:\/\/www.youtube.com\/watch\?(?:\S*?)?v\=([^\?&=]+)(?:\S*)/i', '<iframe class="youtube" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $value['text']);
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
            if (empty($_SESSION['mircryption'][$_SESSION['channel']]['encode']) === false) {
                $key = $_SESSION['mircryption'][$_SESSION['channel']]['encode'];
                $crypt = new Mircryption;
                $input = '+OK ' . $crypt->encode($input, $key);
            }
            $return = $this->getActions()->privmsg($_SESSION['channel'], $input);
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
        $action = strtolower($action);
        $param = trim($param);
        $data = json_encode(['channel' => $_SESSION['channel'], 'param' => $param]);
        switch ($action) {
            case 'exit':
            case 'logout':
                return ['action' => 'logout'];
                break;
            case 'me':
                return $this->getActions()->me($_SESSION['channel'], $param);
                break;
            case 'nick':
                return $this->getActions()->nick($param);
                break;
            case 'join':
                return $this->getActions()->join($param);
                break;
            case 'part':
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
                break;
            case 'mircryption':
                $params = explode(' ', $param);
                if (strtolower($params[0]) == 'unset') {
                    unset($_SESSION['mircryption'][$_SESSION['channel']]);
                } elseif (strtolower($params[0]) == 'set') {
                    if (strtolower($params[1]) == 'encode') {
                        $_SESSION['mircryption'][$_SESSION['channel']]['encode'] = trim($params[2]);
                    } elseif (strtolower($params[1]) == 'decode') {
                        $_SESSION['mircryption'][$_SESSION['channel']]['decode'] = trim($params[2]);
                    } else {
                        $_SESSION['mircryption'][$_SESSION['channel']]['encode'] = trim($params[1]);
                        $_SESSION['mircryption'][$_SESSION['channel']]['decode'] = trim($params[1]);
                    }
                }
                break;
            default:
                return $this->getActions()->control($action, $data);
                break;
        }
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
}
