<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthenticatoinFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        if (!empty($loggeduserdata))
        {
            $userModel = model('System/UserModel');
            $user = $userModel->find($loggeduserdata['id']);
            
            if($user['login_id'] != ($loggeduserdata['login_id']??' _'))
                return redirect()->to(site_url('/login'));

            $user_role = $loggeduserdata['role'];
            $allowed_roles = [];
            if(is_array($arguments))
                $allowed_roles = $arguments;
            if(!$this->is_logged_user_role_permitted($user_role, $allowed_roles))
                return redirect()->to(site_url('/?permission=0'));
        }
        else
            return redirect()->to(site_url('/login'));
    }

    private function is_logged_user_role_permitted(string $user_role, array $allowed_roles)
    {
        if($user_role === 'SUPER_ADMIN')
            return true;

        foreach($allowed_roles as $allowed_role)
        {
            if(($allowed_role === $user_role) || ($allowed_role === 'ANY_USER'))
                return true;
        }
        return false;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
