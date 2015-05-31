<?php

class CountryController extends \ControllerBase
{

    /*
    * This method is executed first before any other methods
    */
    public function initialize()
    {
        parent::initialize();

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Countries';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        // page title icon
        $this->view->pageTitleIcon = '<i class="fa-fw fa fa-flag"></i>';
    }

    public function indexAction()
    {

        // set page title
        $this->view->pageTitle = 'Countries';

        // process get
        if ( $this->request->isGet() ) {
            
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
            $sortBy        = 'name';
            $sortDirection = 'desc';
        }

        // order by
        if ( $sortBy == 'date' ) {
            $orderBy = 'created ' . strtoupper($sortDirection);
        } elseif ( $sortBy == 'name' ) {
            $orderBy = 'name ' . strtoupper($sortDirection);
        }

        // get all user activity logs
        if ( !empty($filter) ) {

            // find countries using filter
            $country = Countries::find(array(
                'name LIKE :filter:',
                'order' => $orderBy,
                'bind' => array(
                    'filter' => '%' . $filter . '%'
                )
            ));

        } else {
            
            $country = Countries::find(array(
                'order' => $orderBy
            ));
        }

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $country,
                "limit"=> $itemsPerPage,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $this->view->page = $paginator->getPaginate();
        $this->view->itemsPerPage = $itemsPerPage;
        $this->view->filter = $filter;
        $this->view->sort = $sortBy;
        $this->view->direction = $sortDirection;
        $this->view->dropdownLinks = array(
            'edit'   => '/country/edit',
            'delete' => '/country/delete',
        );

    }

    public function addAction()
    {

        // set page title
        $this->view->pageTitle = 'Add Country';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Add Country';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        $this->view->name         = null;
        $this->view->abbreviation = null;

        $country = new Countries();

        // process post
        if ($this->request->isPost()) {

            // Receiving the variables sent by POST
            $this->view->name         = $this->filter->sanitize($this->request->getPost('name', 'string'), "trim");
            $this->view->abbreviation = $this->filter->sanitize($this->request->getPost('abbreviation'), "trim");

            // save
            $country->name            = $this->view->name;
            $country->abbreviation    = $this->view->abbreviation;
            $country->created         = date('Y-m-d H:i:s');
            $country->modified        = date('Y-m-d H:i:s');
            $country->modified_by     = $this->userSession['email'];

            if ( $country->create() == false ) {
                
                $this->logger->log("Failed to save", \Phalcon\Logger::ERROR);
                foreach ( $country->getMessages() as $message ) {
                    $this->logger->log($message, \Phalcon\Logger::ERROR);
                }
                $this->getFlashSession('error', 'Sorry, we could not create a new country record. Please try again.', true);

            } else {

                $this->getFlashSession('success', 'New country record is created.', true);
                
                // Forward to index
                return $this->response->redirect("/country");

            }

            
        } // post

    } // add

    public function editAction( $id = null )
    {

        if ( empty($id) ) {
            
            // Forward to index
            return $this->response->redirect("/country");

        }

        // set page title
        $this->view->pageTitle = 'Edit Country';

        // breadcrumb
        $this->pageBreadcrumbs[] = 'Edit Country';
        $this->view->setVar('pageBreadcrumbs', $this->pageBreadcrumbs);

        $this->view->id           = $id;
        $this->view->name         = null;
        $this->view->abbreviation = null;

        $country = Countries::findFirst(array(
            "id = :id:",
            "bind" => array(
                'id' => $id
            )
        ));

        if ( empty($country) ) {

            $this->getFlashSession('error', 'Invalid country.', true);
            
            // Forward to index
            return $this->response->redirect("/country");
        }

        // process post
        if ( $this->request->isPost() ) {

            // Receiving the variables sent by POST
            $this->view->name         = $this->filter->sanitize($this->request->getPost('name', 'string'), "trim");
            $this->view->abbreviation = $this->filter->sanitize($this->request->getPost('abbreviation'), "trim");

            // update
            $country->name                   = $this->view->name;
            $country->abbreviation           = $this->view->abbreviation;
            $country->modified               = date('Y-m-d H:i:s');
            $country->modified_by            = $this->userSession['email'];

            if ( $country->update() == false ) {

                $this->logger->log("Failed to save", \Phalcon\Logger::ERROR);
                foreach ($country->getMessages() as $message) {
                    $this->logger->log($message, \Phalcon\Logger::ERROR);
                }
                $this->getFlashSession('error', 'Sorry, we could not update country. Please try again.', true);

            } else {

                $this->getFlashSession('success', 'Country is updated.', true);
                
                // Forward to index
                return $this->response->redirect("/country");

            }
        } else {

            $this->view->name         = $country->name;
            $this->view->abbreviation = $country->abbreviation;
            
        } // post

    } // edit

    public function deleteAction( $id = null )
    {

        if ( empty($id) ) {
            
            $this->getFlashSession('error', 'Invalid country.', true);

            // Forward to index
            return $this->response->redirect("/country");

        }

        $country = Countries::findFirst(array(
            "id = :id:",
            "bind" => array(
                'id' => $id
            )
        ));

        if ( !empty($country) ) {

            // delete
            if ( $country->delete() == false ) {
                $this->logger->log("Failed to delete country", \Phalcon\Logger::ERROR);
                foreach ($country->getMessages() as $message) {
                    $this->logger->log($message, \Phalcon\Logger::ERROR);
                }
                $this->getFlashSession('error', 'Sorry, we could not delete the record. Please try again.', true);
            } else {

                $this->getFlashSession('success', 'Country record deleted.', true);
                
                // Forward to dashboard
                return $this->response->redirect("/country");
            }

        } else {

            $this->getFlashSession('error', 'Invalid country record.', true);
            
            // Forward to dashboard
            return $this->response->redirect("/country");
        }

        // Forward to dashboard
        return $this->response->redirect("/country");

    }

}
