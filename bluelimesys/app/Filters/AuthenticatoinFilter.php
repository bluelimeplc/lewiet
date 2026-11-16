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

            $user_roles = $loggeduserdata['roles'] ?? [$loggeduserdata['role'] ?? ''];
            $user_roles = is_array($user_roles) ? $user_roles : [$user_roles];
            $allowed_roles = is_array($arguments)
                ? $arguments
                : (empty($arguments) ? [] : explode(',', (string) $arguments));
            if(!$this->are_logged_user_roles_permitted($user_roles, $allowed_roles))
                return redirect()->to(site_url('/?permission=0'));
        }
        else
            return redirect()->to(site_url('/login'));
    }

    private function are_logged_user_roles_permitted(array $user_roles, array $allowed_roles)
    {
        foreach($user_roles as $user_role) {
            $user_role = strtoupper(trim((string) $user_role));
            if($user_role === 'SUPER_ADMIN' || $user_role === 'ADMIN')
                return true;

            foreach($allowed_roles as $allowed_role)
            {
                $allowed_role = strtoupper(trim((string) $allowed_role));
                if(($allowed_role === $user_role) || ($allowed_role === 'ANY_USER'))
                    return true;
            }
        }
        return false;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
