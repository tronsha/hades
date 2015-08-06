<?php

namespace Hades;

use Cerberus\Cerberus;
use Cerberus\Action;

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
        $path = Cerberus::getPath();
        $this->config = parse_ini_file($path . '/config.ini', true);
        $this->db = new Db($this->config['db']);
        $this->db->connect();
        $this->action = new Action(null, $this->db);
    }

    /**
     * @return Action|null
     */
    public function getAction()
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
        $hash = $this->db->getHash($username);
        if (password_verify($password, $hash) === true) {
            $_SESSION['username'] = $username;
            $this->db->setSession($_SESSION['username'], session_id());
            header('Location: index.php');
        }
    }

    /**
     *
     */
    public function logout()
    {
        $this->db->setSession($_SESSION['username'], '');
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
        $channel = $this->db->getChannel($channel);
        foreach ($channel as &$value) {
            $value['topic'] = htmlentities($value['topic']);
        }

        return json_encode($channel);
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
        $this->db->setPassword($this->session, $hash);
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
        $data = $this->db->getChannelOutput($_SESSION['last'], $_SESSION['channel']);
        if (count($data) > 0) {
            $_SESSION['last'] = $data[0]['id'];
            krsort($data);
            $data = array_values($data);
            $formatter = new Formatter;
            foreach ($data as &$value) {
                if (preg_match("/\x01([A-Z]+)( .+)?\x01/i", $value['text'], $matches)) {
                    if ($matches[1] === 'ACTION') {
                        $value['text'] = $matches[2];
                        $value['action'] = '1';
                    }
                }
                $value['text'] = $formatter->irc2html($value['text']);
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
            $return = json_encode($this->getAction()->privmsg($_SESSION['channel'], $input));
        } else {
            preg_match_all('/^\/([a-z]+)(\ (.*))?$/i', $input, $matches, PREG_SET_ORDER);
            $return = $this->doAction($matches[0][1], $matches[0][3]);
        }
        return $return;
    }

    /**
     * @param string $action
     * @param string $param
     * @return string
     */
    public function doAction($action, $param)
    {
        switch ($action) {
            case 'join':
                return json_encode($this->getAction()->join($param));
                break;
            case 'part':
                return json_encode($this->getAction()->part($param));
                break;
            case 'nick':
                return json_encode($this->getAction()->nick($param));
                break;
            default:
                return json_encode(null);
        }
    }

    /**
     * @return int
     */
    public function getBotId()
    {
        $bot = $this->db->getLastBot();

        return $bot['id'];
    }
}
