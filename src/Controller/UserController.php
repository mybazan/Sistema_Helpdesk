<?php


namespace App\Controller;


use App\Entity\Role;
use App\Entity\User;
use App\Form\ChangePwsdFormType;
use App\Form\ChangeUserPasswdFormType;
use App\Form\UserFormType;
use App\Form\NewUserFormType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\user\FiltroType;

/**
 * @Route("/admin/user")
 */
class UserController extends BaseController
{
    private $userRepository;
    private $passwordEncoder;

    private $entityManager;
    private $roleRepository;

    private $paginator;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager,
                                PaginatorInterface $paginator)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/",name="app_admin_users")
     * @IsGranted("USERS_VER")
     */
    public function users(Request $request)
    {
        $formFiltro = $this->createForm(FiltroType::class);
        if ($request->query->get($formFiltro->getName())) {
            $formFiltro->handleRequest($request);
        }
        $objOptions = $formFiltro->getData();
        
        $pagination = $this->paginator->paginate(
            $this->userRepository->findForActionIndex($objOptions),
            $request->query->get('page', 1),
            12
        );
        return $this->render("admin/user/index.html.twig", [
            "pagination"=>$pagination,
            'formFiltro' => $formFiltro->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show_user", requirements={"id":"\d+"}))
     * @IsGranted("USERS_VER")
     */
    public function show(Request $request, User $user)
    {
        /* Niega el acceso a ver info de un superuser sin ser superuser */
        if(!$this->actionOnSuperUser($user)){
            $this->addFlash("danger", "No tienes suficientes permiso para realizar esa acción.");
            return $this->redirectToRoute("app_admin_users");
        }
        
        $role = $this->roleRepository->findByName($user->getRoles()[0]);
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'role' => $role
        ]);                
    }

    /**
     * @Route("/new", name="app_admin_new_user")
     * @IsGranted("USERS_CREAR")
     */
    public function newUser(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(NewUserFormType::class, null, ["translator" => $translator]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            /* Validación de que no exista un user con ese email */
            if($this->userRepository->findOneByUsernameOrEmail($user->getEmail())){
                $this->addFlash("danger","Ya existe un usuario con ese email.");
                return $this->render("admin/user/_form.html.twig", ["userForm" => $form->createView()]);
            }

            $password = $form["justpassword"]->getData();
            $role = $form["role"]->getData();
            $role = $role->getRoleName();
            /* Guardamos la entidad del rol para otras funcionalidades */
            $roleEntity = $this->roleRepository->findByName($role);
            $user->setSuspended(false)
                ->setDeleted(false)
                ->setPassword($this->passwordEncoder->encodePassword($user, $password))
                ->setRoles([$role])
                ->setRolActual($roleEntity)
                ->setUbicacion($form["ubicacion"]->getData());


            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash("success", "Usuario creado correctamente.");
            return $this->redirectToRoute("app_admin_users");
        }        
        return $this->render("admin/user/_form.html.twig", ["userForm" => $form->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="app_admin_edit_user", requirements={"id":"\d+"}))
     * @IsGranted("USERS_EDITAR")
     */
    public function editUser(User $user, Request $request, TranslatorInterface $translator)
    {
        /* Niega el acceso a ver info de un superuser sin ser superuser */
        if(!$this->actionOnSuperUser($user)){
            $this->addFlash("danger", "No tienes suficientes permiso para realizar esa acción.");
            return $this->redirectToRoute("app_admin_users");
        }
        $form = $this->createForm(UserFormType::class, $user, ["translator" => $translator]);        
        $therole = $this->roleRepository->findOneBy(["roleName" => $user->getRoles()[0]]);
        $form->get('role')->setData($therole);
        $form->get('ubicacion')->setData($form->getData()->getUbicacion());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* Validación de que no exista un user con ese email */
            if($this->userRepository->findRepetido($user)){
                $this->addFlash("danger","Ya existe un usuario con ese email.");
                return $this->render("admin/user/_form.html.twig", ["userForm" => $form->createView()]);
            }
            $role = $form["role"]->getData();

            $role = $role->getRoleName();
            /* Guardamos la entidad del rol para otras funcionalidades */
            $roleEntity = $this->roleRepository->findByName($role);
            $user->setRoles([$role]);
            $user->setRolActual($roleEntity);
            $user->setUbicacion($form["ubicacion"]->getData());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash("success", "Usuario editado correctamente.");
            return $this->redirectToRoute("app_admin_users");
        }
        return $this->render("admin/user/_form.html.twig", ["userForm" => $form->createView()]);
    }

    /**
     * @Route("/changevalidite/{id}",name="app_admin_changevalidite_user", requirements={"id":"\d+"}))
     * @IsGranted("USERS_ACTIVAR")
     */
    public function activate(User $user)
    {
        /* Niega el acceso a ver info de un superuser sin ser superuser */
        if(!$this->actionOnSuperUser($user)){
            $this->addFlash("danger", "No tienes suficientes permiso para realizar esa acción.");
            return $this->redirectToRoute("app_admin_users");
        }

        $user = $this->userRepository->changeValidite($user);
        return $this->redirectToRoute("show_user", ['id' => $user->getId()]);
    }

    /**
     * @Route("/delete/{id}",name="app_admin_delete_user", requirements={"id":"\d+"}))
     * @IsGranted("USERS_ELIMINAR")
     */
    public function delete(Request $request, User $user)
    {
        /* Niega el acceso a ver info de un superuser sin ser superuser */
        if(!$this->actionOnSuperUser($user)){
            $this->addFlash("danger", "No tienes suficientes permiso para realizar esa acción.");
            return $this->redirectToRoute("app_admin_users");
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $resp = $this->userRepository->delete($user);
            if($resp === true){
                $this->addFlash("success", "Usuario eliminado correctamente.");
            }else{
                if(str_contains($resp->getMessage(),"SQLSTATE[23000]")){
                    $this->addFlash("danger", "No se puede eliminar el usuario '".$user->getUsername()."' porque tiene registros asociados. La opción correcta es suspender al usuario.");
                }else{
                    $this->addFlash("danger", "Error al eliminar el usuario.");
                }
            }
        }
        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * @Route("/changePassword",name="app_admin_changepswd")
     */
    public function changePswd(Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePwsdFormType::class, $user, ["translator" => $translator]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password =  $form["justpassword"]->getData();
            $newPassword = $form["newpassword"]->getData();

            if ($this->passwordEncoder->isPasswordValid($user, $password)) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
            } else {
                $this->addFlash("danger", "Las contraseñas deben ser iguales.");
                return $this->render("admin/params/changeMdpForm.html.twig", ["passwordForm" => $form->createView()]);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash("success", "Contraseña actualizada correctamente.");
            return $this->redirectToRoute("inicio_index");
        }
        return $this->render("admin/params/changeMdpForm.html.twig", ["passwordForm" => $form->createView()]);
    }

    /**
     * @Route("/changePassword/{id}", name="change_user_passwd", requirements={"id":"\d+"}))
     * @IsGranted("USERS_PASSWORD")
     */
    public function changeUserPasswd(Request $request, User $user)
    {
        /* Niega el acceso a ver info de un superuser sin ser superuser */
        if(!$this->actionOnSuperUser($user)){
            $this->addFlash("danger", "No tienes suficientes permiso para realizar esa acción.");
            return $this->redirectToRoute("app_admin_users");
        }

        $form = $this->createForm(ChangeUserPasswdFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form["newpassword"]->getData();

            $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash("success", "Contraseña actualizada correctamente!");

            $role = $this->roleRepository->findByName($user->getRoles()[0]);
            return $this->render('admin/user/show.html.twig', [
                'user' => $user,
                'role' => $role
            ]);
        }
        return $this->render("admin/params/changeMdpForm.html.twig", ["passwordForm" => $form->createView()]);
    }

    private function actionOnSuperUser(User $user){
        if($user->getRoles()[0] == "ROLE_SUPERUSER" && $this->getUser()->getRoles()[0] != "ROLE_SUPERUSER"){
           return false;
        }else{
            return true;
        }
    }

}