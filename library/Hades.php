<?php

namespace Hades;

use Cerberus\Cerberus;

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
    protected $db = null;

    /**
     *
     */
    public function __construct()
    {
        session_start();
        $this->session = session_id();
        $path = Cerberus::getPath();
        $config = parse_ini_file($path . '/config.ini', true);
        $this->db = new Db($config['db']);
        $this->db->connect();
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
    public function pull()
    {
        return json_encode(
            array(
                array(
                    'time' => date("H:i:s"),
                    'name' => 'Neo',
                    'text' => 'follow the white rabbit'
                )
            )
        );
    }

    /**
     *
     */
    public function push()
    {

    }
}
