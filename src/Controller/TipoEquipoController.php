<?php

namespace App\Controller;

use App\Entity\TipoEquipo;
use App\Form\TipoEquipoType;
use App\Form\tipo_equipo\FiltroType;
use App\Repository\TipoEquipoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/admin/tipo/equipo")
 */
class TipoEquipoController extends AbstractController
{
    private $entityManager;
    private $paginator;
    private $tipoEquipoRepository;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator, TipoEquipoRepository $tipoEquipoRepository)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->tipoEquipoRepository = $tipoEquipoRepository;
    }
    /**
     * @Route("/", name="tipo_equipo_index", methods={"GET"})
     * @IsGranted("TIPO_EQUIPO_VER")
     */
    public function index(TipoEquipoRepository $tipoEquipoRepository, Request $request): Response
    {
        $formFiltros = $this->createForm(FiltroType::class);
        if ($request->query->get($formFiltros->getName())) {
            $formFiltros->handleRequest($request);
        }
        $filtros = $formFiltros->getData();

        $pagination = $this->paginator->paginate(
            $this->tipoEquipoRepository->findForActionIndex($filtros),
            $request->query->get('page', 1),
            12
        );
        return $this->render('admin/tipo_equipo/index.html.twig', [
            'pagination' => $pagination,
            'formFiltros' => $formFiltros->createView()
        ]);
    }

    /**
     * @Route("/new", name="tipo_equipo_new", methods={"GET", "POST"})
     * @IsGranted("TIPO_EQUIPO_CREAR")
     */
    public function new(Request $request, TipoEquipoRepository $tipoEquipoRepository): Response
    {
        $tipo = new TipoEquipo();
        $tipo->habilitar();
        $form = $this->createForm(TipoEquipoType::class, $tipo, ['is_edit' => false]);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if (preg_match('/^[\p{L}\p{N} ]+$/u', $tipo->getNombre())) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tipo);
                $em->flush();

                $this->addFlash("success", "Tipo de equipo creado correctamente.");
                return $this->redirectToRoute('tipo_equipo_index');
            } else {
                $this->addFlash("danger", "El nombre solo debe contener caracteres alfanuméricos y espacios.");
            }
            
        }

        return $this->render('admin/tipo_equipo/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tipo_equipo_show", methods={"GET"})
     * @IsGranted("TIPO_EQUIPO_VER")
     */
    public function show(TipoEquipo $tipoEquipo): Response
    {
        return $this->render('admin/tipo_equipo/show.html.twig', [
            'tipo' => $tipoEquipo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tipo_equipo_edit", methods={"GET", "POST"})
     * @IsGranted("TIPO_EQUIPO_EDITAR")
     */
    public function edit(Request $request, TipoEquipo $tipoEquipo, TipoEquipoRepository $tipoEquipoRepository): Response
    {
        $form = $this->createForm(TipoEquipoType::class, $tipoEquipo, ['is_edit' => true]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Validación usando expresión regular para permitir letras, números y espacios
            if (preg_match('/^[\p{L}\p{N} ]+$/u', $tipoEquipo->getNombre())) {
                $tipoEquipoRepository->add($tipoEquipo);
                $this->addFlash("success", "Editado correctamente.");
                return $this->redirectToRoute('tipo_equipo_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash("danger", "El nombre debe contener solo caracteres alfanuméricos y espacios.");
            }
        }
    
        return $this->render('admin/tipo_equipo/new.html.twig', [
            'tipo_equipo' => $tipoEquipo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tipo_equipo_delete", methods={"POST"})
     * @IsGranted("TIPO_EQUIPO_ELIMINAR")
     */
    public function delete(Request $request, TipoEquipo $tipoEquipo, TipoEquipoRepository $tipoEquipoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoEquipo->getId(), $request->request->get('_token'))) {
            if (count($tipoEquipo->getEquipos()) > 0) {
                $this->addFlash('danger', 'No se puede eliminar el tipo porque tiene equipos asociados.');
                return $this->redirectToRoute('tipo_equipo_show', ['id' => $tipoEquipo->getId()], Response::HTTP_SEE_OTHER);
            }

            $tipoEquipoRepository->remove($tipoEquipo);
            $this->addFlash('success', 'Tipo de equipo eliminado correctamente.');
        }

        return $this->redirectToRoute('tipo_equipo_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/edit/active/{id}", name="tipo_equipo_edit_active", requirements={"id":"\d+"})
     * @IsGranted("TIPO_EQUIPO_EDITAR")
     */
    public function editActive(TipoEquipo $tipoEquipo, Request $request){
        $tipoEquipo->setIsActive(!($tipoEquipo->getIsActive()));
        $em = $this->getDoctrine()->getManager();
        $tipoEquipoNombre = $tipoEquipo->getNombre();
        $em->persist($tipoEquipo);
        $em->flush();

        $this->addFlash("success","Tipo '$tipoEquipoNombre' ".($tipoEquipo->getIsActive() ? 'activado' : 'desactivado')." correctamente.");
        return $this->redirectToRoute("tipo_equipo_index");
    }
}
