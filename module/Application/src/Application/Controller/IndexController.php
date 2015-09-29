<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $ORM = NULL;

    public function indexAction()
    {
        $agrData = [];
        $axis = $series = NULL;
        $success = FALSE;
        $form = new \Application\Forms\JiraForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $cache = $this->getServiceLocator()->get('cache');
                $key = $request->getPost()->get('host'); // jira host

                $cacheData = $cache->getItem($key, $success);
                if (!$success) {
                    $data = [];
                    $api = new \chobie\Jira\Api(
                        $properties->getHost(),
                        new \chobie\Jira\Api\Authentication\Basic($request->getPost()->get('login'), $request->getPost()->get('password'))
                    );
                    $walker = new \chobie\Jira\Issues\Walker($api);
                    $walker->push("ORDER BY createdDate");
                    $i = 0;
                    foreach ($walker as $issue) {
                        $data[] = $issue;
                        if($i == 99) {
                            break;
                        } else {
                            $i++;
                        }
                    }
                    $cache->setItem($key, $data);
                }

                foreach($cacheData as $node) {
                    if(!array_key_exists($node->getProject()['name'], $agrData)) {
                        $agrData[$node->getProject()['name']] = 0;
                    }
                    if (preg_match('/^\d{4}-\d{2}-\d{2}.*/', $node->getUpdated())) {
                        $agrData[$node->getProject()['name']]++;
                    }
                }

                foreach($agrData as $key => $value) {
                    $axis .= '\'' .$key . '\',';
                    $series .= $value . ',';
                }
            }
        }

        return new ViewModel([
            'axis' => $axis,
            'series' => $series,
            'form' => $form,
            'success' => $success,
        ]);
    }

    /**
     * @return \Doctrine\ORM\EntityManager $Object
     */
    private function getORM() {
        if (!$this->ORM) {
            $this->ORM = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->ORM;
    }
}
