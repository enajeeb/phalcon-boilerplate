<?php
/**
* ACL for the application
*/

use Phalcon\Events\Event,
    Phalcon\Mvc\User\Plugin,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Acl,
    Phalcon\DI\FactoryDefault as PhDi;

class Security extends Plugin {

    public function beforeDispatch(Event $event, Dispatcher $dispatcher) {

        $di = PhDi::getDefault();

        // global config
        $config = $di['config'];

        // Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        
        // No ACL checks for AccessController
        if ( $controller == 'access' ) {
            return true;
        }

        // Check whether the "auth" variable exists in session to define the active role
        $auth = $this->session->get('auth');

        if ( !$auth ) {

            // user not logged in
            $dispatcher->forward(
                array(
                    'controller' => 'access',
                    'action' => 'signin'
                )
            );
            return false;
        } else {
            $role = $auth['role'];
        }

        // Check whether acl data already exist
        $aclFileName = $config->application['securityDir'] . "acl.data";
        if ( !is_file($aclFileName) ) {

            // Obtain the ACL list
            $acl = $this->getAcl();

            // Store serialized list into plain file
            file_put_contents($aclFileName, serialize($acl));

        } else {

            //Restore acl object from serialized file
            $acl = unserialize(file_get_contents($aclFileName));
        }

        // Check if the Role have access to the controller (resource)
        $allowed = $acl->isAllowed($role, $controller, $action);

        if ( $allowed != Acl::ALLOW ) {

            // If user doesn't have access forward to the index controller
            $flashMessage = <<<EOT
<div class="alert alert-block alert-danger">
    <a class="close" data-dismiss="alert" href="#">×</a>
    <h4 class="alert-heading">Error!</h4>
    You don't have access to this module.
</div>
EOT;
            $this->flashSession->warning($flashMessage);
            $dispatcher->forward(
                array(
                    'controller' => 'index',
                    'action' => 'index'
                )
            );
            
            // Returning "false" will tell to the dispatcher to stop the current operation
            return false;
        }

    }

    public function getAcl() {

        // Create the ACL
        $acl = new Phalcon\Acl\Adapter\Memory();

        // The default action is DENY access
        $acl->setDefaultAction(Phalcon\Acl::DENY);

        // Register roles
        $roles = array(
            'admin'   => new Phalcon\Acl\Role('admin'),
            'user'    => new Phalcon\Acl\Role('user')
        );
        
        // Adding Roles to the ACL
        foreach ($roles as $role) {
            $acl->addRole($role);
        }

        // Adding Resources (controllers/actions)
        // resources allowed for all groups
        $publicResources = array(
            'index'     => array('index', 'notFound', 'forbidden', 'internalServerError'),
            'user'      => array('myProfile', 'changePassword'),
            'country'   => array('index', 'add', 'edit', 'delete')
        );

        $privateResources = array(
            'user'      => array('index', 'add', 'edit', 'delete', 'resetPassword')
        );
        
        foreach ($publicResources as $resource => $actions) {
            $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
        }

        foreach ($privateResources as $resource => $actions) {
            $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
        }

        // Defining Access Controls
        // Grant access to public areas to all roles
        foreach ($roles as $role) {
            foreach ($publicResources as $resource => $actions) {
                foreach ($actions as $action) {
                    $acl->allow($role->getName(), $resource, $action);
                }
            }
        }

        // Grant access to private area only to certain roles
        foreach ($privateResources as $resource => $actions) {
            foreach ($actions as $action) {
                $acl->allow($roles['admin']->getName(), $resource, $action);
            }
        }

        return $acl;
    }

}