<?php

namespace {{ namespace }}\Entity;

use Symfony\Component\HttpFoundation\Request;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

use Kunstmaan\AdminBundle\Entity\DeepCloneableIFace;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNode;
use Kunstmaan\SearchBundle\Entity\Indexable;

use {{ namespace }}\Form\HomePageAdminType;
use {{ namespace }}\PagePartAdmin\HomePagePagePartAdminConfigurator;

/**
 * HomePage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}homepage")
 * @ORM\HasLifecycleCallbacks()
 */
class HomePage extends AbstractPage
{

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new HomePageAdminType();
    }

    /**
     * Return content to be indexed
     *
     * @param $container
     * @param $entity
     *
     * @return string
     */
    public function getContentForIndexing($container, $entity)
    {
        $renderer = $container->get('templating');
        $em = $container
            ->get('doctrine')
            ->getEntityManager();

        $pageparts = $em
            ->getRepository('KunstmaanPagePartBundle:PagePartRef')
            ->getPageParts($this);

        $classname = ClassLookup::getClassName($this);

        $view = '{{ bundle.getName() }}:Elastica:' . $classname . '.elastica.twig';

        $temp = $renderer->render($view, array('page' => $this, 'pageparts' => $pageparts));

        return strip_tags($temp);
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleChildPageTypes()
    {
        $array[] = array('name' => 'ContentPage', 'class'=> "{{ namespace }}\Entity\ContentPage");

        return $array;
    }

    /**
     * @return array
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new HomePagePagePartAdminConfigurator());
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:HomePage:view.html.twig";
    }
}