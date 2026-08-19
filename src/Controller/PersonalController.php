<?php

namespace App\Controller;

use App\Entity\Personal;
use App\Entity\UsuarioEquipo;
use App\Form\PersonalType;
use App\Repository\PersonalRepository;
use App\Repository\EquipoRepository;
use App\Repository\UsuarioEquipoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Personal\FiltroType;

/**
 * @Route("/personal")
 */
class PersonalController extends AbstractController
{
    private $personalRepository;
    private $equipoRepository;
    private $entityManager;
    private $paginator;
    private $encoders;
    private $normalizers;
    private $serializer;

    public function __construct(PersonalRepository $personalRepository,EquipoRepository $equipoRepository,EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->personalRepository = $personalRepository;
        $this->personalRepository = $personalRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }
    
    /**
     * @Route("/", name="personal_index", methods={"GET"})
     * @IsGranted("PERSONAL_VER")
     */
    public function index(PersonalRepository $personalRepository,EquipoRepository $equipoRepository, UsuarioEquipoRepository $usuarioEquipoRepository,Request $request): Response
    {
        $formFiltro = $this->createForm(FiltroType::class);

        if ($request->query->get($formFiltro->getName())) {
            $formFiltro->handleRequest($request);
        }

        $objOptions = $formFiltro->getData();

        // Verifica si viene id de equipo
        if($request->query->get('idT')){
            $objOptions['estacionTraspaso'] = $request->query->get('idT');
            $personal = $this->personalRepository->findForActionIndex($objOptions);
            $equipo = $equipoRepository->find($request->query->get('idT'));
            $usuariosVinculados = $usuarioEquipoRepository->findBy(['equipo' => $equipo, 'isActual' => true]);
    
            $equipo = $equipoRepository->find($request->query->get('idT'));
                $pagination = $this->paginator->paginate(
                $personal,
                $request->query->get('page', 1),
                12
            );
            // Muestra listado de personal a vincular
            return $this->render('equipo/eleccion_personal.html.twig', [
                "estacionTraspaso" => $request->query->get('idT'),
                "pagination"=>$pagination,
                "usuariosVinculados"=>$usuariosVinculados,
                "equipo"=>$equipo,
                "formFiltro" => $formFiltro->createView()
            ]);
        }elseif($request->query->get('id')){
            $objOptions['estacion'] = $request->query->get('id');
            $personal = $this->personalRepository->findForActionIndex($objOptions);
            $equipo = $equipoRepository->find($request->query->get('id'));
            $usuariosVinculados = $usuarioEquipoRepository->findBy(['equipo' => $equipo, 'isActual' => true]);
                $pagination = $this->paginator->paginate(
                $personal,
                $request->query->get('page', 1),
                12
            );
            // Muestra listado de personal a vincular
            return $this->render('personal/eleccion_personal.html.twig', [
                "estacion" => $request->query->get('id'),
                "pagination"=>$pagination,
                "usuariosVinculados"=>$usuariosVinculados,
                "equipo"=>$equipo,
                "formFiltro" => $formFiltro->createView()
            ]);
        }else{
            // Muestra listado de personal
            $personal = $this->personalRepository->findForActionIndex($objOptions);
            $pagination = $this->paginator->paginate(
                $personal,
                $request->query->get('page', 1),
                12
            );
            return $this->render('personal/index.html.twig', [
                "pagination"=>$pagination,
                "formFiltro" => $formFiltro->createView()
            ]);
        }
    }

    /**
     * @Route("/new", name="personal_new", methods={"GET", "POST"})
     * @IsGranted("PERSONAL_CREAR")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $personal = new Personal();
        $personal->setUbicacion(null);
        $form = $this->createForm(PersonalType::class, $personal, ['validation_groups' => ['registro']]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $personal->setSuspended(false);
            $personal->setDeleted(false);
            $personal->setUbicacion($form["ubicacion"]->getData());
            $entityManager->persist($personal);
            $entityManager->flush();

            $this->addFlash("success","Personal creado correctamente.");
            // Verifica si viene el id del equipo
            if($request->query->get('id')){
                // Muestra listado de personal a vincular
                return $this->redirectToRoute('personal_index', ["id" => $request->query->get('id') ], Response::HTTP_SEE_OTHER);
            }else{
                // Muestra listado de personal
                return $this->redirectToRoute('personal_show', ['id' => $personal->getId()], Response::HTTP_SEE_OTHER);
            }    
        }

        return $this->render('personal/new.html.twig', [
            'personal' => $personal,
            'form' => $form->createView(),
        ]);
        
    }

    /**
     * @Route("/{id}", name="personal_show", methods={"GET"})
     * @IsGranted("PERSONAL_VER")
     */
    public function show(Personal $personal): Response
    {
        return $this->render('personal/show.html.twig', [
            'personal' => $personal,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="personal_edit", methods={"GET", "POST"})
     * @IsGranted("PERSONAL_EDITAR")
     */
    public function edit(Request $request, Personal $personal, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonalType::class, $personal, ['validation_groups' => ['registro']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $personal->setUbicacion($form["ubicacion"]->getData());
            $entityManager->flush();

            $this->addFlash("success","Personal editado correctamente.");
            return $this->redirectToRoute('personal_show', ['id' => $personal->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personal/new.html.twig', [
            'personal' => $personal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="personal_delete", methods={"DELETE"})
     * @IsGranted("PERSONAL_ELIMINAR")
     */
    public function delete(Request $request, Personal $personal, EntityManagerInterface $entityManager): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$personal->getId(), $request->request->get('_token'))) {
            
            $entityManager->remove($personal);
            $entityManager->flush();

            $this->addFlash("success","Personal Eliminado correctamente.");
        }

        return $this->redirectToRoute('personal_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/seleccionEquipo/{id}", name="eleccion_equipo_personal", methods={"GET"})
     * @IsGranted("EQUIPO_VER")
     */
    public function eleccionEquipo(EquipoRepository $equipoRepository, Request $request): Response
    {
        $formFiltro = $this->createForm(FiltroType::class);
        if ($request->query->get($formFiltro->getName())) {
            $formFiltro->handleRequest($request);
        }
        $objOptions = $formFiltro->getData();
        $equipo = $equipoRepository->findForActionIndex($objOptions);
        $pagination = $this->paginator->paginate(
            $equipo,
            $request->query->get('page', 1),
            12
        );
        
        return $this->render('equipo/eleccion_equipo.html.twig', [
                "personal" => $request->get('id'),
                "pagination"=>$pagination,
                "formFiltro" => $formFiltro->createView()
            ]);

    }

    /**
     * @Route("/{id}/agregarEquipo", name="agregar_equipo_personal", methods={"GET", "POST"})
     * @IsGranted("PERSONAL_EDITAR")
     */
    public function agregarEquipo(Request $request, Personal $personal, EquipoRepository $equipoRepository, PersonalRepository $personalRepository, UsuarioEquipoRepository $userEquipoRepository): Response
    {
        // Consulta si viene el id del equipo  
        if($request->query->get('idE')){
            // Recupera los datos del equipo
            $equipo = $equipoRepository->find($request->get('idE'));
            if (!$equipo) {
                $this->addFlash("danger", "El equipo seleccionado no existe.");
                return $this->redirectToRoute('personal_show', ['id' => $personal->getId()], Response::HTTP_SEE_OTHER);
            }

            // Solo bloquea si ya está a cargo actualmente (permite re-vincular tras una baja)
            if ($userEquipoRepository->usuarioActual($personal->getId(), (int) $request->get('idE'))) {
                $this->addFlash("danger", "El usuario ya tiene a cargo el equipo.");
            } else {
                // Guarda el registro de usuario de equipo
                $usuario = new UsuarioEquipo();
                $usuario->setUsuario($personal);
                $usuario->setEquipo($equipo);
                $usuario->setFechaInicio(new \DateTime());
                $usuario->setIsActual(true);
                $this->entityManager->persist($usuario);
                $this->entityManager->flush();
                $this->addFlash("success","Se Agrego el Equipo correctamente.");
            }    
        }
        
        return $this->redirectToRoute('personal_show', ['id' => $personal->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/eliminarEquipo", name="eliminar_equipo_personal", methods={"GET", "POST"})
     * @IsGranted("PERSONAL_EDITAR")
     */
    public function eliminarEquipo(Request $request, UsuarioEquipo $user, EquipoRepository $equipoRepository, PersonalRepository $personalRepository): Response
    {
        // Recupera el id del personal
        $id = $user->getUsuario()->getId();    
        // eliminar el usuario del equipo
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash("success","Se Elimino Equipo correctamente.");
        
        return $this->redirectToRoute('personal_show', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
