           

            <!-- =============================================== -->
            <!-- Left side column. contains the sidebar -->
            <?php
                $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
                $logged_user = $loggeduserdata['name'] ?? '';
                
                switch ($loggeduserdata['role'] ?? '') {
                case "STORE":
                    $user_img  ="store.jpg";
                    break;
                case "DOCTOR":
                    $user_img  ="doctors.jpeg";
                    break;
                case "LABORATORY":
                    $user_img  ="laboratory.png";
                    break;
                case "RECEPTION":
                    $user_img  ="reception.png";
                    break;
                case "NURSE":
                    $user_img  ="nurse.png";
                    break;
                case "ADMIN":
                    $user_img  ="admin.png";
                    break;
                case "FINANCE":
                    $user_img  ="finance.png";
                    break;
                case "DISPENSARY":
                    $user_img  ="Pharmacy.jpg";
                    break;
                case "COMPOUNDING":
                    $user_img  ="compounding.png";
                    break;
                case "CASHIER_INVENTORY":
                    $user_img  ="cashier.png";
                    break;
                case "CASHIER_HOSPITAL":
                    $user_img  ="cashier.png";
                    break;
                case "CALL_CENTER":
                    $user_img  ="callcenter.png";
                    break;
                case "PATHOLOGY":
                    $user_img  ="pathology.png";
                    break;
                default:
                    $user_img  ="patient.png";
                }
            ?>

            <aside class="main-sidebar">
                <!-- sidebar -->
                <div class="sidebar">
                <!-- Sidebar user panel -->

                    <div class="user-panel text-center">
                        <div class="image">
                            <img src="<?php echo base_url().'assets/images/users/'.$user_img ?>" class="img-circle" alt="User Image">
                        </div>
                        <div class="info">
                            <p><?php echo $logged_user ?></p>
                            <a href="#"><i class="fa fa-circle text-success"> &nbsp; Online</i> </a>
                        </div>
                    </div>

                   

                        