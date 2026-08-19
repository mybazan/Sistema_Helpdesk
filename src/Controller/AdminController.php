<?php


namespace App\Controller;


use App\Repository\CategorieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends BaseController
{

    /* @IsGranted("ROLE_SUPERUSER", message="No access! Get out!") */

    /**
     * @Route("/admin",name="app_admin_index")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function index(){
        return $this->render("admin/index.html.twig");
    }

}
