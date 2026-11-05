<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index()
    {
        return view('login_form')
            . view('footer');
    }

    public function logout()
    {
        $userLoginfoModel = model('System/UserLoginfoModel');
        \BluelimeSystem::$Authentication::logout();
        if(session()->has('logged_info_id')){
            $logged_info_id = session()->get('logged_info_id');
            $userLoginfoModel->update($logged_info_id,['logout_time'=>date('Y-m-d h:i:s')]);
        }
        return redirect()->to(site_url('/login'));
    }

    public function authentication()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password))
            return redirect()->to(site_url('/login'));

        if (!$this->validate_login($username, $password))
            return redirect()->to(site_url('/login?fail=1'));


        $role_home = [
            "RECEPTION" => '/'.strtolower(BLULIME_DEFAULT_APP).'/reception',
            "DOCTOR" => '/'.strtolower(BLULIME_DEFAULT_APP).'/doctor',
            "NURSE" => '/'.strtolower(BLULIME_DEFAULT_APP).'/nurse',
            "ADMIN" => '/'.strtolower(BLULIME_DEFAULT_APP).'/admin',
            "MIS" => '/'.strtolower(BLULIME_DEFAULT_APP).'/report',
            "LABORATORY" => '/'.strtolower(BLULIME_DEFAULT_APP).'/laboratory',
            "STORE" => '/inventory/store',
            "CASHIER_HOSPITAL" => '/cashier',
            "CASHIER_INVENTORY" => '/cashier',
            "CALL_CENTER" => '/hospital/callcenter',
            "PATHOLOGY" => '/hospital/pathology',
            "FINANCE" => '/inventory/finance',
            "DISPENSARY" => '/inventory/dispensary',
            "SUPER_ADMIN" => '/'
        ];

        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();

        $role = $loggeduserdata['role'];

        $home_page_url = $role_home[$role] ?? '';

        if ($home_page_url === '')
            return "User role '$role' is not recognized.";
        $login_info = [
            "user_id" => $loggeduserdata['id'],
            "agent_device" => $this->get_user_agent_info(),
            "ip_address" => $this->request->getIPAddress(),
            "login_time" => date('Y-m-d h:i:s')
        ];

        $userLoginfoModel = model('System/UserLoginfoModel');
        $logged_id = $userLoginfoModel->insert($login_info,true);
        $_SESSION['logged_info_id'] = $logged_id;
        return redirect()->to(site_url($home_page_url));
    }

    private function validate_login(string $username, string $password)
    {
        $userModel = model('System/UserModel');

        $user = $userModel->where('username', $username)
                          ->where('password', md5($password))
                          ->where('active', 1)
                          ->findAll();
        
        if (empty($user))
            return false;

        $loggeduser = $user[0];
        $loggeduserdata = [
            'id' => $loggeduser['id'],
            'username' => $loggeduser['username'],
            'title' => $loggeduser['title'],
            'name' => $loggeduser['name'],
            'mobile' => $loggeduser['mobile'],
            'pin_code' => $loggeduser['pin_code'],
            'digital_signature' => $loggeduser['digital_signature'],
            'active' => $loggeduser['active'],
            'role' => $loggeduser['role'],
            'extra' => $loggeduser['extra'],
        ];

        $loggeduserdata = \BluelimeSystem::$Authentication::set_logged_user_data($loggeduserdata);

        $userModel->update($loggeduser['id'],['login_id' => $loggeduserdata['login_id']]);

        return true;
    }
    private function get_user_agent_info()
    {
        $agent = $this->request->getUserAgent();

        if ($agent->isBrowser()) {
            $currentAgent = $agent->getBrowser() . ' ' . $agent->getVersion() . ' ' . $agent->getPlatform(). ' ' . $agent->getMobile();
        } elseif ($agent->isRobot()) {
            $currentAgent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $currentAgent = $agent->getMobile();
        } else {
            $currentAgent = 'Unidentified User Agent';
        }

        return $currentAgent;
    }

    public function changepassword()
    {
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');

       $user = $userModel->where('id',$loggeduserdata['id'])->findAll();
       
        $data['user'] = $user;

        $data['active_menu'] = '';
        $data['loggeduserdata'] = $loggeduserdata;

        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')
        
        .view(($loggeduserdata['role']!="SUPER_ADMIN")? ucfirst(@$_GET['loc']).'/'.ucfirst(strtolower($loggeduserdata['role'])).'/_left_sidebar_'.strtolower($loggeduserdata['role']):'Include/_modals')
        
        .view('Include/_left_sidebar_bottom')

        .view('change_password')

        .view('Include/_footer');
    }
    
    public function updatepassword()
    {
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        
        $id = $loggeduserdata['id'];

        $saved_password = $userModel->where('id',$id)->findColumn('password');
        @$old_password  = $_POST['old_password'];
        @$new_password = $_POST['new_password'];

        if(md5($old_password)==$saved_password[0]){
            $userModel->where('id',$id)->set('password',md5($new_password))->update();
            return redirect()->to(site_url('changepassword?loc='.@$_REQUEST['loc']));
        }
        return redirect()->to(site_url('changepassword?loc='.@$_REQUEST['loc'].'&fail=1'));
    }
    public function updatepin()
    {
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        
        $id = $loggeduserdata['id'];

        $saved_pin = $userModel->where('id',$id)->findColumn('pin_code');
        @$old_pin  = $_POST['old_pin'];
        @$new_pin = $_POST['new_pin'];
       // BluelimeSystem::show_and_die($new_pin);
        if(md5($old_pin)==$saved_pin[0]){
            $userModel->where('id',$id)->set('pin_code',md5($new_pin))->update();
            return redirect()->to(site_url('changepassword?loc='.@$_REQUEST['loc']));
        }
        return redirect()->to(site_url('changepassword?loc='.@$_REQUEST['loc'].'&fail=1'));
    }

    public function userinfo()
    {
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        
        $id = $loggeduserdata['id'];
        $name    = $_POST['full_name'];
        $email            = $_POST['email'];
        $mobile        = $_POST['mobile'];
        
        $data = [
            'name'           => $name,
            'email'                  => $email,
            'mobile'              => $mobile
        ];
        //BluelimeSystem::show_and_die($data);
        $userModel->update($id,$data);
       
        return redirect()->to(site_url('/hospital/doctor/changepassword'));
    }
    public function updatesignature()
    {
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        
        $id = $loggeduserdata['id'];

        if (is_uploaded_file($_FILES['digital_signature']['tmp_name'])) {
            $digital_signature = file_get_contents($_FILES['digital_signature']['tmp_name']);
            
            $data = ['digital_signature'=> $digital_signature];
            $userModel->update($id,$data);
            return redirect()->to(site_url('/hospital/doctor/changepassword'));
            }
       return redirect()->to(site_url('/hospital/doctor/changepassword?fail=1'));
    }
}