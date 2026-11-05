<?php
    
    if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::COMPOUNDING)
        echo view('Inventory/Compounding/_left_sidebar_compounding');
    elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::DISPENSARY)
        echo view('Inventory/Dispensary/_left_sidebar_dispensary');
    elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::STORE)
        echo view('Inventory/Store/_left_sidebar_store');
    