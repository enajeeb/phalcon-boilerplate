<?php
/**
* This controller handles all non secure requests
* Sign In, Sign out, Reset password, New sign up
*/

class AccessController extends \ControllerBase
{

    /*
    * This method is executed first before any other methods
    */
    public function initialize()
    {
        parent::initialize();
    }

    private function _registerSession($user)
    {
        $this->session->set('auth', array(
            'id'         => $user->id,
            'email'      => $user->username,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'role'       => $user->Groups->name
        ));
    }

    /**
    * Signin Page
    */
    public function signinAction()
    {

        // set page title
        $this->view->pageTitle = 'Sign In';

        // process post if not forwarded from other actions (forgotPassword)
        if ( !$this->dispatcher->wasForwarded() && $this->request->isPost() ) {

            // Receiving the variables sent by POST
            $email        = $this->filter->sanitize($this->request->getPost('email', 'email'), "trim");
            $password     = $this->filter->sanitize($this->request->getPost('password'), "trim");
            $hashPassword = hash('sha256', $this->config->application['securitySalt'] . $password);

            // find user in the database
            $user = Users::findFirst(array(
                "username = :email: AND password = :password: AND status = :status:",
                "bind" => array(
                    'email'    => $email,
                    'password' => $hashPassword,
                    'status'   => 'active'
                )
            ));

            if ( !empty($user) ) {

                // save session
                $this->_registerSession($user);

                // redirect to dashboard
                return $this->response->redirect("/");

            }

            $this->getFlashSession('error', 'Wrong email/password.', false);

        }

    }

    /**
    * Sign out
    */
    public function signoutAction()
    {

        // Destroy the whole session
        $this->session->destroy();

        // Destroy Session Cookie
        setcookie($this->config->application->appName, '', time() - 42000, '/');

        // Redirect to home page
        return $this->response->redirect("/signin");
    }

    /**
    * Forgot Password
    */
    public function forgotPasswordAction()
    {
        
        // set page title
        $this->view->pageTitle = 'Forgot Password';

        // process post
        if ($this->request->isPost()) {

            // Receiving the variables sent by POST
            $email = $this->filter->sanitize($this->request->getPost('email', 'email'), "trim");

            if ( !empty($email) ) {

                // find user in the database
                $user = Users::findFirst(array(
                    "username = :email: AND status = :status:",
                    "bind" => array(
                        'email' => $email,
                        'status' => 'active'
                    )
                ));

                if ( !empty($user) ) {

                    // generate reset hash
                    $resetHashToken = $this->security->hash('forgotPassword' . date('mdY H:m:s') . $email);

                    // save hash in database
                    $user->hashtoken_reset = $resetHashToken;
                    $user->hashtoken_expire = date('Y-m-d H:i:s', strtotime('+' . $this->config->application->hashTokenExpiryHours . ' hours'));

                    if ( $user->update() == false ) {
                        $this->logger->log("Failed to save user forgot password hash", \Phalcon\Logger::ERROR);
                        foreach ($user->getMessages() as $message) {
                            $this->logger->log($message, \Phalcon\Logger::ERROR);
                        }

                        $this->getFlashSession('error', 'Sorry, we could not initiate forgot password process. Please try again.', false);
                    } else {

                        // email user
                        Basics::sendEmail( array(
                            'type'     => 'reset',
                            'toName'   => $user->first_name . " " . $user->last_name,
                            'toEmail'  => $user->username,
                            'resetUrl' => $this->config->application['baseUrl'] . '/reset-password/' . $resetHashToken
                        ));

                        $this->getFlashSession('success', 'Please check your email for instructions on resetting your password.', false);
                    }

                    // Forward to signin
                    return $this->dispatcher->forward(array(
                        'controller' => 'access',
                        'action' => 'signin'
                    ));

                } else {
                    $this->getFlashSession('error', 'Sorry, we could not find a user with that address. Please try again.', false);
                }

            } else {
                $this->getFlashSession('error', 'Sorry, we could not find a user with that address. Please try again.', false);
            }
        }

    }

    /**
    * Reset Password
    */
    public function resetPasswordAction()
    {
        
        // set page title
        $this->view->pageTitle = 'Reset Password';

        $resetHashToken = $this->dispatcher->getParam("token");
        if ( empty($resetHashToken) ) {

            $this->getFlashSession('error', 'Invalid reset link', false);

            // Forward to signin
            return $this->dispatcher->forward(array(
                'controller' => 'access',
                'action'     => 'signin'
            ));
        } else {

            // verify hash token exists in database
            // find user in the database
            $user = Users::findFirst(array(
                "hashtoken_reset = :token: AND status = :status: AND hashtoken_expire IS NOT NULL AND hashtoken_expire > NOW()",
                "bind" => array(
                    'token'  => $resetHashToken,
                    'status' => 'active'
                )
            ));

            if ( empty($user) ) {
                $this->getFlashSession('error', 'Your password reset link has expired. Try send the reset request again.', false);

                // Forward to signin
                return $this->dispatcher->forward(array(
                    'controller' => 'access',
                    'action'     => 'signin'
                ));
            }

            $this->view->resetHashToken = $resetHashToken;
        }

        // process post
        if ( $this->request->isPost() ) {

            // Receiving the variables sent by POST
            $newPassword     = $this->filter->sanitize($this->request->getPost('new_password'), "trim");
            $confirmPassword = $this->filter->sanitize($this->request->getPost('confirm_password'), "trim");

            if ( !empty($newPassword) && !empty($confirmPassword) ) {

                // match the two passwords
                if ( $newPassword == $confirmPassword ) {

                    // update password
                    $password = hash('sha256', $this->config->application['securitySalt'] . $newPassword);
                    $user->password = $password;
                    $user->hashtoken_reset = null;
                    $user->hashtoken_expire = null;
                    if ( $user->update() == false ) {
                        $this->logger->log("Failed to reset user's password", \Phalcon\Logger::ERROR);
                        foreach ($user->getMessages() as $message) {
                            $this->logger->log($message, \Phalcon\Logger::ERROR);
                        }

                        $this->getFlashSession('error', 'Sorry, we could not reset your password. Please try again.', false);
                    } else {

                        // email user
                        Basics::sendEmail( array(
                            'type'     => 'resetConfirm',
                            'toName'   => $user->first_name . " " . $user->last_name,
                            'toEmail'  => $user->username
                        ));

                        $this->getFlashSession('success', 'Your password has been changed. You can now sign in with your new password.', false);
                        
                        // Forward to signin
                        return $this->dispatcher->forward(array(
                            'controller' => 'access',
                            'action'     => 'signin'
                        ));
                    }

                } else {
                    $this->getFlashSession('error', 'Both passwords should match.', false);
                }

            } else {
                $this->getFlashSession('error', 'Please enter both passwords.', false);
            }
        }
    }

}
