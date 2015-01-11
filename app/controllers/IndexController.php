<?php

class IndexController extends \ControllerBase {

    /*
    * This method is executed first before any other methods
    */
    public function initialize() {
        parent::initialize();
    }

    public function indexAction() {

        // set page title
        $this->view->pageTitle = 'Dashboard';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Dashboard';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

    }

    /**
    * Catch page not found (404)
    */
    public function notFoundAction() {

        // set page title
        $this->view->pageTitle = 'Error 404';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Error 404';
        $this->view->pageBreadcrumbs = $this->pageBreadcrumbs;

        $this->response->setHeader(404, 'Not Found');
        $this->view->pick('error/404');
    }

    /**
    * Catch access denied (403) for non admins
    */
    public function forbiddenAction() {

        $this->response->setHeader(403, 'Forbidden');
        $this->view->pick('error/forbidden');

    }

    /**
    * Catch Internal Server Error (500)
    */
    public function internalServerErrorAction() {

        // set page title
        $this->view->pageTitle = 'Error 500';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Error 500';
        $this->view->pageBreadcrumbs = $this->pageBreadcrumbs;

        $this->response->setHeader(500, 'Internal Server Error');
        $this->view->pick('error/500');
    }

}
