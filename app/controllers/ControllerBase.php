<?php

use Phalcon\Mvc\Controller,
    Phalcon\DI\FactoryDefault as PhDi;

class ControllerBase extends Controller {

    public $config;
    public $userSession;
    public $response;
    public $filter;

    // initialize breadcrumbs
    public $pageBreadcrumbs = array(
        'Home'
    );

    public function initialize() {

        $di = PhDi::getDefault();

        // initiate filter object
        $this->filter = new \Phalcon\Filter();

        // global response
        $this->response = new \Phalcon\Http\Response();

        // global config
        $this->config = $di['config'];

        // get current controller and action names
        $currentControllerName = $di['router']->getControllerName();
        $currentActionName = $di['router']->getActionName();

        // navigation
        $this->view->setVar('selLeftNav', null);
        if ( $currentControllerName == 'index' && $currentActionName == 'index' ) {
            $this->view->setVar('selLeftNav', 'Dashboard');
        } else {
            $this->view->setVar('selLeftNav', $currentControllerName);
        }

        // shortcut selection
        $this->view->setVar('selShortcutNav', null);
        switch ( $currentControllerName ) {
            case 'user':
                $this->view->setVar('selShortcutNav', $currentActionName);
            break;
        }

        // user name
        $userSession = $this->session->get('auth');
        if ( !empty($userSession) ) {
            $this->userSession = $userSession;
            $this->view->setVar('sessionUserName', $userSession['first_name'] . ' ' . $userSession['last_name']);
            $this->view->setVar('sessionUserRole', $userSession['role']);
        } else {
            $this->view->setVar('sessionUserName', 'john.doe');
            $this->view->setVar('sessionUserRole', 'user');
        }

        // default page title icon
        $this->view->pageTitleIcon = '<i class="fa-fw fa fa-home"></i>';

        // application name
        $this->view->appTitle = $this->config->application['appTitle'];

        // setting environment for view
        $this->view->setVar('env', $this->config->application['env']);

    }

    /**
    * Get GET/POST parameters
    */
    protected function getUriParameter($parameter) {
        return $this->dispatcher->getParam($parameter);
    }

    /**
    * Create Flash Message using template
    * @param $type [success, error, warning, info]
    */
    protected function getFlashSession( $type, $message = null, $block = false, $autoHide = false ) {
        
        if ( !empty($message) ) {

            switch ( $type ) {
                case 'success':
                    if ( !$block ) {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-success fade in">
    <button class="close" data-dismiss="alert">
        ×
    </button>
    <i class="fa-fw fa fa-check"></i>
    <strong>Success!</strong> {$message}
</div>
EOT;
                    } else {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-block alert-success">
    <a class="close" data-dismiss="alert" href="#">×</a>
    <h4 class="alert-heading">Success!</h4>
    {$message}
</div>
EOT;
                    }

                    // Flash auto hide
                    if ( $autoHide ) {
                        $output .= <<<EOT
<script>
setTimeout('Flash.hide("flashMessage")', 3000);
</script>
EOT;
                    }

                    $this->flashSession->success($output);
                break;
                case 'error':
                    if ( !$block ) {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-danger fade in">
    <button class="close" data-dismiss="alert">
        ×
    </button>
    <i class="fa-fw fa fa-times"></i>
    <strong>Error!</strong> {$message}
</div>
EOT;
                    } else {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-block alert-danger">
    <a class="close" data-dismiss="alert" href="#">×</a>
    <h4 class="alert-heading">Error!</h4>
    {$message}
</div>
EOT;
                    }
                    
                    // Flash auto hide
                    if ( $autoHide ) {
                        $output .= <<<EOT
<script>
setTimeout('Flash.hide("flashMessage")', 3000);
</script>
EOT;
                    }
                    $this->flashSession->error($output);
                break;
                case 'warning':
                    if ( !$block ) {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-warning fade in">
    <button class="close" data-dismiss="alert">
        ×
    </button>
    <i class="fa-fw fa fa-warning"></i>
    <strong>Warning!</strong> {$message}
</div>
EOT;
                    } else {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-block alert-warning">
    <a class="close" data-dismiss="alert" href="#">×</a>
    <h4 class="alert-heading">Warning!</h4>
    {$message}
</div>
EOT;
                    }

                    // Flash auto hide
                    if ( $autoHide ) {
                        $output .= <<<EOT
<script>
setTimeout('Flash.hide("flashMessage")', 3000);
</script>
EOT;
                    }
                    $this->flashSession->warning($output);
                break;
                case 'info':
                    if ( !$block ) {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-info fade in">
    <button class="close" data-dismiss="alert">
        ×
    </button>
    <i class="fa-fw fa fa-info"></i>
    <strong>Info!</strong> {$message}
</div>
EOT;
                    } else {
                        $output = <<<EOT
<div id="flashMessage" class="alert alert-block alert-info">
    <a class="close" data-dismiss="alert" href="#">×</a>
    <h4 class="alert-heading">Info!</h4>
    {$message}
</div>
EOT;
                    }

                    // Flash auto hide
                    if ( $autoHide ) {
                        $output .= <<<EOT
<script>
setTimeout('Flash.hide("flashMessage")', 3000);
</script>
EOT;
                    }
                    $this->flashSession->notice($output);
                break;
            }
        }
    }

}
