<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\pedido_estado\FiltroType;
use App\Form\PedidoEstadoFormType;

use App\Repository\PedidoEstadoRepository;
use App\Entity\PedidoEstado;

class PedidoEstadoController extends AbstractController
{
    private $entityManager;
    private $paginator;
    private $pedidoEstadoRepository;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator, PedidoEstadoRepository $pedidoEstadoRepository)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->pedidoEstadoRepository = $pedidoEstadoRepository;
    }

    /**
     * @Route("/admin/pedido-estado", name="pedido_estado_index")
     * @IsGranted("TICKET_ASIGNAR")
     */
    public function index(Request $request): Response
    {
        $formFiltros = $this->createForm(FiltroType::class);
        if ($request->query->get($formFiltros->getName())) {
            $formFiltros->handleRequest($request);
        }
        $filtros = $formFiltros->getData();

        $pagination = $this->paginator->paginate(
            $this->pedidoEstadoRepository->findForActionIndex($filtros),
            $request->query->get('page', 1),
            12
        );

        return $this->render('admin/pedido_estado/index.html.twig', [
            'pagination' => $pagination,
            'formFiltros' => $formFiltros->createView(),
        ]);
    }

    /**
     * @Route("/admin/pedido-estado/new", name="pedido_estado_new")
     * @IsGranted("TICKET_ASIGNAR")
     */
    public function new(Request $request): Response
    {
        $estado = new PedidoEstado();
        $form = $this->createForm(PedidoEstadoFormType::class, $estado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->pedidoEstadoRepository->findOneByNombre($form['nombre']->getData())) {
                $this->addFlash('danger', "El elemento '".$form['nombre']->getData()."' ya se encuentra creado.");
            } else {
                $estado->setIsActive(true);
                $this->entityManager->persist($estado);
                $this->entityManager->flush();
                $this->addFlash('success', 'Estado creado correctamente.');
                return $this->redirectToRoute('pedido_estado_index');
            }
        }

        return $this->render('admin/pedido_estado/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/pedido-estado/edit/{id}", name="pedido_estado_edit", requirements={"id":"\d+"})
     * @IsGranted("TICKET_ASIGNAR")
     */
    public function edit(PedidoEstado $estado, Request $request): Response
    {
        $form = $this->createForm(PedidoEstadoFormType::class, $estado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existente = $this->pedidoEstadoRepository->findOneByNombre($form['nombre']->getData());
            if ($existente && $existente->getId() !== $estado->getId()) {
                $this->addFlash('danger', "El elemento '".$form['nombre']->getData()."' ya se encuentra creado.");
            } else {
                $this->entityManager->flush();
                $this->addFlash('success', 'Estado editado correctamente.');
                return $this->redirectToRoute('pedido_estado_index');
            }
        }

        return $this->render('admin/pedido_estado/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/pedido-estado/toggle/{id}", name="pedido_estado_toggle", requirements={"id":"\d+"})
     * @IsGranted("TICKET_ASIGNAR")
     */
    public function toggle(PedidoEstado $estado): Response
    {
        $estado->setIsActive(!$estado->getIsActive());
        $this->entityManager->flush();

        $this->addFlash(
            'success',
            sprintf(
                'Estado "%s" marcado como %s.',
                $estado->getNombre(),
                $estado->getIsActive() ? 'activo' : 'inactivo'
            )
        );

        return $this->redirectToRoute('pedido_estado_index');
    }
}
