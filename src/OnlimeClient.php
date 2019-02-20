<?php
namespace AppZz\Http;
use AppZz\Helpers\Arr;
use AppZz\Http\CurlClient;
use Exception;

class OnlimeClient {

	const AUTH_URL = 'https://my.onlime.ru/session/login';
	const CABINET_URL = 'https://my.onlime.ru/json/cabinet';

    private $_username;
    private $_password;
    private $_csrf;
    private $_cookies;

	public function __construct ($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
	}

    private function _auth ()
    {
        $fields = [
            'login_credentials[login]'    => $this->_username,
            'login_credentials[password]' => $this->_password
        ];

        $request = CurlClient::post (OnlimeClient::AUTH_URL, $fields)
                                ->browser('chrome', 'mac')
                                ->referer('https://www.onlime.ru')
                                ->accept('json', 'gzip')
                                ->form();

        $status = null;
        $response = $request->send ();

        if ($response->get_status() === 200)
        {
            $body = $response->get_body ();
            $this->_cookies = (string) $response->get_headers()->offsetGet('cookies');

            if (preg_match("#var.+wtf.+\=.+'(.*)'#iu", $body, $pr)) {
                $this->_csrf = $pr[1];
                return true;
            }
        }

        return false;
    }

    public function balance()
    {
        if ( ! ($this->_auth ())) {
            throw new Exception ('Auth Failed');
        }

        if ( ! $this->_csrf) {
            throw new Exception ('CSRF Failed');
        }

        $request = CurlClient::get (OnlimeClient::CABINET_URL)
                                ->browser('chrome', 'mac')
                                ->accept('json', 'gzip')
                                ->cookies ($this->_cookies)
                                ->headers (['X-Wtf'=>$this->_csrf])
                                ->ajax()
                                ->form();

        $response = $request->send ();

        if ($response->get_status() === 200)
        {
            $body = $response->get_body ();
            return json_decode ($body, true);
        }

        return false;
    }
}
