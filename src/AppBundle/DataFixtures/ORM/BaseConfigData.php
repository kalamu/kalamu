<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kalamu\CmsAdminBundle\Entity\Page;
use Kalamu\CmsAdminBundle\Entity\Post;
use Kalamu\CmsAdminBundle\Entity\PublishStatus;
use Kalamu\CmsAdminBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class BaseConfigData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadAdminUser($manager);
        $this->loadPublishStatus($manager);
        $this->loadHomepage($manager);

        $this->setWebsiteConfiguration();
    }

    protected function loadAdminUser(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@test.com');
        $admin->setPlainPassword('admin');
        $admin->setEnabled(true);
        $admin->addRole('ROLE_SUPER_ADMIN');

        $manager->persist($admin);
        $manager->flush();

        $this->addReference('admin-user', $admin);
    }

    protected function loadPublishStatus(ObjectManager $manager)
    {
        $publish_post = new PublishStatus();
        $publish_post->setClass(Post::class);
        $publish_post->setTitle("Published");
        $publish_post->setDefault(true);
        $publish_post->setVisible(true);

        $draft_post = new PublishStatus();
        $draft_post->setClass(Post::class);
        $draft_post->setTitle("Draft");
        $draft_post->setDefault(false);
        $draft_post->setVisible(false);

        $publish_page = new PublishStatus();
        $publish_page->setClass(Page::class);
        $publish_page->setTitle("Published");
        $publish_page->setDefault(true);
        $publish_page->setVisible(true);

        $draft_page = new PublishStatus();
        $draft_page->setClass(Page::class);
        $draft_page->setTitle("Draft");
        $draft_page->setDefault(false);
        $draft_page->setVisible(false);

        $manager->persist($publish_post);
        $manager->persist($draft_post);
        $manager->persist($publish_page);
        $manager->persist($draft_page);
        $manager->flush();

        $this->addReference('published-page', $publish_page);
    }

    protected function loadHomepage(ObjectManager $manager)
    {
        $page = new Page();
        $page->setTitle("Homepage");
        $page->setContenu('{"childs":[{},{"type":"row","datas":{"col":"1","cols":[{"md":12,"widgets":[{"context":"cms","type":"cms.content","identifier":"kalamu_cms.element.cms.wysiwyg","params":[{"name":"form[content]","value":"<h1 style=\"text-align: center;\"><strong>Congratulations, KalamuCMS is installed !</strong></h1>\r\n<p style=\"text-align: center;\">You can now use the <a href=\"/admin/dynamique-config\">administration interface</a> to start configuring your website.</p>"},{"name":"parent_md_size","value":12}]}],"responsive":{"visible":["lg","md","sm","xs"],"size":{"lg":12,"md":12,"sm":12,"xs":12},"class":"","id":""}}],"responsive":{"visible":["lg","md","sm","xs"],"class":"","id":""}}}]}');
        $page->setPublishStatus($this->getReference('published-page'));
        $page->setCreatedBy($this->getReference('admin-user'));
        $page->setUpdatedBy($this->getReference('admin-user'));

        $manager->persist($page);
        $manager->flush();
    }

    protected function setWebsiteConfiguration()
    {
        $config = $this->container->get('kalamu_dynamique_config');

        $config->set('cms_main_config', [
            'title'             => 'Title of the website',
            'description'       => 'Short description of the website',
            'homepage_content'  => [
                'display'       => '<strong class="text-muted">Pages :</strong> <strong>Homepage</strong>',
                'type'          => 'page',
                'context'       => null,
                'identifier'    => 'homepage',
                'url'           => '/page/homepage',
            ],
            'search_engine_allow' => false
        ]);
    }

    public function getOrder() {
        return 1;
    }

}
