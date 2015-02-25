<?php

namespace Hades;

use Cerberus\Cerberus;

class Hades
{
    protected $db = null;

    public function __construct()
    {
        $path = Cerberus::getPath();
        $config = parse_ini_file($path . '/config.ini', true);
        $this->db = new Db($config['db']);
        $this->db->connect();
    }

    public function pull()
    {
        return json_encode(
            array(
                array(
                    'time' => date("H:i:s"),
                    'name' => 'Foo',
                    'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'
                ),
                array(
                    'time' => date("H:i:s"),
                    'name' => 'Bar',
                    'text' => 'At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.'
                ),
                array(
                    'time' => date("H:i:s"),
                    'name' => 'Baz',
                    'text' => 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim.'
                )
            )
        );
    }

    public function push()
    {

    }
}
