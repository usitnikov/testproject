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
        $data = [];

        $jiraData = $this->getORM()->getRepository('\Application\Entity\JiraDataEntity')->findAll();
        if(count($jiraData)) {
            foreach($jiraData as $node) {
                if(!array_key_exists($node->getProject(), $data)) {
                    $data[$node->getProject()] = 0;
                }

                if(preg_match('/^\d{4}-\d{2}-\d{2}.*/', $node->getUpdated())) {
                    $data[$node->getProject()]++;
                }
            }
        } else {
            echo "Данных нет, пожалуйста загрузите их действием application/index/getdata";
        }

        $axis = $series = '';
        foreach($data as $key => $value) {
            $axis .= '\'' .$key . '\',';
            $series .= $value . ',';
        }

        return new ViewModel([
            'axis' => $axis,
            'series' => $series,
        ]);
    }

    public function getdataAction() {

        $properties = $this->getORM()->getRepository('\Application\Entity\PropertiesEntity')->find(1);
        if(!$properties instanceof \Application\Entity\PropertiesEntity) {
            die();
        }

        $connection = $this->getORM()->getConnection();
        $platform   = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('JiraDataEntity', true /* whether to cascade */));

        $api = new \chobie\Jira\Api(
            $properties->getHost(),
            new \chobie\Jira\Api\Authentication\Basic($properties->getLogin(), $properties->getPassword())
        );
        $walker = new \chobie\Jira\Issues\Walker($api);
        $walker->push("ORDER BY createdDate");
        $i = 0;
        foreach ($walker as $issue) {
            $jiraData = new \Application\Entity\JiraDataEntity();
            $jiraData->setIssue($issue->getKey());
            $jiraData->setProject($issue->getProject()['name']);
            $jiraData->setUpdated($issue->getUpdated());
            $this->getORM()->persist($jiraData);
            unset($jiraData);

            if($i == 99) {
                break;
            } else {
                $i++;
            }
        }
        $this->getORM()->flush();
        echo "done!";
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
