<?php

class UserController extends \ControllerBase
{

    /*
    * This method is executed first before any other methods
    */
    public function initialize()
    {
        parent::initialize();

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Users';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        // page title icon
        $this->view->pageTitleIcon = '<i class="fa-fw fa fa-user"></i>';
    }

    public function indexAction()
    {

        // set page title
        $this->view->pageTitle = 'Users';

        // process get
        if ($this->request->isGet()) {
            
            // Current page to show
            $currentPage = $this->request->getQuery('page', 'int');
            $currentPage = ( empty($currentPage) )? 1 : $currentPage;

            // items per page
            $itemsPerPage = $this->request->getQuery('limit', 'int');
            $itemsPerPage = ( empty($itemsPerPage) )? $this->config->application['pagination']['itemsPerPage'] : $itemsPerPage;

            // filter
            $filter = $this->request->getQuery('filter', 'trim');

            // sort
            $sortBy = $this->request->getQuery('sort', 'string');
            $sortBy = ( empty($sortBy) )? 'name' : $sortBy;

            // direction
            $sortDirection = $this->request->getQuery('direction', 'string');
            $sortDirection = ( empty($sortDirection) )? 'asc' : $sortDirection;

        } else {

            // set defaults
            $currentPage   = 1;
            $itemsPerPage  = $this->config->application['pagination']['itemsPerPage'];
            $filter        = null;
            $sortBy        = 'date';
            $sortDirection = 'desc';
        }

        // order by
        if ( $sortBy == 'date' ) {
            $orderBy = 'created ' . strtoupper($sortDirection);
        } elseif ( $sortBy == 'name' ) {
            $orderBy = 'first_name ' . strtoupper($sortDirection);
        } elseif ( $sortBy == 'type' ) {
            $orderBy = 'group_id ' . strtoupper($sortDirection);
        } elseif ( $sortBy == 'email' ) {
            $orderBy = 'username ' . strtoupper($sortDirection);
        }

        // get all user activity logs
        if ( !empty($filter) ) {

            // find users
            $user = Users::find(array(
                '( CONCAT(first_name, " ", last_name) LIKE :filter: OR username LIKE :filter: ) AND id <> :id:',
                'order' => $orderBy,
                'bind' => array(
                    'filter' => '%' . $filter . '%',
                    'filter' => '%' . $filter . '%',
                    'id'     => $this->userSession['id']
                )
            ));

        } else {
            
            // find users for current customer except yourself
            $user = Users::find(array(
                'id <> :id:',
                'order' => $orderBy,
                'bind' => array(
                    'id' => $this->userSession['id']
                )
            ));
        }

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $user,
                "limit"=> $itemsPerPage,
                "page" => $currentPage
            )
        );

        // get groups
        $this->view->groups = Groups::find(array(
            'order' => 'name'
        ));
        
        // create group list
        $groupList = array();
        foreach( $this->view->groups as $group ) {
            $groupList[$group->id] = $group->label;
        }

        // Get the paginated results
        $this->view->page          = $paginator->getPaginate();
        $this->view->itemsPerPage  = $itemsPerPage;
        $this->view->filter        = $filter;
        $this->view->sort          = $sortBy;
        $this->view->direction     = $sortDirection;
        $this->view->groups        = $groupList;
        $this->view->dropdownLinks = array(
            'edit'     => '/user/edit',
            'password' => '/user/resetPassword',
            'delete'   => '/user/delete',
        );

    }

    public function changePasswordAction()
    {

        // set page title
        $this->view->pageTitle = 'Password';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Password';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        // process post
        if ($this->request->isPost()) {

            // Receiving the variables sent by POST
            $currentPassword = $this->filter->sanitize($this->request->getPost('current_password'), "trim");
            $newPassword     = $this->filter->sanitize($this->request->getPost('new_password'), "trim");
            $confirmPassword = $this->filter->sanitize($this->request->getPost('confirm_new_password'), "trim");

            // verify current password is correct
            $hashPassword = hash('sha256', $this->config->application['securitySalt'] . $currentPassword);

            // find user in the database
            $user = Users::findFirst(array(
                "username = :email: AND password = :password: AND status = :status:",
                "bind" => array(
                    'email'    => $this->userSession['email'],
                    'password' => $hashPassword,
                    'status'   => 'active'
                )
            ));
            
            if ( empty($user) ) {

                $this->getFlashSession('error', 'Current password does not match.', true);
                return true;

            } else {
                
                if ( !empty($newPassword) && !empty($confirmPassword) ) {

                    // match the two passwords
                    if ( $newPassword == $confirmPassword ) {

                        // update password
                        $password = hash('sha256', $this->config->application['securitySalt'] . $newPassword);
                        $user->password = $password;
                        if ( $user->update() == false ) {
                            $this->logger->log("Failed to update password", \Phalcon\Logger::ERROR);
                            foreach ($user->getMessages() as $message) {
                                $this->logger->log($message, \Phalcon\Logger::ERROR);
                            }
                            $this->getFlashSession('error', 'Sorry, we could not change your password. Please try again.', true);
                        } else {

                            $this->getFlashSession('success', 'Your password has been changed.', true);
                            
                            // Forward to dashboard
                            return $this->response->redirect("/");
                        }

                    } else {
                        $this->getFlashSession('error', 'Both passwords should match.', true);
                    }

                } else {
                    $this->getFlashSession('error', 'Please enter both passwords.', true);
                }

            }
            
        } // post

    }

    public function addAction()
    {

        // set page title
        $this->view->pageTitle = 'Add User';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Add User';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        // get groups
        $this->view->groups = Groups::find(array(
            'name <> "admin"',
            'order' => 'name'
        ));
        
        // create group list
        $groupList = array();
        foreach( $this->view->groups as $group ) {
            $groupList[$group->id] = $group->label;
        }

        $this->view->groupId         = null;
        $this->view->firstName       = null;
        $this->view->lastName        = null;
        $this->view->username        = null;
        $this->view->newPassword     = null;
        $this->view->confirmPassword = null;
        $this->view->status          = null;

        // process post
        if ($this->request->isPost()) {

            // Receiving the variables sent by POST
            $this->view->groupId             = $this->request->getPost('group_id', 'int');
            $this->view->firstName           = $this->filter->sanitize($this->request->getPost('first_name', 'string'), "trim");
            $this->view->lastName            = $this->filter->sanitize($this->request->getPost('last_name', 'string'), "trim");
            $this->view->username            = $this->filter->sanitize($this->request->getPost('username', 'email'), "trim");
            $this->view->newPassword         = $this->filter->sanitize($this->request->getPost('new_password'), "trim");
            $this->view->confirmPassword     = $this->filter->sanitize($this->request->getPost('confirm_new_password'), "trim");
            $this->view->status              = $this->request->getPost('status', 'string');

            // make sure email does not exists
            // find user in the database
            $user = Users::findFirst(array(
                "username = :email:",
                "bind" => array(
                    'email' => $this->view->username
                )
            ));
            
            if ( !empty($user) ) {
                $this->getFlashSession('error', 'Email already exists for another user.', true);
                return true;
            } else {
                
                // match the two passwords
                if ( $this->view->newPassword != $this->view->confirmPassword ) {
                    $this->getFlashSession('error', 'Both passwords should match.', true);
                    return;
                } elseif ( !in_array($this->view->groupId, array_keys($groupList)) ) {
                    $this->getFlashSession('error', 'Invalid user type selection.', true);
                    return;
                } else {
                    
                    $user = new Users();
                    $user->group_id        = $this->view->groupId;
                    $user->first_name      = $this->view->firstName;
                    $user->last_name       = $this->view->lastName;
                    $user->username        = $this->view->username;
                    $user->password        = hash('sha256', $this->config->application['securitySalt'] . $this->view->newPassword);
                    $user->status          = ($this->view->status == 'on')? 'active' : 'inactive';
                    $user->created         = date('Y-m-d H:i:s');
                    $user->modified        = date('Y-m-d H:i:s');
                    $user->modified_by     = $this->userSession['email'];

                    if ( $user->create() == false ) {
                        $this->logger->log("Failed to save user", \Phalcon\Logger::ERROR);
                        foreach ($user->getMessages() as $message) {
                            $this->logger->log($message, \Phalcon\Logger::ERROR);
                        }
                        $this->getFlashSession('error', 'Sorry, we could not create a new user. Please try again.', true);
                    } else {

                        // email user
                        Basics::sendEmail( array(
                            'type'          => 'newUser',
                            'toName'        => $user->first_name . " " . $user->last_name,
                            'toEmail'       => $user->username,
                            'tempPassword'  => $this->view->newPassword,
                            'welcomeUrl'    => $this->config->application['baseUrl']
                        ));

                        $this->getFlashSession('success', 'New user is created.', true);
                        
                        // Forward to index
                        return $this->response->redirect("/user");
                    }

                }

            }
            
        } // post

    } // add

    public function editAction( $id = null )
    {

        if ( empty($id) ) {
            // Forward to index
            return $this->response->redirect("/user");
        }

        // set page title
        $this->view->pageTitle = 'Edit User';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Edit User';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        // get groups
        $this->view->groups = Groups::find(array(
            'name <> "admin"',
            'order' => 'name'
        ));
        
        // create group list
        $groupList = array();
        foreach( $this->view->groups as $group ) {
            $groupList[$group->id] = $group->label;
        }

        $this->view->id         = $id;
        $this->view->groupId    = null;
        $this->view->firstName  = null;
        $this->view->lastName   = null;
        $this->view->username   = null;
        $this->view->status     = null;

        // process post
        if ($this->request->isPost()) {

            // Receiving the variables sent by POST
            $this->view->groupId      = $this->request->getPost('group_id', 'int');
            $this->view->firstName    = $this->request->getPost('first_name', 'string');
            $this->view->lastName     = $this->request->getPost('last_name', 'string');
            $this->view->username     = $this->request->getPost('username', 'email');
            $this->view->status       = $this->request->getPost('status', 'string');

            // make sure email does not exists
            // find user in the database
            $user = Users::findFirst(array(
                "username = :email: AND id <> :id:",
                "bind" => array(
                    'email' => $this->view->username,
                    'id' => $id
                )
            ));
            
            if ( !empty($user) ) {

                $this->getFlashSession('error', 'Email already exists for another user.', true);
                return true;

            } else {
                
                // verify group selection
                if ( !in_array($this->view->groupId, array_keys($groupList)) ) {

                    $this->getFlashSession('error', 'Invalid user type selection.', true);
                    return;

                } else {
                    
                    // make sure you only edit the user of logged in customer
                    $user = Users::findFirst(array(
                        "id = :id:",
                        "bind" => array(
                            'id' => $id
                        )
                    ));

                    // invalid user
                    if ( empty($user) ) {

                        $this->getFlashSession('error', 'Invalid user.', true);
                        
                        // Forward to dashboard
                        return $this->response->redirect("/user");
                    }

                    $user->group_id         = $this->view->groupId;
                    $user->first_name       = $this->view->firstName;
                    $user->last_name        = $this->view->lastName;
                    $user->username         = $this->view->username;
                    $user->status           = ($this->view->status == 'on')? 'active' : 'inactive';
                    $user->modified         = date('Y-m-d H:i:s');
                    $user->modified_by      = $this->userSession['email'];

                    if ( $user->update() == false ) {

                        $this->logger->log("Failed to save user", \Phalcon\Logger::ERROR);
                        foreach ($user->getMessages() as $message) {
                            $this->logger->log($message, \Phalcon\Logger::ERROR);
                        }
                        $this->getFlashSession('error', 'Sorry, we could not update the user record. Please try again.', true);

                    } else {

                        $this->getFlashSession('success', 'User record updated.', true);
                        
                        // Forward to dashboard
                        return $this->response->redirect("/user");
                    }

                }

            }
            
        } else {

            // make sure you only edit the user to logged in customer
            $user = Users::findFirst(array(
                "id = :id:",
                "bind" => array(
                    'id' => $id
                )
            ));

            if ( !empty($user) ) {

                $this->view->id         = $user->id;
                $this->view->groupId    = $user->group_id;
                $this->view->firstName  = $user->first_name;
                $this->view->lastName   = $user->last_name;
                $this->view->username   = $user->username;
                $this->view->status     = ( $user->status == 'active' )? 'on' : 'off';

            } else {

                $this->getFlashSession('error', 'Invalid user.', true);
                        
                // Forward to dashboard
                return $this->response->redirect("/user");

            }
        }

    } // edit

    public function deleteAction( $id = null )
    {

        if ( empty($id) ) {
            
            $this->getFlashSession('error', 'Invalid user.', true);

            // Forward to dashboard
            return $this->response->redirect("/user");
        }

        $user = Users::findFirst(array(
            "id = :id:",
            "bind" => array(
                'id' => $id
            )
        ));

        if ( !empty($user) ) {

            // delete user
            if ( $user->delete() == false ) {
                
                $this->logger->log("Failed to delete user", \Phalcon\Logger::ERROR);
                foreach ($user->getMessages() as $message) {
                    $this->logger->log($message, \Phalcon\Logger::ERROR);
                }
                $this->getFlashSession('error', 'Sorry, we could not delete the user record. Please try again.', true);

            } else {

                $this->getFlashSession('success', 'User record deleted.', true);
                
                // Forward to dashboard
                return $this->response->redirect("/user");
            }

        } else {

            $this->getFlashSession('error', 'Invalid user.', true);
            
            // Forward to dashboard
            return $this->response->redirect("/user");
        }

        // Forward to dashboard
        return $this->response->redirect("/user");

    }

    public function resetPasswordAction( $id = null )
    {

        if ( empty($id) ) {
            
            // Forward to index
            return $this->response->redirect("/user");
        }

        // set page title
        $this->view->pageTitle = 'Reset Password';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Reset Password';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        $this->view->id = $id;

        // process post
        if ($this->request->isPost()) {

            // Receiving the variables sent by POST
            $newPassword     = $this->filter->sanitize($this->request->getPost('new_password'), "trim");
            $confirmPassword = $this->filter->sanitize($this->request->getPost('confirm_new_password'), "trim");

            $user = Users::findFirst(array(
                "id = :id:",
                "bind" => array(
                    'id' => $id
                )
            ));
            
            if ( empty($user) ) {

                $this->getFlashSession('error', 'Invalid user.', true);
                return true;

            } else {

                if ( !empty($newPassword) && !empty($confirmPassword) ) {

                    // match the two passwords
                    if ( $newPassword == $confirmPassword ) {

                        // update password
                        $password = hash('sha256', $this->config->application['securitySalt'] . $newPassword);
                        $user->password = $password;

                        if ( $user->update() == false ) {
                        
                            $this->logger->log("Failed to update password", \Phalcon\Logger::ERROR);
                            foreach ($user->getMessages() as $message) {
                                $this->logger->log($message, \Phalcon\Logger::ERROR);
                            }
                            $this->getFlashSession('error', 'Sorry, we could not change the password. Please try again.', true);

                        } else {

                            $this->getFlashSession('success', 'User\'s password has been changed.', true);
                            
                            // Forward to index
                            return $this->response->redirect("/user");

                        }

                    } else {
                        $this->getFlashSession('error', 'Both passwords should match.', true);
                    }

                } else {
                    $this->getFlashSession('error', 'Please enter both passwords.', true);
                }

            }

        } // post

    }

}
