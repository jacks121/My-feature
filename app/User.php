<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Request;

class User extends Model
{
    public function signup()
    {
        $has_username_and_password = $this->has_username_and_password();
        if (!$has_username_and_password)
            return ['status' => 0 , 'msg' => '用户名和密码不能为空'];

        $username = $has_username_and_password['username'];
        $password = $has_username_and_password['password'];

        $user_exists = $this
            ->where('username',$username)
            ->exists();

        if ($user_exists)
            return ['status' => 0, 'msg' => '用户名已存在'];

        $user = $this;
        $hashed_password = Hash::make($password);
        $this->password = $hashed_password;
        $this->username = $username;
        if ($this->save())
            return ['status' => 1, 'msg' => $user->id];
        else
            return ['status' => 0, 'msg' => '注册失败'];

    }

    public function login()
    {
        //检查用户名密码是否存在
        $has_username_and_password = $this->has_username_and_password();
        if (!$has_username_and_password)
            return ['status' => 0 , 'msg' => '用户名和密码不能为空'];

        $username = $has_username_and_password['username'];
        $password = $has_username_and_password['password'];

        //检查用户是否已经注册
        $user = $this->where('username',$username)->first();
        if (!$user)
            return ['status' => 0 , 'msg' => '用户不存在'];
        $hashed_password = $user->password;

        //检查密码是否正确
        if (!Hash::check($password,$hashed_password))
            return ['status' => 0 , 'msg' => '密码有误'];

        session()->put('username',$user->username);
        session()->put('user_id',$user->id);

        return ['status' => 1 , 'id' => $user->id];
    }

    private function has_username_and_password()
    {
        $username = Request::get('username');
        $password = Request::get('password');
        if ($username && $password)
            return ['username' => $username,'password' => $password];
        return false;
    }

    public function logout()
    {
        session()->forget('username');
        session()->forget('password');
        return ['status'=> 1];
    }

    public function is_login_in()
    {
        return session('user_id') ?:false;
    }
}
