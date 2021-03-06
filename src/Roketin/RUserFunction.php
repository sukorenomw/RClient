<?php

namespace Roketin;

class RUserFunction extends Roketin
{
    /**
     * @var string
     */
    protected $arrUpdate = "";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $first_name
     * @param $last_name
     * @param $email
     * @param $phone
     * @param $password
     * @param $password_confirmation
     * @return mixed
     */
    public function register($first_name, $last_name, $email, $phone, $password, $password_confirmation)
    {
        $password              = $this->encrypter->encrypt($password);
        $password_confirmation = $this->encrypter->encrypt($password_confirmation);

        $result = $this->callAPI("user/register", compact('first_name', 'last_name', 'email', 'phone', 'password', 'password_confirmation'), "POST");
        return $result;
    }

    /**
     * @param $token
     * @return mixed
     */
    public function activate($token)
    {
        $result = $this->callAPI("user/activation/" . $token);
        if (!isset($result->errors)) {
            return true;
        }
        return $result;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function resendActivation($email)
    {
        $result = $this->callAPI("user/activation/resend/" . $email);
        if (!isset($result->errors)) {
            return true;
        }
        return $result;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function forgot($email)
    {
        $result = $this->callAPI("user/password/forgot", compact('email'), "POST");
        if (!isset($result->errors)) {
            return true;
        }
        return $result;
    }

    /**
     * @param $token
     * @param $password
     * @param $password_confirmation
     * @return mixed
     */
    public function resetPassword($token, $password, $password_confirmation)
    {
        $password              = $this->encrypter->encrypt($password);
        $password_confirmation = $this->encrypter->encrypt($password_confirmation);

        $result = $this->callAPI('user/password/reset/' . $token, compact('password', 'password_confirmation'), "POST");
        if (!isset($result->errors)) {
            return true;
        }
        return $result;
    }

    /**
     * @param $token
     * @return mixed
     */
    public function transactionHistory($token = null)
    {
        $token        = is_null($token) ? session('token') : $token;
        $this->routes = "user/transaction/history?token=" . $token;
        return $this;
    }

    /**
     * @param array $array
     * @return mixed
     */
    public function update(array $array)
    {
        if (!isset($array['old_password'])) {
            throw new \Exception("key 'old_password' is required", 1);
        }
        $array['old_password'] = $this->encrypter->encrypt($array['old_password']);
        if (isset($array['password'])) {
            $array['password'] = $this->encrypter->encrypt($array['password']);
        }

        return $this->callAPI('user/update?token=' . session('token'), $array, "POST");
    }
}
