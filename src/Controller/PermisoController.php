<?php


namespace App\Controller;


use App\Entity\Permiso;
use App\Entity\Role;
use App\Repository\PermisoRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PermisoController extends BaseController
{

    private $permisoRepository;
    private $roleRepository;
    private $entityManager;

    public function __construct(PermisoRepository $permisoRepository, RoleRepository $roleRepository,EntityManagerInterface $entityManager)
    {
        $this->permisoRepository = $permisoRepository;
        $this->roleRepository = $roleRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/permiso/new/{id}", name="new_permiso", requirements={"id":"\d+"})
     */
    public function new(Request $request, Role $role){

        $userId = $this->getUser()->getId();
        if ($this->isCsrfTokenValid('guardarRoles'.$userId, $request->request->get('_token'))) {
            
            $arrayPermisos = [];
            foreach($request->request->all() as $nombre=>$valor){
                if($valor == 'on'){
                    array_push($arrayPermisos, $nombre);
                }
            }
            $borrarPermisosViejos = $this->permisoRepository->delete($role);
            if($borrarPermisosViejos === true){
                $asignarPermisosNuevos = $this->permisoRepository->asignarRoles($role, $arrayPermisos);
                if($asignarPermisosNuevos === true){
                    $this->addFlash("success","Permisos actualizados correctamente.");
                }else{
                    $this->addFlash("danger","Error al modificar los permisos. Inténtelo nuevamente.");
                }
                
            }else{
                $this->addFlash("danger","");
            }
        }
        return $this->redirectToRoute('show_role', ["id"=> $role->getId()]); 
    }
    
}
